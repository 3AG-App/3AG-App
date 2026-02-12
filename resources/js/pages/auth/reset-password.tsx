import { Form, Head } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';
import { useTranslations } from '@/hooks/use-translations';

interface Props {
    token: string;
    email: string;
}

export default function ResetPassword({ token, email }: Props) {
    const { t } = useTranslations();

    return (
        <>
            <Head title={t('auth.resetPassword.title', 'Reset Password')} />

            <div className="flex flex-1 items-center justify-center px-4 py-12">
                <Card className="w-full max-w-md">
                    <CardHeader className="text-center">
                        <CardTitle className="text-2xl">{t('auth.resetPassword.heading', 'Reset password')}</CardTitle>
                        <CardDescription>{t('auth.resetPassword.subheading', 'Enter your new password below.')}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form action="/reset-password" method="post">
                            {({ errors, processing }) => (
                                <div className="space-y-4">
                                    <input type="hidden" name="token" value={token} />

                                    <div className="space-y-2">
                                        <Label htmlFor="email">{t('auth.email', 'Email')}</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            defaultValue={email}
                                            autoComplete="email"
                                            aria-invalid={!!errors.email}
                                        />
                                        {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="password">{t('auth.newPassword', 'New Password')}</Label>
                                        <PasswordInput
                                            id="password"
                                            name="password"
                                            placeholder={t('auth.passwordPlaceholder', '••••••••')}
                                            autoComplete="new-password"
                                            autoFocus
                                            aria-invalid={!!errors.password}
                                        />
                                        {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="password_confirmation">{t('auth.confirmPassword', 'Confirm Password')}</Label>
                                        <PasswordInput
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder={t('auth.passwordPlaceholder', '••••••••')}
                                            autoComplete="new-password"
                                            aria-invalid={!!errors.password_confirmation}
                                        />
                                        {errors.password_confirmation && <p className="text-sm text-destructive">{errors.password_confirmation}</p>}
                                    </div>

                                    <Button type="submit" className="w-full" disabled={processing}>
                                        {processing
                                            ? t('auth.resetPassword.resetting', 'Resetting...')
                                            : t('auth.resetPassword.reset', 'Reset password')}
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
