<x-app-layout>
    @slot('header')
        <h2 class="text-lg md:text-xl font-semibold text-slate-800 tracking-tight truncate">Bảng điều khiển danh mục</h2>
    @endslot

    <div class="space-y-6">
        
        <!-- Header Actions: Update Cash -->
        <div class="flex flex-wrap justify-end items-center gap-4">
            <button onclick="document.getElementById('balance-modal').classList.remove('hidden')" class="flex items-center gap-2 px-3 py-1.5 bg-[#2563EB] text-white border border-transparent text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Cập nhật Tiền các Sàn
            </button>
        </div>

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Invested -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-6 flex flex-col justify-center items-start">
                <div class="text-sm font-medium text-slate-500 mb-1">Tổng vốn đầu tư</div>
                <div class="text-2xl md:text-3xl font-bold text-slate-900 private-number" data-value="{{ $invested }}" data-currency="đ">{{ number_format($invested, 2, ',', '.') }}đ</div>
            </div>

            <!-- Current Portfolio Value -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-6 flex flex-col justify-center items-start">
                <div class="text-sm font-medium text-slate-500 mb-1">Giá trị hiện tại</div>
                <div class="text-2xl md:text-3xl font-bold text-slate-900 private-number" id="current-value-display" data-value="{{ $currentValue }}" data-currency="đ">{{ number_format($currentValue, 2, ',', '.') }}đ</div>
                <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs font-medium border-t border-slate-50 pt-2 w-full">
                    <div class="flex items-center text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                        Cổ phiếu: <span class="ml-1 text-slate-700 private-number" data-value="{{ $wealthBreakdown['stocks'] }}" data-currency="đ">{{ number_format($wealthBreakdown['stocks'], 2, ',', '.') }}đ</span>
                    </div>
                    <div class="flex items-center text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        Tiền mặt: <span class="ml-1 text-slate-700 private-number" data-value="{{ $wealthBreakdown['liquid_cash'] }}" data-currency="đ">{{ number_format($wealthBreakdown['liquid_cash'], 2, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>

            <!-- Total P&L -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-6 flex flex-col justify-center items-start">
                <div class="text-sm font-medium text-slate-500 mb-1">Tổng Lợi nhuận / Thua lỗ</div>
                <div class="flex items-baseline space-x-2">
                    <div id="profit-loss-display" class="text-2xl md:text-3xl font-bold {{ $profitLoss['absolute'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} private-number" data-value="{{ $profitLoss['absolute'] }}" data-sign="{{ $profitLoss['absolute'] > 0 ? '+' : '' }}" data-currency="đ">
                        {{ $profitLoss['absolute'] > 0 ? '+' : '' }}{{ number_format($profitLoss['absolute'], 2, ',', '.') }}đ
                    </div>
                    <div id="profit-loss-percentage" class="text-sm font-medium {{ $profitLoss['percentage'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} private-number" data-value="{{ $profitLoss['percentage'] }}" data-sign="{{ $profitLoss['percentage'] > 0 ? '+' : '' }}" data-suffix="%">
                        ({{ $profitLoss['percentage'] > 0 ? '+' : '' }}{{ number_format($profitLoss['percentage'], 2, ',', '.') }}%)
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Liquid Cash -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-6 flex flex-col items-start h-[200px] md:h-[250px] overflow-hidden relative">
                <div class="text-sm font-medium text-slate-500 mb-3 flex items-center justify-between w-full">
                    <div class="flex items-center gap-1">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="truncate">Tiền mặt (Sức mua)</span>
                    </div>
                </div>
                
                <div class="w-full flex-1 overflow-y-auto pr-1 space-y-3">
                    @if(count($liquidCashData['by_exchange']) > 0)
                        @foreach($liquidCashData['by_exchange'] as $exchange)
                            <div class="border-l-2 border-amber-400 pl-3 py-1">
                                <div class="font-medium text-slate-800 flex justify-between">
                                    <span class="truncate mr-2">{{ $exchange['exchange_name'] }}</span>
                                    <span class="text-amber-600 font-bold shrink-0 private-number" data-value="{{ $exchange['liquid_cash'] }}" data-currency="đ">
                                        {{ number_format($exchange['liquid_cash']) }}đ
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-sm text-slate-400 italic">Chưa có dữ liệu, hãy cập nhật.</div>
                    @endif
                </div>
                
                <div class="w-full border-t border-slate-100 pt-3 mt-auto flex justify-between items-baseline">
                    <div class="text-xs text-slate-500">Tổng sức mua:</div>
                    <div class="text-lg font-bold text-amber-600 private-number" data-value="{{ $liquidCashData['total_liquid'] }}" data-currency="đ">
                        {{ number_format($liquidCashData['total_liquid'], 0) }}đ
                    </div>
                </div>
            </div>

            <!-- Bank Equivalent -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-6 flex flex-col justify-start h-auto md:h-[250px] overflow-visible md:overflow-hidden relative">
                <div class="text-sm font-medium text-slate-500 mb-4 flex items-center gap-1">
                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                    <span class="truncate">So sánh Ngân hàng ({{ number_format(auth()->user()->bank_interest_rate ?? 7, 1) }}%/năm)</span>
                </div>
                
                @if($bankComparison['total_net_deposited'] > 0)
                <div class="w-full flex-1 flex flex-col justify-center">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-slate-100/60">
                                <td class="py-1.5 text-slate-500">Tổng nạp các sàn:</td>
                                <td class="py-1.5 text-right font-semibold text-slate-800 private-number" data-value="{{ $bankComparison['total_net_deposited'] }}" data-currency="đ">{{ number_format($bankComparison['total_net_deposited']) }}đ</td>
                            </tr>
                            <tr class="border-b border-slate-100/60">
                                <td class="py-1.5 text-slate-500 align-top">Nếu gửi Ngân hàng:</td>
                                <td class="py-1.5 text-right">
                                    <div class="font-bold text-slate-800 private-number" data-value="{{ $bankComparison['total_bank_value'] }}" data-currency="đ">{{ number_format($bankComparison['total_bank_value']) }}đ</div>
                                    <div class="text-[10px] text-emerald-600 font-medium private-number" data-value="{{ $bankComparison['total_bank_profit'] }}" data-sign="+" data-currency="đ">Lãi: +{{ number_format($bankComparison['total_bank_profit']) }}đ</div>
                                </td>
                            </tr>
                            @php $investValue = $bankComparison['total_net_deposited'] + $bankComparison['actual_profit']; @endphp
                            <tr class="border-b border-slate-100/60">
                                <td class="py-1.5 text-slate-500 align-top">Nếu nạp Đầu tư:</td>
                                <td class="py-1.5 text-right">
                                    <div class="font-bold text-indigo-600 private-number" data-value="{{ $investValue }}" data-currency="đ">{{ number_format($investValue) }}đ</div>
                                    <div class="text-[10px] {{ $bankComparison['actual_profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-medium private-number" data-value="{{ $bankComparison['actual_profit'] }}" data-sign="{{ $bankComparison['actual_profit'] >= 0 ? '+' : '' }}" data-currency="đ">
                                        Lời: {{ $bankComparison['actual_profit'] >= 0 ? '+' : '' }}{{ number_format($bankComparison['actual_profit']) }}đ
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 text-slate-700 font-semibold">Hiệu suất đầu tư:</td>
                                <td class="py-2 text-right">
                                    <div class="text-base font-bold {{ $bankComparison['profit_difference'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} private-number" data-value="{{ $bankComparison['profit_difference'] }}" data-sign="{{ $bankComparison['profit_difference'] > 0 ? '+' : '' }}" data-currency="đ">
                                        {{ $bankComparison['profit_difference'] > 0 ? '+' : '' }}{{ number_format($bankComparison['profit_difference']) }}đ
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col justify-center items-center h-full opacity-70 w-full">
                    <div class="text-sm text-slate-600 mt-2">Chưa có dữ liệu nạp tiền để tính lãi.</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Allocation Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-5 flex flex-col h-[300px] md:h-[350px]">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Phân bổ theo Ngành</h3>
                <div class="flex-1 relative w-full h-full">
                    @if(empty($allocationIndustry['data']))
                        <div class="flex items-center justify-center h-full text-slate-400 text-sm">No data</div>
                    @else
                        <canvas id="industryChart"></canvas>
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-5 flex flex-col h-[300px] md:h-[350px]">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Phân bổ theo Sàn</h3>
                <div class="flex-1 relative w-full h-full">
                    @if(empty($allocationExchange['data']))
                        <div class="flex items-center justify-center h-full text-slate-400 text-sm">No data</div>
                    @else
                        <canvas id="exchangeChart"></canvas>
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-5 flex flex-col h-[300px] md:h-[350px]">
                <h3 class="text-sm font-semibold text-slate-800 mb-4 border-b border-slate-100 pb-2">Phân bổ theo Cổ phiếu</h3>
                <div class="flex-1 relative w-full h-full">
                    @if(empty($allocationStock['data']))
                        <div class="flex items-center justify-center h-full text-slate-400 text-sm">No data</div>
                    @else
                        <canvas id="stockChart"></canvas>
                    @endif
                </div>
            </div>
        </div>

        <!-- Asset History Chart with Period Toggle -->
        @if($assetHistory['has_real_data'])
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 md:p-6 flex flex-col h-[350px] md:h-[450px]">
            <div class="flex flex-wrap justify-between items-center mb-4 border-b border-slate-100 pb-3 gap-2">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-slate-800">Tăng trưởng tài sản</h3>
                    <span id="last-updated-time" class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 hidden md:inline-block">Đồng bộ...</span>
                </div>
                <div class="flex flex-wrap items-center gap-3" id="chart-controls">
                    <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1" id="date-picker-container" style="display: none;">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <input type="text" id="chart-date-picker" class="bg-transparent border-none p-0 text-xs font-medium text-slate-600 focus:ring-0 w-24 cursor-pointer" value="{{ now()->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="flex gap-1" id="chart-period-toggles">
                        <button data-period="custom_60" class="px-3 py-1 text-xs font-medium rounded period-btn bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">60 ngày từ ngày chọn</button>
                        <button data-period="month" class="px-3 py-1 text-xs font-medium rounded period-btn bg-blue-50 text-blue-700 border border-blue-200 transition-colors">Hôm nay</button>
                        <button data-period="year" class="px-3 py-1 text-xs font-medium rounded period-btn bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">Xem tất cả</button>
                    </div>
                </div>
            </div>
            <div class="flex-1 relative w-full h-full">
                <canvas id="assetHistoryChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Assets Breakdown Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            <div class="px-4 md:px-6 py-4 md:py-5 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3 bg-slate-50/50">
                <h3 class="text-lg font-semibold text-slate-800">Chi tiết danh mục</h3>
                <div class="flex items-center gap-3">
                    <button id="toggle-privacy-btn" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                        <svg id="privacy-icon-show" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg id="privacy-icon-hide" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        <span id="privacy-text">Ẩn số liệu</span>
                    </button>
                    <a href="{{ route('stocks.create') }}" class="flex items-center gap-1 text-sm text-[#2563EB] hover:text-blue-800 font-medium cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Thêm cổ phiếu
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200" id="portfolio-table">
                    <thead class="bg-slate-50 select-none">
                        <tr>
                            <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap w-12">STT</th>
                            <th scope="col" data-sort="symbol" class="cursor-pointer px-4 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Mã <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="exchange" class="cursor-pointer px-4 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Công ty CK <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="industry" class="cursor-pointer px-4 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Ngành <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="quantity" class="cursor-pointer px-4 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Số lượng <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="price" class="cursor-pointer px-4 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Giá vốn <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="current-price" class="cursor-pointer px-4 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Giá hiện tại <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="invested" class="cursor-pointer px-4 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Tổng vốn ĐT <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="current-value" class="cursor-pointer px-4 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Giá trị hiện tại <span class="sort-arrow text-slate-300">↕</span></th>
                            <th scope="col" data-sort="profit" class="cursor-pointer px-4 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider group whitespace-nowrap">Lãi/Lỗ <span class="sort-arrow text-slate-300">↕</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse($stockPerformance as $performance)
                            <tr class="hover:bg-slate-50/70 transition-colors portfolio-row"
                                data-symbol="{{ $performance['symbol'] }}"
                                data-exchange="{{ $performance['exchange_display'] }}"
                                data-industry="{{ $performance['industry'] }}"
                                data-quantity="{{ $performance['quantity'] }}"
                                data-price="{{ $performance['avg_price'] }}"
                                data-current-price="{{ $performance['current_price'] }}"
                                data-invested="{{ $performance['invested'] }}"
                                data-current-value="{{ $performance['current_value'] }}"
                                data-profit="{{ $performance['profit'] }}"
                                data-profit-pct="{{ $performance['profit_percentage'] }}"
                            >
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-500 stt-cell">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 border border-slate-200 rounded px-2 py-1 inline-block bg-slate-50 text-sm">{{ $performance['symbol'] }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $performance['exchange_display'] ?: '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $performance['industry'] ?: '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-slate-700 private-number" data-value="{{ $performance['quantity'] }}">
                                    {{ number_format($performance['quantity']) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm text-slate-600">
                                    {{ number_format($performance['avg_price'] / 1000, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-slate-800" id="current-price-{{ $performance['symbol'] }}">
                                    {{ number_format($performance['current_price'] / 1000, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm text-slate-600 private-number" data-value="{{ $performance['invested'] / 1000000 }}" data-suffix=" Tr">
                                    {{ number_format($performance['invested'] / 1000000, 2, ',', '.') }} Tr
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-slate-800 private-number" data-value="{{ $performance['current_value'] / 1000000 }}" data-suffix=" Tr">
                                    {{ number_format($performance['current_value'] / 1000000, 2, ',', '.') }} Tr
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <div id="pnl-abs-{{ $performance['symbol'] }}" class="font-bold text-sm {{ $performance['profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} private-number" data-value="{{ $performance['profit'] / 1000000 }}" data-sign="{{ $performance['profit'] > 0 ? '+' : '' }}" data-suffix=" Tr">
                                        {{ $performance['profit'] > 0 ? '+' : '' }}{{ number_format($performance['profit'] / 1000000, 2, ',', '.') }} Tr
                                    </div>
                                    <div id="pnl-pct-{{ $performance['symbol'] }}" class="text-xs font-medium {{ $performance['profit_percentage'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $performance['profit_percentage'] > 0 ? '+' : '' }}{{ number_format($performance['profit_percentage'], 2, ',', '.') }}%
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-10 text-center text-slate-500">Chưa có cổ phiếu nào trong danh mục.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Balance Update Modal Popup -->
    <div id="balance-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('balance-modal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('dashboard.update-balance') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Cập nhật số dư Sức mua</h3>
                                <div class="mt-4 space-y-4">
                                    @isset($allExchanges)
                                        @foreach($allExchanges as $exchange)
                                            <div class="flex items-center justify-between gap-4">
                                                <label class="text-sm font-medium text-slate-700 w-1/3">{{ $exchange->name }}</label>
                                                <div class="flex items-center gap-2 w-2/3">
                                                    <input type="text" name="balances[{{ $exchange->id }}]" value="{{ isset($userBalances[$exchange->id]) ? number_format($userBalances[$exchange->id], 0, '.', ',') : '' }}" placeholder="VD: 50,000,000" class="money-input border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm w-full" />
                                                    <span class="text-xs text-slate-400">VNĐ</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-slate-500 italic">Đang tải dữ liệu sàn...</p>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Lưu</button>
                        <button type="button" onclick="document.getElementById('balance-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chart.js and scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const formatMoney = (value) => new Intl.NumberFormat('vi-VN').format(value);

            // 1. Data Privacy Toggle (Show/Hide Values)
            let isPrivate = localStorage.getItem('investassist_privacy') === 'true';
            const btnToggle = document.getElementById('toggle-privacy-btn');
            const iconShow  = document.getElementById('privacy-icon-show');
            const iconHide  = document.getElementById('privacy-icon-hide');
            const toggleText = document.getElementById('privacy-text');

            // ── Scrub real values out of the DOM into JS memory ──────────────
            // This way F12 / Elements panel shows *** in text and no data-value attr.
            const _privMeta = new WeakMap();
            document.querySelectorAll('.private-number').forEach(el => {
                _privMeta.set(el, {
                    value:    parseFloat(el.getAttribute('data-value') || 0),
                    sign:     el.getAttribute('data-sign')     || '',
                    suffix:   el.getAttribute('data-suffix')   || '',
                    currency: el.getAttribute('data-currency') || '',
                });
                // Strip attrs from DOM so DevTools can't read the raw number
                el.removeAttribute('data-value');
                el.removeAttribute('data-sign');
                el.removeAttribute('data-suffix');
                el.removeAttribute('data-currency');
            });

            function formatPrivateEl(el) {
                const meta   = _privMeta.get(el);
                if (!meta) return;
                const { value, sign, suffix, currency } = meta;
                let fmt;
                if (suffix === ' Tr' || suffix === '%') {
                    fmt = new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 2 }).format(value);
                } else {
                    fmt = formatMoney(value);
                }
                el.innerText = (sign + fmt + suffix + currency).replace('+-', '-');
            }

            function applyPrivacyState() {
                const els = document.querySelectorAll('.private-number');
                if (isPrivate) {
                    els.forEach(el => { el.innerText = '***'; });
                    // Remove CSS guard — *** text IS the privacy, element must stay visible
                    document.documentElement.classList.remove('privacy-active');
                    iconShow.classList.remove('hidden');
                    iconHide.classList.add('hidden');
                    toggleText.innerText = 'Hiện số liệu';
                } else {
                    els.forEach(el => formatPrivateEl(el));
                    document.documentElement.classList.remove('privacy-active');
                    iconShow.classList.add('hidden');
                    iconHide.classList.remove('hidden');
                    toggleText.innerText = 'Ẩn số liệu';
                }
            }

            btnToggle.addEventListener('click', () => {
                isPrivate = !isPrivate;
                localStorage.setItem('investassist_privacy', isPrivate);
                applyPrivacyState();
            });

            // Run once — sets *** or formatted numbers, removes CSS visibility guard
            applyPrivacyState();


            // 2. Chart Configurations
            const colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#06b6d4', '#ec4899', '#6366f1', '#14b8a6', '#f43f5e', '#84cc16'];

            const commonDoughnutOptions = {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, font: { size: 10 } } },
                    tooltip: {
                        backgroundColor: '#18181b', padding: 12, cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                const val = context.parsed || 0;
                                label += formatMoney(val) + ' VND';
                                
                                const dataset = context.dataset;
                                const total = dataset.data.reduce((a, b) => a + (b || 0), 0);
                                const percentage = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return `${label} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            };

            // Init Doughnuts
            ['industryChart', 'exchangeChart', 'stockChart'].forEach((id, idx) => {
                let elem = document.getElementById(id);
                if (!elem) return;
                
                let labels = [];
                let data = [];
                if (idx === 0) { labels = {!! json_encode($allocationIndustry['labels']) !!}; data = {!! json_encode($allocationIndustry['data']) !!}; }
                if (idx === 1) { labels = {!! json_encode($allocationExchange['labels']) !!}; data = {!! json_encode($allocationExchange['data']) !!}; }
                if (idx === 2) { labels = {!! json_encode($allocationStock['labels']) !!}; data = {!! json_encode($allocationStock['data']) !!}; }

                new Chart(elem, {
                    type: 'doughnut',
                    data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 0 }] },
                    options: commonDoughnutOptions
                });
            });

            // 3. Asset History Chart
            const histCtx = document.getElementById('assetHistoryChart');
            let historyChart = null;
            if (histCtx) {
                historyChart = new Chart(histCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($assetHistory['labels']) !!},
                        datasets: [{ label: 'Giá trị (VND)', data: {!! json_encode($assetHistory['data']) !!}, backgroundColor: '#3b82f6', borderRadius: 4 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: { grid: { drawBorder: false }, ticks: { callback: v => formatMoney(v) } },
                            x: { 
                                grid: { display: false },
                                ticks: {
                                    maxRotation: 45,
                                    autoSkip: false,
                                    font: { size: 9 }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: ctx => 'Tổng: ' + formatMoney(ctx.parsed.y) + ' VND' } }
                        }
                    }
                });
            }

            // Handle period and date changes
            let currentPeriod = 'month';

            const datePicker = flatpickr("#chart-date-picker", {
                dateFormat: "d/m/Y",
                maxDate: "today",
                defaultDate: "today",
                onChange: function() {
                    updateHistoryChart();
                }
            });

            function updateHistoryChart() {
                let url = `{{ route('dashboard.asset-history') }}?period=${currentPeriod}`;
                
                if (currentPeriod === 'custom_60') {
                    const date = document.getElementById('chart-date-picker').value;
                    url += `&date=${date}`;
                }
                
                fetch(url)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        if (historyChart && data.labels && data.data) {
                            historyChart.data.labels = data.labels;
                            historyChart.data.datasets[0].data = data.data;
                            historyChart.update();
                        }
                    })
                    .catch(err => console.error('Fetch error:', err));
            }

            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    currentPeriod = this.getAttribute('data-period');
                    
                    // Update active styling
                    document.querySelectorAll('.period-btn').forEach(b => {
                        b.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-200');
                        b.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
                    });
                    this.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
                    this.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200');

                    // Show/hide date-picker-container
                    const container = document.getElementById('date-picker-container');
                    if (currentPeriod === 'custom_60') {
                        container.style.display = 'flex';
                    } else {
                        container.style.display = 'none';
                    }

                    updateHistoryChart();
                });
            });


            // 4. Real-time Polling
            function isTradingSession() {
                const now = new Date();
                const day = now.getDay(); // 0: CN, 6: T7
                const hour = now.getHours();
                const minute = now.getMinutes();
                const timeStr = hour * 100 + minute; // VD: 9h15 -> 915, 14h45 -> 1445

                if (day === 0 || day === 6) return false; // Cuối tuần nghỉ
                if (timeStr < 900 || timeStr > 1445) return false; // Ngoài giờ nghỉ
                
                return true;
            }

            function fetchRealtimeData() {
                if (!isTradingSession()) {
                    console.log('Ngoài phiên giao dịch, tạm dừng cập nhật tự động.');
                    return;
                }
                fetch('{{ route('dashboard.realtime') }}')
                    .then(r => r.json())
                    .then(data => {
                        const valDisplay = document.getElementById('current-value-display');
                        const pnlDisplay = document.getElementById('profit-loss-display');
                        const pctDisplay = document.getElementById('profit-loss-percentage');

                        const currentVal = parseFloat(data.currentValue.replace(/,/g,''));
                        const pnlAbs     = parseFloat(data.profitLossAbsolute.replace(/,/g,''));
                        const pnlPct     = parseFloat(data.profitLossPercentage.replace(/,/g,''));
                        const isProfit   = data.isProfit;

                        // Update in-memory store (keeps DOM clean of raw values)
                        _privMeta.set(valDisplay, { value: currentVal, sign: '',                    suffix: '' });
                        _privMeta.set(pnlDisplay, { value: pnlAbs,     sign: isProfit ? '+' : '',  suffix: '' });
                        _privMeta.set(pctDisplay, { value: pnlPct,     sign: isProfit ? '(+' : '(', suffix: '%)' });

                        // Update color classes
                        [pnlDisplay, pctDisplay].forEach(el => {
                            el.classList.toggle('text-emerald-600', isProfit);
                            el.classList.toggle('text-rose-600', !isProfit);
                        });

                        // Render according to current privacy state
                        if (isPrivate) {
                            [valDisplay, pnlDisplay, pctDisplay].forEach(el => { el.innerText = '***'; });
                        } else {
                            [valDisplay, pnlDisplay, pctDisplay].forEach(el => formatPrivateEl(el));
                        }
                    }).catch(() => {});
            }
            setInterval(fetchRealtimeData, 600000); // 10 phút


            // 5. Table Sorting
            const thSort = document.querySelectorAll('th[data-sort]');
            let currentSort = null;
            let currentDir = 'desc';

            thSort.forEach(th => {
                th.addEventListener('click', () => {
                    const sortKey = th.getAttribute('data-sort');
                    const tbody = document.querySelector('#portfolio-table tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr.portfolio-row'));

                    if (currentSort === sortKey) {
                        currentDir = currentDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort = sortKey;
                        currentDir = 'desc';
                    }

                    // Update arrow indicators
                    document.querySelectorAll('th[data-sort] .sort-arrow').forEach(arrow => arrow.textContent = '↕');
                    const arrow = th.querySelector('.sort-arrow');
                    if (arrow) arrow.textContent = currentDir === 'asc' ? '↑' : '↓';

                    rows.sort((a, b) => {
                        let aVal = a.getAttribute('data-' + sortKey) || '';
                        let bVal = b.getAttribute('data-' + sortKey) || '';

                        // Text fields: exchange, industry, symbol
                        const textKeys = ['exchange', 'industry', 'symbol'];
                        if (textKeys.includes(sortKey)) {
                            return currentDir === 'asc'
                                ? aVal.localeCompare(bVal, 'vi')
                                : bVal.localeCompare(aVal, 'vi');
                        }

                        // Numeric fields: quantity, price, current-price, invested, current-value, profit
                        aVal = parseFloat(aVal) || 0;
                        bVal = parseFloat(bVal) || 0;
                        
                        if (aVal < bVal) return currentDir === 'asc' ? -1 : 1;
                        if (aVal > bVal) return currentDir === 'asc' ? 1 : -1;
                        return 0;
                    });

                    tbody.innerHTML = '';
                    rows.forEach((r, index) => {
                        const sttCell = r.querySelector('.stt-cell');
                        if (sttCell) sttCell.textContent = index + 1;
                        tbody.appendChild(r);
                    });
                });
            });
        });
    </script>
</x-app-layout>
