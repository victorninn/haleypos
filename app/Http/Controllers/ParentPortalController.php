<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Child;
use App\Models\PlaySession;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    public function show(Request $request, Business $business)
    {
        $code = trim((string) $request->input('code'));
        $child = null;
        $activeSession = null;
        $sessions = collect();
        $subscriptionBlocked = false;

        // Block parent portal lookup if business inactive / subscription expired
        if (! $business->is_active || $business->archived_at) {
            $subscriptionBlocked = true;
        } else {
            $sub = $business->subscription;
            if (! $sub || $sub->isExpired()) {
                $subscriptionBlocked = true;
            }
        }

        if ($code !== '' && ! $subscriptionBlocked) {
            $child = Child::withoutBusinessScope()
                ->where('business_id', $business->id)
                ->where('child_code', $code)
                ->first();

            if ($child) {
                app()->instance('tenant.business_id', (int) $business->id);
                view()->share('currentBusiness', $business);

                $activeSession = PlaySession::with('package')
                    ->where('child_id', $child->id)
                    ->where('status', 'active')
                    ->latest('start_time')
                    ->first();
                $sessions = PlaySession::with(['package', 'receipt'])
                    ->where('child_id', $child->id)
                    ->orderByDesc('start_time')
                    ->limit(15)
                    ->get();
            }
        }

        return view('parent.lookup', compact('business', 'code', 'child', 'activeSession', 'sessions', 'subscriptionBlocked'));
    }

    public function qr(Business $business, Child $child)
    {
        abort_unless($child->business_id === $business->id, 404);

        $url = urlencode(route('parent.lookup', ['business' => $business->slug, 'code' => $child->child_code]));
        return redirect("https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={$url}");
    }
}