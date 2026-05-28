@php
    $action = isset($child) && $child->exists ? route('children.update', $child) : route('children.store');
    $method = isset($child) && $child->exists ? 'PUT' : 'POST';
@endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card p-6 space-y-5 max-w-3xl">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Child's name</label>
            <input class="input" name="name" required value="{{ old('name', $child->name) }}">
        </div>
        <div>
            <label class="label">Age</label>
            <input class="input" type="number" name="age" min="0" max="25" value="{{ old('age', $child->age) }}">
        </div>
        <div>
            <label class="label">Gender</label>
            <select class="input" name="gender">
                <option value="">Prefer not to say</option>
                @foreach(['male', 'female', 'other'] as $g)
                    <option value="{{ $g }}" @selected(old('gender', $child->gender) === $g)>{{ ucfirst($g) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Photo (optional)</label>
            <input class="input" type="file" name="photo" accept="image/*">
        </div>
        <div>
            <label class="label">Guardian name</label>
            <input class="input" name="guardian_name" value="{{ old('guardian_name', $child->guardian_name) }}">
        </div>
        <div>
            <label class="label">Contact number</label>
            <input class="input" name="contact_number" value="{{ old('contact_number', $child->contact_number) }}">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Emergency contact</label>
            <input class="input" name="emergency_contact" value="{{ old('emergency_contact', $child->emergency_contact) }}">
        </div>
        <div class="sm:col-span-2">
            <label class="label">Notes</label>
            <textarea class="input" name="notes" rows="3">{{ old('notes', $child->notes) }}</textarea>
        </div>
    </div>

    <div class="flex gap-2">
        <button class="btn btn-primary">Save child</button>
        <a href="{{ route('children.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
