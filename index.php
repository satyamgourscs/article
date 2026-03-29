<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/core/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/core/vendor/autoload.php';

// Subdirectory installs: the web path still contains /subfolder/... while Laravel routes are /admin, /freelancer/...
// Strip in order: APP_URL path from core/.env (matches URL even if disk folder casing differs), dirname(SCRIPT_NAME),
// then first URI segment vs. install folder basename (case-insensitive).
$rawUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$parsed = parse_url($rawUri);
$path = $parsed['path'] ?? '/';
$query = isset($parsed['query']) ? '?'.$parsed['query'] : '';

$stripPrefix = static function (string $path, string $prefix): string {
    $prefix = rtrim(str_replace('\\', '/', $prefix), '/');
    if ($prefix === '' || $prefix === '/') {
        return $path;
    }
    $pathNorm = str_replace('\\', '/', $path);
    $len = strlen($prefix);
    if (strlen($pathNorm) >= $len && substr($pathNorm, 0, $len) === $prefix
        && (strlen($pathNorm) === $len || $pathNorm[$len] === '/')) {
        $trimmed = substr($pathNorm, $len);

        return ($trimmed === '' || $trimmed === '/') ? '/' : $trimmed;
    }

    // Case mismatch: /articleconnect/... vs prefix /Articleconnect
    if (strlen($pathNorm) >= $len && strcasecmp(substr($pathNorm, 0, $len), $prefix) === 0
        && (strlen($pathNorm) === $len || ($pathNorm[$len] ?? '') === '/')) {
        $trimmed = substr($pathNorm, $len);

        return ($trimmed === '' || $trimmed === '/') ? '/' : $trimmed;
    }

    return $path;
};

$envFile = __DIR__.'/core/.env';
if (is_readable($envFile)) {
    if (preg_match('/^\s*APP_URL\s*=\s*(.+)\s*$/m', (string) file_get_contents($envFile), $m)) {
        $appUrl = trim($m[1], " \t\r\n\"'");
        $urlParts = parse_url($appUrl);
        $urlPath = isset($urlParts['path']) ? rtrim((string) $urlParts['path'], '/') : '';
        if ($urlPath !== '' && $urlPath !== '/') {
            $path = $stripPrefix($path, $urlPath);
        }
    }
}

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$webPrefix = rtrim(dirname($scriptName), '/');
if ($webPrefix !== '' && $webPrefix !== '.' && $webPrefix !== '/') {
    $path = $stripPrefix($path, $webPrefix);
}

$installFolder = basename(str_replace('\\', '/', __DIR__));
$trimPath = trim($path, '/');
if ($trimPath !== '' && $installFolder !== '' && $installFolder !== '.') {
    $segments = explode('/', $trimPath);
    if (isset($segments[0]) && strcasecmp($segments[0], $installFolder) === 0) {
        array_shift($segments);
        $path = $segments === [] ? '/' : '/'.implode('/', $segments);
    }
}

$_SERVER['REQUEST_URI'] = $path.$query;

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/core/bootstrap/app.php')
    ->handleRequest(Request::capture());
