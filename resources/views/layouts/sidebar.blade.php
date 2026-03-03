{{-- ====== MOBILE SIDEBAR DRAWER ====== --}}
{{-- Full-screen dim overlay, closes when clicked --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-slate-900/50 z-40 md:hidden"
     @click="sidebarOpen = false"
     style="display: none;">
</div>

{{-- Mobile-only slide-in sidebar --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col md:hidden"
     style="display: none;">
    @include('layouts._sidebar_content')
</div>

{{-- ====== DESKTOP SIDEBAR (always visible on md+) ====== --}}
<aside class="hidden md:flex w-64 bg-white border-r border-slate-200 flex-col shrink-0 h-screen sticky top-0">
    @include('layouts._sidebar_content')
</aside>
