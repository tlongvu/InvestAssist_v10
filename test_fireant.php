<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$sym = 'FPT';
$endDate = now()->format('Y-m-d');
$startDate = now()->subDays(5)->format('Y-m-d');
$url = "https://www.fireant.vn/api/Data/Markets/HistoricalQuotes?symbol={$sym}&startDate={$startDate}&endDate={$endDate}";
$response = Http::timeout(5)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
print_r($response->json());
