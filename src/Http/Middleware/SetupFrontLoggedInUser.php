<?php namespace WebEd\Base\Users\Http\Middleware;

use \Closure;

class SetupFrontLoggedInUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()) {
            set_current_logged_user($request->user());
        }

        return $next($request);
    }
}
