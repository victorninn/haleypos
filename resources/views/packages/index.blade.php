@extends('layouts.app')
@section('title', 'Packages')
@section('subtitle', 'Time-based pricing for your playhouse')

@section('topbar-actions')
@if(auth()->user()->isAdmin())
<a href="{{ route('packages.create') }}" class="btn btn-primary">+ New package</a>
@endif
@endsection

@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($packages as $p)
        <div class="card p-5 {{ $p->is_active ? '' : 'opacity-60' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-bold text-lg text-stone-900">{{ $p->name }}</div>
                    <div class="text-sm text-stone-500">{{ $p->duration_label }}</div>
                </div>
                <span class="w-3 h-3 rounded-full" style="background: {{ $p->color }}"></span>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-stone-900">{{ $posBrand['currency_symbol'] }}{{ number_format($p->price, 2) }}</div>
            <div class="mt-3 flex items-center gap-2">
                @if(!$p->is_active) <span class="chip chip-gray">Inactive</span> @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('packages.edit', $p) }}" class="text-brand-600 hover:underline text-sm">Edit</a>
                    @if($p->is_active)
                    <form method="POST" action="{{ route('packages.destroy', $p) }}" class="inline ms-auto">
                        @csrf @method('DELETE')
                        <button class="text-rose-600 hover:underline text-sm">Deactivate</button>
                    </form>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <div class="card p-10 text-center text-stone-500 col-span-full">No packages yet.</div>
    @endforelse
</div>
@endsection
