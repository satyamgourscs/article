<?php

namespace App\Providers;

use App\Constants\Status;
use App\Lib\Searchable;
use App\Models\AdminNotification;
use App\Models\Buyer;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Job;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\PostedJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip installation check if already on installer pages
        $currentPath = request()->path();
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $isInstallPage = strpos($currentPath, 'install') === 0 || 
                         strpos($currentPath, 'installing') === 0 ||
                         strpos($requestUri, '/install') !== false ||
                         strpos($requestUri, '/installing') !== false ||
                         request()->is('install*') || 
                         request()->is('installing*');
        
        if ($isInstallPage) {
            // On installer page, skip all installation checks
            return;
        }
        
        // Check installation status - use cache first, then verify with DB if needed
        $isInstalled = (bool) cache()->get('SystemInstalled', false);

        if (! $isInstalled) {
            $envFilePath = base_path('.env');

            if (file_exists($envFilePath)) {
                $envContents = file_get_contents($envFilePath);
                if (! empty($envContents)) {
                    try {
                        $keyTables = ['admins', 'users', 'general_settings'];
                        $existingTables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                        $tableNames = array_map(function ($table) {
                            return array_values((array) $table)[0];
                        }, $existingTables);

                        $hasKeyTables = count(array_intersect($keyTables, $tableNames)) == count($keyTables);

                        if ($hasKeyTables) {
                            $isInstalled = true;
                            cache()->put('SystemInstalled', true, now()->addYears(10));
                        }
                    } catch (\Exception $e) {
                        $isInstalled = false;
                    }
                }
            } else {
                try {
                    $keyTables = ['admins', 'users', 'general_settings'];
                    $existingTables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                    $tableNames = array_map(function ($table) {
                        return array_values((array) $table)[0];
                    }, $existingTables);

                    $hasKeyTables = count(array_intersect($keyTables, $tableNames)) == count($keyTables);

                    if ($hasKeyTables) {
                        $isInstalled = true;
                        cache()->put('SystemInstalled', true, now()->addYears(10));
                    }
                } catch (\Exception $e) {
                    $isInstalled = false;
                }
            }
        }

        // If still not installed after all checks, redirect to installer
        if (!$isInstalled) {
            $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            $dir = dirname(str_replace('\\', '/', $script));
            $webBase = ($dir === '/' || $dir === '\\' || $dir === '.') ? '' : rtrim($dir, '/');
            $installPath = ($webBase === '' ? '' : $webBase) . '/install';
            header('Location: ' . $installPath);
            exit;
        }

        $this->configurePublicUrls();

        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            try {
                $view->with([
                    'jobPendingCount'  => Job::pending()->where('status', Status::JOB_PUBLISH)->count(),
                    'jobRejectedCount' => Job::rejected()->count(),
                    'jobDraftedCount'  => Job::drafted()->count(),

                    'projectReportedCount'  => Project::reported()->count(),

                    'incompleteProfileUsersCount'  => User::incompleteProfile()->count(),
                    'bannedUsersCount'           => User::banned()->count(),
                    'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                    'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                    'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                    'kycPendingUsersCount'   => User::kycPending()->count(),

                    'bannedBuyersCount'   => Buyer::banned()->count(),
                    'emailUnverifiedBuyersCount' => Buyer::emailUnverified()->count(),
                    'mobileUnverifiedBuyersCount'   => Buyer::mobileUnverified()->count(),
                    'kycUnverifiedBuyersCount'   => Buyer::kycUnverified()->count(),
                    'kycPendingBuyersCount'   => Buyer::kycPending()->count(),

                    'pendingTicketCount'   => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                    'pendingDepositsCount'    => Deposit::pending()->count(),
                    'pendingWithdrawCount'    => Withdrawal::pending()->count(),
                    'updateAvailable'    => version_compare(gs('available_version'), systemDetails()['version'], '>') ? 'v' . gs('available_version') : false,
                ]);
            } catch (\Exception $e) {
                // If database query fails, set default values to prevent undefined variable errors
                \Log::error('Sidenav composer error: ' . $e->getMessage());
                $view->with([
                    'jobPendingCount' => 0,
                    'jobRejectedCount' => 0,
                    'jobDraftedCount' => 0,
                    'projectReportedCount' => 0,
                    'incompleteProfileUsersCount' => 0,
                    'bannedUsersCount' => 0,
                    'emailUnverifiedUsersCount' => 0,
                    'mobileUnverifiedUsersCount' => 0,
                    'kycUnverifiedUsersCount' => 0,
                    'kycPendingUsersCount' => 0,
                    'bannedBuyersCount' => 0,
                    'emailUnverifiedBuyersCount' => 0,
                    'mobileUnverifiedBuyersCount' => 0,
                    'kycUnverifiedBuyersCount' => 0,
                    'kycPendingBuyersCount' => 0,
                    'pendingTicketCount' => 0,
                    'pendingDepositsCount' => 0,
                    'pendingWithdrawCount' => 0,
                    'updateAvailable' => false,
                ]);
            }
        });

        view()->composer('admin.partials.topnav', function ($view) {
            try {
                $view->with([
                    'adminNotifications' => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                    'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
                ]);
            } catch (\Exception $e) {
                // If database query fails, set default values to prevent undefined variable errors
                \Log::error('Topnav composer error: ' . $e->getMessage());
                $view->with([
                    'adminNotifications' => collect([]),
                    'adminNotificationCount' => 0,
                ]);
            }
        });




        view()->composer('partials.seo', function ($view) {
            try {
                $seo = Frontend::where('data_keys', 'seo.data')->first();
                // Normalize SEO data to always be an object, never null
                $seoData = null;
                if ($seo) {
                    $dataValues = $seo->data_values;
                    if (is_string($dataValues)) {
                        // Decode JSON string to array
                        $decoded = json_decode($dataValues, true);
                        $seoData = is_array($decoded) ? (object)$decoded : $decoded;
                    } elseif (is_array($dataValues)) {
                        // Convert array to object for consistent property access
                        $seoData = (object)$dataValues;
                    } elseif (is_object($dataValues)) {
                        // Already an object, use as-is
                        $seoData = $dataValues;
                    }
                }
                // Always set $seo variable, even if null
                $view->with('seo', $seoData);
            } catch (\Exception $e) {
                // If database query fails, set seo to null to prevent undefined variable error
                \Log::error('SEO composer error: ' . $e->getMessage());
                $view->with('seo', null);
            }
        });


        view()->composer('*', function ($view) {
            $data = $view->getData();
            if (! array_key_exists('unreadCount', $data)) {
                $view->with('unreadCount', 0);
            }
        });

        $composeStudentUnread = function ($view) {
            $unreadCount = 0;
            try {
                if (auth()->guard('web')->check()) {
                    $user = auth()->guard('web')->user();
                    $unreadCount = Message::whereHas('conversation', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->whereNull('read_at')->count();
                }
            } catch (\Throwable $e) {
                \Log::debug('Student unread message count: '.$e->getMessage());
            }
            $view->with('unreadCount', $unreadCount);
        };

        view()->composer([
            'templates.*.layouts.master',
            'Template::layouts.master',
        ], $composeStudentUnread);

        $composeFirmUnread = function ($view) {
            $unreadCount = 0;
            try {
                $buyerGuard = auth()->guard('buyer');
                if ($buyerGuard->check()) {
                    $buyer = $buyerGuard->user();
                    $unreadCount = Message::whereHas('conversation', function ($query) use ($buyer) {
                        $query->where('buyer_id', $buyer->id);
                    })
                        ->whereNull('buyer_read_at')
                        ->count();
                }
            } catch (\Throwable $e) {
                \Log::debug('Firm unread message count: '.$e->getMessage());
            }
            $view->with('unreadCount', $unreadCount);
        };

        view()->composer([
            'templates.*.layouts.buyer_master',
            'Template::layouts.buyer_master',
        ], $composeFirmUnread);


        if (gs('force_ssl')) {
            \URL::forceScheme('https');
        }


        Route::bind('postedJob', function (string $value) {
            if (! jobPortalSchemaReady()) {
                throw new HttpResponseException(response()->view('Template::job_portal.setup_required', [
                    'pageTitle' => __('Job portal setup'),
                ], 503));
            }

            return PostedJob::query()->whereKey($value)->firstOrFail();
        });

        Route::bind('jobApplication', function (string $value) {
            if (! jobPortalSchemaReady()) {
                throw new HttpResponseException(response()->view('Template::job_portal.setup_required', [
                    'pageTitle' => __('Job portal setup'),
                ], 503));
            }

            return JobApplication::query()->whereKey($value)->firstOrFail();
        });

        Paginator::useBootstrapFive();
    }

    /**
     * Align route() and asset() with the public base URL (required for subdirectory installs).
     */
    private function configurePublicUrls(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->syncAppUrlWithScriptSubdirectory();

        $root = rtrim((string) config('app.url'), '/');
        if ($root === '') {
            return;
        }

        \Illuminate\Support\Facades\URL::forceRootUrl($root);

        $assetRoot = config('app.asset_url');
        $assetRoot = is_string($assetRoot) && $assetRoot !== ''
            ? rtrim($assetRoot, '/')
            : $root;

        \Illuminate\Support\Facades\URL::useAssetOrigin($assetRoot);
    }

    /**
     * When APP_URL has no path but index.php is under /subfolder/, extend app.url and asset_url.
     */
    private function syncAppUrlWithScriptSubdirectory(): void
    {
        $url = (string) config('app.url');
        $parts = parse_url($url);
        $configuredPath = isset($parts['path']) ? rtrim((string) $parts['path'], '/') : '';

        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = rtrim(dirname($scriptName), '/');
        if ($scriptDir === '.' || $scriptDir === '' || $scriptDir === '/') {
            return;
        }

        if ($configuredPath !== '' && $configuredPath !== '/') {
            return;
        }

        $scheme = ($parts['scheme'] ?? request()->getScheme()).'://';
        $host = $parts['host'] ?? request()->getHost();
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        $merged = $scheme.$host.$port.$scriptDir;
        config(['app.url' => $merged]);
        config(['app.asset_url' => $merged]);
    }
}
