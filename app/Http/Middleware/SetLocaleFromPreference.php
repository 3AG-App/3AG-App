<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromPreference
{
    public const COOKIE_NAME = 'locale';

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            App::setLocale((string) config('app.locale'));

            return $next($request);
        }

        App::setLocale($this->determineLocale($request));

        return $next($request);
    }

    private function determineLocale(Request $request): string
    {
        $supportedLocales = (array) config('app.supported_locales', ['en']);

        $user = $request->user();
        if ($user instanceof User) {
            $userLocale = $user->preference()->value('locale');
            if (is_string($userLocale) && in_array($userLocale, $supportedLocales, true)) {
                return $userLocale;
            }
        }

        $cookieLocale = $request->cookie(self::COOKIE_NAME);
        if (is_string($cookieLocale) && in_array($cookieLocale, $supportedLocales, true)) {
            return $cookieLocale;
        }

        $defaultLocale = (string) config('app.locale');

        return in_array($defaultLocale, $supportedLocales, true)
            ? $defaultLocale
            : (string) ($supportedLocales[0] ?? 'en');
    }
}
