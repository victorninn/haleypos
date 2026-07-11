@extends('layouts.base')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-stone-100 p-4">
    <div class="w-full max-w-5xl grid md:grid-cols-2 bg-white rounded-3xl shadow-xl overflow-hidden">

        {{-- LEFT: Form --}}
        <div class="flex flex-col justify-center p-8 md:p-14">
            <div class="w-full max-w-sm mx-auto">

                <div class="flex items-center gap-2 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($posBrand['name'], 0, 1)) }}
                    </div>
                    <span class="font-bold text-lg text-stone-900">{{ $posBrand['name'] }}</span>
                </div>

                <h1 class="text-2xl font-bold text-stone-900">Log in to your Account</h1>
                <p class="text-stone-500 text-sm mt-1">Welcome back! Select a method to log in:</p>

                @if($errors->any())
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Social buttons (static for now — will map to connected providers later) --}}
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button type="button" class="flex items-center justify-center gap-2 border border-stone-200 rounded-xl py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.47a5.54 5.54 0 0 1-2.4 3.63v3h3.88c2.27-2.09 3.57-5.17 3.57-8.82Z"/><path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.95-2.91l-3.88-3c-1.08.72-2.46 1.15-4.07 1.15-3.13 0-5.78-2.11-6.73-4.96H1.26v3.11A11.99 11.99 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.27 14.28A7.2 7.2 0 0 1 4.89 12c0-.79.14-1.56.38-2.28V6.61H1.26A12 12 0 0 0 0 12c0 1.94.46 3.77 1.26 5.39l4.01-3.11Z"/><path fill="#EA4335" d="M12 4.77c1.76 0 3.35.6 4.6 1.8l3.44-3.44C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.69 1.26 6.61l4.01 3.11C6.22 6.88 8.87 4.77 12 4.77Z"/></svg>
                        Google
                    </button>
                    <button type="button" class="flex items-center justify-center gap-2 border border-stone-200 rounded-xl py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50 transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#1877F2" d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.7 4.53-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.95.93-1.95 1.89v2.26h3.32l-.53 3.49h-2.79V24C19.61 23.1 24 18.1 24 12.07Z"/></svg>
                        Facebook
                    </button>
                </div>

                <div class="flex items-center gap-3 my-6">
                    <div class="h-px bg-stone-200 flex-1"></div>
                    <span class="text-xs text-stone-400">or continue with email</span>
                    <div class="h-px bg-stone-200 flex-1"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div class="relative">
                        <svg class="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <input class="input pl-9" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="relative">
                        <svg class="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input class="input pl-9 pr-9" type="password" name="password" placeholder="Password" required>
                        <svg class="w-4 h-4 text-stone-400 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.243 4.243M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                    </div>

<div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-stone-600">
                            <input type="checkbox" name="remember" class="rounded" checked>
                            Remember me
                        </label>
                    </div>

                    <button class="btn btn-primary w-full justify-center py-3 text-base">Log In</button>
                </form>

                <div class="mt-6 text-xs text-stone-500 bg-stone-50 rounded-xl p-3">
                    <div class="font-semibold text-stone-700 mb-1">Demo credentials</div>
                    Admin: <code>admin@haleys.test</code> / <code>password</code><br>
                    Staff: <code>staff@haleys.test</code> / <code>password</code>
                </div>


            </div>
        </div>

        {{-- RIGHT: Illustration panel (static placeholder — will become a live product/integration showcase) --}}
        <div class="hidden md:flex relative bg-brand-600 items-center justify-center overflow-hidden">
            <div class="absolute w-[420px] h-[420px] rounded-full bg-white/10"></div>

            <div class="relative flex items-center gap-10">
                {{-- Connector nodes --}}
                <div class="flex flex-col gap-8">
                    <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-brand-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 4a2 2 0 110 4 2 2 0 010-4zm0 12a6 6 0 01-5-2.7c.03-1.66 3.33-2.6 5-2.6s4.97.94 5 2.6A6 6 0 0112 18z"/></svg>
                    </div>
                    <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-lg">
                        <div class="w-6 h-6 rounded bg-brand-600"></div>
                    </div>
                    <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.47a5.54 5.54 0 0 1-2.4 3.63v3h3.88c2.27-2.09 3.57-5.17 3.57-8.82Z"/><path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.95-2.91l-3.88-3c-1.08.72-2.46 1.15-4.07 1.15-3.13 0-5.78-2.11-6.73-4.96H1.26v3.11A11.99 11.99 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.27 14.28A7.2 7.2 0 0 1 4.89 12c0-.79.14-1.56.38-2.28V6.61H1.26A12 12 0 0 0 0 12c0 1.94.46 3.77 1.26 5.39l4.01-3.11Z"/><path fill="#EA4335" d="M12 4.77c1.76 0 3.35.6 4.6 1.8l3.44-3.44C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.69 1.26 6.61l4.01 3.11C6.22 6.88 8.87 4.77 12 4.77Z"/></svg>
                    </div>
                </div>

                {{-- Connector lines --}}
                <svg class="absolute -left-2 w-16 h-40" viewBox="0 0 60 160" fill="none">
                    <path d="M0 20 H30 V80 H60" stroke="white" stroke-opacity="0.4" stroke-width="2"/>
                    <path d="M0 80 H60" stroke="white" stroke-opacity="0.4" stroke-width="2"/>
                    <path d="M0 140 H30 V80 H60" stroke="white" stroke-opacity="0.4" stroke-width="2"/>
                </svg>

                {{-- Card mockup --}}
                <div class="w-56 bg-white rounded-2xl shadow-2xl p-4">
                    <div class="flex gap-1.5 mb-3">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    </div>
                    <div class="h-2 w-3/4 bg-stone-200 rounded mb-4"></div>
                    @foreach(range(1,3) as $i)
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 rounded-full bg-stone-200"></div>
                            <div class="h-2 flex-1 bg-stone-200 rounded"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="absolute bottom-14 text-center px-10">
                <h2 class="text-white text-xl font-bold">Connect with every application.</h2>
                <p class="text-white/70 text-sm mt-1">Everything you need in an easily customizable dashboard.</p>
                <div class="flex items-center justify-center gap-1.5 mt-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection