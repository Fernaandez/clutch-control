<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Check a recent event
$e = App\Models\Event::latest('updated_at')->first();
echo "Latest event max_participants: " . ($e->max_participants ?? 'NULL') . "\n";

// 2. Try to update it to 5
$e->max_participants = 5;
$e->save();

$e2 = App\Models\Event::find($e->id);
echo "After save: " . ($e2->max_participants ?? 'NULL') . "\n";
