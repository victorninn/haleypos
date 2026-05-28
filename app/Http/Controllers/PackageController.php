<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('sort_order')->orderBy('price')->get();
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('packages.create', ['package' => new Package()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $this->validateData($request);
        $data['is_unlimited'] = (bool) ($data['is_unlimited'] ?? false);
        if ($data['is_unlimited']) {
            $data['duration_minutes'] = null;
        }
        Package::create($data);
        return redirect()->route('packages.index')->with('status', 'Package created.');
    }

    public function edit(Package $package)
    {
        $this->authorizeAdmin();
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $this->authorizeAdmin();
        $data = $this->validateData($request);
        $data['is_unlimited'] = (bool) ($data['is_unlimited'] ?? false);
        if ($data['is_unlimited']) {
            $data['duration_minutes'] = null;
        }
        $package->update($data);
        return redirect()->route('packages.index')->with('status', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $this->authorizeAdmin();
        $package->is_active = false;
        $package->save();
        return back()->with('status', 'Package deactivated.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:80',
            'duration_minutes' => 'nullable|integer|min:5|max:1440',
            'price' => 'required|numeric|min:0',
            'is_unlimited' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'color' => 'nullable|string|max:16',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }

    protected function authorizeAdmin(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403);
        }
    }
}
