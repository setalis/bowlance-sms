<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCabinetUser
{
    /**
     * Handle an incoming request.
     * Разрешает доступ только пользователям с ролью User (не админам).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('cabinet.login');
        }

        if ($request->user()->role === UserRole::Admin) {
            abort(403, 'Доступ в личный кабинет запрещён для администраторов.');
        }

        return $next($request);
    }
}
