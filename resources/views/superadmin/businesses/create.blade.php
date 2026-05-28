@extends('superadmin.layout')
@section('title', 'New business')

@section('content')
<form method="POST" action="{{ route('superadmin.businesses.store') }}" enctype="multipart/form-data" class="space-y-5 max-w-3xl" data-testid="business-create-form">
    @csrf

    <div class="sa-card p-6">
        <h2 class="text-lg font-bold text-slate-100 mb-4">Business profile</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="sa-label">Business name</label>
                <input class="sa-input" name="name" required value="{{ old('name') }}" data-testid="bz-name">
            </div>
            <div>
                <label class="sa-label">Receipt code prefix</label>
                <input class="sa-input uppercase font-mono" name="code" required maxlength="10" value="{{ old('code') }}" data-testid="bz-code">
            </div>
            <div>
                <label class="sa-label">Primary color</label>
                <div class="flex gap-2">
                    <input class="sa-input flex-1 font-mono" name="primary_color" value="{{ old('primary_color', '#f97316') }}" data-testid="bz-color">
                    <input type="color" name="primary_color_picker" value="{{ old('primary_color', '#f97316') }}" oninput="this.previousElementSibling.value=this.value" class="w-12 h-10 rounded bg-slate-900 border border-slate-700 cursor-pointer">
                </div>
            </div>
            <div>
                <label class="sa-label">Contact email</label>
                <input class="sa-input" type="email" name="contact_email" value="{{ old('contact_email') }}" data-testid="bz-email">
            </div>
            <div>
                <label class="sa-label">Phone</label>
                <input class="sa-input" name="phone" value="{{ old('phone') }}">
            </div>
            <div class="sm:col-span-2">
                <label class="sa-label">Address</label>
                <input class="sa-input" name="address" value="{{ old('address') }}">
            </div>
            <div>
                <label class="sa-label">Currency symbol</label>
                <input class="sa-input" name="currency_symbol" value="{{ old('currency_symbol', '₹') }}">
            </div>
            <div>
                <label class="sa-label">Logo (optional)</label>
                <input class="sa-input" type="file" name="logo" accept="image/*">
            </div>
        </div>
    </div>

    <div class="sa-card p-6">
        <h2 class="text-lg font-bold text-slate-100 mb-4">Owner / admin account</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="sa-label">Full name</label>
                <input class="sa-input" name="owner_name" required value="{{ old('owner_name') }}" data-testid="owner-name">
            </div>
            <div>
                <label class="sa-label">Email</label>
                <input class="sa-input" type="email" name="owner_email" required value="{{ old('owner_email') }}" data-testid="owner-email">
            </div>
            <div class="sm:col-span-2">
                <label class="sa-label">Initial password</label>
                <input class="sa-input" type="text" name="owner_password" required value="{{ old('owner_password') }}" data-testid="owner-password">
                <p class="text-xs text-slate-500 mt-1">Share this securely with the owner. They can change it later.</p>
            </div>
        </div>
    </div>

    <div class="sa-card p-6">
        <h2 class="text-lg font-bold text-slate-100 mb-4">Initial subscription</h2>
        <div class="grid sm:grid-cols-2 gap-2">
            @foreach($plans as $key => $plan)
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-800 cursor-pointer hover:border-cyan-700">
                    <input type="radio" name="plan_type" value="{{ $key }}" {{ old('plan_type', 'trial_1m') === $key ? 'checked' : '' }} class="text-cyan-400" data-testid="plan-{{ $key }}">
                    <div>
                        <div class="font-semibold text-slate-200">{{ $plan['label'] }}</div>
                        <div class="text-xs text-slate-500">
                            @if($plan['is_trial']) Free trial · @endif
                            @if($plan['is_lifetime']) 10-year validity @else {{ $plan['months'] }} {{ $plan['months'] === 1 ? 'month' : 'months' }} @endif
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex gap-2">
        <button class="sa-btn sa-btn-primary" data-testid="submit-create-business">Create business</button>
        <a href="{{ route('superadmin.businesses.index') }}" class="sa-btn sa-btn-ghost">Cancel</a>
    </div>
</form>
@endsection
