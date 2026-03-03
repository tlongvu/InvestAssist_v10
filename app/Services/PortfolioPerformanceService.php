<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\CashFlow;
use App\Models\StockTransaction;
use App\Models\Exchange;
use App\Models\UserExchangeBalance;
use Illuminate\Support\Collection;

class PortfolioPerformanceService
{
    /**
     * Lấy user_id của user hiện tại (hoặc truyền vào).
     */
    protected function userId(): int
    {
        return auth()->id();
    }

    public function calculateTotalInvested(): float
    {
        // Net Investment = Total Deposits - Total Withdrawals
        $flows = CashFlow::where('user_id', $this->userId())->get();
        return $flows->sum(fn($f) => $f->type === 'deposit' ? $f->amount : -$f->amount);
    }

    public function calculateTotalCurrentValue(): float
    {
        // Total Wealth = Value of current stocks + Liquid Cash on exchanges
        $stocksValue = Stock::where('user_id', $this->userId())->get()->sum(function ($stock) {
            return $stock->quantity * $stock->current_price;
        });
        
        $liquidCash = $this->getLiquidCashByExchange()['total_liquid'] ?? 0;
        
        return $stocksValue + $liquidCash;
    }

    public function calculateProfitLoss(): array
    {
        $invested = $this->calculateTotalInvested();
        $currentValue = $this->calculateTotalCurrentValue();
        $profit = $currentValue - $invested;
        $profitPercentage = $invested > 0 ? ($profit / $invested) * 100 : 0;

        return [
            'absolute'   => $profit,
            'percentage' => $profitPercentage,
        ];
    }

    public function getStockPerformance(): Collection
    {
        return Stock::where('user_id', $this->userId())
            ->with(['exchange', 'industry'])
            ->get()
            ->map(function ($stock) {
                $invested = $stock->quantity * $stock->avg_price;
                $currentValue = $stock->quantity * $stock->current_price;
                $profit = $currentValue - $invested;
                $profitPercentage = $invested > 0 ? ($profit / $invested) * 100 : 0;

                return [
                    'stock'            => $stock,
                    'invested'         => $invested,
                    'current_value'    => $currentValue,
                    'profit'           => $profit,
                    'profit_percentage' => $profitPercentage,
                ];
            });
    }

    /**
     * So sánh lợi nhuận thực tế với gửi ngân hàng (lãi kép).
     * Chỉ tính deposits của user hiện tại.
     */
    public function getBankComparison($annualRate = null): array
    {
        if (is_null($annualRate)) {
            $annualRate = auth()->check() ? (auth()->user()->bank_interest_rate / 100) : 0.07;
        }

        $now = \Carbon\Carbon::now();

        $deposits = CashFlow::where('user_id', $this->userId())
            ->where('type', 'deposit')
            ->get();

        $totalBankProfit = 0;
        $totalNetDeposited = 0;

        foreach ($deposits as $flow) {
            $daysHeld = $flow->transaction_date->diffInDays($now);
            $yearsHeld = $daysHeld / 365;

            $futureValue = $flow->amount * pow(1 + $annualRate, $yearsHeld);
            $profit = $futureValue - $flow->amount;

            $totalBankProfit += $profit;
            $totalNetDeposited += $flow->amount;
        }

        $totalBankValue = $totalNetDeposited + $totalBankProfit;
        $actualProfitData = $this->calculateProfitLoss();
        $totalCurrentValue = $this->calculateTotalCurrentValue();
        $profitDifference = $actualProfitData['absolute'] - $totalBankProfit;

        return [
            'total_net_deposited' => $totalNetDeposited,
            'total_bank_profit'   => round($totalBankProfit, 0),
            'total_bank_value'    => round($totalBankValue, 0),
            'total_actual_value'  => round($totalCurrentValue, 0),
            'actual_profit'       => $actualProfitData['absolute'],
            'actual_profit_pct'   => $actualProfitData['percentage'],
            'profit_difference'   => round($profitDifference, 0),
            'bank_outperformed'   => $profitDifference > 0,
        ];
    }

    public function getLiquidCashByExchange(): array
    {
        $userId = $this->userId();
        $exchanges = Exchange::all();
        $results = [];
        $totalLiquid = 0;

        foreach ($exchanges as $exchange) {
            $balanceRow = UserExchangeBalance::where('user_id', $userId)
                ->where('exchange_id', $exchange->id)
                ->first();

            $liquidCash = $balanceRow ? $balanceRow->balance : null;

            // Chỉ hiển thị sàn đã nhập số dư
            if ($liquidCash !== null) {
                $results[] = [
                    'exchange_name' => $exchange->name,
                    'exchange_id'   => $exchange->id,
                    'liquid_cash'   => $liquidCash,
                ];
                $totalLiquid += $liquidCash;
            }
        }

        return ['by_exchange' => $results, 'total_liquid' => $totalLiquid];
    }

    public function getAllocationByIndustry(): array
    {
        $allocation = [];
        Stock::where('user_id', $this->userId())->with('industry')->get()
            ->each(function ($stock) use (&$allocation) {
                $name = optional($stock->industry)->name ?? 'Other';
                $allocation[$name] = ($allocation[$name] ?? 0) + ($stock->quantity * $stock->current_price);
            });

        arsort($allocation);
        return ['labels' => array_keys($allocation), 'data' => array_values($allocation)];
    }

    public function getAllocationByExchange(): array
    {
        $allocation = [];
        Stock::where('user_id', $this->userId())->with('exchange')->get()
            ->each(function ($stock) use (&$allocation) {
                $name = optional($stock->exchange)->name ?? 'Other';
                $allocation[$name] = ($allocation[$name] ?? 0) + ($stock->quantity * $stock->current_price);
            });

        arsort($allocation);
        return ['labels' => array_keys($allocation), 'data' => array_values($allocation)];
    }

    public function getAllocationByStock(): array
    {
        $allocation = [];
        Stock::where('user_id', $this->userId())->get()
            ->each(function ($stock) use (&$allocation) {
                $allocation[$stock->symbol] = $stock->quantity * $stock->current_price;
            });

        arsort($allocation);
        return ['labels' => array_keys($allocation), 'data' => array_values($allocation)];
    }

    public function getAssetHistory(string $period = 'month'): array
    {
        $days = 30;
        if ($period === 'week') $days = 7;
        if ($period === 'day') $days = 2; // For day, we show yesterday and today at least

        $endDate = \Carbon\Carbon::now();
        $startDate = \Carbon\Carbon::now()->subDays($days + 5); // Fetch extra for weekend gap filling
        
        $userId = $this->userId();
        $stocks = Stock::where('user_id', $userId)->get();
        if ($stocks->isEmpty()) {
            return ['labels' => [], 'data' => []];
        }

        // 1. Fetch historical prices for unique symbols
        $symbols = $stocks->pluck('symbol')->unique()->toArray();
        $historicalPrices = [];
        foreach ($symbols as $symbol) {
            $prices = \Illuminate\Support\Facades\Cache::remember("hist_prices_{$symbol}", 3600, function () use ($symbol, $startDate, $endDate) {
                $sym = strtoupper($symbol);
                $url = "https://www.fireant.vn/api/Data/Markets/HistoricalQuotes?symbol={$sym}&startDate={$startDate->format('Y-m-d')}&endDate={$endDate->format('Y-m-d')}";
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
                    if ($response->successful()) {
                        $json = $response->json();
                        $res = [];
                        foreach ((array)$json as $item) {
                            $res[\Carbon\Carbon::parse($item['Date'])->format('Y-m-d')] = $item['Close'];
                        }
                        return $res;
                    }
                } catch (\Exception $e) {}
                return [];
            });
            $historicalPrices[$symbol] = $prices;
        }

        // 2. Build time series
        $labels = [];
        $values = [];
        $currentCash = $this->getLiquidCashByExchange()['total_liquid'];

        // Optimization: Fetch flows and trades once
        $allFlows = CashFlow::where('user_id', $userId)->where('transaction_date', '>', $startDate->startOfDay())->get();
        $allTrades = StockTransaction::whereHas('stock', fn($q) => $q->where('user_id', $userId))
            ->where('transaction_date', '>', $startDate->startOfDay())->get();

        for ($i = ($period === 'day' ? 1 : $days-1); $i >= 0; $i--) {
            $dateObj = \Carbon\Carbon::now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $endOfDay = (clone $dateObj)->endOfDay();
            
            $valuation = 0;
            foreach ($stocks as $stock) {
                // Determine quantity on this day by walking back transactions
                $tradesAfter = $allTrades->where('stock_id', $stock->id)->where('transaction_date', '>', $dateStr);
                $buysAfter = $tradesAfter->where('type', 'buy')->sum('quantity');
                $sellsAfter = $tradesAfter->where('type', 'sell')->sum('quantity');
                $dayQuantity = $stock->quantity - $buysAfter + $sellsAfter;

                if ($dayQuantity <= 0) continue;

                $p = $historicalPrices[$stock->symbol][$dateStr] ?? null;
                if (!$p) { // Weekend/Holiday fallback
                    for ($j=1; $j<=5; $j++) {
                        $prevDate = (clone $dateObj)->subDays($j)->format('Y-m-d');
                        if (isset($historicalPrices[$stock->symbol][$prevDate])) {
                            $p = $historicalPrices[$stock->symbol][$prevDate];
                            break;
                        }
                    }
                }
                if (!$p) $p = ($dateObj->isFuture() || $dateObj->isToday()) ? $stock->current_price : $stock->avg_price;
                $valuation += ($dayQuantity * $p);
            }

            // Cash on Day D = Current Cash - Deposits since Day D + Withdrawals since Day D
            $netFlowSinceDay = $allFlows->where('transaction_date', '>', $dateStr)
                ->sum(fn($f) => $f->type === 'deposit' ? $f->amount : -$f->amount);
            
            $netTradeCashSinceDay = $allTrades->where('transaction_date', '>', $dateStr)
                ->sum(fn($t) => $t->type === 'buy' ? -$t->quantity * $t->price : $t->quantity * $t->price);
            
            $dayCash = $currentCash - $netFlowSinceDay - $netTradeCashSinceDay;
            
            $labels[] = ($period === 'day' && $i === 0) ? \Carbon\Carbon::now()->format('H:00') : $dateObj->format('d/m');
            $values[] = round($valuation + $dayCash, 0);
        }

        return ['labels' => $labels, 'data' => $values];
    }
}
