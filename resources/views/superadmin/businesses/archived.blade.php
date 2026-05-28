@extends('superadmin.layout')
@section('title', 'Archive')

@section('topbar-actions')
<a href="{{ route('superadmin.businesses.index') }}" class="sa-btn sa-btn-ghost">← All businesses</a>
@endsection

@section('content')
<p class="text-slate-400 text-sm mb-5">Archived businesses are blocked from logging in but retained in the database. You can restore them at any time.</p>

<div class="sa-card overflow-hidden">
    <table class="sa-table" data-testid="archived-table">
        <thead><tr><th>Business</th><th>Code</th><th>Archived</th><th>Last sub</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
            @foreach($businesses as $b)
                <tr>
                    <td class="font-semibold text-slate-200">{{ $b->name }}</td>
                    <td class="font-mono text-xs text-slate-500">{{ $b->code }}</td>
                    <td class="text-slate-400 text-xs font-mono">{{ $b->archived_at?->format('d M Y H:i') }}</td>
                    <td class="text-slate-400 text-sm">{{ $b->subscription?->planLabel() ?? '—' }}</td>
                    <td class="text-right">
                        <form method="POST" action="{{ route('superadmin.businesses.restore', $b) }}" class="inline">
                            @csrf
                            <button class="sa-btn sa-btn-primary text-xs" data-testid="restore-{{ $b->id }}">Restore</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            @if($businesses->isEmpty())
                <tr><td colspan="5" class="text-center py-10 text-slate-500">No archived businesses.</td></tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $businesses->links() }}</div>
@endsection
