<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Cấu hình Ứng dụng') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Cài đặt nhận thông báo qua Telegram và thiết lập mức lãi suất tham chiếu gửi tiết kiệm.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-settings') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="bank_interest_rate" :value="__('Lãi suất ngân hàng mức tham chiếu (%/năm)')" />
            <x-text-input id="bank_interest_rate" name="bank_interest_rate" type="number" step="0.01" class="mt-1 block w-full" :value="old('bank_interest_rate', $user->bank_interest_rate)" required />
            <x-input-error class="mt-2" :messages="$errors->get('bank_interest_rate')" />
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <label for="telegram_enabled" class="inline-flex items-center">
                <input id="telegram_enabled" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="telegram_enabled" value="1" {{ old('telegram_enabled', $user->telegram_enabled) ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600 font-medium">{{ __('Kích hoạt nhận Webhook / Log qua Telegram Bot') }}</span>
            </label>
        </div>

        <div>
            <x-input-label for="telegram_bot_token" :value="__('Telegram Bot API Key (Token)')" />
            <x-text-input id="telegram_bot_token" name="telegram_bot_token" type="text" class="mt-1 block w-full" :value="old('telegram_bot_token', $user->telegram_bot_token)" placeholder="e.g. 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ" />
            <x-input-error class="mt-2" :messages="$errors->get('telegram_bot_token')" />
            <p class="text-xs text-slate-500 mt-1">Lấy token từ @BotFather qua Telegram.</p>
        </div>

        <div>
            <x-input-label for="telegram_chat_id" :value="__('Telegram Chat ID')" />
            <x-text-input id="telegram_chat_id" name="telegram_chat_id" type="text" class="mt-1 block w-full" :value="old('telegram_chat_id', $user->telegram_chat_id)" placeholder="e.g. 123456789 (hoặc ChatID nhóm bắt đầu bằng dấu trừ -123...)" />
            <x-input-error class="mt-2" :messages="$errors->get('telegram_chat_id')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Lưu cấu hình') }}</x-primary-button>

            @if (session('status') === 'settings-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Đã cập nhật cấu hình.') }}</p>
            @endif
        </div>
    </form>
</section>
