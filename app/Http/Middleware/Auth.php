<?php

namespace App\Http\Middleware;

use Closure;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route()->getName();

        if(!auth()->user()->can($route))
        {
            if($request->ajax())
            {
                abort(403, trans('common.ajax.result.prompt.no_permission'));
            }else{
                return response()->view('error.deny' , ['error'=>trans('common.ajax.result.prompt.no_permission')] , 403);
            }
        }
        return $next($request);
    }
}
