<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Edit Cash Flow') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form method="POST" action="{{ route('cash-flows.update', $cashFlow) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Exchange -->
                            <div>
                                <x-input-label for="exchange_id" :value="__('Công ty Chứng khoán')" />
                                <select id="exchange_id" name="exchange_id" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    @foreach($exchanges as $exchange)
                                        <option value="{{ $exchange->id }}" {{ (old('exchange_id', $cashFlow->exchange_id) == $exchange->id) ? 'selected' : '' }}>
                                            {{ $exchange->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('exchange_id')" class="mt-2" />
                            </div>

                            <!-- Type -->
                            <div>
                                <x-input-label for="type" :value="__('Loại giao dịch')" />
                                <select id="type" name="type" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="deposit" {{ (old('type', $cashFlow->type) == 'deposit') ? 'selected' : '' }}>Nạp tiền</option>
                                    <option value="withdraw" {{ (old('type', $cashFlow->type) == 'withdraw') ? 'selected' : '' }}>Rút tiền</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Amount -->
                            <div>
                                <x-input-label for="amount" :value="__('Số tiền')" />
                                <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0.01" name="amount" :value="old('amount', $cashFlow->amount)" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <x-input-label for="transaction_date" :value="__('Ngày giao dịch')" />
                                <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', $cashFlow->transaction_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('cash-flows.index') }}" class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">
                                Hủy bỏ
                            </a>
                            <x-primary-button class="ms-3">
                                {{ __('Cập nhật') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
