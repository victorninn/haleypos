@extends('superadmin.layout')
@section('title', 'Overview')

@section('topbar-actions')
<a href="{{ route('superadmin.businesses.create') }}" class="sa-btn sa-btn-primary" data-testid="cta-new-business">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
    New business
</a>
@endsection

@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
    <div class="sa-card p-5">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 font-mono">Businesses</div>
        <div class="mt-2 text-3xl font-extrabold text-slate-100">{{ $stats['businesses_total'] }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ $stats['businesses_active'] }} active · {{ $stats['businesses_archived'] }} archived</div>
    </div>
    <div class="sa-card p-5">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 font-mono">Active subs</div>
        <div class="mt-2 text-3xl font-extrabold text-emerald-400">{{ $stats['subs_active'] }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ $stats['subs_trial'] }} in trial</div>
    </div>
    <div class="sa-card p-5">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 font-mono">Expired subs</div>
        <div class="mt-2 text-3xl font-extrabold text-rose-400">{{ $stats['subs_expired'] }}</div>
        <div class="text-xs text-slate-500 mt-1">Need attention</div>
    </div>
    <div class="sa-card p-5">
        <div class="text-[11px] uppercase tracking-widest text-slate-500 font-mono">Tenant users</div>
        <div class="mt-2 text-3xl font-extrabold text-slate-100">{{ $stats['users_total'] }}</div>
        <div class="text-xs text-slate-500 mt-1">across all businesses</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-4">
    <div class="sa-card overflow-hidden" data-testid="expiring-soon">
        <div class="px-5 py-4 border-b border-ink-700/60 flex items-center justify-between">
            <h2 class="font-bold text-slate-100">Expiring within 14 days</h2>
            <span class="sa-chip sa-chip-yellow">{{ $expiringSoon->count() }}</span>
        </div>
        @if($expiringSoon->isEmpty())
            <div class="px-5 py-8 text-center text-slate-500 text-sm">No subscriptions expiring soon.</div>
        @else
            <table class="sa-table">
                <thead><tr><th>Business</th><th>Plan</th><th>Expires</th></tr></thead>
                <tbody>
                @foreach($expiringSoon as $sub)
                    <tr>
                        <td class="font-semibold text-slate-200">
                            <a href="{{ route('superadmin.businesses.show', $sub->business) }}" class="hover:text-cyan-400">
                                {{ $sub->business->name }}
                            </a>
                        </td>
                        <td class="text-slate-400">{{ $sub->planLabel() }}</td>
                        <td class="text-slate-400 font-mono text-xs">{{ $sub->expires_at?->format('d M Y') }} · {{ $sub->daysRemaining() }}d</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="sa-card overflow-hidden" data-testid="recent-businesses">
        <div class="px-5 py-4 border-b border-ink-700/60 flex items-center justify-between">
            <h2 class="font-bold text-slate-100">Recently added</h2>
            <a href="{{ route('superadmin.businesses.index') }}" class="text-cyan-400 text-xs hover:underline">All →</a>
        </div>
        @if($recentBusinesses->isEmpty())
            <div class="px-5 py-8 text-center text-slate-500 text-sm">No businesses yet. Create your first one.</div>
        @else
            <table class="sa-table">
                <thead><tr><th>Name</th><th>Code</th><th>Users</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($recentBusinesses as $b)
                    <tr>
                        <td>
                            <a href="{{ route('superadmin.businesses.show', $b) }}" class="font-semibold text-slate-200 hover:text-cyan-400">{{ $b->name }}</a>
                        </td>
                        <td class="font-mono text-xs text-slate-500">{{ $b->code }}</td>
                        <td class="text-slate-400">{{ $b->users_count }}</td>
                        <td>
                            @if($b->archived_at)
                                <span class="sa-chip sa-chip-gray">Archived</span>
                            @elseif($b->is_active)
                                <span class="sa-chip sa-chip-green">Active</span>
                            @else
                                <span class="sa-chip sa-chip-red">Disabled</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
