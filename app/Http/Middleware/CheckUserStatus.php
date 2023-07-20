<?php

namespace App\Http\Middleware;

use App\Models\ThirdParty;
use Closure;
use Encore\Admin\Facades\Admin;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $admin_user = Admin::user();
        if (!Admin::guard()->guest()) {
            if ($admin_user) {
                if (ThirdParty::ACTIVE == $admin_user->status) {
                    return $next($request);
                } else {
                    Admin::guard()->logout();

                    return $next($request);
                }
            }
        } else {
            return $next($request);
        }
    }
}
