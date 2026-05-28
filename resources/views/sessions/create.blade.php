@extends('layouts.app')
@section('title', 'Start a session')
@section('subtitle', 'Choose a child and a package')

@section('content')
<form method="POST" action="{{ route('sessions.store') }}" class="card p-6 max-w-3xl space-y-6">
    @csrf
    <div>
        <label class="label">Child</label>
        <select name="child_id" class="input" required>
            <option value="">Select a child…</option>
            @foreach($children as $c)
                <option value="{{ $c->id }}" @selected(($child?->id ?? old('child_id')) == $c->id)>
                    {{ $c->name }} — {{ $c->child_code }}
                </option>
            @endforeach
        </select>
        <a href="{{ route('children.create') }}" class="text-sm text-brand-600 hover:underline mt-2 inline-block">+ Add new child</a>
    </div>

    <div>
        <label class="label mb-3">Pricing package</label>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($packages as $p)
                <label class="cursor-pointer relative">
                    <input type="radio" name="package_id" value="{{ $p->id }}"
                           @checked(old('package_id', request('package')) == $p->id)
                           class="peer sr-only" required>
                    <div class="border-2 border-stone-200 peer-checked:border-brand-500 peer-checked:bg-brand-50 rounded-2xl p-5 transition">
                        <div class="w-9 h-9 rounded-lg mb-3" style="background: {{ $p->color }}1a; color: {{ $p->color }}">
                            <div class="w-full h-full flex items-center justify-center font-bold">{{ substr($p->name, 0, 1) }}</div>
                        </div>
                        <div class="font-bold text-stone-900">{{ $p->name }}</div>
                        <div class="text-sm text-stone-500">{{ $p->duration_label }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-stone-900">{{ $posBrand['currency_symbol'] }}{{ number_format($p->price, 2) }}</div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label class="label">Notes (optional)</label>
        <textarea name="notes" rows="2" class="input">{{ old('notes') }}</textarea>
    </div>

    <div class="flex gap-2">
        <button class="btn btn-primary text-base py-3 px-6">Start session</button>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
