@php
    $action = isset($package) && $package->exists ? route('packages.update', $package) : route('packages.store');
    $method = isset($package) && $package->exists ? 'PUT' : 'POST';
@endphp
<form method="POST" action="{{ $action }}" class="card p-6 space-y-5 max-w-2xl">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div>
        <label class="label">Name</label>
        <input class="input" name="name" required value="{{ old('name', $package->name) }}" placeholder="e.g. 1 Hour Play">
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Duration (minutes)</label>
            <input class="input" type="number" name="duration_minutes" value="{{ old('duration_minutes', $package->duration_minutes) }}" placeholder="60">
            <p class="text-xs text-stone-500 mt-1">Leave blank if unlimited.</p>
        </div>
        <div>
            <label class="label">Price ({{ $posBrand['currency_symbol'] }})</label>
            <input class="input" type="number" step="0.01" name="price" required value="{{ old('price', $package->price) }}">
        </div>
        <div>
            <label class="label">Color</label>
            <input class="input" type="color" name="color" value="{{ old('color', $package->color ?: '#6366f1') }}">
        </div>
        <div>
            <label class="label">Sort order</label>
            <input class="input" type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_unlimited" value="1" @checked(old('is_unlimited', $package->is_unlimited))>
        Unlimited duration package
    </label>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active ?? true))>
        Active and available at check-in
    </label>

    <div class="flex gap-2">
        <button class="btn btn-primary">Save package</button>
        <a href="{{ route('packages.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
