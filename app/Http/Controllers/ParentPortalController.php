<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\PlaySession;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParentPortalController extends Controller
{
    public function show(Request $request)
    {
        $code = trim((string) $request->input('code'));
        $child = null;
        $activeSession = null;
        $sessions = collect();

        if ($code !== '') {
            $child = Child::withoutBusinessScope()->where('child_code', $code)->first();
            if ($child) {
                app()->instance('tenant.business_id', (int) $child->business_id);

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

        return view('parent.lookup', compact('code', 'child', 'activeSession', 'sessions'));
    }

    public function qr(Child $child)
    {
        // Allow cross-tenant retrieval since the child slug is unique
        $url = route('parent.lookup', ['code' => $child->child_code]);
        $svg = QrCode::format('svg')->size(220)->margin(1)->generate($url);
        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
