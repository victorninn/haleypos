<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::query()->orderByDesc('id');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('child_code', 'like', "%{$search}%")
                  ->orWhere('guardian_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $children = $query->paginate(20)->withQueryString();
        return view('children.index', compact('children'));
    }

    public function create()
    {
        return view('children.create', ['child' => new Child()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['child_code'] = $this->generateChildCode();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('children', 'public');
        }

        $child = Child::create($data);
        return redirect()->route('children.show', $child)->with('status', 'Child registered.');
    }

    public function show(Child $child)
    {
        $sessions = $child->playSessions()
            ->with(['package', 'receipt'])
            ->orderByDesc('start_time')
            ->paginate(10);
        return view('children.show', compact('child', 'sessions'));
    }

    public function edit(Child $child)
    {
        return view('children.edit', compact('child'));
    }

    public function update(Request $request, Child $child)
    {
        $data = $this->validateData($request, $child->id);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('children', 'public');
        }

        $child->update($data);
        return redirect()->route('children.show', $child)->with('status', 'Child updated.');
    }

    public function destroy(Child $child)
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403);
        }
        $child->delete();
        return redirect()->route('children.index')->with('status', 'Child removed.');
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'age' => ['nullable', 'integer', 'min:0', 'max:25'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'guardian_name' => ['nullable', 'string', 'max:120'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    protected function generateChildCode(): string
    {
        do {
            $code = 'C-'.strtoupper(Str::random(6));
        } while (Child::withoutBusinessScope()->where('child_code', $code)->exists());
        return $code;
    }
}
