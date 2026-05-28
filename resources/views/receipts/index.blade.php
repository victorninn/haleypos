@extends('layouts.app')
@section('title', 'Receipts')
@section('subtitle', 'All issued receipts (latest first)')

@section('content')
<div class="card overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-stone-50 text-stone-500 text-sm">
            <tr>
                <th class="px-4 py-3">Receipt #</th>
                <th class="px-4 py-3">Child</th>
                <th class="px-4 py-3">Package</th>
                <th class="px-4 py-3">Issued at</th>
                <th class="px-4 py-3 text-right">Amount</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($receipts as $r)
            <tr class="border-t border-stone-100 hover:bg-stone-50/60">
                <td class="px-4 py-3 font-mono text-sm">{{ $r->receipt_number }}</td>
                <td class="px-4 py-3">{{ $r->playSession?->child?->name }}</td>
                <td class="px-4 py-3">{{ $r->playSession?->package?->name }}</td>
                <td class="px-4 py-3">{{ $r->issued_at->format('d M Y · h:i A') }}</td>
                <td class="px-4 py-3 text-right font-semibold">{{ $posBrand['currency_symbol'] }}{{ number_format($r->amount, 2) }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('receipts.show', $r) }}" class="text-brand-600 hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-stone-500">No receipts yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $receipts->links() }}</div>
@endsection
