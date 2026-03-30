<?php

use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\Auth\SignupController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Frontend;
use App\Models\Page;

Route::get('/activate', function () {
    return response('OK', 200);
})->name('activate');

Route::post('pusher/auth/{socketId}/{channelName}', 'SiteController@pusher')->name('pusher');

/* Legacy paths → /student (panel) and /company (firm panel) */
Route::permanentRedirect('freelancer', url('student'));
Route::any('freelancer/{path}', function (string $path) {
    return redirect(url('student/'.$path), 301);
})->where('path', '.*');

Route::permanentRedirect('user', url('student'));
Route::any('user/{path}', function (string $path) {
    return redirect(url('student/'.$path), 301);
})->where('path', '.*');

Route::permanentRedirect('buyer', url('company'));
Route::any('buyer/{path}', function (string $path) {
    return redirect(url('company/'.$path), 301);
})->where('path', '.*');

Route::permanentRedirect('firm', url('company'));
Route::any('firm/{path}', function (string $path) {
    return redirect(url('company/'.$path), 301);
})->where('path', '.*');

Route::controller(OtpAuthController::class)->prefix('auth/otp')->name('auth.otp.')->group(function () {
    Route::post('send', 'send')->middleware('throttle:12,1')->name('send');
    Route::post('verify', 'verify')->middleware('throttle:24,1')->name('verify');
    Route::post('complete', 'complete')->middleware('throttle:12,1')->name('complete');
    Route::get('complete-form', 'completeForm')->name('complete_form');
});

Route::controller(SignupController::class)->prefix('signup')->name('signup.')->group(function () {
    Route::get('student', 'student')->name('student');
    Route::post('student/start', 'startStudent')->name('student.start');
    Route::get('company', 'company')->name('company');
    Route::post('company/start', 'startCompany')->name('company.start');
});

Route::middleware(['auth'])->group(function () {
    Route::get('my-applications', function () {
        return redirect()->route('user.portal.job_applications');
    })->name('portal.my_applications');
    Route::get('profile', function () {
        return redirect()->route('user.profile.setting');
    })->name('portal.student_profile');
});

Route::redirect('firms', '/talents', 301)->name('firms.directory');

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// Debug route for account content (must be before catch-all routes)
Route::get('/debug-account', function() {
    $output = "<!DOCTYPE html><html><head><title>Account Debug</title><style>body{font-family:Arial;padding:20px;} .section{margin:20px 0;padding:15px;border:1px solid #ccc;} .success{background:#d4edda;} .error{background:#f8d7da;} pre{background:#f5f5f5;padding:10px;overflow-x:auto;}</style></head><body><h1>Account Content Debug</h1>";
    
    // Template check
    $templateName = session('template') ?? gs('active_template');
    $output .= "<div class='section'><h2>1. Template Name</h2><p><strong>Active Template:</strong> " . htmlspecialchars($templateName) . "</p><p><strong>activeTemplate():</strong> " . htmlspecialchars(activeTemplate()) . "</p></div>";
    
    // Frontend content - Force fresh query
    $content = Frontend::where('tempname', 'basic')->where('data_keys', 'account.content')->orderBy('id', 'desc')->first();
    if ($content) {
        // Force refresh from database
        $content->refresh();
        $rawData = \DB::table('frontends')->where('id', $content->id)->value('data_values');
        $decodedRaw = json_decode($rawData);
        
        $output .= "<div class='section success'><h2>2. Frontend Content Found</h2><p>ID: {$content->id}</p><p>Tempname: " . htmlspecialchars($content->tempname) . "</p>";
        $output .= "<p><strong>Raw DB JSON:</strong> " . htmlspecialchars(substr($rawData, 0, 200)) . "...</p>";
        if (is_object($content->data_values)) {
            $output .= "<p class='success'>✅ data_values is object</p><p><strong>Model freelancer_title:</strong> " . htmlspecialchars($content->data_values->freelancer_title ?? 'NOT SET') . "</p><p><strong>Model buyer_title:</strong> " . htmlspecialchars($content->data_values->buyer_title ?? 'NOT SET') . "</p>";
        } else {
            $output .= "<p class='error'>❌ data_values is " . gettype($content->data_values) . "</p>";
        }
        if ($decodedRaw) {
            $output .= "<p><strong>Raw Decoded freelancer_title:</strong> " . htmlspecialchars($decodedRaw->freelancer_title ?? 'NOT SET') . "</p><p><strong>Raw Decoded buyer_title:</strong> " . htmlspecialchars($decodedRaw->buyer_title ?? 'NOT SET') . "</p>";
        }
        $output .= "</div>";
    } else {
        $output .= "<div class='section error'><h2>2. Frontend Content</h2><p>❌ NOT FOUND</p></div>";
    }
    
    // getContent test
    $activeTemplateName = activeTemplateName();
    $output .= "<div class='section'><h2>3. Template Name Used by getContent()</h2><p><strong>activeTemplateName():</strong> " . htmlspecialchars($activeTemplateName ?? 'NULL') . "</p></div>";
    $getContentResult = getContent('account.content', true);
    if ($getContentResult) {
        $account = $getContentResult->data_values;
        $output .= "<div class='section success'><h2>4. getContent() Result</h2><p>✅ Found (ID: {$getContentResult->id}, tempname: " . htmlspecialchars($getContentResult->tempname ?? 'NULL') . ")</p>";
        if (is_object($account)) {
            $output .= "<p><strong>freelancer_title:</strong> " . htmlspecialchars($account->freelancer_title ?? 'NOT SET') . "</p><p><strong>buyer_title:</strong> " . htmlspecialchars($account->buyer_title ?? 'NOT SET') . "</p>";
        }
        $output .= "</div>";
    } else {
        $output .= "<div class='section error'><h2>4. getContent() Result</h2><p>❌ NOT FOUND (using template: " . htmlspecialchars($activeTemplateName ?? 'NULL') . ")</p></div>";
    }
    
    // Homepage sections
    $page = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
    if ($page) {
        $secs = json_decode($page->secs);
        $output .= "<div class='section'><h2>5. Homepage Sections</h2><p>Sections: " . htmlspecialchars($page->secs) . "</p>";
        if (is_array($secs) && in_array('account', $secs)) {
            $output .= "<p class='success'>✅ 'account' is in sections list</p>";
        } else {
            $output .= "<p class='error'>❌ 'account' NOT in sections list</p>";
        }
        $output .= "</div>";
    }
    
    $output .= "<hr><p><a href='/'>← Back to Homepage</a></p></body></html>";
    return $output;
})->name('debug.account');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::middleware('legacy.freelance.explore')->group(function () {
    Route::controller('JobExploreController')->group(function () {
        Route::get('freelance-jobs', 'freelanceJobs')->name('freelance.jobs');
        Route::get('freelance-filter-jobs', 'filterJobs')->name('freelance.filter.jobs');
        Route::get('explore-job/{slug}', 'exploreJob')->name('explore.bid.job');

        Route::get('explore-get-similar-freelancers', 'getSimilarFreelancers')->name('explore.get-similar-freelancers');
        Route::get('explore-get-similar-jobs', 'getSimilarJobs')->name('explore.get-similar-jobs');

        Route::get('talent/details/{username}', 'exploreFreelancer')->name('talent.explore');
    });
});

Route::controller('JobPortalController')->group(function () {
    Route::get('jobs', 'index')->name('jobs.portal.index');
    Route::get('job/{postedJob}', 'show')->name('jobs.portal.show');
    Route::post('job/apply', 'apply')->middleware('auth')->name('jobs.portal.apply');
});

Route::controller('SiteController')->group(function () {

    Route::get('talents', 'allFreelancers')->name('all.freelancers');

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::post('subscribe', 'subscribe')->name('subscribe');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    // Blogs removed from public site — keep names as redirects for old URLs
    Route::get('blogs', fn () => redirect('/', 301))->name('blogs');
    Route::get('blog/{slug}', fn () => redirect('/', 301))->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

Route::get('/dashboard', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role == 'admin') return redirect()->route('admin.dashboard');
        elseif ($user->role == 'company') return redirect()->route('buyer.home');
        else return redirect()->route('user.home');
    }
    return redirect('/login');
});
