<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCabinet
{
    /**
     * Handle an incoming request.
     * Редирект на страницу входа в личный кабинет, если пользователь не аутентифицирован.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('cabinet.login'));
        }

        return $next($request);
    }
}
