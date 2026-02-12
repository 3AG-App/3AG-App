import { Link, usePage } from '@inertiajs/react';
import { Check, ChevronDown, LayoutDashboard, LogOut, Menu, Moon, Settings, Sun, User } from 'lucide-react';
import { useTheme } from 'next-themes';
import { useState } from 'react';

import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { useTranslations } from '@/hooks/use-translations';
import type { SharedData } from '@/types';

const navigationItems = [
    { href: '/', labelKey: 'nav.home', fallback: 'Home' },
    { href: '/products', labelKey: 'nav.products', fallback: 'Products' },
];

const localeMeta: Record<string, { label: string; flag: string }> = {
    en: { label: 'English', flag: '🇺🇸' },
    de: { label: 'Deutsch', flag: '🇩🇪' },
    fr: { label: 'Français', flag: '🇫🇷' },
};

function getLocaleMeta(locale: string): { label: string; flag: string } {
    return localeMeta[locale] ?? { label: locale.toUpperCase(), flag: '🏳️' };
}

export function Navbar() {
    const { auth } = usePage<SharedData>().props;
    const { t, locale, supportedLocales } = useTranslations();
    const { theme, setTheme } = useTheme();
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    return (
        <header className="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div className="container mx-auto flex h-16 items-center justify-between px-4">
                {/* Logo */}
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

                {/* Desktop Navigation */}
                <nav className="hidden items-center gap-1 md:flex">
                    {navigationItems.map((item) => (
                        <Button key={item.href} variant="ghost" size="sm" asChild>
                            <Link href={item.href}>{t(item.labelKey, item.fallback)}</Link>
                        </Button>
                    ))}
                </nav>

                {/* Right Side Actions */}
                <div className="flex items-center gap-2">
                    {/* Locale Switcher */}
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm" className="hidden sm:inline-flex">
                                <span className="flex items-center gap-2">
                                    <span aria-hidden className="text-base leading-none">
                                        {getLocaleMeta(locale ?? 'en').flag}
                                    </span>
                                    <span className="text-sm font-medium">{(locale ?? 'en').toUpperCase()}</span>
                                </span>
                                <ChevronDown className="ml-1 h-4 w-4 text-muted-foreground" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" className="w-48">
                            {supportedLocales.map((supportedLocale) => (
                                <DropdownMenuItem key={supportedLocale} asChild>
                                    <Link
                                        href="/locale"
                                        method="post"
                                        as="button"
                                        className="flex w-full cursor-pointer items-center gap-2"
                                        data={{ locale: supportedLocale }}
                                        preserveScroll
                                        preserveState
                                    >
                                        <span aria-hidden className="text-base leading-none">
                                            {getLocaleMeta(supportedLocale).flag}
                                        </span>
                                        <span className="flex-1 text-left">{getLocaleMeta(supportedLocale).label}</span>
                                        {supportedLocale === (locale ?? 'en') && <Check className="h-4 w-4 text-muted-foreground" />}
                                    </Link>
                                </DropdownMenuItem>
                            ))}
                        </DropdownMenuContent>
                    </DropdownMenu>

                    {/* Theme Toggle */}
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}
                        className="hidden sm:inline-flex"
                    >
                        <Sun className="h-4 w-4 scale-100 rotate-0 transition-all dark:scale-0 dark:-rotate-90" />
                        <Moon className="absolute h-4 w-4 scale-0 rotate-90 transition-all dark:scale-100 dark:rotate-0" />
                        <span className="sr-only">{t('nav.toggleTheme', 'Toggle theme')}</span>
                    </Button>

                    {auth?.user ? (
                        <>
                            {/* Dashboard Button */}
                            <Button variant="outline" size="sm" asChild className="hidden sm:inline-flex">
                                <Link href="/dashboard">
                                    <LayoutDashboard className="mr-2 h-4 w-4" />
                                    Dashboard
                                </Link>
                            </Button>

                            {/* User Menu */}
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="ghost" className="relative flex items-center gap-2 px-2">
                                        <Avatar className="h-8 w-8">
                                            <AvatarFallback className="bg-primary text-sm text-primary-foreground">
                                                {getInitials(auth.user.name)}
                                            </AvatarFallback>
                                        </Avatar>
                                        <ChevronDown className="h-4 w-4 text-muted-foreground" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="w-56">
                                    <div className="flex items-center justify-start gap-2 p-2">
                                        <div className="flex flex-col space-y-1 leading-none">
                                            <p className="font-medium">{auth.user.name}</p>
                                            <p className="text-sm text-muted-foreground">{auth.user.email}</p>
                                        </div>
                                    </div>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem asChild>
                                        <Link href="/dashboard" className="cursor-pointer">
                                            <LayoutDashboard className="mr-2 h-4 w-4" />
                                            Dashboard
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem asChild>
                                        <Link href="/dashboard/profile" className="cursor-pointer">
                                            <User className="mr-2 h-4 w-4" />
                                            Profile
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem asChild>
                                        <Link href="/dashboard/settings" className="cursor-pointer">
                                            <Settings className="mr-2 h-4 w-4" />
                                            Settings
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem asChild>
                                        <Link href="/logout" method="post" as="button" className="w-full cursor-pointer">
                                            <LogOut className="mr-2 h-4 w-4" />
                                            Logout
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </>
                    ) : (
                        <>
                            <Button variant="ghost" size="sm" asChild className="hidden sm:inline-flex">
                                <Link href="/login">{t('nav.login', 'Login')}</Link>
                            </Button>
                            <Button size="sm" asChild>
                                <Link href="/register">{t('nav.getStarted', 'Get Started')}</Link>
                            </Button>
                        </>
                    )}

                    {/* Mobile Menu */}
                    <Sheet open={mobileMenuOpen} onOpenChange={setMobileMenuOpen}>
                        <SheetTrigger asChild>
                            <Button variant="ghost" size="icon" className="md:hidden">
                                <Menu className="h-5 w-5" />
                                <span className="sr-only">Toggle menu</span>
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" className="w-[300px] sm:w-[400px]">
                            <SheetHeader>
                                <SheetTitle>
                                    <Link href="/" className="flex items-center" onClick={() => setMobileMenuOpen(false)}>
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
                                </SheetTitle>
                            </SheetHeader>
                            <nav className="mt-8 flex flex-col gap-2">
                                {navigationItems.map((item) => (
                                    <Button
                                        key={item.href}
                                        variant="ghost"
                                        className="justify-start"
                                        asChild
                                        onClick={() => setMobileMenuOpen(false)}
                                    >
                                        <Link href={item.href}>{t(item.labelKey, item.fallback)}</Link>
                                    </Button>
                                ))}

                                {auth?.user ? (
                                    <>
                                        <div className="my-4 border-t" />
                                        <Button variant="ghost" className="justify-start" asChild onClick={() => setMobileMenuOpen(false)}>
                                            <Link href="/dashboard">
                                                <LayoutDashboard className="mr-2 h-4 w-4" />
                                                {t('nav.dashboard', 'Dashboard')}
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" className="justify-start" asChild onClick={() => setMobileMenuOpen(false)}>
                                            <Link href="/dashboard/profile">
                                                <User className="mr-2 h-4 w-4" />
                                                {t('nav.profile', 'Profile')}
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" className="justify-start" asChild onClick={() => setMobileMenuOpen(false)}>
                                            <Link href="/dashboard/settings">
                                                <Settings className="mr-2 h-4 w-4" />
                                                {t('nav.settings', 'Settings')}
                                            </Link>
                                        </Button>
                                        <div className="my-4 border-t" />
                                        <Button variant="ghost" className="justify-start text-destructive" asChild>
                                            <Link href="/logout" method="post" as="button">
                                                <LogOut className="mr-2 h-4 w-4" />
                                                {t('nav.logout', 'Logout')}
                                            </Link>
                                        </Button>
                                    </>
                                ) : (
                                    <>
                                        <div className="my-4 border-t" />
                                        <Button variant="ghost" className="justify-start" asChild onClick={() => setMobileMenuOpen(false)}>
                                            <Link href="/login">{t('nav.login', 'Login')}</Link>
                                        </Button>
                                        <Button asChild onClick={() => setMobileMenuOpen(false)}>
                                            <Link href="/register">{t('nav.getStarted', 'Get Started')}</Link>
                                        </Button>
                                    </>
                                )}

                                <div className="my-4 border-t" />
                                <div className="flex items-center justify-between px-4">
                                    <span className="text-sm text-muted-foreground">{t('nav.language', 'Language')}</span>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="ghost" size="sm">
                                                <span className="flex items-center gap-2">
                                                    <span aria-hidden className="text-base leading-none">
                                                        {getLocaleMeta(locale ?? 'en').flag}
                                                    </span>
                                                    <span className="text-sm font-medium">{(locale ?? 'en').toUpperCase()}</span>
                                                </span>
                                                <ChevronDown className="ml-1 h-4 w-4 text-muted-foreground" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" className="w-48">
                                            {supportedLocales.map((supportedLocale) => (
                                                <DropdownMenuItem key={supportedLocale} asChild>
                                                    <Link
                                                        href="/locale"
                                                        method="post"
                                                        as="button"
                                                        className="flex w-full cursor-pointer items-center gap-2"
                                                        data={{ locale: supportedLocale }}
                                                        preserveScroll
                                                        preserveState
                                                        onClick={() => setMobileMenuOpen(false)}
                                                    >
                                                        <span aria-hidden className="text-base leading-none">
                                                            {getLocaleMeta(supportedLocale).flag}
                                                        </span>
                                                        <span className="flex-1 text-left">{getLocaleMeta(supportedLocale).label}</span>
                                                        {supportedLocale === (locale ?? 'en') && <Check className="h-4 w-4 text-muted-foreground" />}
                                                    </Link>
                                                </DropdownMenuItem>
                                            ))}
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>

                                <div className="flex items-center justify-between px-4">
                                    <span className="text-sm text-muted-foreground">{t('nav.theme', 'Theme')}</span>
                                    <Button variant="ghost" size="icon" onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}>
                                        <Sun className="h-4 w-4 scale-100 rotate-0 transition-all dark:scale-0 dark:-rotate-90" />
                                        <Moon className="absolute h-4 w-4 scale-0 rotate-90 transition-all dark:scale-100 dark:rotate-0" />
                                    </Button>
                                </div>
                            </nav>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>
    );
}
