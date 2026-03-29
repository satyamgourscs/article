<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyBiddingEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (legacyBiddingEnabled()) {
            return $next($request);
        }

        if (auth()->guard('admin')->check()) {
            return to_route('admin.posted.portal.index')->withNotify([
                ['warning', __('Legacy opportunity / bid management is disabled. Use Job portal.')],
            ]);
        }

        if ($request->user('buyer')) {
            return to_route('firm.posted_jobs.index')->withNotify([
                ['warning', __('Project bidding is disabled. Use posted jobs instead.')],
            ]);
        }

        if ($request->user()) {
            return to_route('jobs.portal.index')->withNotify([
                ['warning', __('Project bidding is disabled. Browse posted jobs instead.')],
            ]);
        }

        return to_route('jobs.portal.index');
    }
}
