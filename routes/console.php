<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * Command to clear log files
 */
Artisan::command('logs:clear', function() {
    array_map('unlink', array_filter((array) glob(storage_path('logs/*.log'))));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
