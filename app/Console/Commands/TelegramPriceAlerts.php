<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Stock;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPriceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:price-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra giá realtime và gửi cảnh báo tự động qua Telegram (Lãi > 10% hoặc Lỗ > 7%)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('telegram_enabled', true)
                     ->whereNotNull('telegram_bot_token')
                     ->whereNotNull('telegram_chat_id')
                     ->get();

        if ($users->isEmpty()) {
            $this->info('Không có user nào cấu hình nhận cảnh báo giá.');
            return;
        }

        $stocks = Stock::where('quantity', '>', 0)->get();
        if ($stocks->isEmpty()) {
            $this->info('Không có danh mục nào đang nắm giữ để cảnh báo.');
            return;
        }

        // Cập nhật giá realtime từ FireAnt (Giống logic DashboardController)
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays(5)->format('Y-m-d');
        $uniqueSymbols = $stocks->pluck('symbol')->unique();
        
        $currentPrices = [];

        foreach ($uniqueSymbols as $symbol) {
            try {
                $sym = strtoupper($symbol);
                $url = "https://www.fireant.vn/api/Data/Markets/HistoricalQuotes?symbol={$sym}&startDate={$startDate}&endDate={$endDate}";
                $response = Http::timeout(5)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (is_array($data) && count($data) > 0) {
                        $latest = end($data);
                        $currentPrices[$sym] = $latest['Close'];
                        
                        // Cập nhật vào DB luôn
                        Stock::where('symbol', $sym)->update([
                            'current_price' => $latest['Close'],
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Telegram Alerts - Lỗi fetch giá {$symbol}: " . $e->getMessage());
            }
        }

        // Check alerts per user (in a real app, stocks belong to users. Setup currently aggregates all stocks.)
        // Ngưỡng chốt lời / cắt lỗ
        $takeProfitLimit = 10.0;
        $cutLossLimit = -7.0;

        $alerts = [];

        foreach ($stocks as $stock) {
            if (!isset($currentPrices[$stock->symbol])) continue;

            $currentPrice = $currentPrices[$stock->symbol];
            $invested = $stock->avg_price;
            
            if ($invested == 0) continue;

            // % Lãi Lỗ
            $profitPct = (($currentPrice - $invested) / $invested) * 100;

            if ($profitPct >= $takeProfitLimit) {
                $alerts[] = "🚀 *[CHỐT LỜI]* {$stock->symbol} đang lãi mạnh *" . number_format($profitPct, 2) . "%*\n"
                          . " - Giá vốn: " . number_format($invested / 1000, 2) . "\n"
                          . " - Giá hiện tại: " . number_format($currentPrice / 1000, 2);
            } elseif ($profitPct <= $cutLossLimit) {
                $alerts[] = "⚠️ *[CẮT LỖ]* {$stock->symbol} đang âm tới *" . number_format($profitPct, 2) . "%*\n"
                          . " - Giá vốn: " . number_format($invested / 1000, 2) . "\n"
                          . " - Giá hiện tại: " . number_format($currentPrice / 1000, 2);
            }
        }

        if (empty($alerts)) {
            $this->info('Chưa có mã nào chạm ngưỡng 🔴 Cắt lỗ (7%) hoặc 🟢 Chốt lời (10%).');
            return;
        }

        $message = "🔔 *CẢNH BÁO DANH MỤC TRONG PHIÊN* 🔔\n\n" . implode("\n\n---\n", $alerts);

        foreach ($users as $user) {
            TelegramService::sendMessage($user, $message);
        }

        $this->info('Đã gửi cảnh báo giá qua Telegram thành công.');
    }
}
