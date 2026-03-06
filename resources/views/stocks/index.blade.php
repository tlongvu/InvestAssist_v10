<x-app-layout>
    <x-slot name="header">
        <style>
            [x-cloak] { display: none !important; }
        </style>
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Cổ phiếu') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{
        showBalances: (localStorage.getItem('finance_showBalances') ?? 'true') === 'true'
    }" x-init="$watch('showBalances', val => localStorage.setItem('finance_showBalances', val))">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Top bar --}}
            <div class="flex flex-wrap justify-between items-center gap-3">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-slate-800">Quản lý danh mục cổ phiếu</h3>
                    {{-- Privacy toggle --}}
                    <button @click="showBalances = !showBalances"
                            class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none"
                            title="Ẩn/hiện số liệu">
                        <svg x-cloak x-show="showBalances" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="!showBalances" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.53-2.923M15 12a3 3 0 00-3-3m-1.127-1.127A3.001 3.001 0 0012 15m4.354-4.354A9.953 9.953 0 0012 5c-4.478 0-8.268 2.943-9.542 7a10.021 10.021 0 001.077 2.067M15 12h.01M21.542 12a10.05 10.05 0 01-1.53 2.923" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <a href="{{ route('stocks.create') }}"
                   class="px-4 py-2 bg-blue-600 rounded-lg font-semibold text-sm text-white hover:bg-blue-700 transition-colors shadow-sm">
                    THÊM CỔ PHIẾU MỚI
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($stocksByExchange->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-6 py-12 text-center text-slate-400 text-sm">
                    Chưa có cổ phiếu nào. <a href="{{ route('stocks.create') }}" class="text-blue-600 hover:underline">Thêm ngay</a>
                </div>
            @endif

            {{-- ── One card per exchange ── --}}
            @php
                $headers = [
                    ['label' => 'STT',             'sort' => null,            'align' => 'center'],
                    ['label' => 'Mã',              'sort' => 'symbol',        'align' => 'left'],
                    ['label' => 'Ngành',            'sort' => null,            'align' => 'left'],
                    ['label' => 'Số lượng',         'sort' => 'quantity',      'align' => 'right'],
                    ['label' => 'Giá vốn',          'sort' => null,            'align' => 'right'],
                    ['label' => 'Giá hiện tại',     'sort' => null,            'align' => 'right'],
                    ['label' => 'Tổng vốn ĐT',      'sort' => 'invested',      'align' => 'right'],
                    ['label' => 'Giá trị hiện tại', 'sort' => 'current_value', 'align' => 'right'],
                    ['label' => 'Lãi/Lỗ',           'sort' => 'profit',        'align' => 'right'],
                ];
            @endphp
            @foreach($stocksByExchange as $exchangeName => $exchangeStocks)
                @php
                    $exTotalInvested = $exchangeStocks->sum(fn($s) => $s->quantity * $s->avg_price);
                    $exTotalValue    = $exchangeStocks->sum(fn($s) => $s->quantity * $s->current_price);
                    $exProfit        = $exTotalValue - $exTotalInvested;
                    $exProfitPct     = $exTotalInvested > 0 ? ($exProfit / $exTotalInvested) * 100 : 0;
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden"
                     x-data="{ open: true }">

                    {{-- Exchange header --}}
                    <div class="flex items-center justify-between px-4 md:px-6 py-3 bg-slate-50 border-b border-slate-100 cursor-pointer select-none"
                         @click="open = !open">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                            <span class="font-semibold text-slate-800">{{ $exchangeName }}</span>
                            <span class="text-xs text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">
                                {{ $exchangeStocks->count() }} mã
                            </span>
                            @if(isset($balances[$exchangeName]))
                                <span class="text-xs font-medium text-amber-600 bg-amber-50 rounded-lg px-2 py-0.5">
                                    <span x-show="showBalances" x-cloak>Số dư: {{ number_format($balances[$exchangeName]) }}đ</span>
                                    <span x-show="!showBalances" x-cloak>Số dư: ***</span>
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            {{-- Exchange P&L summary badge --}}
                            <div class="flex items-baseline gap-1">
                                <span x-show="showBalances" x-cloak
                                      class="text-sm font-semibold {{ $exProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $exProfit >= 0 ? '+' : '' }}{{ number_format($exProfit / 1000000, 2, ',', '.') }} Tr
                                </span>
                                <span x-show="!showBalances" x-cloak class="text-sm text-slate-400">***</span>
                                <span class="text-xs font-medium {{ $exProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    ({{ $exProfit >= 0 ? '+' : '' }}{{ number_format($exProfitPct, 2, ',', '.') }}%)
                                </span>
                            </div>
                            {{-- Chevron --}}
                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Collapsible table --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50/60">
                                    <tr>
                                        @foreach($headers as $h)
                                            <th scope="col" class="px-4 md:px-6 py-3 text-{{ $h['align'] }} text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                @if($h['sort'])
                                                    <a href="{{ route('stocks.index', array_merge(request()->query(), ['sort' => $h['sort'], 'direction' => ($sort == $h['sort'] && $direction == 'asc') ? 'desc' : 'asc'])) }}"
                                                       class="group inline-flex items-center gap-1 hover:text-slate-700">
                                                        {{ $h['label'] }}
                                                        <span class="text-slate-300 group-hover:text-slate-500">
                                                            @if($sort == $h['sort'])
                                                                {{ $direction == 'asc' ? '↑' : '↓' }}
                                                            @else
                                                                ↕
                                                            @endif
                                                        </span>
                                                    </a>
                                                @else
                                                    {{ $h['label'] }}
                                                @endif
                                            </th>
                                        @endforeach
                                        <th scope="col" class="px-4 md:px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach($exchangeStocks as $stock)
                                        @php
                                            $invested     = $stock->quantity * $stock->avg_price;
                                            $currentValue = $stock->quantity * $stock->current_price;
                                            $profit       = $currentValue - $invested;
                                            $profitPct    = $invested > 0 ? ($profit / $invested) * 100 : 0;
                                        @endphp
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center text-slate-500 font-medium text-sm">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                                <span class="font-bold text-slate-900 border border-slate-200 rounded px-2 py-1 bg-slate-50 text-sm">{{ $stock->symbol }}</span>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-slate-500">
                                                {{ $stock->industry ? $stock->industry->name : '-' }}
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right font-medium text-slate-700">
                                                <span x-show="showBalances" x-cloak>{{ number_format($stock->quantity) }}</span>
                                                <span x-show="!showBalances" x-cloak class="text-slate-400">***</span>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-slate-600">
                                                {{ number_format($stock->avg_price / 1000, 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right font-medium text-slate-800">
                                                {{ number_format($stock->current_price / 1000, 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-slate-600">
                                                <span x-show="showBalances" x-cloak>{{ number_format($invested / 1000000, 2, ',', '.') }} Tr</span>
                                                <span x-show="!showBalances" x-cloak class="text-slate-400">***</span>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right font-medium text-slate-800">
                                                <span x-show="showBalances" x-cloak>{{ number_format($currentValue / 1000000, 2, ',', '.') }} Tr</span>
                                                <span x-show="!showBalances" x-cloak class="text-slate-400">***</span>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right">
                                                <div x-show="showBalances" x-cloak class="font-bold {{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $profit >= 0 ? '+' : '' }}{{ number_format($profit / 1000000, 2, ',', '.') }} Tr
                                                </div>
                                                <div x-show="!showBalances" x-cloak class="text-slate-400 text-sm">***</div>
                                                <div class="text-xs font-medium {{ $profitPct >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                    {{ $profitPct >= 0 ? '+' : '' }}{{ number_format($profitPct, 2, ',', '.') }}%
                                                </div>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('stocks.edit', $stock) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                                <form action="{{ route('stocks.destroy', $stock) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Bạn có chắc không?')">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                {{-- Exchange summary footer --}}
                                <tfoot>
                                    <tr class="bg-slate-50 border-t-2 border-slate-200">
                                        <td colspan="5" class="px-4 md:px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Tổng {{ $exchangeName }}
                                        </td>
                                        <td class="px-4 md:px-6 py-3 text-right font-semibold text-slate-700">
                                            <span x-show="showBalances" x-cloak>{{ number_format($exTotalInvested / 1000000, 2, ',', '.') }} Tr</span>
                                            <span x-show="!showBalances" x-cloak class="text-slate-400">***</span>
                                        </td>
                                        <td class="px-4 md:px-6 py-3 text-right font-semibold text-slate-800">
                                            <span x-show="showBalances" x-cloak>{{ number_format($exTotalValue / 1000000, 2, ',', '.') }} Tr</span>
                                            <span x-show="!showBalances" x-cloak class="text-slate-400">***</span>
                                        </td>
                                        <td class="px-4 md:px-6 py-3 text-right">
                                            <div x-show="showBalances" x-cloak class="font-bold {{ $exProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $exProfit >= 0 ? '+' : '' }}{{ number_format($exProfit / 1000000, 2, ',', '.') }} Tr
                                            </div>
                                            <div x-show="!showBalances" x-cloak class="text-slate-400 text-sm">***</div>
                                            <div class="text-xs font-medium {{ $exProfitPct >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                {{ $exProfitPct >= 0 ? '+' : '' }}{{ number_format($exProfitPct, 2, ',', '.') }}%
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
