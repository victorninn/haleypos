<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function edit()
    {
        $this->authorizeAdmin();
        $business = Auth::user()->business;
        return view('settings.edit', compact('business'));
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();
        $business = Auth::user()->business;

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:120',
            'address' => 'nullable|string|max:500',
            'currency_symbol' => 'nullable|string|max:8',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        $business->update(collect($data)->except('logo')->toArray());
        return back()->with('status', 'Business settings updated.');
    }

    protected function authorizeAdmin(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403);
        }
    }
}
