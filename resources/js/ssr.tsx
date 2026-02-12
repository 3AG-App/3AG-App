import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { renderToString } from 'react-dom/server';

import { initI18n } from '@/i18n';
import MainLayout from '@/layouts/main-layout';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer(async (page) => {
    const initialLocale = (page?.props as { locale?: string } | undefined)?.locale ?? 'en';
    await initI18n(initialLocale);

    return createInertiaApp({
        page,
        render: renderToString,
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: (name) =>
            resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')).then((page) => {
                const pageComponent = page as { default: { layout?: unknown } };
                if (!('layout' in pageComponent.default)) {
                    pageComponent.default.layout = (page: React.ReactNode) => <MainLayout>{page}</MainLayout>;
                }
                return page;
            }),
        setup: ({ App, props }) => <App {...props} />,
    });
});
