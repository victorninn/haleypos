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
}
