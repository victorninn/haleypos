<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperadmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->is_superadmin) {
            if (! $user) {
                return redirect()->route('superadmin.login');
            }
            abort(403, 'Superadmin access only.');
        }
        // Superadmin must NOT be tenant-scoped
        app()->forgetInstance('tenant.business_id');
        return $next($request);
    }
}
