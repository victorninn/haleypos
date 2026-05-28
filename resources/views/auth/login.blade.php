@extends('layouts.base')

@section('body')
<div class="min-h-screen grid md:grid-cols-2">
    <div class="hidden md:flex flex-col justify-between p-12 bg-gradient-to-br from-brand-500 to-rose-500 text-white">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center text-2xl font-extrabold">
                {{ strtoupper(substr($posBrand['name'], 0, 1)) }}
            </div>
            <div>
                <div class="font-bold text-xl">{{ $posBrand['name'] }}</div>
                <div class="text-white/80 text-sm">{{ $posBrand['tagline'] }}</div>
            </div>
        </div>
        <div>
            <h2 class="text-4xl lg:text-5xl font-extrabold leading-tight">
                Playhouse POS, simplified.
            </h2>
            <p class="mt-4 text-white/90 max-w-md">
                Manage check-ins, sessions, packages, receipts and reports — all from one tablet-friendly screen.
            </p>
        </div>
        <div class="text-sm text-white/80">© {{ date('Y') }} {{ $posBrand['name'] }}</div>
    </div>

    <div class="flex items-center justify-center p-8 md:p-16">
        <div class="w-full max-w-md card p-8">
            <h1 class="text-2xl font-bold text-stone-900">Welcome back</h1>
            <p class="text-stone-500 mt-1">Sign in to manage the playhouse.</p>

            @if($errors->any())
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="label">Email</label>
                    <input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div>
                    <label class="label">Password</label>
                    <input class="input" type="password" name="password" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox" name="remember" class="rounded">
                    Remember me on this tablet
                </label>
                <button class="btn btn-primary w-full justify-center py-3 text-base">Sign in</button>
            </form>

            <div class="mt-6 text-xs text-stone-500 bg-stone-50 rounded-xl p-3">
                <div class="font-semibold text-stone-700 mb-1">Demo credentials</div>
                Admin: <code>admin@haleys.test</code> / <code>password</code><br>
                Staff: <code>staff@haleys.test</code> / <code>password</code>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('parent.lookup') }}" class="text-sm text-brand-600 hover:underline">Parent? Look up your child →</a>
            </div>
        </div>
    </div>
</div>
@endsection
