<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\SessionService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sessions:sweep', function (SessionService $service) {
    $count = $service->sweepExpiredSessions();
    $this->info("Marked {$count} session(s) as expired.");
})->purpose('Mark active sessions whose time has passed as expired and generate receipts.');
