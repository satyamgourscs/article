<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\LegacyBiddingEnabled;
use App\Http\Middleware\CheckBuyerStatus;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\BuyerRegistrationStep;
use App\Http\Middleware\Demo;
use App\Http\Middleware\KycMiddleware;
use App\Http\Middleware\BuyerKycMiddleware;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfBuyer;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotBuyer;
use App\Http\Middleware\RegistrationStep;
use App\Http\Middleware\RefreshSubscriptions;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laramin\Utility\VugiChugi;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::namespace('App\Http\Controllers')->middleware([VugiChugi::mdNm()])->group(function () {
                Route::middleware(['web'])
                    ->namespace('Admin')
                    ->prefix('admin')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                Route::middleware(['web', 'maintenance'])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                    Route::middleware(['web', 'maintenance'])->prefix('company')->group(base_path('routes/buyer.php'));

                    Route::middleware(['web', 'maintenance'])->prefix('student')->group(base_path('routes/user.php'));

                Route::middleware(['web', 'maintenance'])->group(base_path('routes/web.php'));
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\ActiveTemplateMiddleware::class,
            RefreshSubscriptions::class,
        ]);

        $middleware->alias([
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            'admin' => RedirectIfNotAdmin::class,
            'admin.guest' => RedirectIfAdmin::class,

            'buyer' => RedirectIfNotBuyer::class,
            'buyer.guest' => RedirectIfBuyer::class,
            'check.buyer.status' => CheckBuyerStatus::class,
            'buyer.registration.complete' => BuyerRegistrationStep::class,
            'buyer.kyc' => BuyerKycMiddleware::class,

            'check.status' => CheckStatus::class,
            'demo' => Demo::class,
            'kyc' => KycMiddleware::class,
            'registration.complete' => RegistrationStep::class,
            'maintenance' => MaintenanceMode::class,

            'legacy.bidding' => LegacyBiddingEnabled::class,
            'legacy.freelance.explore' => \App\Http\Middleware\RedirectLegacyFreelanceExplore::class,
        ]);

        $middleware->validateCsrfTokens(
            except: ['student/deposit', 'ipn*','pusher/auth*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function () {
            if (request()->is('api/*')) {
                return true;
            }
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! config('app.debug')) {
                return null;
            }
            $appUrl = rtrim((string) config('app.url'), '/');
            $adminUrl = $appUrl === '' ? '/admin' : $appUrl . '/admin';
            $body = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>404 Not Found</title>'
                . '<style>body{font-family:system-ui,sans-serif;padding:1.5rem;max-width:42rem;line-height:1.5}'
                . 'code{background:#f3f4f6;padding:.1rem .35rem;border-radius:4px}</style></head><body>'
                . '<h1>404 — route not found</h1>'
                . '<p><strong>Message:</strong> ' . e($e->getMessage()) . '</p>'
                . '<p><strong>Request path:</strong> <code>' . e($request->path()) . '</code></p>'
                . '<p><strong>APP_URL:</strong> <code>' . e((string) config('app.url')) . '</code></p>'
                . '<p><strong>Try admin login:</strong> <a href="' . e($adminUrl) . '"><code>' . e($adminUrl) . '</code></a></p>'
                . '<p>If the app is in a subdirectory (e.g. <code>/articleconnect</code>), set <code>APP_URL</code> in <code>core/.env</code> to that full base URL, '
                . 'uncomment <code>RewriteBase</code> in the project root <code>.htaccess</code> if URLs return 404, then run <code>php artisan config:clear</code> from the <code>core</code> directory.</p>'
                . '</body></html>';

            return new Response($body, 404, ['Content-Type' => 'text/html; charset=UTF-8']);
        });
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    $notify[] = 'Unauthorized request';
                    return response()->json([
                        'remark' => 'unauthenticated',
                        'status' => 'error',
                        'message' => ['error' => $notify]
                    ]);
                }
            }

            return $response;
        });
    })->create();
