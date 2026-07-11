{{-- Superadmin (SaaS) layout — independent of any tenant branding. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Superadmin') · {{ config('platform.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      html, body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
      body { background: #070b14; color: #e2e8f0; }
      .sa-card { background: #0b1220; border: 1px solid rgba(148,163,184,.12); border-radius: 1rem; }
      .sa-input { width:100%; background:#0b1220; color:#e2e8f0; border:1px solid #1c2742; border-radius:.6rem; padding:.65rem .85rem; font-size:.95rem; }
      .sa-input:focus { outline:none; border-color:#22d3ee; box-shadow:0 0 0 3px rgba(34,211,238,.15); }
      .sa-label { display:block; font-size:.78rem; font-weight:600; color:#94a3b8; margin-bottom:.35rem; letter-spacing:.04em; text-transform:uppercase; }
      .sa-btn { display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1rem; font-weight:600; border-radius:.6rem; transition:all .15s ease; font-size:.9rem; }
      .sa-btn-primary { background:#06b6d4; color:#062b34; }
      .sa-btn-primary:hover { background:#22d3ee; }
      .sa-btn-ghost { background:#0b1220; color:#cbd5e1; border:1px solid #1c2742; }
      .sa-btn-ghost:hover { background:#111a2e; border-color:#2a3656; }
      .sa-btn-danger { background:#be123c; color:#fff5f7; }
      .sa-btn-danger:hover { background:#9f1239; }
      .sa-link { display:flex; align-items:center; gap:.6rem; padding:.6rem .85rem; border-radius:.55rem; color:#94a3b8; font-weight:500; font-size:.92rem; }
      .sa-link:hover { background:#0f1729; color:#22d3ee; }
      .sa-link.active { background:#0f1729; color:#22d3ee; border-left:2px solid #22d3ee; }
      .sa-chip { display:inline-flex; padding:.18rem .55rem; border-radius:999px; font-size:.7rem; font-weight:600; letter-spacing:.04em; text-transform:uppercase; }
      .sa-chip-green { background:rgba(16,185,129,.15); color:#34d399; }
      .sa-chip-yellow { background:rgba(245,158,11,.15); color:#fbbf24; }
      .sa-chip-red { background:rgba(244,63,94,.15); color:#fb7185; }
      .sa-chip-gray { background:rgba(148,163,184,.15); color:#94a3b8; }
      table.sa-table { width:100%; }
      .sa-table thead th { text-align:left; font-size:.74rem; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:.85rem 1rem; border-bottom:1px solid #1c2742; }
      .sa-table tbody td { padding:.95rem 1rem; border-bottom:1px solid rgba(28,39,66,.55); font-size:.92rem; }
      .sa-table tbody tr:hover { background:rgba(28,39,66,.35); }
    </style>
</head>
<body class="min-h-screen">
<div class="min-h-screen flex">
    <aside class="hidden md:flex md:flex-col w-64 bg-ink-900/80 border-r border-ink-700/60 px-3 py-6 gap-1">
        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 mb-6" data-testid="sa-brand">
            <div class="w-10 h-10 rounded-xl bg-accent-500 text-ink-950 flex items-center justify-center font-extrabold text-lg">
                {{ strtoupper(substr(config('platform.name'), 0, 1)) }}
            </div>
            <div>
                <div class="font-bold text-slate-100 leading-tight">{{ config('platform.name') }}</div>
                <div class="text-[11px] text-slate-500 font-mono uppercase tracking-widest">superadmin</div>
            </div>
        </a>

        @php $r = request()->route()?->getName(); @endphp
        <a href="{{ route('superadmin.dashboard') }}" class="sa-link {{ $r === 'superadmin.dashboard' ? 'active' : '' }}" data-testid="nav-dashboard">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
            Overview
        </a>
        <a href="{{ route('superadmin.businesses.index') }}" class="sa-link {{ str_starts_with($r ?? '', 'superadmin.businesses.') && $r !== 'superadmin.businesses.archived' ? 'active' : '' }}" data-testid="nav-businesses">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg>
            Businesses
        </a>
        <a href="{{ route('superadmin.businesses.archived') }}" class="sa-link {{ $r === 'superadmin.businesses.archived' ? 'active' : '' }}" data-testid="nav-archive">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>
            Archive
        </a>
        <a href="{{ route('superadmin.businesses.trashed') }}" class="sa-link {{ $r === 'superadmin.businesses.trashed' ? 'active' : '' }}" data-testid="nav-trash">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14z"/></svg>
            Trash
        </a>

        <div class="mt-auto pt-4 border-t border-ink-700/60">
            <div class="px-3 py-2 text-sm">
                <div class="font-semibold text-slate-200">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500 font-mono uppercase tracking-wider">superadmin</div>
            </div>
            <form method="POST" action="{{ route('superadmin.logout') }}" class="px-1">
                @csrf
                <button class="w-full text-left sa-link text-rose-400 hover:text-rose-300" data-testid="logout-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4-4-4M21 12H9M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/></svg>
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-w-0">
        <header class="px-8 py-5 border-b border-ink-700/60 bg-ink-900/40 sticky top-0 z-30 backdrop-blur">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-[11px] uppercase tracking-widest text-accent-400 font-mono">{{ config('platform.name') }} / control</div>
                    <h1 class="text-2xl font-bold text-slate-100 leading-tight mt-1">@yield('title', 'Overview')</h1>
                </div>
                <div class="flex items-center gap-2">@yield('topbar-actions')</div>
            </div>
        </header>

        @if(session('status'))
            <div class="mx-8 mt-5 rounded-xl border border-emerald-700/40 bg-emerald-900/20 text-emerald-300 px-4 py-3" data-testid="flash-status">
                {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mx-8 mt-5 rounded-xl border border-rose-700/40 bg-rose-900/20 text-rose-300 px-4 py-3" data-testid="flash-errors">
                <ul class="list-disc ms-5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="px-8 py-7">
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>