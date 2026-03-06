<div class="h-16 flex items-center px-6 border-b border-slate-200 shrink-0">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
        <img src="{{ asset('logo.svg') }}" alt="InvestAssist Logo" class="w-8 h-8 rounded-lg">
        <span class="text-xl font-bold tracking-tight text-[#09090B]">InvestAssist</span>
    </a>
</div>

<div class="flex-1 overflow-y-auto py-4 px-3">
    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-slate-100 text-[#2563EB] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-[#2563EB]' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Bảng điều khiển
        </a>

        <a href="{{ route('stocks.index') }}" class="{{ request()->routeIs('stocks.*') ? 'bg-slate-100 text-[#2563EB] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('stocks.*') ? 'text-[#2563EB]' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Danh mục của tôi
        </a>

        <a href="{{ route('stock-transactions.index') }}" class="{{ request()->routeIs('stock-transactions.*') ? 'bg-slate-100 text-[#2563EB] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('stock-transactions.*') ? 'text-[#2563EB]' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Lịch sử giao dịch
        </a>

        <a href="{{ route('cash-flows.index') }}" class="{{ request()->routeIs('cash-flows.*') ? 'bg-slate-100 text-[#2563EB] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('cash-flows.*') ? 'text-[#2563EB]' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Dòng tiền
        </a>

        {{-- Admin Section – chỉ hiển thị với admin --}}
        @if(Auth::user()->isAdmin())
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-purple-400 uppercase tracking-wider">Quản trị</p>
            </div>

            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Quản lý Tài khoản
            </a>

            <a href="{{ route('admin.exchanges.index') }}" class="{{ request()->routeIs('admin.exchanges.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.exchanges.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Công ty Chứng khoán
            </a>

            <a href="{{ route('admin.industries.index') }}" class="{{ request()->routeIs('admin.industries.*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} group flex items-center px-3 py-2.5 text-sm rounded-md transition-colors cursor-pointer">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.industries.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Ngành nghề
            </a>
        @endif
    </nav>
</div>

{{-- User Profile Footer --}}
<div class="border-t border-slate-200 p-4 shrink-0">
    <a href="{{ route('profile.edit') }}" class="flex items-center group cursor-pointer w-full">
        <div class="flex-shrink-0">
            <div class="h-9 w-9 rounded-full bg-[#2563EB] flex items-center justify-center text-white font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
        <div class="ml-3 min-w-0 flex-1">
            <div class="text-sm font-medium text-slate-700 truncate group-hover:text-slate-900">{{ Auth::user()->name }}</div>
            <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</div>
        </div>
    </a>
</div>
