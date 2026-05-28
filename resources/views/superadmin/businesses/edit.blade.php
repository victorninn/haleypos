@extends('superadmin.layout')
@section('title', 'Edit · '.$business->name)

@section('topbar-actions')
<a href="{{ route('superadmin.businesses.show', $business) }}" class="sa-btn sa-btn-ghost">← Back</a>
@endsection

@section('content')
<form method="POST" action="{{ route('superadmin.businesses.update', $business) }}" enctype="multipart/form-data" class="sa-card p-6 max-w-3xl space-y-5" data-testid="business-edit-form">
    @csrf
    @method('PUT')
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="sa-label">Business name</label>
            <input class="sa-input" name="name" required value="{{ old('name', $business->name) }}">
        </div>
        <div>
            <label class="sa-label">Code</label>
            <input class="sa-input uppercase font-mono" name="code" required maxlength="10" value="{{ old('code', $business->code) }}">
        </div>
        <div>
            <label class="sa-label">Primary color</label>
            <div class="flex gap-2">
                <input class="sa-input flex-1 font-mono" name="primary_color" value="{{ old('primary_color', $business->primary_color) }}">
                <input type="color" value="{{ $business->primary_color }}" oninput="this.previousElementSibling.value=this.value" class="w-12 h-10 rounded bg-slate-900 border border-slate-700 cursor-pointer">
            </div>
        </div>
        <div>
            <label class="sa-label">Email</label>
            <input class="sa-input" type="email" name="email" value="{{ old('email', $business->email) }}">
        </div>
        <div>
            <label class="sa-label">Phone</label>
            <input class="sa-input" name="phone" value="{{ old('phone', $business->phone) }}">
        </div>
        <div class="sm:col-span-2">
            <label class="sa-label">Address</label>
            <input class="sa-input" name="address" value="{{ old('address', $business->address) }}">
        </div>
        <div>
            <label class="sa-label">Currency symbol</label>
            <input class="sa-input" name="currency_symbol" value="{{ old('currency_symbol', $business->currency_symbol) }}">
        </div>
        <div>
            <label class="sa-label">Replace logo</label>
            <input class="sa-input" type="file" name="logo" accept="image/*">
        </div>
    </div>

    <div class="flex gap-2 pt-2">
        <button class="sa-btn sa-btn-primary">Save changes</button>
        <a href="{{ route('superadmin.businesses.show', $business) }}" class="sa-btn sa-btn-ghost">Cancel</a>
    </div>
</form>
@endsection
