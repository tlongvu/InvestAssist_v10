<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserExchangeBalance;
use App\Services\PortfolioPerformanceService;

class DashboardController extends Controller
{
    public function index(PortfolioPerformanceService $portfolioService)
    {
        $invested = $portfolioService->calculateTotalInvested();
        $currentValue = $portfolioService->calculateTotalCurrentValue();
        $profitLoss = $portfolioService->calculateProfitLoss();
        $stockPerformance = $portfolioService->getStockPerformance();
        $bankComparison = $portfolioService->getBankComparison();
        
        $allocationIndustry = $portfolioService->getAllocationByIndustry();
        $allocationExchange = $portfolioService->getAllocationByExchange();
        $allocationStock = $portfolioService->getAllocationByStock();
        // Mặc định lấy theo "Tháng" (30 ngày)
        $assetHistory = $portfolioService->getAssetHistory('month');
        $liquidCashData = $portfolioService->getLiquidCashByExchange();

        // Lấy tất cả sàn để hiển thị trong form cập nhật số dư
        $allExchanges = \App\Models\Exchange::all();
        $userBalances = UserExchangeBalance::where('user_id', auth()->id())
            ->pluck('balance', 'exchange_id');

        return view('dashboard', compact(
            'invested', 
            'currentValue', 
            'profitLoss', 
            'stockPerformance', 
            'bankComparison',
            'allocationIndustry',
            'allocationExchange',
            'allocationStock',
            'assetHistory',
            'liquidCashData',
            'allExchanges',
            'userBalances'
        ));
    }

    public function syncPrices()
    {
        \Illuminate\Support\Facades\Artisan::call('app:sync-stock-prices');
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        return back()->with('success', 'Prices synced successfully!');
    }

    public function realtime(PortfolioPerformanceService $portfolioService)
    {
        // 1. Fetch the latest live prices from FireAnt for all unique symbols of current user
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays(10)->format('Y-m-d');
        $symbols = \App\Models\Stock::where('user_id', auth()->id())
            ->select('symbol')->distinct()->pluck('symbol');
        
        foreach ($symbols as $symbol) {
            try {
                $sym = strtoupper($symbol);
                $url = "https://www.fireant.vn/api/Data/Markets/HistoricalQuotes?symbol={$sym}&startDate={$startDate}&endDate={$endDate}";
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (is_array($data) && count($data) > 0) {
                        $latest = end($data);
                        $price = $latest['Close'];
                        // Chỉ update stocks của user hiện tại
                        \App\Models\Stock::where('symbol', $sym)
                            ->where('user_id', auth()->id())
                            ->update([
                                'current_price' => $price,
                                'updated_at'    => now(),
                            ]);
                        \Illuminate\Support\Facades\Log::info("Realtime: Updated {$sym} to {$price} (FireAnt)");
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Realtime fetch error for {$symbol}: " . $e->getMessage());
            }
        }

        // 3. Compute real-time value based on fresh db rows
        $currentValue = $portfolioService->calculateTotalCurrentValue();
        $profitLoss = $portfolioService->calculateProfitLoss();
        $stockPerformance = $portfolioService->getStockPerformance();

        // format stock performance to be easily parsable by JS
        $stocks = [];
        foreach ($stockPerformance as $perf) {
            $stocks[$perf['stock']->symbol] = [
                'current_price' => $perf['stock']->current_price,
                'current_price_formatted' => number_format($perf['stock']->current_price / 1000, $perf['stock']->current_price % 1000 == 0 ? 0 : 2, ',', '.'),
                'pnl_absolute' => number_format($perf['profit'], 0, ',', '.'),
                'pnl_percentage' => number_format($perf['profit_percentage'], $perf['profit_percentage'] == (int)$perf['profit_percentage'] ? 0 : 2, ',', '.'),
                'is_profit' => $perf['profit'] >= 0,
            ];
        }

        return response()->json([
            'invested' => number_format($portfolioService->calculateTotalInvested(), 0, ',', '.'),
            'currentValue' => number_format($currentValue, 0, ',', '.'),
            'profitLossAbsolute' => number_format($profitLoss['absolute'], 0, ',', '.'),
            'profitLossPercentage' => number_format($profitLoss['percentage'], $profitLoss['percentage'] == (int)$profitLoss['percentage'] ? 0 : 2, ',', '.'),
            'isProfit' => $profitLoss['absolute'] >= 0,
            'stocks' => $stocks
        ]);
    }

    public function updateBalance(Request $request)
    {
        $request->validate([
            'balances'   => ['required', 'array'],
            'balances.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($request->balances as $exchangeId => $balance) {
            if ($balance === null || $balance === '') {
                UserExchangeBalance::where('user_id', auth()->id())
                    ->where('exchange_id', $exchangeId)
                    ->delete();
                continue;
            }

            UserExchangeBalance::updateOrCreate(
                ['user_id' => auth()->id(), 'exchange_id' => $exchangeId],
                ['balance' => $balance]
            );
        }

        return back()->with('success', 'Đã cập nhật số dư các sàn thành công.');
    }

    public function assetHistory(Request $request, PortfolioPerformanceService $portfolioService)
    {
        $period = $request->get('period', 'month'); // day, week, month
        return response()->json($portfolioService->getAssetHistory($period));
    }
}
