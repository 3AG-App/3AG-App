import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ThemeProvider } from 'next-themes';
import { createRoot } from 'react-dom/client';

import { initI18n } from '@/i18n';
import MainLayout from '@/layouts/main-layout';

const appName = import.meta.env.VITE_APP_NAME || '3AG APP';
const gleapSdkToken = import.meta.env.VITE_GLEAP_SDK_TOKEN;
const gleapScriptId = 'gleap-sdk-script';
const gleapSdkUrl = 'https://sdk.gleap.io/latest/index.js';
let gleapPromise: Promise<GleapClient | null> | null = null;
let gleapInitialized = false;

type GleapClient = {
    initialize: (sdkKey: string, disablePing?: boolean) => void;
    identify: (
        userId: string,
        customerData: {
            name?: string | null;
            email?: string | null;
        },
        userHash?: string,
    ) => void;
    clearIdentity: () => void;
};

function getWindowGleap(): GleapClient | null {
    if (typeof window === 'undefined') {
        return null;
    }

    const gleap = (window as Window & { Gleap?: GleapClient }).Gleap;

    return gleap ?? null;
}

function initializeGleap(gleap: GleapClient): GleapClient {
    if (!gleapInitialized && gleapSdkToken) {
        gleap.initialize(gleapSdkToken);
        gleapInitialized = true;
    }

    return gleap;
}

function getGleapClient(): Promise<GleapClient | null> {
    if (typeof window === 'undefined' || !gleapSdkToken) {
        return Promise.resolve(null);
    }

    if (!gleapPromise) {
        gleapPromise = new Promise((resolve) => {
            const existingGleap = getWindowGleap();

            if (existingGleap) {
                resolve(initializeGleap(existingGleap));
                return;
            }

            const handleLoad = () => {
                const loadedGleap = getWindowGleap();
                if (!loadedGleap) {
                    resolve(null);
                    return;
                }

                resolve(initializeGleap(loadedGleap));
            };

            const existingScript = document.getElementById(gleapScriptId) as HTMLScriptElement | null;
            if (existingScript) {
                existingScript.addEventListener('load', handleLoad, { once: true });
                existingScript.addEventListener('error', () => resolve(null), { once: true });
                return;
            }

            const script = document.createElement('script');
            script.id = gleapScriptId;
            script.src = gleapSdkUrl;
            script.async = true;
            script.addEventListener('load', handleLoad, { once: true });
            script.addEventListener('error', () => resolve(null), { once: true });
            document.head.appendChild(script);
        });
    }

    return gleapPromise;
}

function configureGleapIdentity(pageProps: unknown): void {
    if (!gleapSdkToken) {
        return;
    }

    void getGleapClient().then((gleapClient) => {
        if (!gleapClient) {
            return;
        }

        const auth = (pageProps as { auth?: { user?: { id?: number | string; name?: string; email?: string } | null } }).auth;
        const user = auth?.user;

        if (!user?.id) {
            gleapClient.clearIdentity();
            return;
        }

        gleapClient.identify(String(user.id), {
            name: user.name ?? null,
            email: user.email ?? null,
        });
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
