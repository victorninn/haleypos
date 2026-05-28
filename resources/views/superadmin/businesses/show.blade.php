@extends('superadmin.layout')
@section('title', $business->name)

@section('topbar-actions')
<a href="{{ route('superadmin.businesses.edit', $business) }}" class="sa-btn sa-btn-ghost" data-testid="link-edit">Edit profile</a>
@endsection

@section('content')
@php $sub = $business->subscription; $badge = $sub?->statusBadge(); @endphp

<div class="grid lg:grid-cols-3 gap-5">
    <div class="sa-card p-6 lg:col-span-2">
        <div class="flex items-start gap-4">
            @if($business->logo_path)
                <img src="{{ asset('storage/'.$business->logo_path) }}" class="w-16 h-16 rounded-xl object-cover bg-slate-800">
            @else
                <div class="w-16 h-16 rounded-xl flex items-center justify-center font-extrabold text-2xl" style="background:{{ $business->primary_color }}33; color:{{ $business->primary_color }}">
                    {{ strtoupper(substr($business->name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-100">{{ $business->name }}</h2>
                <div class="text-slate-400 text-sm mt-0.5">
                    <span class="font-mono">{{ $business->code }}</span>
                    @if($business->email) · {{ $business->email }} @endif
                    @if($business->phone) · {{ $business->phone }} @endif
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    @if($business->archived_at)
                        <span class="sa-chip sa-chip-gray">Archived</span>
                    @elseif($business->is_active)
                        <span class="sa-chip sa-chip-green">Active</span>
                    @else
                        <span class="sa-chip sa-chip-red">Disabled</span>
                    @endif
                    @if($badge)
                        <span class="sa-chip sa-chip-{{ $badge['tone'] }}">{{ $badge['label'] }} · {{ $sub->planLabel() }}</span>
                    @endif
                    <span class="text-xs text-slate-500 font-mono">color {{ $business->primary_color }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6 grid sm:grid-cols-3 gap-3 text-sm">
            <div class="bg-slate-900/60 rounded-lg p-3">
                <div class="text-[10px] uppercase tracking-widest text-slate-500 font-mono">Address</div>
                <div class="mt-1 text-slate-300">{{ $business->address ?: '—' }}</div>
            </div>
            <div class="bg-slate-900/60 rounded-lg p-3">
                <div class="text-[10px] uppercase tracking-widest text-slate-500 font-mono">Currency</div>
                <div class="mt-1 text-slate-300 font-mono">{{ $business->currency_symbol }}</div>
            </div>
            <div class="bg-slate-900/60 rounded-lg p-3">
                <div class="text-[10px] uppercase tracking-widest text-slate-500 font-mono">Users</div>
                <div class="mt-1 text-slate-300">{{ $business->users->count() }}</div>
            </div>
        </div>

        <h3 class="mt-7 mb-3 text-sm uppercase tracking-widest text-slate-500 font-mono">Lifecycle actions</h3>
        <div class="flex flex-wrap gap-2">
            @if($business->archived_at)
                <form method="POST" action="{{ route('superadmin.businesses.restore', $business) }}">
                    @csrf
                    <button class="sa-btn sa-btn-primary" data-testid="restore-business">Restore from archive</button>
                </form>
            @else
                @if($business->is_active)
                    <form method="POST" action="{{ route('superadmin.businesses.deactivate', $business) }}">
                        @csrf
                        <button class="sa-btn sa-btn-ghost" data-testid="deactivate-business">Deactivate</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('superadmin.businesses.reactivate', $business) }}">
                        @csrf
                        <button class="sa-btn sa-btn-primary" data-testid="reactivate-business">Reactivate</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('superadmin.businesses.archive', $business) }}" onsubmit="return confirm('Archive this business? Owners can no longer log in.')">
                    @csrf
                    <button class="sa-btn sa-btn-ghost" data-testid="archive-business">Archive</button>
                </form>
            @endif
            <form method="POST" action="{{ route('superadmin.businesses.destroy', $business) }}" onsubmit="return confirm('Soft-delete this business?')">
                @csrf
                @method('DELETE')
                <button class="sa-btn sa-btn-danger" data-testid="delete-business">Soft-delete</button>
            </form>
        </div>

        <h3 class="mt-7 mb-3 text-sm uppercase tracking-widest text-slate-500 font-mono">Users</h3>
        <div class="overflow-hidden border border-slate-800 rounded-lg">
            <table class="sa-table">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
                <tbody data-testid="users-table">
                    @foreach($business->users as $u)
                        <tr>
                            <td class="text-slate-200">{{ $u->name }}</td>
                            <td class="text-slate-400">{{ $u->email }}</td>
                            <td class="text-slate-400 capitalize">{{ $u->role }}</td>
                            <td>
                                <span class="sa-chip {{ $u->is_active ? 'sa-chip-green' : 'sa-chip-gray' }}">
                                    {{ $u->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    @if($business->users->isEmpty())
                        <tr><td colspan="4" class="text-center py-6 text-slate-500">No users.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="sa-card p-6" data-testid="subscription-panel">
        <h2 class="text-lg font-bold text-slate-100">Subscription</h2>

        @if($sub)
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs uppercase tracking-widest font-mono">Plan</span>
                    <span class="text-slate-200 font-semibold">{{ $sub->planLabel() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs uppercase tracking-widest font-mono">Status</span>
                    <span class="sa-chip sa-chip-{{ $badge['tone'] }}">{{ $badge['label'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs uppercase tracking-widest font-mono">Started</span>
                    <span class="text-slate-300 font-mono text-xs">{{ $sub->starts_at?->format('d M Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs uppercase tracking-widest font-mono">Expires</span>
                    <span class="text-slate-300 font-mono text-xs">{{ $sub->expires_at?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs uppercase tracking-widest font-mono">Days left</span>
                    <span class="text-slate-200 font-semibold">{{ $sub->daysRemaining() ?? '∞' }}</span>
                </div>
            </div>

            @if($sub->isExpired())
                <form method="POST" action="{{ route('superadmin.businesses.subscription.reactivate', $business) }}" class="mt-5">
                    @csrf
                    <button class="sa-btn sa-btn-primary w-full justify-center" data-testid="reactivate-subscription">Reactivate subscription</button>
                </form>
            @endif
        @else
            <p class="text-slate-400 text-sm mt-3">No subscription yet. Assign one below.</p>
        @endif

        <form method="POST" action="{{ route('superadmin.businesses.subscription.update', $business) }}" class="mt-6 space-y-3" data-testid="update-subscription-form">
            @csrf
            <label class="sa-label">Change / extend plan</label>
            <select class="sa-input" name="plan_type" required>
                @foreach($plans as $key => $plan)
                    <option value="{{ $key }}" {{ $sub?->plan_type === $key ? 'selected' : '' }}>{{ $plan['label'] }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 pt-1">
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="radio" name="mode" value="replace" checked> Replace
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="radio" name="mode" value="extend"> Extend
                </label>
            </div>
            <p class="text-[11px] text-slate-500">
                <strong class="text-slate-400">Replace</strong> resets the start date to today.
                <strong class="text-slate-400">Extend</strong> adds the plan duration on top of the current expiry.
            </p>

            <button class="sa-btn sa-btn-primary w-full justify-center" data-testid="apply-subscription">Apply</button>
        </form>
    </div>
</div>
@endsection
