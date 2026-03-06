<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Gửi tin nhắn qua Telegram Bot
     *
     * @param \App\Models\User $user Khách hàng cài đặt Telegram
     * @param string $message Nội dung tin nhắn
     * @param string $parseMode Định dạng chữ (mặc định là Markdown)
     * @return bool Trả về true nếu thành công, false nếu thất bại hoặc chưa cấu hình
     */
    public static function sendMessage($user, $message, $parseMode = 'Markdown')
    {
        // Chặn nếu user chưa bật cấu hình hoặc chưa nhập thông tin
        if (!$user || !$user->telegram_enabled || empty($user->telegram_bot_token) || empty($user->telegram_chat_id)) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$user->telegram_bot_token}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $user->telegram_chat_id,
                'text' => $message,
                'parse_mode' => $parseMode,
            ]);

            if (!$response->successful()) {
                Log::warning('Lỗi gửi Telegram: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi ngoại lệ gửi Telegram: ' . $e->getMessage());
            return false;
        }
    }
}
