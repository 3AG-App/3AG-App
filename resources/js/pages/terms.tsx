import { Head } from '@inertiajs/react';
import { useEffect } from 'react';

import { useTranslations } from '@/hooks/use-translations';

export default function Terms() {
    const { t } = useTranslations();

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
    }, []);

    return (
        <>
            <Head title={t('legal.terms', 'Terms of Service')} />

            <div className="container mx-auto max-w-4xl px-4 py-16">
                <div
                    className="getterms-document-embed prose max-w-none prose-neutral dark:prose-invert"
                    data-getterms="6mM6w"
                    data-getterms-document="terms-of-service"
                    data-getterms-lang="en-us"
                    data-getterms-mode="direct"
                    data-getterms-env="https://gettermscdn.com"
                />
            </div>
        </>
    );
}
