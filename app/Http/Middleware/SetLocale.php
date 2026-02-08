<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Список поддерживаемых локалей (должны существовать папки lang/{locale}).
     */
    private const SUPPORTED_LOCALES = ['ru', 'ka', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale', config('app.locale', 'ru'));

        if (in_array($locale, self::SUPPORTED_LOCALES)) {
            App::setLocale($locale);
        } else {
            App::setLocale(config('app.fallback_locale', 'ru'));
        }

        return $next($request);
    }
}
