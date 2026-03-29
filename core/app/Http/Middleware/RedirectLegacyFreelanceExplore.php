<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When legacy bidding is off, the public job explore / freelance URLs are disabled
 * in favor of the CA job portal (/jobs).
 */
class RedirectLegacyFreelanceExplore
{
    public function handle(Request $request, Closure $next): Response
    {
        if (legacyBiddingEnabled()) {
            return $next($request);
        }

        $notify[] = ['info', __('Browse posted articleship jobs in the job portal.')];

        return to_route('jobs.portal.index')->withNotify($notify);
    }
}
