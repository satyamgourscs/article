<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use App\Support\SafeSchema;
use Closure;
use Illuminate\Http\Request;

class RegistrationStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $needsStep = SafeSchema::hasColumn('users', 'profile_complete')
            && (int) $user->profile_complete !== Status::YES;

        if ($needsStep) {
            if ($request->is('api/*')) {
                $notify[] = 'Please complete your profile to go next';
                return response()->json([
                    'remark'=>'profile_incomplete',
                    'status'=>'error',
                    'message'=>['error'=>$notify],
                ]);
            }else{
                return to_route('user.data');
            }
        }
        return $next($request);
    }
}
