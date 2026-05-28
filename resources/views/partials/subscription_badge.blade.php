{{-- Reusable subscription badge for tenant dashboards/header. --}}
@php
    $sub = $sub ?? (auth()->user()?->business?->subscription);
    if (! $sub) return;
    $badge = $sub->statusBadge();
    $days = $sub->daysRemaining();
    $warn = $days !== null && $days <= 7 && ! $sub->is_lifetime;
@endphp

<div class="flex items-center gap-2 text-sm" data-testid="subscription-badge">
    <span class="chip chip-{{ $badge['tone'] }}">{{ $badge['label'] }}</span>
    <span class="text-stone-500">
        {{ $sub->planLabel() }}
        @if($sub->expires_at && ! $sub->is_lifetime)
            · expires {{ $sub->expires_at->format('d M Y') }}
            @if($days !== null) ({{ $days }}d) @endif
        @elseif($sub->is_lifetime)
            · lifetime
        @endif
    </span>
    @if($warn)
        <span class="ml-1 text-amber-700 text-xs font-semibold">⚠ Renew soon</span>
    @endif
</div>
