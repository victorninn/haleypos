<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $receipts = Receipt::with('playSession.child', 'playSession.package')
            ->orderByDesc('issued_at')
            ->paginate(20);
        return view('receipts.index', compact('receipts'));
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('playSession.child', 'playSession.package', 'business');
        return view('receipts.show', compact('receipt'));
    }

    public function print(Receipt $receipt)
    {
        $receipt->load('playSession.child', 'playSession.package', 'business');
        return view('receipts.print', compact('receipt'));
    }

    public function exportCsv()
    {
        $filename = 'receipts-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Receipt #', 'Child', 'Package', 'Issued At', 'Amount']);

            Receipt::with('playSession.child', 'playSession.package')
                ->orderBy('issued_at')
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $r) {
                        fputcsv($out, [
                            $r->receipt_number,
                            $r->playSession?->child?->name,
                            $r->playSession?->package?->name,
                            $r->issued_at?->toDateTimeString(),
                            number_format((float) $r->amount, 2, '.', ''),
                        ]);
                    }
                });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function destroyAll(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'in:DELETE'],
        ]);

        $count = Receipt::query()->count();
        Receipt::query()->delete();

        return redirect()->route('receipts.index')->with('status', "Deleted {$count} receipts.");
    }
}