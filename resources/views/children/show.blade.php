@extends('layouts.app')
@section('title', $child->name)
@section('subtitle', 'Child profile · '.$child->child_code)

@section('topbar-actions')
<a href="{{ route('sessions.create', ['child_id' => $child->id]) }}" class="btn btn-primary">Start session</a>
<a href="{{ route('children.edit', $child) }}" class="btn btn-ghost">Edit</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-5">
    <div class="card p-6 lg:col-span-1">
        <div class="flex items-center gap-4">
            <img src="{{ $child->photo_url }}" class="w-20 h-20 rounded-2xl object-cover bg-stone-100" alt="">
            <div>
                <div class="font-bold text-xl text-stone-900">{{ $child->name }}</div>
                <div class="text-sm text-stone-500">{{ $child->age ? $child->age.' yrs · ' : '' }}{{ ucfirst($child->gender ?? '—') }}</div>
                <div class="text-xs font-mono mt-1 text-stone-600">{{ $child->child_code }}</div>
            </div>
        </div>

        <dl class="mt-6 space-y-3 text-sm">
            <div><dt class="text-stone-500">Guardian</dt><dd class="font-semibold">{{ $child->guardian_name ?: '—' }}</dd></div>
            <div><dt class="text-stone-500">Contact</dt><dd class="font-semibold">{{ $child->contact_number ?: '—' }}</dd></div>
            <div><dt class="text-stone-500">Emergency</dt><dd class="font-semibold">{{ $child->emergency_contact ?: '—' }}</dd></div>
            <div><dt class="text-stone-500">Notes</dt><dd>{{ $child->notes ?: '—' }}</dd></div>
        </dl>

        <div class="mt-6 pt-5 border-t border-stone-100 text-center">
            <div class="text-sm text-stone-500 mb-2">Parent lookup QR</div>
            <img src="{{ route('children.qr', $child) }}" class="mx-auto w-44 h-44 bg-white p-2 border border-stone-200 rounded-xl" alt="QR">
            <div class="mt-2 text-xs text-stone-500 break-all">{{ route('parent.lookup', ['code' => $child->child_code]) }}</div>
        </div>
    </div>

    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-stone-900">Session history</h2>
            <span class="text-sm text-stone-500">{{ $sessions->total() }} total</span>
        </div>
        <div class="mt-3 divide-y divide-stone-100">
            @forelse($sessions as $s)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-stone-900">{{ $s->package->name }}</div>
                        <div class="text-xs text-stone-500">{{ $s->start_time->format('d M Y · h:i A') }} — {{ optional($s->end_time)->format('h:i A') ?: 'ongoing' }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="chip chip-{{ $s->statusColor() }}">{{ ucfirst($s->status) }}</span>
                        <span class="font-semibold">{{ $posBrand['currency_symbol'] }}{{ number_format($s->final_price, 2) }}</span>
                        @if($s->receipt)
                            <a href="{{ route('receipts.show', $s->receipt) }}" class="text-brand-600 hover:underline text-sm">Receipt</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-stone-500">No sessions yet for this child.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection
