import { Form, Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';
import { useTranslations } from '@/hooks/use-translations';

export default function Register() {
    const { t } = useTranslations();

    return (
        <>
            <Head title={t('auth.register.title', 'Register')} />

            <div className="flex flex-1 items-center justify-center px-4 py-12">
                <Card className="w-full max-w-md">
                    <CardHeader className="text-center">
                        <CardTitle className="text-2xl">{t('auth.register.heading', 'Create an account')}</CardTitle>
                        <CardDescription>{t('auth.register.subheading', 'Enter your details to get started')}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form action="/register" method="post">
                            {({ errors, processing }) => (
                                <div className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="name">{t('auth.name', 'Name')}</Label>
                                        <Input
                                            id="name"
                                            name="name"
                                            type="text"
                                            placeholder={t('auth.namePlaceholder', 'John Doe')}
                                            autoComplete="name"
                                            autoFocus
                                            aria-invalid={!!errors.name}
                                        />
                                        {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="email">{t('auth.email', 'Email')}</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            placeholder={t('auth.emailPlaceholder', 'name@example.com')}
                                            autoComplete="email"
                                            aria-invalid={!!errors.email}
                                        />
                                        {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="password">{t('auth.password', 'Password')}</Label>
                                        <PasswordInput
                                            id="password"
                                            name="password"
                                            placeholder={t('auth.passwordPlaceholder', '••••••••')}
                                            autoComplete="new-password"
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
                                            ? t('auth.register.creating', 'Creating account...')
                                            : t('auth.register.create', 'Create account')}
                                    </Button>

                                    <p className="text-center text-xs text-muted-foreground">
                                        {t('auth.register.agreePrefix', 'By creating an account, you agree to our')}{' '}
                                        <Link href="/terms" className="underline underline-offset-4 hover:text-foreground">
                                            {t('legal.terms', 'Terms of Service')}
                                        </Link>{' '}
                                        {t('auth.register.agreeAnd', 'and')}{' '}
                                        <Link href="/privacy" className="underline underline-offset-4 hover:text-foreground">
                                            {t('legal.privacy', 'Privacy Policy')}
                                        </Link>
                                        .
                                    </p>

                                    <p className="text-center text-sm text-muted-foreground">
                                        {t('auth.register.haveAccount', 'Already have an account?')}{' '}
                                        <Link href="/login" className="font-medium text-primary underline-offset-4 hover:underline">
                                            {t('auth.register.signIn', 'Sign in')}
                                        </Link>
                                    </p>
                                </div>
                            )}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
