import { Head } from '@inertiajs/react';
import { useEffect } from 'react';

import { getGetTermsLang, useTranslations } from '@/hooks/use-translations';

export default function AcceptableUse() {
    const { t, locale } = useTranslations();

    useEffect(() => {
        const scriptId = 'getterms-embed-js';
        const existingScript = document.getElementById(scriptId);

        if (existingScript) {
            existingScript.remove();
        }

        const script = document.createElement('script');
        script.id = scriptId;
        script.src = 'https://gettermscdn.com/dist/js/embed.js';
        script.async = true;

        document.body.appendChild(script);
    }, [locale]);

    return (
        <>
            <Head title={t('legal.acceptableUse', 'Acceptable Use Policy')} />

            <div className="container mx-auto max-w-4xl px-4 py-16">
                <div
                    className="getterms-document-embed prose max-w-none prose-neutral dark:prose-invert"
                    data-getterms="6mM6w"
                    data-getterms-document="acceptable-use"
                    data-getterms-lang={getGetTermsLang(locale)}
                    data-getterms-mode="direct"
                    data-getterms-env="https://gettermscdn.com"
                />
            </div>
        </>
    );
}
