<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · {{ config('platform.name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <style>
      body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background:#070b14; color:#e2e8f0; }
      .grid-bg {
        background-image:
          radial-gradient(rgba(34,211,238,.10) 1px, transparent 1px),
          radial-gradient(rgba(34,211,238,.04) 1px, transparent 1px);
        background-size: 40px 40px, 14px 14px;
        background-position: 0 0, 20px 20px;
      }
      .sa-input { width:100%; background:#0b1220; color:#e2e8f0; border:1px solid #1c2742; border-radius:.6rem; padding:.75rem .9rem; font-size:.95rem; }
      .sa-input:focus { outline:none; border-color:#22d3ee; box-shadow:0 0 0 3px rgba(34,211,238,.15); }
      .sa-btn-primary { background:#22d3ee; color:#062b34; padding:.85rem 1.1rem; border-radius:.6rem; font-weight:700; }
      .sa-btn-primary:hover { background:#67e8f9; }
    </style>
</head>
<body class="min-h-screen grid-bg">
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-3 mb-8 justify-center" data-testid="sa-login-brand">
            <div class="w-12 h-12 rounded-xl bg-cyan-400 text-slate-900 flex items-center justify-center font-extrabold text-xl">
                {{ strtoupper(substr(config('platform.name'), 0, 1)) }}
            </div>
            <div class="text-left">
                <div class="font-extrabold text-slate-100 text-xl leading-none">{{ config('platform.name') }}</div>
                <div class="text-[11px] text-cyan-400 font-mono uppercase tracking-widest mt-1">superadmin · control plane</div>
            </div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-7 backdrop-blur">
            <h1 class="text-2xl font-bold text-slate-100">Sign in</h1>
            <p class="text-slate-400 text-sm mt-1">Operator access to the platform.</p>

            @if($errors->any())
                <div class="mt-4 rounded-lg border border-rose-700/40 bg-rose-900/20 text-rose-300 px-4 py-2.5 text-sm" data-testid="sa-login-errors">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.login.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Email</label>
                    <input class="sa-input" type="email" name="email" value="{{ old('email') }}" required autofocus data-testid="sa-login-email">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Password</label>
                    <input class="sa-input" type="password" name="password" required data-testid="sa-login-password">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-400">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700">
                    Remember this device
                </label>
                <button class="sa-btn-primary w-full" data-testid="sa-login-submit">Sign in</button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Tenant user? <a href="{{ route('login') }}" class="text-cyan-400 hover:underline">Use the tenant login →</a>
            </div>
        </div>

        <div class="text-center text-xs text-slate-600 font-mono mt-6 uppercase tracking-widest">
            © {{ date('Y') }} {{ config('platform.name') }}
        </div>
    </div>
</div>
</body>
</html>
