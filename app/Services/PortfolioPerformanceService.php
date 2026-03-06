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
    protected $customUserId = null;

    /**
     * Set a custom user ID for CLI or background job execution.
     */
    public function setUserId(int $userId): self
    {
        $this->customUserId = $userId;
        return $this;
    }

    /**
     * Lấy user_id của user hiện tại (hoặc truyền vào).
     */
    protected function userId(): int
    {
        return $this->customUserId ?? auth()->id();
    }

    public function calculateTotalInvested(): float
    {
        // Net Investment = Total Deposits - Total Withdrawals
        $flows = CashFlow::where('user_id', $this->userId())->get();
        return $flows->sum(fn($f) => $f->type === 'deposit' ? $f->amount : -$f->amount);
    }

    public function calculateTotalCurrentValue(): float
    {
        // Total Wealth = Value of current stocks + Liquid Cash on exchanges (manual input)
        $stocksValue = Stock::where('user_id', $this->userId())->get()->sum(function ($stock) {
            return $stock->quantity * $stock->current_price;
        });
        
        $liquidCash = $this->getLiquidCashByExchange()['total_liquid'] ?? 0;
        
        return $stocksValue + $liquidCash;
    }

    public function getWealthBreakdown(): array
    {
        $stocksValue = Stock::where('user_id', $this->userId())->get()->sum(function ($stock) {
            return $stock->quantity * $stock->current_price;
        });
        
        $liquidCash = $this->getLiquidCashByExchange()['total_liquid'] ?? 0;
        
        return [
            'stocks'      => $stocksValue,
            'liquid_cash' => $liquidCash,
            'total'       => $stocksValue + $liquidCash,
        ];
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
            ->groupBy('symbol')
            ->map(function ($stocks) {
                $totalQty      = $stocks->sum('quantity');
                $totalInvested = $stocks->sum(fn($s) => $s->quantity * $s->avg_price);
                $currentPrice  = $stocks->first()->current_price;
                $currentValue  = $totalQty * $currentPrice;
                $profit        = $currentValue - $totalInvested;
                $profitPct     = $totalInvested > 0 ? ($profit / $totalInvested) * 100 : 0;
                $avgPrice      = $totalQty > 0 ? round($totalInvested / $totalQty) : 0;

                $exchangeDisplay = $stocks
                    ->map(fn($s) => optional($s->exchange)->name)
                    ->filter()
                    ->unique()
                    ->join(' + ');

                $industry = optional($stocks->first()->industry)->name ?? '';

                return [
                    'symbol'           => $stocks->first()->symbol,
                    'exchange_display' => $exchangeDisplay,
                    'industry'         => $industry,
                    'quantity'         => $totalQty,
                    'avg_price'        => $avgPrice,
                    'current_price'    => $currentPrice,
                    'invested'         => $totalInvested,
                    'current_value'    => $currentValue,
                    'profit'           => $profit,
                    'profit_percentage'=> $profitPct,
                ];
            })
            ->values();
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

    public function getAssetHistory(string $period = 'month', ?string $endDateStr = null): array
    {
        $userId = $this->userId();
        $today = \Carbon\Carbon::now()->startOfDay();

        try {
            $endDate = $endDateStr 
                ? \Carbon\Carbon::createFromFormat('d/m/Y', $endDateStr)->startOfDay() 
                : $today;
        } catch (\Exception $e) {
            $endDate = $today;
        }

        // Không cho phép xem data tương lai
        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        $query = \App\Models\AssetHistory::where('user_id', $userId)
            ->where('date', '<=', $endDate->format('Y-m-d'));

        if ($period === 'year') {
            $rawHistories = $query->orderBy('date', 'asc')->get();
            
            $histories = $rawHistories->groupBy(function ($history) {
                return \Carbon\Carbon::parse($history->date)->startOfWeek()->format('Y-m-d');
            })->map(function ($weekItems) {
                return $weekItems->sortBy('date')->last();
            })->values();
        } else {
            // month (hôm nay) or custom_60
            $histories = $query->orderBy('date', 'desc')
                ->limit(60)
                ->get()
                ->reverse()
                ->values();
        }

        $labels = [];
        $values = [];

        foreach ($histories as $history) {
            $dateObj = \Carbon\Carbon::parse($history->date);
            $labels[] = $period === 'year'
                ? 'W' . $dateObj->isoWeek() . '/' . $dateObj->year
                : $dateObj->format('d/m');
            $values[] = round($history->total_value, 0);
        }

        return [
            'labels' => $labels,
            'data' => $values,
            'has_real_data' => count($values) > 0
        ];
    }
}
