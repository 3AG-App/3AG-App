import { Link, usePage } from '@inertiajs/react';
import { CreditCard, Home, Key, LayoutDashboard, LogOut, Receipt, Settings, User } from 'lucide-react';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useTranslations } from '@/hooks/use-translations';
import type { SharedData } from '@/types';

const navigationItems = [
    {
        titleKey: 'dashboard.nav.overview',
        fallback: 'Overview',
        url: '/dashboard',
        icon: LayoutDashboard,
    },
    {
        titleKey: 'dashboard.nav.subscriptions',
        fallback: 'Subscriptions',
        url: '/dashboard/subscriptions',
        icon: CreditCard,
    },
    {
        titleKey: 'dashboard.nav.licenses',
        fallback: 'Licenses',
        url: '/dashboard/licenses',
        icon: Key,
    },
    {
        titleKey: 'dashboard.nav.invoices',
        fallback: 'Invoices',
        url: '/dashboard/invoices',
        icon: Receipt,
    },
    {
        titleKey: 'dashboard.nav.profile',
        fallback: 'Profile',
        url: '/dashboard/profile',
        icon: User,
    },
    {
        titleKey: 'dashboard.nav.settings',
        fallback: 'Settings',
        url: '/dashboard/settings',
        icon: Settings,
    },
];

export function DashboardSidebar() {
    const { auth } = usePage<SharedData>().props;
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
    const { t } = useTranslations();

    return (
        <Sidebar>
            <SidebarHeader>
                <div className="flex items-center gap-2 px-2 py-3">
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
                </div>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupLabel>{t('dashboard.nav.dashboard', 'Dashboard')}</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            {navigationItems.map((item) => (
                                <SidebarMenuItem key={item.url}>
                                    <SidebarMenuButton asChild isActive={currentPath === item.url}>
                                        <Link href={item.url}>
                                            <item.icon className="h-4 w-4" />
                                            <span>{t(item.titleKey, item.fallback)}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>

                <SidebarGroup>
                    <SidebarGroupLabel>{t('dashboard.nav.quickLinks', 'Quick Links')}</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild>
                                    <Link href="/">
                                        <Home className="h-4 w-4" />
                                        <span>{t('dashboard.nav.backToWebsite', 'Back to Website')}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <div className="flex items-center gap-3 rounded-lg px-3 py-2">
                            <div className="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                {auth?.user?.name?.charAt(0)?.toUpperCase() ?? 'U'}
                            </div>
                            <div className="flex flex-1 flex-col truncate">
                                <span className="truncate text-sm font-medium">{auth?.user?.name ?? t('common.user', 'User')}</span>
                                <span className="truncate text-xs text-muted-foreground">{auth?.user?.email ?? ''}</span>
                            </div>
                        </div>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton asChild>
                            <Link href="/logout" method="post" as="button" className="w-full">
                                <LogOut className="h-4 w-4" />
                                <span>{t('nav.logout', 'Logout')}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarFooter>
        </Sidebar>
    );
}
