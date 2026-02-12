<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocaleFromPreference;
use App\Http\Requests\Locale\UpdateLocaleRequest;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function update(UpdateLocaleRequest $request): RedirectResponse
    {
        $locale = $request->validated('locale');

        $user = $request->user();
        if ($user !== null) {
            $user->getOrCreatePreference()->update([
                'locale' => $locale,
            ]);
        }

        $cookie = cookie()->forever(SetLocaleFromPreference::COOKIE_NAME, $locale);

        return redirect()->back()->withCookie($cookie);
    }
}
