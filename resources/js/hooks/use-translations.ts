import { usePage } from '@inertiajs/react';

import type { SharedData } from '@/types';

function getTranslations(props: SharedData): Record<string, string> {
    return props.translations ?? {};
}

export function useTranslations() {
    const props = usePage<SharedData>().props;
    const translations = getTranslations(props);

    const t = (key: string, fallback?: string, params?: Record<string, string | number>) => {
        const template = translations[key] ?? fallback ?? key;

        if (!params) {
            return template;
        }

        return Object.entries(params).reduce((result, [paramKey, paramValue]) => {
            return result.replaceAll(`{${paramKey}}`, String(paramValue));
        }, template);
    };

    return {
        t,
        locale: props.locale,
        supportedLocales: props.supportedLocales,
    };
}

export function getGetTermsLang(locale: string): string {
    switch (locale) {
        case 'de':
            return 'de-de';
        case 'fr':
            return 'fr-fr';
        case 'en':
        default:
            return 'en-us';
    }
}
