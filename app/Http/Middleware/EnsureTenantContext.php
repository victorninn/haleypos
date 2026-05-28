<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Binds the current authenticated user's business_id into the container
 * so the BelongsToBusiness trait can apply its global scope.
 */
class EnsureTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->business_id) {
            abort(403, 'No tenant context.');
        }
        app()->instance('tenant.business_id', (int) $user->business_id);
        view()->share('currentBusiness', $user->business);
        return $next($request);
    }
}
