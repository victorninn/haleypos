@extends('layouts.app')
@section('title', 'Staff')
@section('subtitle', 'Admin & front-desk accounts')

@section('topbar-actions')
<a href="{{ route('staff.create') }}" class="btn btn-primary">+ Add staff</a>
@endsection

@section('content')
<div class="card overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-stone-50 text-stone-500 text-sm">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
                <tr class="border-t border-stone-100">
                    <td class="px-4 py-3 font-semibold">{{ $u->name }}</td>
                    <td class="px-4 py-3">{{ $u->email }}</td>
                    <td class="px-4 py-3 capitalize">{{ $u->role }}</td>
                    <td class="px-4 py-3">
                        <span class="chip {{ $u->is_active ? 'chip-green' : 'chip-gray' }}">
                            {{ $u->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('staff.toggle', $u) }}" class="inline">
                                @csrf
                                <button class="text-brand-600 hover:underline">{{ $u->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
