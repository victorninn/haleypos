<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\PlaySession;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    public function show(Request $request)
    {
        $code = trim((string) $request->input('code'));
        $child = null;
        $activeSession = null;
        $sessions = collect();
        $subscriptionBlocked = false;

        if ($code !== '') {
            $child = Child::withoutBusinessScope()->where('child_code', $code)->first();
            if ($child) {
                $business = $child->business;

                // Block parent portal lookup if business inactive / subscription expired
                if (! $business || ! $business->is_active || $business->archived_at) {
                    $subscriptionBlocked = true;
                    $child = null;
                } else {
                    $sub = $business->subscription;
                    if (! $sub || $sub->isExpired()) {
                        $subscriptionBlocked = true;
                        $child = null;
                    }
                }

                if ($child) {
                    app()->instance('tenant.business_id', (int) $child->business_id);
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
        }

        return view('parent.lookup', compact('code', 'child', 'activeSession', 'sessions', 'subscriptionBlocked'));
    }

public function qr(Child $child)
{
    $url = urlencode(route('parent.lookup', ['code' => $child->child_code]));
    return redirect("https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={$url}");
}
}
