import { router } from '@inertiajs/react';
import { type PropsWithChildren, useEffect } from 'react';
import { toast } from 'sonner';

import { Footer } from '@/components/footer';
import { Navbar } from '@/components/navbar';
import { Toaster } from '@/components/ui/sonner';
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
    useEffect(() => {
        const scriptId = 'getterms-consent-banner-js';

        if (document.getElementById(scriptId)) {
            return;
        }

        const script = document.createElement('script');
        script.id = scriptId;
        script.src = 'https://gettermscmp.com/cookie-consent/embed/da88da5d-b184-4c05-a6dd-4fbd24473859/en-us?auto=true';
        script.async = true;

        document.body.appendChild(script);
    }, []);
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
