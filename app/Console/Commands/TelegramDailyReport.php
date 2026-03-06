<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\TelegramService;
use App\Services\PortfolioPerformanceService;

class TelegramDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi báo cáo tổng kết tài sản hàng ngày qua Telegram';

    /**
     * Execute the console command.
     */
    public function handle(PortfolioPerformanceService $portfolioService)
    {
        $users = User::where('telegram_enabled', true)
                     ->whereNotNull('telegram_bot_token')
                     ->whereNotNull('telegram_chat_id')
                     ->get();

        if ($users->isEmpty()) {
            $this->info('Không có cấu hình Telegram nào được bật.');
            return;
        }

        foreach ($users as $user) {
            $invested = $portfolioService->calculateTotalInvested();
            $currentValue = $portfolioService->calculateTotalCurrentValue();
            $profitLoss = $portfolioService->calculateProfitLoss();

            $cashData = $portfolioService->getLiquidCashByExchange();
            $totalLiquid = $cashData['total_liquid'] ?? 0;

            $totalAssets = $currentValue + $totalLiquid;

            $date = \Carbon\Carbon::now()->format('d/m/Y');
            
            $statusIcon = $profitLoss['absolute'] >= 0 ? '🟢' : '🔴';
            $pnlMark = $profitLoss['absolute'] >= 0 ? '+' : '';

            $message = "📊 *BÁO CÁO TÀI SẢN (CUỐI NGÀY {$date})*\n\n"
                     . "Tổng tài sản: *" . number_format($totalAssets, 0, ',', '.') . " VND*\n"
                     . "Trong đó:\n"
                     . " - Cổ phiếu: " . number_format($currentValue, 0, ',', '.') . " VND\n"
                     . " - Sức mua: " . number_format($totalLiquid, 0, ',', '.') . " VND\n\n"
                     . "{$statusIcon} *Hiệu suất (Tính riêng cổ phiếu):*\n"
                     . " - Tổng mua: " . number_format($invested, 0, ',', '.') . " VND\n"
                     . " - Lãi/Lỗ: *" . $pnlMark . number_format($profitLoss['absolute'], 0, ',', '.') . " VND (" . $pnlMark . number_format($profitLoss['percentage'], 2) . "% )*\n\n"
                     . "Chúc bạn một buổi tối vui vẻ! ✨";

            TelegramService::sendMessage($user, $message);
        }

        $this->info('Daily reports sent successfully.');
    }
}
