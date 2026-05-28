@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Live overview of active sessions')

@section('content')
@if($subscription)
    @php $days = $subscription->daysRemaining(); @endphp
    @if(! $subscription->is_lifetime && $days !== null && $days <= 7)
        <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-start gap-3" data-testid="subscription-warning">
            <svg class="w-5 h-5 mt-0.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.2 16a2 2 0 001.73 3z"/></svg>
            <div class="text-sm">
                <div class="font-bold text-amber-900">
                    @if($days <= 0)
                        Subscription expires today.
                    @elseif($days <= 3)
                        Subscription expires in {{ $days }} {{ $days === 1 ? 'day' : 'days' }}.
                    @else
                        Subscription expires in {{ $days }} days.
                    @endif
                </div>
                <div class="text-amber-800 mt-0.5">Contact your administrator to renew the {{ $subscription->planLabel() }} plan.</div>
            </div>
        </div>
    @endif
@endif

<div class="card p-5 mb-6" data-testid="subscription-card">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="text-xs uppercase tracking-widest text-stone-500 font-semibold">Subscription</div>
            <div class="mt-1 font-bold text-stone-900">{{ $subscription?->planLabel() ?? 'No active plan' }}</div>
            @if($subscription)
                <div class="text-xs text-stone-500 mt-0.5">
                    Started {{ $subscription->starts_at?->format('d M Y') }}
                    @if($subscription->expires_at) · expires {{ $subscription->expires_at->format('d M Y') }} @endif
                </div>
            @endif
        </div>
        <div class="flex items-center gap-3">
            @if($subscription)
                @php $badge = $subscription->statusBadge(); $days = $subscription->daysRemaining(); @endphp
                <span class="chip chip-{{ $badge['tone'] }}">{{ $badge['label'] }}</span>
                <div class="text-right">
                    <div class="text-2xl font-extrabold text-stone-900">{{ $days ?? '∞' }}</div>
                    <div class="text-[10px] uppercase tracking-widest text-stone-500 font-mono">days left</div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="text-sm text-stone-500">Active right now</div>
        <div class="mt-2 text-3xl font-extrabold text-stone-900">{{ $stats['active'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-sm text-stone-500">Completed today</div>
        <div class="mt-2 text-3xl font-extrabold text-stone-900">{{ $stats['today_completed'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-sm text-stone-500">Revenue today</div>
        <div class="mt-2 text-3xl font-extrabold text-brand-600">{{ $posBrand['currency_symbol'] }}{{ number_format($stats['today_revenue'], 2) }}</div>
    </div>
    <div class="card p-5">
        <div class="text-sm text-stone-500">Active packages</div>
        <div class="mt-2 text-3xl font-extrabold text-stone-900">{{ $stats['packages'] }}</div>
    </div>
</div>

<div class="flex items-center justify-between mb-3">
    <h2 class="text-xl font-bold text-stone-900">Active sessions</h2>
    <div class="text-sm text-stone-500">Auto-refreshes every 30s</div>
</div>

@if($activeSessions->isEmpty())
    <div class="card p-10 text-center text-stone-500">
        <div class="text-5xl mb-3">🛝</div>
        <div class="font-semibold text-stone-700 text-lg">No one is playing right now</div>
        <p class="mt-1">Start a new session from the top right.</p>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary mt-5 inline-flex">Start session</a>
    </div>
@else
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($activeSessions as $s)
            @php $color = $s->statusColor(); $rem = $s->remainingMinutes(); @endphp
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $s->child->photo_url }}" class="w-12 h-12 rounded-xl object-cover bg-stone-100" alt="">
                        <div>
                            <div class="font-bold text-stone-900">{{ $s->child->name }}</div>
                            <div class="text-xs text-stone-500">{{ $s->child->child_code }} · {{ $s->package->name }}</div>
                        </div>
                    </div>
                    <span class="chip chip-{{ $color }}">
                        {{ $s->isUnlimited() ? 'Unlimited' : ($rem === null ? '—' : ($rem.' min left')) }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-3 text-center text-sm">
                    <div>
                        <div class="text-stone-500 text-xs">Started</div>
                        <div class="font-semibold">{{ $s->start_time->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-stone-500 text-xs">Ends</div>
                        <div class="font-semibold">{{ $s->isUnlimited() ? '—' : $s->expected_end_time->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-stone-500 text-xs">Price</div>
                        <div class="font-semibold text-brand-600">{{ $posBrand['currency_symbol'] }}{{ number_format($s->final_price, 2) }}</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('sessions.extend', $s) }}" class="flex items-center gap-2">
                        @csrf
                        <select name="minutes" class="input py-2 text-sm">
                            <option value="15">+15 min</option>
                            <option value="30" selected>+30 min</option>
                            <option value="60">+60 min</option>
                        </select>
                        <button class="btn btn-ghost py-2 text-sm">Extend</button>
                    </form>

                    <form method="POST" action="{{ route('sessions.end', $s) }}">
                        @csrf
                        <button class="btn btn-danger py-2 text-sm">End & receipt</button>
                    </form>

                    <a href="{{ route('children.show', $s->child) }}" class="btn btn-ghost py-2 text-sm">Profile</a>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if($activePackages->isNotEmpty())
<h2 class="text-xl font-bold text-stone-900 mt-10 mb-3">Quick check-in</h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($activePackages as $p)
        <a href="{{ route('sessions.create', ['package' => $p->id]) }}" class="card p-5 hover:-translate-y-0.5 transition">
            <div class="w-10 h-10 rounded-xl mb-3" style="background: {{ $p->color }}1a; color: {{ $p->color }}">
                <div class="w-full h-full flex items-center justify-center font-bold">{{ substr($p->name, 0, 1) }}</div>
            </div>
            <div class="font-bold text-lg text-stone-900">{{ $p->name }}</div>
            <div class="text-sm text-stone-500">{{ $p->duration_label }}</div>
            <div class="mt-3 text-2xl font-extrabold text-stone-900">{{ $posBrand['currency_symbol'] }}{{ number_format($p->price, 2) }}</div>
        </a>
    @endforeach
</div>
@endif

@push('scripts')
<script>setTimeout(()=>location.reload(), 30000);</script>
@endpush
@endsection
