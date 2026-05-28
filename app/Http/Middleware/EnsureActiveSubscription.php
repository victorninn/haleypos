<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated tenant user's business is active and has a valid
 * (non-expired) subscription. Run AFTER EnsureTenantContext.
 */
class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $business = $user?->business;

        if (! $business) {
            abort(403, 'No business.');
        }

        if (! $business->is_active || $business->archived_at) {
            return redirect()->route('subscription.expired')
                ->with('reason', 'business_inactive');
        }

        $sub = $business->subscription;
        if (! $sub || $sub->isExpired()) {
            return redirect()->route('subscription.expired')
                ->with('reason', 'subscription_expired');
        }

        return $next($request);
    }
}
