<?php
// Direct debug page - Access via: http://localhost/article/debug_account.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Frontend;
use App\Models\Page;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Content Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Account Content Debug</h1>
    
    <div class="section">
        <h2>1. Template Name Check</h2>
        <?php
        $templateName = session('template') ?? gs('active_template');
        echo "<p><strong>Active Template:</strong> " . htmlspecialchars($templateName) . "</p>";
        echo "<p><strong>activeTemplate():</strong> " . htmlspecialchars(activeTemplate()) . "</p>";
        ?>
    </div>
    
    <div class="section">
        <h2>2. Frontend Content Query</h2>
        <?php
        $content = Frontend::where('tempname', 'basic')
            ->where('data_keys', 'account.content')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($content) {
            echo "<p class='success'>✅ Found Frontend Record (ID: {$content->id})</p>";
            echo "<p><strong>Tempname:</strong> " . htmlspecialchars($content->tempname) . "</p>";
            echo "<p><strong>Data Keys:</strong> " . htmlspecialchars($content->data_keys) . "</p>";
            echo "<p><strong>Data Values Type:</strong> " . gettype($content->data_values) . "</p>";
            
            if (is_object($content->data_values)) {
                echo "<p class='success'>✅ data_values is an object</p>";
                echo "<p><strong>freelancer_title:</strong> " . htmlspecialchars($content->data_values->freelancer_title ?? 'NOT SET') . "</p>";
                echo "<p><strong>buyer_title:</strong> " . htmlspecialchars($content->data_values->buyer_title ?? 'NOT SET') . "</p>";
            } else {
                echo "<p class='error'>❌ data_values is NOT an object (type: " . gettype($content->data_values) . ")</p>";
                echo "<pre>" . htmlspecialchars(json_encode($content->data_values, JSON_PRETTY_PRINT)) . "</pre>";
            }
        } else {
            echo "<p class='error'>❌ NO Frontend Record Found!</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. getContent() Function Test</h2>
        <?php
        $getContentResult = getContent('account.content', true);
        if ($getContentResult) {
            echo "<p class='success'>✅ getContent() returned a result</p>";
            echo "<p><strong>ID:</strong> {$getContentResult->id}</p>";
            echo "<p><strong>Tempname:</strong> " . htmlspecialchars($getContentResult->tempname) . "</p>";
            
            $account = $getContentResult->data_values;
            if (is_object($account)) {
                echo "<p class='success'>✅ data_values is an object</p>";
                echo "<p><strong>freelancer_title:</strong> " . htmlspecialchars($account->freelancer_title ?? 'NOT SET') . "</p>";
                echo "<p><strong>buyer_title:</strong> " . htmlspecialchars($account->buyer_title ?? 'NOT SET') . "</p>";
            } else {
                echo "<p class='error'>❌ data_values is NOT an object</p>";
            }
        } else {
            echo "<p class='error'>❌ getContent() returned NULL</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Homepage Sections Check</h2>
        <?php
        $page = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
        if ($page) {
            echo "<p class='success'>✅ Found Homepage Page (ID: {$page->id})</p>";
            echo "<p><strong>Tempname:</strong> " . htmlspecialchars($page->tempname) . "</p>";
            echo "<p><strong>Sections (secs):</strong> " . htmlspecialchars($page->secs) . "</p>";
            
            $secs = json_decode($page->secs);
            if (is_array($secs)) {
                echo "<p><strong>Sections Array:</strong></p><ul>";
                foreach ($secs as $sec) {
                    $highlight = ($sec === 'account') ? ' <strong style="color:green;">← ACCOUNT SECTION</strong>' : '';
                    echo "<li>" . htmlspecialchars($sec) . $highlight . "</li>";
                }
                echo "</ul>";
                
                if (in_array('account', $secs)) {
                    echo "<p class='success'>✅ 'account' is in sections list</p>";
                } else {
                    echo "<p class='error'>❌ 'account' is NOT in sections list</p>";
                }
            }
        } else {
            echo "<p class='error'>❌ NO Homepage Page Found!</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Template File Check</h2>
        <?php
        $templatePath = resource_path('views/templates/basic/sections/account.blade.php');
        if (file_exists($templatePath)) {
            echo "<p class='success'>✅ Template file exists</p>";
            echo "<p><strong>Path:</strong> " . htmlspecialchars($templatePath) . "</p>";
            echo "<p><strong>Last Modified:</strong> " . date('Y-m-d H:i:s', filemtime($templatePath)) . "</p>";
        } else {
            echo "<p class='error'>❌ Template file NOT found!</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>6. Expected Output</h2>
        <p>If everything is working, you should see:</p>
        <ul>
            <li><strong>freelancer_title:</strong> "Sign Up as a Student"</li>
            <li><strong>buyer_title:</strong> "Sign Up as a Firm"</li>
        </ul>
    </div>
    
    <hr>
    <p><a href="/">← Back to Homepage</a></p>
</body>
</html>
