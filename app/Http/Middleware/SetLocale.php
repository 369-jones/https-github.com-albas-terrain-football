<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported'));

        // Priority: explicit query/session choice > logged-in user preference > browser Accept-Language > default
        $locale = $request->query('lang')
            ?? session('locale')
            ?? optional($request->user())->preferred_locale
            ?? $request->getPreferredLanguage($supported)
            ?? config('locales.default');

        if (! in_array($locale, $supported, true)) {
            $locale = config('locales.default');
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
