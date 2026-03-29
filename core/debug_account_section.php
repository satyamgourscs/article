<?php
// Debug script to check what getContent() actually returns
// Access via: http://localhost/article/core/debug_account_section.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Frontend;

echo "<h1>Debug: Account Content</h1>";
echo "<pre>";

// Check what getContent returns
$content = Frontend::where('tempname', 'basic')
    ->where('data_keys', 'account.content')
    ->orderBy('id', 'desc')
    ->first();

if ($content) {
    echo "Found Frontend Record:\n";
    echo "ID: " . $content->id . "\n";
    echo "Tempname: " . $content->tempname . "\n";
    echo "Data Keys: " . $content->data_keys . "\n";
    echo "Data Values Type: " . gettype($content->data_values) . "\n";
    echo "\n";
    
    if (is_object($content->data_values)) {
        echo "Data Values Object:\n";
        echo "freelancer_title: " . ($content->data_values->freelancer_title ?? 'NOT SET') . "\n";
        echo "freelancer_content: " . ($content->data_values->freelancer_content ?? 'NOT SET') . "\n";
        echo "freelancer_button_name: " . ($content->data_values->freelancer_button_name ?? 'NOT SET') . "\n";
        echo "buyer_title: " . ($content->data_values->buyer_title ?? 'NOT SET') . "\n";
        echo "buyer_content: " . ($content->data_values->buyer_content ?? 'NOT SET') . "\n";
        echo "buyer_button_name: " . ($content->data_values->buyer_button_name ?? 'NOT SET') . "\n";
    } else {
        echo "Data Values (raw):\n";
        print_r($content->data_values);
    }
    
    echo "\n";
    echo "JSON Encoded:\n";
    echo json_encode($content->data_values, JSON_PRETTY_PRINT);
} else {
    echo "NO RECORD FOUND!\n";
    echo "\nChecking all account.content records:\n";
    $all = Frontend::where('data_keys', 'account.content')->get();
    foreach ($all as $record) {
        echo "ID: {$record->id}, Tempname: {$record->tempname}\n";
    }
}

echo "</pre>";
