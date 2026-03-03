<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Thêm cổ phiếu mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form method="POST" action="{{ route('stocks.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Symbol -->
                            <div>
                                <x-input-label for="symbol" :value="__('Mã chứng khoán')" />
                                <x-text-input id="symbol" class="block mt-1 w-full uppercase" type="text" name="symbol" :value="old('symbol')" required autofocus />
                                <x-input-error :messages="$errors->get('symbol')" class="mt-2" />
                            </div>

                            <!-- Exchange -->
                            <div>
                                <x-input-label for="exchange_id" :value="__('Công ty Chứng khoán')" />
                                <select id="exchange_id" name="exchange_id" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="" disabled selected>Chọn Công ty/Tài khoản</option>
                                    @foreach($exchanges as $exchange)
                                        <option value="{{ $exchange->id }}" {{ old('exchange_id') == $exchange->id ? 'selected' : '' }}>
                                            {{ $exchange->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('exchange_id')" class="mt-2" />
                            </div>

                            <!-- Industry -->
                            <div>
                                <x-input-label for="industry_id" :value="__('Ngành nghề')" />
                                <select id="industry_id" name="industry_id" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="" disabled selected>Chọn một ngành</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry->id }}" {{ old('industry_id') == $industry->id ? 'selected' : '' }}>
                                            {{ $industry->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('industry_id')" class="mt-2" />
                            </div>

                            <!-- Quantity -->
                            <div>
                                <x-input-label for="quantity" :value="__('Số lượng')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" min="0" name="quantity" :value="old('quantity', 0)" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <!-- Avg Price -->
                            <div>
                                <x-input-label for="avg_price" :value="__('Giá vốn trung bình')" />
                                <x-text-input id="avg_price" class="block mt-1 w-full" type="number" step="0.001" min="0" name="avg_price" :value="old('avg_price', 0)" required />
                                <x-input-error :messages="$errors->get('avg_price')" class="mt-2" />
                            </div>

                            <!-- Current Price -->
                            <div>
                                <x-input-label for="current_price" :value="__('Giá hiện tại')" />
                                <x-text-input id="current_price" class="block mt-1 w-full" type="number" step="0.001" min="0" name="current_price" :value="old('current_price', 0)" required />
                                <x-input-error :messages="$errors->get('current_price')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('stocks.index') }}" class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">
                                Hủy bỏ
                            </a>
                            <x-primary-button class="ms-3">
                                {{ __('Lưu cổ phiếu') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
