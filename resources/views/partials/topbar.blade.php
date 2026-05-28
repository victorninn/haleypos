<header class="px-6 py-4 border-b border-stone-200 bg-white/70 backdrop-blur sticky top-0 z-30">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-stone-900 leading-tight">@yield('title', 'Dashboard')</h1>
            <p class="text-sm text-stone-500 mt-0.5">@yield('subtitle', 'Tablet-ready POS for your playhouse.')</p>
        </div>
        <div class="flex items-center gap-3">
            @auth
                @include('partials.subscription_badge')
            @endauth
            @hasSection('topbar-actions')
                @yield('topbar-actions')
            @else
                <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                    New session
                </a>
            @endif
        </div>
    </div>
</header>
