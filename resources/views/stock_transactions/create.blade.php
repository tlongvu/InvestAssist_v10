<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Record Stock Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form method="POST" action="{{ route('stock-transactions.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Stock -->
                            <div>
                                <x-input-label for="stock_id" :value="__('Stock')" />
                                <select id="stock_id" name="stock_id" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="" disabled selected>Select a Stock</option>
                                    @foreach($stocks as $stock)
                                        <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                                            {{ $stock->symbol }} ({{ $stock->exchange->name ?? 'Unknown' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('stock_id')" class="mt-2" />
                            </div>

                            <!-- Type -->
                            <div>
                                <x-input-label for="type" :value="__('Transaction Type')" />
                                <select id="type" name="type" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="buy" {{ old('type') == 'buy' ? 'selected' : '' }}>Buy</option>
                                    <option value="sell" {{ old('type') == 'sell' ? 'selected' : '' }}>Sell</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Quantity -->
                            <div>
                                <x-input-label for="quantity" :value="__('Quantity')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" min="1" name="quantity" :value="old('quantity')" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <!-- Price -->
                            <div>
                                <x-input-label for="price" :value="__('Price per Share')" />
                                <x-text-input id="price" class="money-input block mt-1 w-full" type="text" name="price" :value="old('price')" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <x-input-label for="transaction_date" :value="__('Date')" />
                                <x-text-input id="transaction_date" class="datepicker block mt-1 w-full" type="text" name="transaction_date" :value="old('transaction_date', \Carbon\Carbon::today()->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('stock-transactions.index') }}" class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">
                                Cancel
                            </a>
                            <x-primary-button class="ms-3">
                                {{ __('Record Transaction') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
