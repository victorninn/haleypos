@extends('layouts.app')
@section('title', 'Session #'.$session->id)
@section('subtitle', $session->child->name.' · '.$session->package->name)

@section('content')
<div class="grid lg:grid-cols-3 gap-5">
    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-stone-900">Session details</h2>
            <span class="chip chip-{{ $session->statusColor() }}">{{ ucfirst($session->status) }}</span>
        </div>

        <dl class="mt-4 grid sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-stone-500">Started</dt><dd class="font-semibold">{{ $session->start_time->format('d M Y · h:i A') }}</dd></div>
            <div><dt class="text-stone-500">Expected end</dt><dd class="font-semibold">{{ $session->expected_end_time?->format('d M Y · h:i A') ?: 'Unlimited' }}</dd></div>
            <div><dt class="text-stone-500">Ended</dt><dd class="font-semibold">{{ optional($session->end_time)->format('d M Y · h:i A') ?: '—' }}</dd></div>
            <div><dt class="text-stone-500">Extended</dt><dd class="font-semibold">{{ $session->extended_minutes }} min</dd></div>
            <div><dt class="text-stone-500">Started by</dt><dd>{{ $session->startedBy?->name ?: '—' }}</dd></div>
            <div><dt class="text-stone-500">Ended by</dt><dd>{{ $session->endedBy?->name ?: '—' }}</dd></div>
            <div><dt class="text-stone-500">Final price</dt><dd class="font-bold text-brand-600 text-lg">{{ $posBrand['currency_symbol'] }}{{ number_format($session->final_price, 2) }}</dd></div>
        </dl>

        @if($session->status === 'active')
        <div class="mt-6 flex flex-wrap gap-2 pt-4 border-t border-stone-100">
            <form method="POST" action="{{ route('sessions.extend', $session) }}" class="flex items-center gap-2">
                @csrf
                <select name="minutes" class="input py-2">
                    <option value="15">+15 min</option>
                    <option value="30" selected>+30 min</option>
                    <option value="60">+60 min</option>
                </select>
                <button class="btn btn-ghost">Extend</button>
            </form>
            <form method="POST" action="{{ route('sessions.end', $session) }}">
                @csrf
                <button class="btn btn-danger">End & generate receipt</button>
            </form>
            <form method="POST" action="{{ route('sessions.end', $session) }}">
                @csrf <input type="hidden" name="early" value="1">
                <button class="btn btn-ghost">End early</button>
            </form>
        </div>
        @endif
    </div>

    <div class="card p-6">
        <h2 class="font-bold text-stone-900">Child</h2>
        <div class="flex items-center gap-3 mt-3">
            <img src="{{ $session->child->photo_url }}" class="w-14 h-14 rounded-xl object-cover bg-stone-100">
            <div>
                <div class="font-semibold">{{ $session->child->name }}</div>
                <div class="text-xs text-stone-500">{{ $session->child->child_code }}</div>
            </div>
        </div>
        <a href="{{ route('children.show', $session->child) }}" class="btn btn-ghost mt-4 w-full justify-center">View child profile</a>

        @if($session->receipt)
            <a href="{{ route('receipts.show', $session->receipt) }}" class="btn btn-primary mt-2 w-full justify-center">View receipt</a>
        @endif
    </div>
</div>
@endsection
