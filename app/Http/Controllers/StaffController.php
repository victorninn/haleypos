<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $users = User::where('business_id', Auth::user()->business_id)
            ->orderBy('name')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('users.create', ['staff' => new User()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ]);
        $data['business_id'] = Auth::user()->business_id;
        $data['is_active'] = true;
        User::create($data);
        return redirect()->route('staff.index')->with('status', 'Staff added.');
    }

    public function toggle(User $user)
    {
        $this->authorizeAdmin();
        if ($user->business_id !== Auth::user()->business_id) {
            abort(403);
        }
        if ($user->id === Auth::id()) {
            return back()->withErrors(['toggle' => 'You cannot disable your own account.']);
        }
        $user->is_active = ! $user->is_active;
        $user->save();
        return back()->with('status', 'Staff status updated.');
    }

    protected function authorizeAdmin(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403);
        }
    }
}
