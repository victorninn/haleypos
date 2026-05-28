<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Subscription;
use Carbon\CarbonImmutable;

class SubscriptionService
{
    /**
     * Create or replace a business subscription with the given plan.
     * If extend=true and an active subscription exists, extend from its expires_at.
     */
    public function applyPlan(Business $business, string $planType, bool $extend = false): Subscription
    {
        $plan = Subscription::PLANS[$planType] ?? null;
        if (! $plan) {
            throw new \InvalidArgumentException("Unknown plan: {$planType}");
        }

        $now = CarbonImmutable::now();
        /** @var Subscription|null $sub */
        $sub = $business->subscription()->first();

        $startsAt = $now;
        if ($extend && $sub && $sub->expires_at && $sub->expires_at->isFuture()) {
            $startsAt = CarbonImmutable::instance($sub->expires_at);
        }

        $expiresAt = $startsAt->addMonths($plan['months']);

        $payload = [
            'business_id' => $business->id,
            'plan_type'   => $planType,
            'starts_at'   => $extend && $sub ? $sub->starts_at : $now,
            'expires_at'  => $expiresAt,
            'status'      => 'active',
            'is_trial'    => $plan['is_trial'],
            'is_lifetime' => $plan['is_lifetime'],
        ];

        if ($sub) {
            $sub->update($payload);
        } else {
            $sub = Subscription::create($payload);
        }

        $business->subscription_status = $plan['is_trial'] ? 'trial' : 'active';
        $business->save();

        return $sub->fresh();
    }

    public function reactivate(Business $business): ?Subscription
    {
        $sub = $business->subscription;
        if (! $sub) {
            return null;
        }
        $sub->status = 'active';
        if ($sub->expires_at && $sub->expires_at->isPast()) {
            // Re-issue same plan starting now if expired
            return $this->applyPlan($business, $sub->plan_type, false);
        }
        $sub->save();
        $business->subscription_status = $sub->is_trial ? 'trial' : 'active';
        $business->save();
        return $sub;
    }

    public function syncBusinessStatus(Business $business): void
    {
        $sub = $business->subscription;
        if (! $sub) {
            $business->subscription_status = 'none';
        } elseif ($sub->isExpired()) {
            $business->subscription_status = 'expired';
        } else {
            $business->subscription_status = $sub->is_trial ? 'trial' : 'active';
        }
        $business->saveQuietly();
    }
}
