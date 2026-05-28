<?php

namespace App\Services;

use App\Models\PlaySession;
use App\Models\Receipt;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    /**
     * Generate (or return existing) receipt for a session.
     * Receipt number format: {BUSINESS_CODE}-{YYYYMMDD}-{XXXX}
     */
    public function generateForSession(PlaySession $session): Receipt
    {
        if ($session->relationLoaded('receipt') && $session->receipt) {
            return $session->receipt;
        }
        $existing = Receipt::where('play_session_id', $session->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($session) {
            $business = $session->business()->first();
            $code = $business->code ?: strtoupper(substr($business->slug ?: 'BIZ', 0, 4));
            $datePart = CarbonImmutable::now()->format('Ymd');

            // Find next sequence for this business today (lock with DB transaction).
            $likePattern = $code.'-'.$datePart.'-%';
            $lastSeq = Receipt::withoutBusinessScope()
                ->where('business_id', $business->id)
                ->where('receipt_number', 'like', $likePattern)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('receipt_number');

            $next = 1;
            if ($lastSeq) {
                $parts = explode('-', $lastSeq);
                $next = ((int) end($parts)) + 1;
            }

            $number = sprintf('%s-%s-%04d', $code, $datePart, $next);

            $child = $session->child()->first();
            $package = $session->package()->first();

            $snapshot = [
                'business' => [
                    'name' => $business->name,
                    'code' => $business->code,
                    'phone' => $business->phone,
                    'address' => $business->address,
                ],
                'child' => [
                    'name' => $child->name,
                    'code' => $child->child_code,
                    'guardian' => $child->guardian_name,
                ],
                'package' => [
                    'name' => $package->name,
                    'duration' => $package->duration_label,
                    'is_unlimited' => $package->is_unlimited,
                ],
                'session' => [
                    'start' => $session->start_time?->toDateTimeString(),
                    'end' => $session->end_time?->toDateTimeString(),
                    'expected_end' => $session->expected_end_time?->toDateTimeString(),
                    'extended_minutes' => $session->extended_minutes,
                    'status' => $session->status,
                ],
            ];

            return Receipt::create([
                'business_id' => $business->id,
                'play_session_id' => $session->id,
                'receipt_number' => $number,
                'amount' => $session->final_price,
                'issued_at' => CarbonImmutable::now(),
                'snapshot' => $snapshot,
            ]);
        });
    }
}
