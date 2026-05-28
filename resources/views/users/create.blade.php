@extends('layouts.app')
@section('title', 'Add staff')

@section('content')
<form method="POST" action="{{ route('staff.store') }}" class="card p-6 max-w-xl space-y-4">
    @csrf
    <div>
        <label class="label">Full name</label>
        <input class="input" name="name" required value="{{ old('name') }}">
    </div>
    <div>
        <label class="label">Email</label>
        <input class="input" type="email" name="email" required value="{{ old('email') }}">
    </div>
    <div>
        <label class="label">Password</label>
        <input class="input" type="password" name="password" required>
    </div>
    <div>
        <label class="label">Role</label>
        <select class="input" name="role">
            <option value="staff">Staff (front desk)</option>
            <option value="admin">Admin (business owner)</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button class="btn btn-primary">Create</button>
        <a href="{{ route('staff.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
