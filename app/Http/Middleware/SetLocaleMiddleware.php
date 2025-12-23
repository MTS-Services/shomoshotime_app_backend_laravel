<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware intelligently sets the locale based on the request type.
     * For web requests, it prioritizes the session. For API requests, it
     * prioritizes the 'Accept-Language' header.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // Get the list of all available language JSON files from the root 'lang' directory.
        $langFiles = File::glob(base_path('lang') . '/*.json');
        $supportedLocales = [];
        foreach ($langFiles as $file) {
            // Extract the filename (e.g., 'en' from 'en.json').
            $supportedLocales[] = pathinfo($file, PATHINFO_FILENAME);
        }

        // Check if the request is an API request by checking the path.
        $isApiRequest = $request->is('api/*');

        if ($isApiRequest) {
            // Logic for API requests: prioritize the 'Accept-Language' header.
            $acceptLanguageHeader = $request->header('Accept-Language');
            if ($acceptLanguageHeader) {
                $requestedLocale = substr($acceptLanguageHeader, 0, 2);
                if (in_array($requestedLocale, $supportedLocales)) {
                    $locale = $requestedLocale;
                }
            }
        } else {
            // Logic for web requests (like your dashboard): prioritize the session.
            if (session()->has('locale')) {
                $sessionLocale = session()->get('locale');
                if (in_array($sessionLocale, $supportedLocales)) {
                    $locale = $sessionLocale;
                }
            }

            // Fallback for web requests: if no session locale is set, check the 'Accept-Language' header.
            if (!$locale) {
                $acceptLanguageHeader = $request->header('Accept-Language');
                if ($acceptLanguageHeader) {
                    $requestedLocale = substr($acceptLanguageHeader, 0, 2);
                    if (in_array($requestedLocale, $supportedLocales)) {
                        $locale = $requestedLocale;
                    }
                }
            }
        }

        // Set the application locale. If no locale was found,
        // use the default fallback locale configured in Laravel.
        App::setLocale($locale ?? config('app.fallback_locale', 'en'));

        return $next($request);
    }
}
