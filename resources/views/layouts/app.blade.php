@extends('layouts.base')

@section('body')
<div class="min-h-screen flex">
    <aside class="hidden md:flex md:flex-col w-64 bg-white border-r border-stone-200 px-4 py-6 gap-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 mb-6">
            <img src="{{ asset(ltrim($posBrand['logo'], '/')) }}"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                 alt="{{ $posBrand['name'] }}"
                 class="w-10 h-10 rounded-xl object-cover">
            <div class="w-10 h-10 rounded-xl bg-brand-500 text-white items-center justify-center font-bold text-lg" style="display:none">
                {{ strtoupper(substr($posBrand['name'], 0, 1)) }}
            </div>
            <div>
                <div class="font-bold text-stone-900 leading-tight">{{ $posBrand['name'] }}</div>
                <div class="text-xs text-stone-500">{{ $posBrand['tagline'] }}</div>
            </div>
        </a>

        @php $r = request()->route()?->getName(); @endphp
        <a href="{{ route('dashboard') }}" class="nav-link {{ $r === 'dashboard' ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
            Dashboard
        </a>
        <a href="{{ route('children.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'children.') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M16 11a4 4 0 10-8 0 4 4 0 008 0zM2 21a8 8 0 0116 0"/></svg>
            Children
        </a>
        <a href="{{ route('sessions.create') }}" class="nav-link {{ $r === 'sessions.create' ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Start Session
        </a>
        <a href="{{ route('packages.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'packages.') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 16V8l-9-5-9 5v8l9 5 9-5z"/></svg>
            Packages
        </a>
        <a href="{{ route('receipts.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'receipts.') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 2v20l3-2 3 2 3-2 3 2V2H9z"/></svg>
            Receipts
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'reports.') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l3-3 4 4 5-7"/></svg>
            Reports
        </a>

        @if(auth()->user()?->isAdmin())
        <div class="mt-4 text-xs uppercase tracking-wider text-stone-400 px-3">Admin</div>
        <a href="{{ route('staff.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'staff.') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 21v-2a4 4 0 00-3-3.87M7 21v-2a4 4 0 013-3.87m4-5a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Staff
        </a>
        <a href="{{ route('settings.edit') }}" class="nav-link {{ str_starts_with($r ?? '', 'settings.') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6zm7-3l2-1-1-3-2 .5-1-1 .5-2-3-1-1 2-1.5-.5-1-2-3 1 .5 2-1 1-2-.5-1 3 2 1v2l-2 1 1 3 2-.5 1 1-.5 2 3 1 1-2 1.5.5 1 2 3-1-.5-2 1-1 2 .5 1-3-2-1v-2z"/></svg>
            Settings
        </a>
        @endif

        <div class="mt-auto pt-4 border-t border-stone-100">
            <div class="px-3 py-2 text-sm">
                <div class="font-semibold text-stone-800">{{ auth()->user()->name }}</div>
                <div class="text-xs text-stone-500 capitalize">{{ auth()->user()->role }} · {{ $currentBusiness->name ?? '' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="px-2">
                @csrf
                <button class="w-full text-left nav-link text-rose-600 hover:bg-rose-50">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-w-0">
        @include('partials.topbar')

        @if(session('status'))
            <div class="mx-6 mt-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3">
                {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mx-6 mt-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3">
                <ul class="list-disc ms-5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="px-6 py-6">
            @yield('content')
        </div>
    </main>
</div>
@endsection
