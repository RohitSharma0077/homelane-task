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
       // abort(403);
       return \Redirect::to('/')->with(['type' => 'error','err_msg' => 'Unauthorized access']);
       //return \Redirect::to('/')->with(['type' => 'success','success_message' => 'Unauthorized access']);
       
       
    }
}
