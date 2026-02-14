<?php

namespace App\Listeners;

use App\Models\User;
use CraftForge\FilamentLanguageSwitcher\Events\LocaleChanged;

class SyncLocalePreferenceOnLocaleChanged
{
    public function handle(LocaleChanged $event): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $user->getOrCreatePreference()->update([
            'locale' => $event->newLocale,
        ]);
    }
}
