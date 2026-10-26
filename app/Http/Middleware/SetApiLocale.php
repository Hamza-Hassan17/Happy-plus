<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

// Picks the API response language from the app's Accept-Language header (e.g. "es", "zh-CN", "ar;q=0.9").
class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = self::localeFor($request);
        App::setLocale($locale);

        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    // Also used by the exception handler, which runs without this middleware for unmatched routes.
    public static function localeFor(Request $request): string
    {
        $supported = config('app.api_locales');

        foreach ($request->getLanguages() as $language) {
            $code = strtolower(substr($language, 0, 2));
            if (in_array($code, $supported, true)) {
                return $code;
            }
        }

        return config('app.fallback_locale');
    }
}
