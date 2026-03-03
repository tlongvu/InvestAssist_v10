<x-app-layout>
    <x-slot name="header">
        <style>
            [x-cloak] { display: none !important; }
        </style>
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Cổ phiếu') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showBalances: (localStorage.getItem('finance_showBalances') ?? 'true') === 'true' }" x-init="$watch('showBalances', val => localStorage.setItem('finance_showBalances', val))">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-4">
                            <h3 class="text-lg font-medium">Quản lý danh mục cổ phiếu</h3>
                            <button @click="showBalances = !showBalances" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" title="Ẩn/hiện số liệu">
                                <!-- Eye Icon -->
                                <svg x-cloak x-show="showBalances" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Slash Icon -->
                                <svg x-show="!showBalances" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.53-2.923M15 12a3 3 0 00-3-3m-1.127-1.127A3.001 3.001 0 0012 15m4.354-4.354A9.953 9.953 0 0012 5c-4.478 0-8.268 2.943-9.542 7a10.021 10.021 0 001.077 2.067M15 12h.01M21.542 12a10.05 10.05 0 01-1.53 2.923" />
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <a href="{{ route('stocks.create') }}" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Thêm cổ phiếu mới
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Mã</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Công ty CK</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ngành</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Số lượng</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Giá vốn</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Giá hiện tại</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Tổng vốn ĐT</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Giá trị hiện tại</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Lãi/Lỗ</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Hành động</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($stocks as $stock)
                                    @php
                                        $invested = $stock->quantity * $stock->avg_price;
                                        $currentValue = $stock->quantity * $stock->current_price;
                                        $profit = $currentValue - $invested;
                                        $profitPct = $invested > 0 ? ($profit / $invested) * 100 : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">{{ $stock->symbol }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ optional($stock->exchange)->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ optional($stock->industry)->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-900">
                                            <span x-cloak x-show="showBalances">{{ number_format($stock->quantity) }}</span>
                                            <span x-cloak x-show="!showBalances">***</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-900">{{ number_format($stock->avg_price / 1000, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-900">{{ number_format($stock->current_price / 1000, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-600">
                                            <span x-cloak x-show="showBalances">{{ number_format($invested / 1000000, 2, ',', '.') }} Tr</span>
                                            <span x-cloak x-show="!showBalances">***</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-slate-800">
                                            <span x-cloak x-show="showBalances">{{ number_format($currentValue / 1000000, 2, ',', '.') }} Tr</span>
                                            <span x-cloak x-show="!showBalances">***</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="whitespace-nowrap text-sm font-bold {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit / 1000000, 2, ',', '.') }} Tr
                                            </div>
                                            <div class="whitespace-nowrap text-xs font-medium {{ $profitPct >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                {{ $profitPct >= 0 ? '+' : '' }}{{ number_format($profitPct, 2) }}%
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('stocks.edit', $stock) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                            <form action="{{ route('stocks.destroy', $stock) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bạn có chắc không?')">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-4 text-center text-sm text-slate-500">Không tìm thấy cổ phiếu nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $stocks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
