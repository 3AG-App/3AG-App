import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/react';
import Gleap from 'gleap';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ThemeProvider } from 'next-themes';
import { createRoot } from 'react-dom/client';

import { initI18n } from '@/i18n';
import MainLayout from '@/layouts/main-layout';

const appName = import.meta.env.VITE_APP_NAME || '3AG APP';
const gleapSdkToken = import.meta.env.VITE_GLEAP_SDK_TOKEN;

if (typeof window !== 'undefined' && gleapSdkToken) {
    Gleap.initialize(gleapSdkToken);
}

function configureGleapIdentity(pageProps: unknown): void {
    if (!gleapSdkToken) {
        return;
    }

    const auth = (pageProps as { auth?: { user?: { id?: number | string; name?: string; email?: string } | null } }).auth;
    const user = auth?.user;

    if (!user?.id) {
        Gleap.clearIdentity();
        return;
    }

    Gleap.identify(String(user.id), {
        name: user.name ?? null,
        email: user.email ?? null,
    });
}

if (gleapSdkToken) {
    router.on('success', (event) => {
        configureGleapIdentity(event.detail.page.props);
    });
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')).then((page) => {
            const pageComponent = page as { default: { layout?: unknown } };
            if (!('layout' in pageComponent.default)) {
                pageComponent.default.layout = (page: React.ReactNode) => <MainLayout>{page}</MainLayout>;
            }
            return page;
        }),
    setup({ el, App, props }) {
        const initialLocale = (props.initialPage?.props as { locale?: string } | undefined)?.locale ?? 'en';
        configureGleapIdentity(props.initialPage?.props);
        const root = createRoot(el);

        void initI18n(initialLocale).finally(() => {
            root.render(
                <ThemeProvider attribute="class" defaultTheme="system" enableSystem disableTransitionOnChange>
                    <App {...props} />
                </ThemeProvider>,
            );
        });
    },
    progress: {
        color: '#4B5563',
    },
});
