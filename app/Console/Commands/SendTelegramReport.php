<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Stock;
use App\Models\AssetHistory;
use App\Services\TelegramService;
use App\Services\PortfolioPerformanceService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendTelegramReport extends Command
{
    protected $signature = 'app:send-telegram-report';
    protected $description = 'Send daily portfolio summary to Telegram';

    public function handle(PortfolioPerformanceService $portfolioService)
    {
        $users = User::where('telegram_enabled', true)
                     ->whereNotNull('telegram_bot_token')
                     ->whereNotNull('telegram_chat_id')
                     ->get();

        if ($users->isEmpty()) {
            $this->info('Không có user nào cấu hình nhận báo cáo Telegram.');
            return;
        }

        // Hàm helper format ra format 'tr' (triệu)
        $formatMoney = function($amount) {
            $absAmount = abs($amount);
            if ($absAmount >= 1000000) {
                // Tránh lỗi hiển thị dài, vd 1.5tr thay vì 1.50tr, hoặc 25tr
                return rtrim(rtrim(number_format($amount / 1000000, 2), '0'), '.') . "tr";
            } elseif ($absAmount >= 1000) {
                return rtrim(rtrim(number_format($amount / 1000, 2), '0'), '.') . "k";
            }
            return number_format($amount);
        };

        $messagesSent = 0;

        foreach ($users as $user) {
            // Set user context
            $portfolioService->setUserId($user->id);

            // 1. Tổng Vốn = Net Capital (Deposits - Withdrawals)
            $totalInvested = $portfolioService->calculateTotalInvested();

            // 2. Hôm nay = Total Current Value (Stocks + Cash)
            $todayValue = $portfolioService->calculateTotalCurrentValue();

            // 3. Hôm qua = Latest snapshot BEFORE today
            $yesterdaySnapshot = AssetHistory::where('user_id', $user->id)
                ->where('date', '<', Carbon::today()->format('Y-m-d'))
                ->orderBy('date', 'desc')
                ->first();
            
            $yesterdayValue = $yesterdaySnapshot ? (float)$yesterdaySnapshot->total_value : $todayValue;

            // 4. Thay đổi hôm nay = Today - Yesterday
            $dailyChange = $todayValue - $yesterdayValue;
            $dailyChangeSign = $dailyChange >= 0 ? '+' : '';

            // 5. Chi tiết các CP (Gộp all sàn)
            $stockDetails = $portfolioService->getStockPerformance();
            
            if ($stockDetails->isEmpty() && $totalInvested == 0) {
                continue; // User này chưa có gì, bỏ qua
            }

            $detailsStrings = [];
            foreach ($stockDetails as $detail) {
                $sign = $detail['profit'] >= 0 ? '+' : '';
                $detailsStrings[] = "- {$detail['symbol']} {$sign}" . number_format($detail['profit'], 0) . " ({$sign}" . round($detail['profit_percentage'], 2) . "%)";
            }

            $message = "<b>Tổng danh mục của {$user->name}</b>\n\n"
                     . "Thay đổi hôm nay: {$dailyChangeSign}" . $formatMoney($dailyChange) . "\n"
                     . "Tổng vốn nạp: " . $formatMoney($totalInvested) . "\n"
                     . "Hôm qua: " . $formatMoney($yesterdayValue) . "\n"
                     . "Hôm nay: " . $formatMoney($todayValue) . "\n"
                     . "------------------------------\n"
                     . "<b>Chi tiết cổ phiếu (Lãi/Lỗ):</b>\n"
                     . (empty($detailsStrings) ? "(Trống)" : implode("\n", $detailsStrings));

            TelegramService::sendMessage($user, $message, 'HTML');
            $messagesSent++;
        }

        $this->info("Đã lặp qua các user và gửi {$messagesSent} báo cáo Telegram cá nhân thành công.");
    }
}
