<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCabinetUser
{
    /**
     * Handle an incoming request.
     * Разрешает доступ всем авторизованным пользователям (включая админов).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('cabinet.login');
        }

        return $next($request);
    }
}
