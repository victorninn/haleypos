@extends('layouts.base')

@section('body')
<div class="min-h-screen bg-gradient-to-br from-stone-50 to-amber-50">
    <header class="px-6 py-4 bg-white/80 backdrop-blur border-b border-stone-200 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset(ltrim($posBrand['logo'], '/')) }}" class="w-10 h-10 rounded-xl object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                <div class="w-10 h-10 rounded-xl bg-brand-500 text-white items-center justify-center font-bold" style="display:none">
                    {{ strtoupper(substr($posBrand['name'], 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold">{{ $posBrand['name'] }}</div>
                    <div class="text-xs text-stone-500">Parent portal</div>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-sm text-stone-500 hover:text-stone-800">Staff login</a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-10">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900">Check on your child</h1>
        <p class="text-stone-600 mt-2">Enter the child code or scan the QR you received at check-in.</p>

        <form method="GET" action="{{ route('parent.lookup', $business->slug) }}" class="mt-6 flex gap-2 max-w-xxl">
            <input class="input text-lg" name="code" value="{{ $code }}" placeholder="e.g. C-AB12CD" required autofocus>
            <button class="btn btn-primary text-lg px-6 justify-center">Look up</button>
        </form>

        @if($code && !$child)
            <div class="mt-6 card p-6 text-center">
                <div class="text-3xl mb-2">🔍</div>
                <div class="font-semibold text-stone-800">No child found for that code.</div>
                <div class="text-stone-500 text-sm">Please double-check the code printed on your receipt.</div>
            </div>
        @endif

        @if($child)
            <div class="mt-8 card p-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $child->photo_url }}" class="w-16 h-16 rounded-2xl object-cover bg-stone-100">
                    <div>
                        <div class="font-bold text-xl text-stone-900">{{ $child->name }}</div>
                        <div class="text-sm text-stone-500">{{ $child->child_code }}</div>
                    </div>
                </div>

                @if($activeSession)
                    @php $rem = $activeSession->remainingMinutes(); $color = $activeSession->statusColor(); @endphp
                    <div class="mt-5 rounded-2xl p-5 {{ $color === 'red' ? 'bg-rose-50' : ($color === 'yellow' ? 'bg-amber-50' : 'bg-emerald-50') }}">
                        <div class="text-sm font-semibold uppercase {{ $color === 'red' ? 'text-rose-700' : ($color === 'yellow' ? 'text-amber-700' : 'text-emerald-700') }}">
                            Currently playing
                        </div>
                        <div class="mt-1 text-3xl font-extrabold text-stone-900">
                            {{ $activeSession->isUnlimited() ? 'Unlimited' : ($rem.' min remaining') }}
                        </div>
                        <div class="text-sm text-stone-600 mt-1">
                            Started {{ $activeSession->start_time->diffForHumans() }} · {{ $activeSession->package->name }}
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-2xl p-5 bg-stone-100">
                        <div class="font-semibold text-stone-800">Not currently in a session</div>
                        <div class="text-sm text-stone-500 mt-1">Visit the front desk to start playing.</div>
                    </div>
                @endif
            </div>

            <h2 class="mt-8 font-bold text-stone-900 text-lg">Recent sessions</h2>
            <div class="mt-3 space-y-3">
                @foreach($sessions as $s)
                    <div class="card p-4 flex items-center justify-between">
                        <div>
                            <div class="font-semibold">{{ $s->package->name }}</div>
                            <div class="text-xs text-stone-500">{{ $s->start_time->format('d M Y · h:i A') }}</div>
                        </div>
                        <div class="text-right">
                            <span class="chip chip-{{ $s->statusColor() }}">{{ ucfirst($s->status) }}</span>
                            <div class="text-sm font-semibold mt-1">{{ $posBrand['currency_symbol'] }}{{ number_format($s->final_price, 2) }}</div>
                            @if($s->receipt)
                                <a href="{{ route('receipts.show', $s->receipt) }}" class="text-brand-600 text-xs hover:underline">View receipt</a>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($sessions->isEmpty())
                    <div class="text-center text-stone-500 py-6">No history yet.</div>
                @endif
            </div>
        @endif
    </main>
</div>
@endsection
