<?php

namespace App\Services;

use App\Models\PlaySession;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Build the base, business-scoped query for reporting.
     */
    public function query(?string $from = null, ?string $to = null, ?string $status = null): Builder
    {
        $q = PlaySession::query()
            ->with(['child:id,name,child_code', 'package:id,name,is_unlimited,duration_minutes', 'receipt:id,play_session_id,receipt_number,amount'])
            ->orderByDesc('start_time');

        if ($from) {
            $q->where('start_time', '>=', CarbonImmutable::parse($from)->startOfDay());
        }
        if ($to) {
            $q->where('start_time', '<=', CarbonImmutable::parse($to)->endOfDay());
        }
        if ($status && in_array($status, ['active', 'completed', 'expired'])) {
            $q->where('status', $status);
        }
        return $q;
    }

    /**
     * Stream a CSV export of sessions matching the filters.
     */
    public function streamCsv(?string $from, ?string $to, ?string $status, string $filename): StreamedResponse
    {
        $query = $this->query($from, $to, $status);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Receipt #', 'Child Name', 'Child Code', 'Package', 'Duration',
                'Start Time', 'End Time', 'Status', 'Extended (min)', 'Amount',
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $s) {
                    fputcsv($out, [
                        $s->receipt?->receipt_number,
                        $s->child?->name,
                        $s->child?->child_code,
                        $s->package?->name,
                        $s->package?->is_unlimited ? 'Unlimited' : ($s->package?->duration_minutes.' min'),
                        $s->start_time?->toDateTimeString(),
                        $s->end_time?->toDateTimeString(),
                        $s->status,
                        $s->extended_minutes,
                        number_format((float) $s->final_price, 2, '.', ''),
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
