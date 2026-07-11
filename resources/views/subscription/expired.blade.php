@extends('layouts.base')

@section('body')
<div class="min-h-screen flex items-center justify-center px-6 bg-stone-50">
    <div class="max-w-lg w-full text-center" data-testid="subscription-expired-page">
        <div class="mx-auto w-20 h-20 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.2 16a2 2 0 001.73 3z"/></svg>
        </div>
        <h1 class="text-3xl font-extrabold text-stone-900">
            @if(session('reason') === 'business_inactive')
                Business inactive
            @else
                Subscription expired
            @endif
        </h1>
        <p class="text-stone-600 mt-3">
            @if(session('reason') === 'business_inactive')
                Your account has been deactivated. Please contact the platform administrator to restore access.
            @else
                Your subscription is no longer active. Dashboard access, sessions and the parent portal are paused until it is renewed.
            @endif
        </p>

        @auth
            @php $b = auth()->user()->business; $sub = $b?->subscription; @endphp
            @if($sub)
                <div class="mt-6 inline-block text-left bg-white rounded-2xl border border-stone-200 p-5">
                    <div class="text-xs uppercase tracking-widest text-stone-500">Last plan</div>
                    <div class="font-semibold text-stone-800 mt-1">{{ $sub->planLabel() }}</div>
                    <div class="text-xs text-stone-500 mt-2">
                        Expired on <span class="font-mono">{{ $sub->expires_at?->format('d M Y') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-7">
                @csrf
                <button class="btn btn-ghost" data-testid="expired-logout-btn">Sign out</button>
            </form>
            <p class="text-stone-600 mt-3">Contact Admin: +63 917 712 3384 or email at admin@playhouse.com</p>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary mt-7 inline-flex">Back to login</a>
        @endauth
    </div>
</div>
@endsection
