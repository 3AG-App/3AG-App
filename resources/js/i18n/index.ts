import i18next, { type i18n as I18nInstance } from 'i18next';
import { initReactI18next } from 'react-i18next';

type Dictionary = Record<string, string>;

const translationLoaders = import.meta.glob<Dictionary>('../../../lang/*.json', {
    import: 'default',
});

const i18n: I18nInstance = i18next.createInstance();

let initPromise: Promise<I18nInstance> | null = null;

function getTranslationLoader(locale: string): (() => Promise<Dictionary>) | null {
    const key = `../../../lang/${locale}.json`;
    const loader = translationLoaders[key];

    return typeof loader === 'function' ? loader : null;
}

async function loadLocaleResources(locale: string): Promise<void> {
    if (i18n.hasResourceBundle(locale, 'translation')) {
        return;
    }

    const loader = getTranslationLoader(locale) ?? getTranslationLoader('en');

    if (!loader) {
        return;
    }

    const dictionary = await loader();
    i18n.addResourceBundle(locale, 'translation', dictionary, true, true);
}

export async function initI18n(initialLocale: string): Promise<I18nInstance> {
    if (!initPromise) {
        initPromise = i18n
            .use(initReactI18next)
            .init({
                lng: initialLocale,
                fallbackLng: 'en',
                defaultNS: 'translation',
                ns: ['translation'],
                resources: {},
                keySeparator: false,
                interpolation: {
                    escapeValue: false,
                    prefix: '{',
                    suffix: '}',
                },
            })
            .then(() => i18n);
    }

    const instance = await initPromise;

    await loadLocaleResources(initialLocale);
    if (instance.language !== initialLocale) {
        await instance.changeLanguage(initialLocale);
    }

    return instance;
}

export async function setI18nLocale(locale: string): Promise<void> {
    await initI18n(locale);
    await loadLocaleResources(locale);

    if (i18n.language !== locale) {
        await i18n.changeLanguage(locale);
    }
}

export default i18n;
