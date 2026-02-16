import { Head, Link } from '@inertiajs/react';
import { type ReactNode } from 'react';

import { Button } from '@/components/ui/button';

type ErrorPageProps = {
    status: number;
};

const errorMessages: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Forbidden',
        description: 'You are not authorized to access this page.',
    },
    404: {
        title: 'Page not found',
        description: 'The page you are looking for could not be found.',
    },
    500: {
        title: 'Server error',
        description: 'An unexpected error occurred on our side.',
    },
    503: {
        title: 'Service unavailable',
        description: 'This service is temporarily unavailable. Please try again shortly.',
    },
};

function getErrorMessage(status: number): { title: string; description: string } {
    return errorMessages[status] ?? errorMessages[500];
}

export default function Error({ status }: ErrorPageProps) {
    const message = getErrorMessage(status);

    return (
        <>
            <Head title={`${status} ${message.title}`} />

            <div className="flex min-h-screen items-center justify-center bg-background px-4 py-12">
                <div className="w-full max-w-xl rounded-lg border bg-card p-8 text-center shadow-sm">
                    <p className="text-sm font-medium text-muted-foreground">Error {status}</p>
                    <h1 className="mt-2 text-3xl font-semibold tracking-tight">{message.title}</h1>
                    <p className="mt-4 text-muted-foreground">{message.description}</p>

                    <div className="mt-8 flex items-center justify-center gap-3">
                        <Button asChild>
                            <Link href="/">Back to home</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </>
    );
}

Error.layout = (page: ReactNode) => page;
