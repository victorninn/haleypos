<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subscriptions)
    {
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $data = $request->validate([
            'plan_type' => ['required', Rule::in(array_keys(Subscription::PLANS))],
            'mode'      => ['required', Rule::in(['replace', 'extend'])],
        ]);

        $this->subscriptions->applyPlan(
            $business,
            $data['plan_type'],
            $data['mode'] === 'extend'
        );

        return back()->with('status', 'Subscription updated.');
    }

    public function reactivate(Business $business): RedirectResponse
    {
        $this->subscriptions->reactivate($business);
        return back()->with('status', 'Subscription reactivated.');
    }
}
