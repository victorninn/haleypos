@php
    $snap = $receipt->snapshot ?? [];
    $ses = $receipt->playSession;
    $child = $ses?->child;
    $pkg = $ses?->package;
@endphp
<div class="card p-8 max-w-2xl mx-auto" id="receiptArea">
    <div class="flex items-center justify-between border-b border-stone-200 pb-5">
        <div class="flex items-center gap-3">
            <img src="{{ asset(ltrim($posBrand['logo'], '/')) }}" alt="" class="w-12 h-12 rounded-xl object-cover" onerror="this.style.display='none'">
            <div>
                <div class="font-bold text-xl">{{ $snap['business']['name'] ?? $posBrand['name'] }}</div>
                <div class="text-sm text-stone-500">{{ $snap['business']['address'] ?? '' }}</div>
                <div class="text-sm text-stone-500">{{ $snap['business']['phone'] ?? '' }}</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-stone-500">Receipt #</div>
            <div class="font-mono font-bold">{{ $receipt->receipt_number }}</div>
            <div class="text-xs text-stone-500 mt-2">{{ $receipt->issued_at->format('d M Y · h:i A') }}</div>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-6 py-5 text-sm">
        <div>
            <div class="text-stone-500 text-xs uppercase">Billed to</div>
            <div class="font-semibold text-stone-900">{{ $child?->name }}</div>
            <div class="text-stone-600">Code: <span class="font-mono">{{ $child?->child_code }}</span></div>
            <div class="text-stone-600">Guardian: {{ $snap['child']['guardian'] ?? '—' }}</div>
        </div>
        <div>
            <div class="text-stone-500 text-xs uppercase">Session</div>
            <div class="text-stone-600">Start: {{ $ses?->start_time?->format('d M Y · h:i A') }}</div>
            <div class="text-stone-600">End: {{ optional($ses?->end_time)->format('d M Y · h:i A') ?: '—' }}</div>
            <div class="text-stone-600">Status: <span class="capitalize">{{ $ses?->status }}</span></div>
        </div>
    </div>

    <table class="w-full text-sm border-t border-stone-200">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-3 py-2 text-left">Description</th>
                <th class="px-3 py-2 text-left">Duration</th>
                <th class="px-3 py-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-t border-stone-100">
                <td class="px-3 py-3">{{ $pkg?->name }}</td>
                <td class="px-3 py-3">{{ $pkg?->is_unlimited ? 'Unlimited' : $pkg?->duration_label }}</td>
                <td class="px-3 py-3 text-right font-semibold">{{ $posBrand['currency_symbol'] }}{{ number_format($receipt->amount, 2) }}</td>
            </tr>
            @if($ses?->extended_minutes > 0)
            <tr class="border-t border-stone-100 text-stone-500">
                <td class="px-3 py-2" colspan="2">Extended time</td>
                <td class="px-3 py-2 text-right">{{ $ses->extended_minutes }} min</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-stone-300">
                <td colspan="2" class="px-3 py-3 text-right text-stone-500">Total</td>
                <td class="px-3 py-3 text-right text-xl font-extrabold text-brand-600">{{ $posBrand['currency_symbol'] }}{{ number_format($receipt->amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="text-center text-xs text-stone-500 mt-6">
        Thank you for visiting {{ $posBrand['name'] }} — see you again soon!
    </div>
</div>
