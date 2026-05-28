<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');

        // sensible default = current month
        if (! $from && ! $to) {
            $from = CarbonImmutable::now()->startOfMonth()->toDateString();
            $to = CarbonImmutable::now()->endOfMonth()->toDateString();
        }

        $query = $this->reports->query($from, $to, $status);

        $totals = [
            'count' => (clone $query)->count(),
            'revenue' => (float) (clone $query)->sum('final_price'),
        ];
        $sessions = $query->paginate(25)->withQueryString();

        // Build a list of last 12 months for quick monthly exports
        $months = collect(range(0, 11))->map(function ($i) {
            $d = CarbonImmutable::now()->startOfMonth()->subMonths($i);
            return ['label' => $d->format('F Y'), 'value' => $d->format('Y-m')];
        });

        return view('reports.index', compact('sessions', 'totals', 'from', 'to', 'status', 'months'));
    }

    public function exportCsv(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');

        $filename = sprintf('sessions-%s-to-%s.csv',
            $from ?: 'all',
            $to ?: CarbonImmutable::now()->toDateString()
        );

        return $this->reports->streamCsv($from, $to, $status, $filename);
    }

    public function exportMonth(Request $request)
    {
        $month = $request->validate(['month' => 'required|date_format:Y-m'])['month'];
        $start = CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->endOfMonth();

        return $this->reports->streamCsv(
            $start->toDateString(),
            $end->toDateString(),
            null,
            'sessions-'.$month.'.csv'
        );
    }
}
