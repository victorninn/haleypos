<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds demo businesses at different subscription lifecycle stages so you can
 * preview every warning / blocked state the platform shows.
 *
 * Run on demand with:
 *   php artisan db:seed --class=DemoExpiringSeeder
 *
 * Tenant login URL: /login   (password is 'password' for every account)
 */
class DemoExpiringSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = [
            // [code, name, days_until_expiry, plan, is_trial]
            ['EXP14', 'Bouncy Castle Co.',      14, Subscription::PLAN_MONTH_1, false],   // no warning yet
            ['EXP07', 'Tiny Tumblers',           7, Subscription::PLAN_MONTH_1, false],   // 7-day warning
            ['EXP03', 'Rainbow Play Zone',       3, Subscription::PLAN_MONTH_1, false],   // 3-day warning
            ['EXP00', 'Sunset Soft Play',        0, Subscription::PLAN_MONTH_1, false],   // expires today
            ['EXPGN', 'Closed Carnival',        -5, Subscription::PLAN_MONTH_1, false],   // already expired -> blocked
            ['TRIAL', 'New Kid On The Block',   25, Subscription::PLAN_TRIAL_1M, true],   // healthy trial
        ];

        foreach ($scenarios as [$code, $name, $days, $plan, $isTrial]) {
            $slug = strtolower($code).'-demo';

            $business = Business::withTrashed()->where('code', $code)->first()
                ?? Business::create([
                    'name'            => $name,
                    'slug'            => $slug,
                    'code'            => $code,
                    'primary_color'   => '#'.substr(md5($code), 0, 6),
                    'email'           => strtolower(str_replace(' ', '', $name)).'@demo.test',
                    'phone'           => '+91 90000 '.str_pad((string)rand(1, 9999), 5, '0', STR_PAD_LEFT),
                    'address'         => 'Demo address, Unit '.rand(1, 99),
                    'currency_symbol' => '₹',
                    'is_active'       => true,
                ]);

            // Restore if previously soft-deleted, ensure active
            if ($business->trashed()) {
                $business->restore();
            }
            $business->is_active = true;
            $business->archived_at = null;
            $business->save();

            // Owner account
            $ownerEmail = 'owner+'.strtolower($code).'@demo.test';
            User::firstOrCreate(
                ['email' => $ownerEmail],
                [
                    'business_id' => $business->id,
                    'name'        => $name.' Owner',
                    'password'    => Hash::make('password'),
                    'role'        => 'admin',
                    'is_active'   => true,
                ]
            );

            // Park the subscription at the target offset
            $startsAt  = now()->subDays(30 - $days); // started 30-days-ago less the remaining days
            $expiresAt = now()->addDays($days);

            // For "expires today" use today's end-of-day so middleware still treats it as active enough to show the banner
            if ($days === 0) {
                $expiresAt = now()->endOfDay();
            }

            Subscription::updateOrCreate(
                ['business_id' => $business->id],
                [
                    'plan_type'   => $plan,
                    'starts_at'   => $startsAt,
                    'expires_at'  => $expiresAt,
                    'status'      => 'active',
                    'is_trial'    => $isTrial,
                    'is_lifetime' => false,
                ]
            );

            $business->subscription_status = $days < 0 ? 'expired' : ($isTrial ? 'trial' : 'active');
            $business->save();

            $this->command?->info(sprintf(
                '  %-30s code=%s  expires in %+d days  login=%s',
                $name, $code, $days, $ownerEmail
            ));
        }

        $this->command?->newLine();
        $this->command?->info('All demo logins use password: password');
        $this->command?->info('Tenant login URL: /login');
    }
}
