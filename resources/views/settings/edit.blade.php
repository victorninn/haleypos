@extends('layouts.app')
@section('title', 'Business settings')
@section('subtitle', 'Brand, contact and currency')

@section('content')
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="card p-6 max-w-2xl space-y-5">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="label">Business name</label>
            <input class="input" name="name" required value="{{ old('name', $business->name) }}">
        </div>
        <div>
            <label class="label">Receipt code prefix</label>
            <input class="input uppercase" name="code" maxlength="10" required value="{{ old('code', $business->code) }}">
            <p class="text-xs text-stone-500 mt-1">Used in receipts like <code>{{ $business->code }}-YYYYMMDD-0001</code></p>
        </div>
        <div>
            <label class="label">Currency symbol</label>
            <input class="input" name="currency_symbol" value="{{ old('currency_symbol', $business->currency_symbol) }}">
        </div>
        <div>
            <label class="label">Phone</label>
            <input class="input" name="phone" value="{{ old('phone', $business->phone) }}">
        </div>
        <div>
            <label class="label">Email</label>
            <input class="input" name="email" value="{{ old('email', $business->email) }}">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Address</label>
            <input class="input" name="address" value="{{ old('address', $business->address) }}">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Logo (image)</label>
            <input class="input" type="file" name="logo" accept="image/*">
            <p class="text-xs text-stone-500 mt-1">PNG/JPG, square recommended. You can also change the global brand in <code>config/pos.php</code> or .env.</p>
        </div>
    </div>

    <button class="btn btn-primary">Save settings</button>
</form>
@endsection
