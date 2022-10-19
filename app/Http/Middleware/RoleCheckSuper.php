<?php

namespace App\Http\Middleware;
use Auth;

use Closure;
use Illuminate\Http\Request;

class RoleCheckSuper
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
       // return $next($request);

        if ( Auth::user() && Auth::user()->user_role == config('constants.ROLES.SUPER') ) {

            return $next($request);

        }
        abort(403);
       // return redirect('/');
        // return back()
        //         ->with('return_popup_status', FALSE) // send back with flashed session data
        //         ->with('return_popup_message', 'Unauthorized Access'); // send back with flashed session data
    }
}
