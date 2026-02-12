import { router } from '@inertiajs/react';
import { type PropsWithChildren, useEffect } from 'react';
import { toast } from 'sonner';

import { Footer } from '@/components/footer';
import { Navbar } from '@/components/navbar';
import { Toaster } from '@/components/ui/sonner';
import { getGetTermsLang, useTranslations } from '@/hooks/use-translations';
import type { FlashData, ToastType } from '@/types';

function showToast(data: NonNullable<FlashData['toast']>) {
    const toastFn: Record<ToastType, typeof toast.success> = {
        success: toast.success,
        error: toast.error,
        warning: toast.warning,
        info: toast.info,
    };

    toastFn[data.type](data.message, {
        description: data.description,
    });
}

function useFlashToast() {
    useEffect(() => {
        return router.on('flash', (event) => {
            const flash = event.detail.flash as FlashData;

            if (flash.toast) {
                showToast(flash.toast);
            }
        });
    }, []);
}

function useConsentBanner() {
    const { locale } = useTranslations();

    useEffect(() => {
        const scriptId = 'getterms-consent-banner-js';

        const existingScript = document.getElementById(scriptId);
        if (existingScript) {
            existingScript.remove();
        }

        const loadScript = () => {
            const script = document.createElement('script');
            script.id = scriptId;
            script.src = `https://gettermscmp.com/cookie-consent/embed/da88da5d-b184-4c05-a6dd-4fbd24473859/${getGetTermsLang(locale)}?auto=true`;
            script.async = true;

            document.body.appendChild(script);
        };

        const globalWithIdle = globalThis as typeof globalThis & {
            requestIdleCallback?: (callback: IdleRequestCallback, options?: IdleRequestOptions) => number;
        };

        if (typeof globalWithIdle.requestIdleCallback === 'function') {
            globalWithIdle.requestIdleCallback(loadScript, { timeout: 2000 });
        } else {
            globalThis.setTimeout(loadScript, 1500);
        }
    }, [locale]);
}

export default function MainLayout({ children }: PropsWithChildren) {
    useFlashToast();
    useConsentBanner();

    return (
        <>
            <div className="flex min-h-screen flex-col bg-background">
                <Navbar />
                <main className="flex-1">{children}</main>
                <Footer />
            </div>
            <Toaster richColors closeButton />
        </>
    );
}
