<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Artisan::call('app:sync-stock-prices');
echo \Illuminate\Support\Facades\Artisan::output();
echo "\n----------------------------------------\n";

\Illuminate\Support\Facades\Artisan::call('portfolio:snapshot');
echo \Illuminate\Support\Facades\Artisan::output();
echo "\n----------------------------------------\n";

\Illuminate\Support\Facades\Artisan::call('app:send-telegram-report');
echo \Illuminate\Support\Facades\Artisan::output();
echo "\n----------------------------------------\n";
