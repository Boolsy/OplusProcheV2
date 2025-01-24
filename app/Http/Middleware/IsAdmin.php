<?php

namespace App\Http\Middleware;

use Closure;


class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role_id === 3) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Accès non autorisé.');
    }
}
