import { Form, Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PasswordInput } from '@/components/ui/password-input';
import { useTranslations } from '@/hooks/use-translations';

export default function Login() {
    const { t } = useTranslations();

    return (
        <>
            <Head title={t('auth.login.title', 'Login')} />

            <div className="flex flex-1 items-center justify-center px-4 py-12">
                <Card className="w-full max-w-md">
                    <CardHeader className="text-center">
                        <CardTitle className="text-2xl">{t('auth.login.heading', 'Welcome back')}</CardTitle>
                        <CardDescription>{t('auth.login.subheading', 'Enter your credentials to access your account')}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form action="/login" method="post">
                            {({ errors, processing }) => (
                                <div className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="email">{t('auth.email', 'Email')}</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            placeholder={t('auth.emailPlaceholder', 'name@example.com')}
                                            autoComplete="email"
                                            autoFocus
                                            aria-invalid={!!errors.email}
                                        />
                                        {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <div className="flex items-center justify-between">
                                            <Label htmlFor="password">{t('auth.password', 'Password')}</Label>
                                            <Link
                                                href="/forgot-password"
                                                className="text-sm text-muted-foreground underline-offset-4 hover:text-primary hover:underline"
                                            >
                                                {t('auth.forgotPassword.link', 'Forgot password?')}
                                            </Link>
                                        </div>
                                        <PasswordInput
                                            id="password"
                                            name="password"
                                            placeholder={t('auth.passwordPlaceholder', '••••••••')}
                                            autoComplete="current-password"
                                            aria-invalid={!!errors.password}
                                        />
                                        {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <input id="remember" name="remember" type="checkbox" className="h-4 w-4 rounded border-input" />
                                        <Label htmlFor="remember" className="text-sm font-normal">
                                            {t('auth.rememberMe', 'Remember me')}
                                        </Label>
                                    </div>

                                    <Button type="submit" className="w-full" disabled={processing}>
                                        {processing ? t('auth.login.signingIn', 'Signing in...') : t('auth.login.signIn', 'Sign in')}
                                    </Button>

                                    <p className="text-center text-sm text-muted-foreground">
                                        {t('auth.login.noAccount', "Don't have an account?")}{' '}
                                        <Link href="/register" className="font-medium text-primary underline-offset-4 hover:underline">
                                            {t('auth.login.signUp', 'Sign up')}
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
