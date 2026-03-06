<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Edit Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <form method="POST" action="{{ route('stocks.update', $stock) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Symbol -->
                            <div>
                                <x-input-label for="symbol" :value="__('Symbol')" />
                                <x-text-input id="symbol" class="block mt-1 w-full uppercase" type="text" name="symbol" :value="old('symbol', $stock->symbol)" required autofocus />
                                <x-input-error :messages="$errors->get('symbol')" class="mt-2" />
                            </div>

                            <!-- Exchange -->
                            <div>
                                <x-input-label for="exchange_id" :value="__('Exchange')" />
                                <select id="exchange_id" name="exchange_id" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    @foreach($exchanges as $exchange)
                                        <option value="{{ $exchange->id }}" {{ (old('exchange_id', $stock->exchange_id) == $exchange->id) ? 'selected' : '' }}>
                                            {{ $exchange->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('exchange_id')" class="mt-2" />
                            </div>

                            <!-- Industry -->
                            <div>
                                <x-input-label for="industry_id" :value="__('Industry')" />
                                <select id="industry_id" name="industry_id" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry->id }}" {{ (old('industry_id', $stock->industry_id) == $industry->id) ? 'selected' : '' }}>
                                            {{ $industry->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('industry_id')" class="mt-2" />
                            </div>

                            <!-- Quantity -->
                            <div>
                                <x-input-label for="quantity" :value="__('Quantity')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" min="0" name="quantity" :value="old('quantity', $stock->quantity)" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <!-- Avg Price -->
                            <div>
                                <x-input-label for="avg_price" :value="__('Average Price')" />
                                <x-text-input id="avg_price" class="money-input block mt-1 w-full" type="text" name="avg_price" :value="old('avg_price', number_format($stock->avg_price / 1000, 2, '.', ','))" required />
                                <x-input-error :messages="$errors->get('avg_price')" class="mt-2" />
                            </div>

                            <!-- Current Price -->
                            <div>
                                <x-input-label for="current_price" :value="__('Current Price')" />
                                <x-text-input id="current_price" class="money-input block mt-1 w-full" type="text" name="current_price" :value="old('current_price', number_format($stock->current_price / 1000, 2, '.', ','))" required />
                                <x-input-error :messages="$errors->get('current_price')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('stocks.index') }}" class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">
                                Cancel
                            </a>
                            <x-primary-button class="ms-3">
                                {{ __('Update Stock') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
