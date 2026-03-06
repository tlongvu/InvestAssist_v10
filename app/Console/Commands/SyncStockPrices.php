<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-stock-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch live stock prices from public APIs and update locally';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting stock price sync (FireAnt API)...');
        
        $stocks = Stock::all()->unique('symbol');
        
        $count = 0;
        
        // Use a 10 day window to ensure we get at least one trading day
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays(10)->format('Y-m-d');

        foreach ($stocks as $stock) {
            $symbol = strtoupper($stock->symbol);
            
            // FireAnt API v1 (no auth required, returns prices in VND directly)
            $url = "https://www.fireant.vn/api/Data/Markets/HistoricalQuotes?symbol={$symbol}&startDate={$startDate}&endDate={$endDate}";
            
            $this->line("Fetching {$symbol} from FireAnt API...");
            
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    // FireAnt returns array of objects with Close price in VND
                    // e.g. [{"Symbol":"FPT","Close":93000.0,"Date":"2026-02-27T00:00:00Z",...}]
                    if (is_array($data) && count($data) > 0) {
                        // Get the most recent closing price (last element = latest date)
                        $latest = end($data);
                        $price = $latest['Close'];
                        
                        // Update all stock entries with this symbol
                        Stock::where('symbol', $symbol)->update([
                            'current_price' => $price,
                            'updated_at' => now(),
                        ]);
                        
                        $this->info("Updated {$symbol} to " . number_format($price) . " VND");
                        $count++;
                    } else {
                        $this->error("Valid price data not found for {$symbol} - " . json_encode($data));
                    }
                } else {
                    $this->error("Failed to fetch {$symbol}. HTTP Status: " . $response->status());
                }
            } catch (\Exception $e) {
                $this->error("Error fetching {$symbol}: " . $e->getMessage());
                Log::error("SyncStockPrices Error for {$symbol}: " . $e->getMessage());
            }
            
            // Sleep a bit to avoid rate limits
            usleep(300000); // 0.3s
        }
        
        $this->info("Successfully updated $count stocks.");
    }
}
