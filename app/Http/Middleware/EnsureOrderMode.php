<?php

namespace App\Http\Middleware;

use App\Enums\OrderMode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderMode
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (OrderMode::current() === null) {
            return redirect()->route('order-mode');
        }

        return $next($request);
    }
}
