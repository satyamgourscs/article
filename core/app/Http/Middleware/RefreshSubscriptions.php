<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshSubscriptions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            app(SubscriptionService::class)->refreshUserSubscription((int) auth()->id());
        }
        if (auth()->guard('buyer')->check()) {
            app(SubscriptionService::class)->refreshBuyerSubscription((int) auth()->guard('buyer')->id());
        }

        return $next($request);
    }
}
