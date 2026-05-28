<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Package;
use App\Models\PlaySession;
use App\Services\SessionService;
use Illuminate\Http\Request;

class PlaySessionController extends Controller
{
    public function __construct(protected SessionService $sessionService)
    {
    }

    public function create(Request $request)
    {
        $child = null;
        if ($request->filled('child_id')) {
            $child = Child::findOrFail($request->integer('child_id'));
        }
        $children = Child::orderBy('name')->get(['id', 'name', 'child_code']);
        $packages = Package::where('is_active', true)
            ->orderBy('sort_order')->orderBy('price')
            ->get();
        return view('sessions.create', compact('child', 'children', 'packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'package_id' => ['required', 'exists:packages,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $child = Child::findOrFail($data['child_id']);
        $package = Package::findOrFail($data['package_id']);

        try {
            $session = $this->sessionService->startSession($child, $package, $data['notes'] ?? null);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['package_id' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('status', 'Session started for '.$child->name);
    }

    public function extend(Request $request, PlaySession $session)
    {
        $minutes = (int) $request->validate(['minutes' => 'required|integer|min:5|max:240'])['minutes'];
        try {
            $this->sessionService->extendSession($session, $minutes);
        } catch (\Throwable $e) {
            return back()->withErrors(['extend' => $e->getMessage()]);
        }
        return back()->with('status', 'Session extended by '.$minutes.' minutes.');
    }

    public function end(Request $request, PlaySession $session)
    {
        $early = (bool) $request->boolean('early');
        try {
            $session = $this->sessionService->endSession($session, $early);
        } catch (\Throwable $e) {
            return back()->withErrors(['end' => $e->getMessage()]);
        }
        $receipt = $session->receipt;
        if ($receipt) {
            return redirect()->route('receipts.show', $receipt)->with('status', 'Session ended. Receipt generated.');
        }
        return redirect()->route('dashboard')->with('status', 'Session ended.');
    }

    public function show(PlaySession $session)
    {
        $session->load(['child', 'package', 'receipt', 'startedBy', 'endedBy']);
        return view('sessions.show', compact('session'));
    }
}
