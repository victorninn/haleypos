@extends('layouts.app')
@section('title', 'Children')
@section('subtitle', 'All registered kids in your playhouse')

@section('topbar-actions')
<a href="{{ route('children.create') }}" class="btn btn-primary">+ New child</a>
@endsection

@section('content')
<form method="GET" class="mb-4 flex gap-2">
    <input class="input" name="q" placeholder="Search by name, code or guardian…" value="{{ request('q') }}">
    <button class="btn btn-ghost">Search</button>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-stone-50 text-stone-500 text-sm">
            <tr>
                <th class="px-4 py-3">Child</th>
                <th class="px-4 py-3">Code</th>
                <th class="px-4 py-3">Guardian</th>
                <th class="px-4 py-3">Contact</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($children as $c)
            <tr class="border-t border-stone-100 hover:bg-stone-50/60">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $c->photo_url }}" class="w-9 h-9 rounded-lg object-cover bg-stone-100" alt="">
                        <div>
                            <div class="font-semibold text-stone-900">{{ $c->name }}</div>
                            <div class="text-xs text-stone-500">{{ $c->age ? $c->age.' yrs · ' : '' }}{{ ucfirst($c->gender ?? '—') }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 font-mono text-sm">{{ $c->child_code }}</td>
                <td class="px-4 py-3">{{ $c->guardian_name ?: '—' }}</td>
                <td class="px-4 py-3">{{ $c->contact_number ?: '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('children.show', $c) }}" class="text-brand-600 hover:underline">View</a>
                    <a href="{{ route('children.edit', $c) }}" class="text-stone-600 hover:underline ms-3">Edit</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-stone-500">No children yet. Add the first one.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $children->links() }}</div>
@endsection
