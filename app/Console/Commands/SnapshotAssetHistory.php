<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SnapshotAssetHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically record the daily total portfolio value for all users.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\PortfolioPerformanceService $portfolioService)
    {
        $this->info('Starting daily portfolio snapshots...');

        $users = \App\Models\User::all();
        $dateStr = \Carbon\Carbon::now()->format('Y-m-d');
        $count = 0;

        foreach ($users as $user) {
            // Set service context to this user
            $portfolioService->setUserId($user->id);

            // Check if user has any cash flows (has started investing)
            $hasFlows = \App\Models\CashFlow::where('user_id', $user->id)->exists();
            if (!$hasFlows) {
                continue;
            }

            // Sync user's real-time stock prices if they have stocks before value calculation
            // This ensures accuracy when the cron job runs
            $symbols = \App\Models\Stock::where('user_id', $user->id)
                ->select('symbol')->distinct()->pluck('symbol');

            if ($symbols->isNotEmpty()) {
                $endDate = now()->format('Y-m-d');
                $startDate = now()->subDays(5)->format('Y-m-d'); // short window to ensure we just get the latest

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
                                \App\Models\Stock::where('symbol', $sym)
                                    ->where('user_id', $user->id)
                                    ->update([
                                        'current_price' => $price,
                                        'updated_at'    => now(),
                                    ]);
                            }
                        }
                    } catch (\Exception $e) {
                         \Illuminate\Support\Facades\Log::warning("Snapshot real-time fetch error for {$sym}: " . $e->getMessage());
                    }
                }
            }

            // Calculate current total value using the internal model data (which is now fresh)
            $currentValue = $portfolioService->calculateTotalCurrentValue();

            // Insert or Update the snapshot for today
            \App\Models\AssetHistory::updateOrCreate(
                ['user_id' => $user->id, 'date' => $dateStr],
                ['total_value' => round($currentValue)]
            );

            $count++;
        }

        $this->info("Completed daily portfolio snapshots for {$count} users.");
    }
}
