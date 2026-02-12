import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { useTranslation } from 'react-i18next';

import { setI18nLocale } from '@/i18n';
import type { SharedData } from '@/types';

export function useTranslations() {
    const props = usePage<SharedData>().props;
    const { t: i18nT } = useTranslation();

    useEffect(() => {
        void setI18nLocale(props.locale);
    }, [props.locale]);

    const t = (key: string, fallback?: string, params?: Record<string, string | number>) => {
        return i18nT(key, {
            defaultValue: fallback ?? key,
            ...(params ?? {}),
        });
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
