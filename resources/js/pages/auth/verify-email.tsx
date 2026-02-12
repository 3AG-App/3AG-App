import { Form, Head } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslations } from '@/hooks/use-translations';

export default function VerifyEmail() {
    const { t } = useTranslations();

    return (
        <>
            <Head title={t('auth.verifyEmail.title', 'Verify Email')} />

            <div className="flex flex-1 items-center justify-center px-4 py-12">
                <Card className="w-full max-w-md">
                    <CardHeader className="text-center">
                        <CardTitle className="text-2xl">{t('auth.verifyEmail.heading', 'Verify your email')}</CardTitle>
                        <CardDescription>
                            {t(
                                'auth.verifyEmail.subheading',
                                'Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.',
                            )}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form action="/email/verification-notification" method="post">
                            {({ processing }) => (
                                <div className="space-y-4">
                                    <Button type="submit" className="w-full" disabled={processing}>
                                        {processing ? t('common.sending', 'Sending...') : t('auth.verifyEmail.resend', 'Resend verification email')}
                                    </Button>
                                </div>
                            )}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
