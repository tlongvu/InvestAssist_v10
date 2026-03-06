<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Chạy tự động cập nhật giá vào 15:00 các ngày trong tuần (Mon-Fri) sau khi đóng cửa
        $schedule->command('app:sync-stock-prices')->weekdays()->at('15:00')->timezone('Asia/Ho_Chi_Minh');

        // Chốt sổ tổng tài sản mỗi ngày vào cuối ngày
        $schedule->command('portfolio:snapshot')->dailyAt('15:00')->timezone('Asia/Ho_Chi_Minh');

        // Gửi báo cáo Telegram lúc 15:00 (sau khi sync giá xong)
        $schedule->command('app:send-telegram-report')->weekdays()->at('15:00')->timezone('Asia/Ho_Chi_Minh');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
