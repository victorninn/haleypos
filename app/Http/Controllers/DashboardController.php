<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PlaySession;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SessionService $sessionService): View
    {
        // Auto-mark expired sessions on every dashboard load
        $sessionService->sweepExpiredSessions();

        $activeSessions = PlaySession::with(['child', 'package'])
            ->where('status', 'active')
            ->orderBy('expected_end_time')
            ->get();

        $stats = [
            'active' => $activeSessions->count(),
            'today_completed' => PlaySession::whereIn('status', ['completed', 'expired'])
                ->whereDate('end_time', now()->toDateString())
                ->count(),
            'today_revenue' => (float) PlaySession::whereIn('status', ['completed', 'expired'])
                ->whereDate('end_time', now()->toDateString())
                ->sum('final_price'),
            'packages' => Package::where('is_active', true)->count(),
        ];

        $activePackages = Package::where('is_active', true)
            ->orderBy('sort_order')->orderBy('price')
            ->get();

        $subscription = Auth::user()?->business?->subscription;

        return view('dashboard.index', compact('activeSessions', 'stats', 'activePackages', 'subscription'));
    }
}
