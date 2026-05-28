@extends('superadmin.layout')
@section('title', 'Trash')

@section('topbar-actions')
<a href="{{ route('superadmin.businesses.index') }}" class="sa-btn sa-btn-ghost">← All businesses</a>
@endsection

@section('content')
<div class="mb-5 rounded-xl border border-rose-900/40 bg-rose-950/20 px-5 py-3 text-rose-300 text-sm" data-testid="trash-notice">
    Soft-deleted businesses live here. <strong>Restore</strong> brings them back exactly as they were.
    <strong>Permanently delete</strong> removes the business and <em>all</em> its users, children, sessions and receipts — this cannot be undone.
</div>

<div class="sa-card overflow-hidden">
    <table class="sa-table" data-testid="trash-table">
        <thead>
            <tr>
                <th>Business</th>
                <th>Code</th>
                <th>Deleted</th>
                <th>Last sub</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($businesses as $b)
                <tr>
                    <td class="font-semibold text-slate-200">{{ $b->name }}</td>
                    <td class="font-mono text-xs text-slate-500">{{ $b->code }}</td>
                    <td class="text-slate-400 text-xs font-mono">{{ $b->deleted_at?->format('d M Y H:i') }}</td>
                    <td class="text-slate-400 text-sm">{{ $b->subscription?->planLabel() ?? '—' }}</td>
                    <td class="text-right space-x-2">
                        <form method="POST" action="{{ route('superadmin.businesses.restoreDeleted', $b->id) }}" class="inline">
                            @csrf
                            <button class="sa-btn sa-btn-primary text-xs" data-testid="trash-restore-{{ $b->id }}">Restore</button>
                        </form>
                        <form method="POST" action="{{ route('superadmin.businesses.forceDelete', $b->id) }}" class="inline"
                              onsubmit="return confirm('Permanently delete {{ addslashes($b->name) }} and ALL its data? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button class="sa-btn sa-btn-danger text-xs" data-testid="trash-purge-{{ $b->id }}">Delete forever</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            @if($businesses->isEmpty())
                <tr><td colspan="5" class="text-center py-10 text-slate-500">Trash is empty.</td></tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $businesses->links() }}</div>
@endsection
