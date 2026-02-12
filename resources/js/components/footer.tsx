import { Link } from '@inertiajs/react';

import { useTranslations } from '@/hooks/use-translations';

export function Footer() {
    const { t } = useTranslations();

    return (
        <footer className="border-t border-border/40 bg-background">
            <div className="container mx-auto px-4 py-12">
                <div className="grid gap-8 md:grid-cols-4">
                    <div className="space-y-4">
                        <Link href="/" className="flex items-center">
                            <img
                                src="/images/logo-black-92x56.webp"
                                srcSet="/images/logo-black-92x56.webp 1x, /images/logo-black-184x112.webp 2x"
                                width={92}
                                height={56}
                                alt="3AG"
                                className="h-8 w-auto dark:hidden"
                            />
                            <img
                                src="/images/logo-white-92x56.webp"
                                srcSet="/images/logo-white-92x56.webp 1x, /images/logo-white-184x112.webp 2x"
                                width={92}
                                height={56}
                                alt="3AG"
                                className="hidden h-8 w-auto dark:block"
                            />
                        </Link>
                        <p className="text-sm text-muted-foreground">
                            {t('footer.tagline', 'Premium WordPress plugins and themes to power your website.')}
                        </p>
                    </div>

                    <div className="space-y-4">
                        <h4 className="text-sm font-semibold">{t('footer.products', 'Products')}</h4>
                        <ul className="space-y-2">
                            <li>
                                <Link href="/products" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('footer.allProducts', 'All Products')}
                                </Link>
                            </li>
                            <li>
                                <Link href="/#features" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('footer.features', 'Features')}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div className="space-y-4">
                        <h4 className="text-sm font-semibold">{t('footer.account', 'Account')}</h4>
                        <ul className="space-y-2">
                            <li>
                                <Link href="/login" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('nav.login', 'Login')}
                                </Link>
                            </li>
                            <li>
                                <Link href="/register" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('footer.register', 'Register')}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div className="space-y-4">
                        <h4 className="text-sm font-semibold">{t('footer.legal', 'Legal')}</h4>
                        <ul className="space-y-2">
                            <li>
                                <a
                                    href="https://3ag.ch/imprint/"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {t('footer.imprint', 'Imprint')}
                                </a>
                            </li>
                            <li>
                                <Link href="/acceptable-use" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('legal.acceptableUse', 'Acceptable Use Policy')}
                                </Link>
                            </li>
                            <li>
                                <Link href="/cookies" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('legal.cookies', 'Cookie Policy')}
                                </Link>
                            </li>
                            <li>
                                <Link href="/privacy" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('legal.privacy', 'Privacy Policy')}
                                </Link>
                            </li>
                            <li>
                                <Link href="/terms" className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {t('legal.terms', 'Terms of Service')}
                                </Link>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    data-gt-cookie-widget-show="true"
                                    className="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {t('legal.manageCookies', 'Manage Cookie Preferences')}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div className="mt-8 border-t border-border/40 pt-8">
                    <p className="text-center text-sm text-muted-foreground">
                        © {new Date().getFullYear()} 3AG. {t('footer.rights', 'All rights reserved.')}
                    </p>
                </div>
            </div>
        </footer>
    );
}
