@extends('layouts.app')
@section('title', 'Business settings')
@section('subtitle', 'Brand, contact and currency')

@section('content')
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="card p-6 max-w-2xl space-y-5" data-testid="settings-form">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="label">Business name</label>
            <input class="input" name="name" required value="{{ old('name', $business->name) }}" data-testid="settings-name">
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

        <div class="sm:col-span-2 border-t border-stone-100 pt-4">
            <div class="text-xs uppercase tracking-widest text-stone-500 font-semibold mb-3">Branding (your view only)</div>
        </div>

        <div>
            <label class="label">Primary color</label>
            <div class="flex gap-2">
                <input class="input flex-1 font-mono" name="primary_color" value="{{ old('primary_color', $business->primary_color ?? '#f97316') }}" data-testid="settings-primary-color">
                <input type="color" value="{{ $business->primary_color ?? '#f97316' }}" oninput="this.previousElementSibling.value=this.value" class="w-12 h-10 rounded-lg border border-stone-200 cursor-pointer">
            </div>
            <p class="text-xs text-stone-500 mt-1">Used in your dashboard accents, receipts and parent portal.</p>
        </div>
        <div>
            <label class="label">Logo (image)</label>
            <input class="input" type="file" name="logo" accept="image/*" data-testid="settings-logo">
            <p class="text-xs text-stone-500 mt-1">PNG/JPG, square recommended.</p>
        </div>

        @if($business->logo_path)
            <div class="sm:col-span-2 flex items-center gap-3 p-3 bg-stone-50 rounded-xl">
                <img src="{{ asset('storage/'.$business->logo_path) }}" class="w-12 h-12 rounded-lg object-cover bg-white">
                <div class="text-sm text-stone-600">Current logo</div>
            </div>
        @endif
    </div>

    <button class="btn btn-primary" data-testid="settings-save">Save settings</button>
</form>
@endsection
