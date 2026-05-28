<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'businesses_total'   => Business::count(),
            'businesses_active'  => Business::where('is_active', true)->whereNull('archived_at')->count(),
            'businesses_archived'=> Business::whereNotNull('archived_at')->count(),
            'subs_active'        => Subscription::where('status', 'active')->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count(),
            'subs_trial'         => Subscription::where('is_trial', true)->where('expires_at', '>', now())->count(),
            'subs_expired'       => Subscription::where(function ($q) {
                $q->where('status', '!=', 'active')->orWhere('expires_at', '<=', now());
            })->count(),
            'users_total'        => User::where('is_superadmin', false)->count(),
        ];

        $expiringSoon = Subscription::with('business')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays(14))
            ->orderBy('expires_at')
            ->limit(10)
            ->get();

        $recentBusinesses = Business::withCount('users')
            ->latest()
            ->limit(8)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'expiringSoon', 'recentBusinesses'));
    }
}
