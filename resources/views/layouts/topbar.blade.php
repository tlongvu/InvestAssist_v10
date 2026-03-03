<!-- Topbar -->
<header class="bg-white rounded-xl shadow-sm border border-slate-100 mx-1 mt-1 md:mx-4 md:mt-4 h-16 flex items-center justify-between px-3 md:px-6 z-10 shrink-0">
    <div class="flex-1 flex items-center gap-2 md:gap-4 truncate">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 focus:outline-none shrink-0" aria-label="Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- Page Title injected if set, otherwise empty -->
        @isset($header)
            {{ $header }}
        @else
            <h2 class="text-xl font-semibold text-slate-800 tracking-tight">InvestAssist</h2>
        @endisset
    </div>

    <div class="ml-2 md:ml-4 flex items-center space-x-2 md:space-x-4 shrink-0">
        <!-- Manual Sync Button -->
        <form method="POST" action="{{ route('dashboard.sync') }}" class="m-0 p-0 inline-flex">
            @csrf
            <button type="submit" id="sync-prices-btn" class="inline-flex items-center gap-1.5 md:gap-2 px-2.5 md:px-3 py-1.5 border border-[#2563EB] text-[#2563EB] text-sm font-medium rounded hover:bg-blue-50 transition-colors cursor-pointer">
                <svg id="sync-spinner" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="hidden md:inline">Đồng bộ giá</span>
                <span class="md:hidden">Sync</span>
            </button>
        </form>

        <!-- Notifications -->
        <button type="button" class="p-1.5 text-slate-400 hover:text-slate-500 bg-slate-50 hover:bg-slate-100 rounded-full transition-colors relative cursor-pointer" aria-label="Notifications">
             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1 right-1 block items-center justify-center h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>

        <!-- Profile Menu Dropdown -->
        <div class="relative ml-2" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" class="flex items-center gap-1 focus:outline-none cursor-pointer">
                <span class="text-sm font-medium text-slate-600 hidden md:block">{{ Auth::user()->name }}</span>
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-md shadow-lg py-1 z-50 overflow-hidden" style="display: none;">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer">Hồ sơ</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
