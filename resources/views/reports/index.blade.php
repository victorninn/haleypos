@extends('layouts.app')
@section('title', 'Reports')
@section('subtitle', 'Daily, monthly, historical session reports')

@section('topbar-actions')
<a href="{{ route('reports.export', request()->only('from','to','status')) }}" class="btn btn-primary">Export CSV</a>
@endsection

@section('content')
<form method="GET" class="card p-4 mb-5 grid sm:grid-cols-4 gap-3 items-end">
    <div>
        <label class="label">From</label>
        <input type="date" class="input" name="from" value="{{ $from }}">
    </div>
    <div>
        <label class="label">To</label>
        <input type="date" class="input" name="to" value="{{ $to }}">
    </div>
    <div>
        <label class="label">Status</label>
        <select name="status" class="input">
            <option value="">All</option>
            @foreach(['active', 'completed', 'expired'] as $s)
                <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-ghost">Apply filters</button>
</form>

<div class="grid sm:grid-cols-3 gap-4 mb-5">
    <div class="card p-5">
        <div class="text-sm text-stone-500">Sessions</div>
        <div class="text-3xl font-extrabold mt-1">{{ $totals['count'] }}</div>
    </div>
    <div class="card p-5">
        <div class="text-sm text-stone-500">Revenue</div>
        <div class="text-3xl font-extrabold mt-1 text-brand-600">{{ $posBrand['currency_symbol'] }}{{ number_format($totals['revenue'], 2) }}</div>
    </div>
    <div class="card p-5">
        <div class="text-sm text-stone-500 mb-2">Monthly export</div>
        <form method="GET" action="{{ route('reports.exportMonth') }}" class="flex items-center gap-2">
            <select name="month" class="input">
                @foreach($months as $m)
                    <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary">Download</button>
        </form>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-stone-50 text-stone-500 text-sm">
            <tr>
                <th class="px-4 py-3">Receipt</th>
                <th class="px-4 py-3">Child</th>
                <th class="px-4 py-3">Package</th>
                <th class="px-4 py-3">Start</th>
                <th class="px-4 py-3">End</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
        @forelse($sessions as $s)
            <tr class="border-t border-stone-100 hover:bg-stone-50/60">
                <td class="px-4 py-3 font-mono text-xs">{{ $s->receipt?->receipt_number ?: '—' }}</td>
                <td class="px-4 py-3">{{ $s->child?->name }}</td>
                <td class="px-4 py-3">{{ $s->package?->name }}</td>
                <td class="px-4 py-3 text-sm">{{ $s->start_time?->format('d M · h:i A') }}</td>
                <td class="px-4 py-3 text-sm">{{ optional($s->end_time)->format('d M · h:i A') ?: '—' }}</td>
                <td class="px-4 py-3"><span class="chip chip-{{ $s->statusColor() }}">{{ ucfirst($s->status) }}</span></td>
                <td class="px-4 py-3 text-right font-semibold">{{ $posBrand['currency_symbol'] }}{{ number_format($s->final_price, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-stone-500">No sessions in this range.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sessions->links() }}</div>
@endsection
