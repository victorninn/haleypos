<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Child;
use App\Models\Package;
use App\Models\PlaySession;
use App\Models\Receipt;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SessionService
{
    public function __construct(protected ReceiptService $receiptService)
    {
    }

    public function startSession(Child $child, Package $package, ?string $notes = null): PlaySession
    {
        if (! $package->is_active) {
            throw new RuntimeException('Selected package is not active.');
        }

        // Check no active session for this child
        $existing = PlaySession::where('child_id', $child->id)
            ->where('status', 'active')
            ->exists();
        if ($existing) {
            throw new RuntimeException('This child already has an active session.');
        }

        return DB::transaction(function () use ($child, $package, $notes) {
            $now = CarbonImmutable::now();
            $expected = $package->is_unlimited ? null : $now->addMinutes((int) $package->duration_minutes);

            $session = PlaySession::create([
                'business_id' => $child->business_id,
                'child_id' => $child->id,
                'package_id' => $package->id,
                'started_by' => Auth::id(),
                'start_time' => $now,
                'expected_end_time' => $expected,
                'final_price' => $package->price,
                'status' => 'active',
                'notes' => $notes,
            ]);

            $this->log($session, 'session.started', [
                'package' => $package->name,
                'price' => (float) $package->price,
                'expected_end' => optional($expected)->toDateTimeString(),
            ]);

            return $session;
        });
    }

    public function extendSession(PlaySession $session, int $minutes): PlaySession
    {
        if ($session->status !== 'active') {
            throw new RuntimeException('Only active sessions can be extended.');
        }
        if ($minutes <= 0) {
            throw new RuntimeException('Extension must be positive.');
        }

        return DB::transaction(function () use ($session, $minutes) {
            $base = $session->expected_end_time ?? CarbonImmutable::now();
            $session->expected_end_time = CarbonImmutable::parse($base)->addMinutes($minutes);
            $session->extended_minutes += $minutes;
            $session->save();

            $this->log($session, 'session.extended', [
                'minutes' => $minutes,
                'new_expected_end' => $session->expected_end_time->toDateTimeString(),
            ]);

            return $session;
        });
    }

    public function endSession(PlaySession $session, bool $earlyTermination = false): PlaySession
    {
        if ($session->status !== 'active') {
            throw new RuntimeException('Session is already ended.');
        }

        return DB::transaction(function () use ($session, $earlyTermination) {
            $session->end_time = CarbonImmutable::now();
            $session->ended_by = Auth::id();
            $session->status = 'completed';
            $session->save();

            $receipt = $this->receiptService->generateForSession($session);

            $this->log($session, $earlyTermination ? 'session.terminated_early' : 'session.ended', [
                'receipt_number' => $receipt->receipt_number,
                'final_price' => (float) $session->final_price,
            ]);

            return $session->fresh(['receipt', 'child', 'package']);
        });
    }

    /**
     * Marks any active session whose expected_end_time has passed as 'expired'
     * and generates a receipt for it. Idempotent. Safe to call from anywhere.
     */
    public function sweepExpiredSessions(): int
    {
        $now = CarbonImmutable::now();
        $sessions = PlaySession::query()
            ->where('status', 'active')
            ->whereNotNull('expected_end_time')
            ->where('expected_end_time', '<=', $now)
            ->get();

        foreach ($sessions as $session) {
            DB::transaction(function () use ($session) {
                $session->status = 'expired';
                $session->end_time = $session->expected_end_time;
                $session->save();

                $this->receiptService->generateForSession($session);
                $this->log($session, 'session.expired', [
                    'final_price' => (float) $session->final_price,
                ]);
            });
        }

        return $sessions->count();
    }

    protected function log(PlaySession $session, string $action, array $payload = []): void
    {
        AuditLog::create([
            'business_id' => $session->business_id,
            'user_id' => Auth::id(),
            'play_session_id' => $session->id,
            'action' => $action,
            'payload' => $payload,
            'ip_address' => request()?->ip(),
        ]);
    }
}
