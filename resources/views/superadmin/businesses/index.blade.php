@extends('superadmin.layout')
@section('title', 'Businesses')

@section('topbar-actions')
<a href="{{ route('superadmin.businesses.trashed') }}" class="sa-btn sa-btn-ghost" data-testid="link-trash">Trash</a>
<a href="{{ route('superadmin.businesses.archived') }}" class="sa-btn sa-btn-ghost" data-testid="link-archive">Archive</a>
<a href="{{ route('superadmin.businesses.create') }}" class="sa-btn sa-btn-primary" data-testid="cta-new-business">+ New business</a>
@endsection

@section('content')
<form method="GET" class="mb-5 flex gap-2 max-w-md">
    <input class="sa-input" name="q" value="{{ $q }}" placeholder="Search name, code, email…" data-testid="search-input">
    <button class="sa-btn sa-btn-ghost">Search</button>
</form>

<div class="sa-card overflow-hidden">
    <table class="sa-table">
        <thead>
            <tr>
                <th>Business</th>
                <th>Code</th>
                <th>Subscription</th>
                <th>Expires</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody data-testid="businesses-table">
            @foreach($businesses as $b)
                @php $sub = $b->subscription; $badge = $sub?->statusBadge(); @endphp
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($b->logo_path)
                                <img src="{{ asset('storage/'.$b->logo_path) }}" class="w-9 h-9 rounded-lg object-cover bg-slate-800">
                            @else
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-sm" style="background: {{ $b->primary_color }}33; color: {{ $b->primary_color }}">
                                    {{ strtoupper(substr($b->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('superadmin.businesses.show', $b) }}" class="font-semibold text-slate-200 hover:text-cyan-400">{{ $b->name }}</a>
                                <div class="text-xs text-slate-500">{{ $b->users_count }} users</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono text-xs text-slate-400">{{ $b->code }}</td>
                    <td class="text-slate-300 text-sm">{{ $sub?->planLabel() ?? '—' }}</td>
                    <td class="text-slate-400 font-mono text-xs">{{ $sub?->expires_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($b->archived_at)
                            <span class="sa-chip sa-chip-gray">Archived</span>
                        @elseif(! $b->is_active)
                            <span class="sa-chip sa-chip-red">Disabled</span>
                        @elseif($badge)
                            <span class="sa-chip sa-chip-{{ $badge['tone'] }}">{{ $badge['label'] }}</span>
                        @else
                            <span class="sa-chip sa-chip-gray">No sub</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('superadmin.businesses.show', $b) }}" class="text-cyan-400 hover:underline text-sm">Manage</a>
                    </td>
                </tr>
            @endforeach
            @if($businesses->isEmpty())
                <tr><td colspan="6" class="text-center py-10 text-slate-500">No businesses found.</td></tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $businesses->links() }}</div>
@endsection
