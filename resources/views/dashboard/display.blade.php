@extends('layouts.base')
@section('title', 'Active Sessions · ' . $posBrand['name'])

@section('body')
<div class="min-h-screen flex flex-col" style="background:#faf6f0;">

    {{-- Header bar --}}
    <header class="bg-white border-b border-stone-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset(ltrim($posBrand['logo'], '/')) }}"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                 alt="{{ $posBrand['name'] }}"
                 class="w-10 h-10 rounded-xl object-cover">
            <div class="w-10 h-10 rounded-xl bg-brand-500 text-white items-center justify-center font-bold text-lg" style="display:none">
                {{ strtoupper(substr($posBrand['name'], 0, 1)) }}
            </div>
            <div>
                <div class="font-bold text-stone-900 leading-tight">{{ $posBrand['name'] }}</div>
                <div class="text-xs text-stone-500">Active Sessions</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{-- Live clock --}}
            <div class="text-right hidden sm:block">
                <div id="display-clock" class="text-2xl font-extrabold text-stone-900 tabular-nums"></div>
                <div id="display-date" class="text-xs text-stone-500 mt-0.5"></div>
            </div>

            {{-- Active count badge --}}
            <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                <span class="font-bold text-emerald-800 text-lg">{{ $activeSessions->count() }}</span>
                <span class="text-sm text-emerald-700">playing now</span>
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="flex-1 px-6 py-6">

        @if($activeSessions->isEmpty())
            <div class="flex flex-col items-center justify-center h-full min-h-[60vh] text-center">
                <div class="text-7xl mb-4">🛝</div>
                <div class="text-2xl font-bold text-stone-700">No active sessions right now</div>
                <p class="text-stone-500 mt-2">This screen will update automatically when sessions begin.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
                @foreach($activeSessions as $s)
                    @php
                        $color = $s->statusColor();
                        $rem   = $s->remainingMinutes();
                    @endphp
                    <div class="card p-5">
                        {{-- Child info --}}
                        <div class="flex items-center gap-4 mb-4">
                            <img src="{{ $s->child->photo_url }}"
                                 class="w-16 h-16 rounded-2xl object-cover bg-stone-100 shrink-0"
                                 alt="">
                            <div class="min-w-0">
                                <div class="font-bold text-stone-900 text-lg leading-tight truncate">{{ $s->child->name }}</div>
                                <div class="text-sm text-stone-500 mt-0.5">{{ $s->child->child_code }}</div>
                                <div class="mt-1">
                                    <span class="chip chip-{{ $color }} text-xs">
                                        {{ $s->isUnlimited() ? 'Unlimited' : ($rem === null ? '—' : ($rem.' min left')) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Package name --}}
                        <div class="text-sm font-semibold text-stone-700 mb-4 truncate">
                            🎟 {{ $s->package->name }}
                        </div>

                        {{-- Time info --}}
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="bg-stone-50 rounded-xl py-3">
                                <div class="text-xs text-stone-500 mb-1">Started</div>
                                <div class="font-bold text-stone-900">{{ $s->start_time->format('h:i A') }}</div>
                            </div>
                            <div class="bg-stone-50 rounded-xl py-3">
                                <div class="text-xs text-stone-500 mb-1">Ends</div>
                                <div class="font-bold text-stone-900">
                                    {{ $s->isUnlimited() ? '—' : $s->expected_end_time->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="border-t border-stone-200 bg-white px-6 py-3 flex items-center justify-between text-xs text-stone-400">
        <span>Parent display · read-only view</span>
        <span>Refreshes automatically every 30 seconds</span>
    </footer>
</div>

@push('scripts')
<script>
    // Live clock
    function tick() {
        const now = new Date();
        const timeEl = document.getElementById('display-clock');
        const dateEl = document.getElementById('display-date');
        if (timeEl) {
            timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
        }
    }
    tick();
    setInterval(tick, 1000);

    // Auto-refresh the page every 30 seconds
    setTimeout(() => location.reload(), 30000);
</script>
@endpush
@endsection