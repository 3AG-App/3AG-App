import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Calendar, CreditCard, ExternalLink, Key, Package, Plus, RefreshCw, Settings, ShoppingBag, Wallet, Zap } from 'lucide-react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { useTranslations } from '@/hooks/use-translations';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { DashboardOverview, License } from '@/types';

type TranslateFn = (key: string, fallback?: string, params?: Record<string, string | number>) => string;

interface OverviewProps {
    user: DashboardOverview['user'];
    stats: DashboardOverview['stats'];
    recent_licenses: DashboardOverview['recent_licenses'];
    subscriptions: DashboardOverview['subscriptions'];
}

function formatRelativeDate(dateString: string | null, locale: string, t: TranslateFn): string {
    if (!dateString) return t('common.never', 'Never');
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = date.getTime() - now.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return t('common.expired', 'Expired');
    if (diffDays === 0) return t('common.today', 'Today');
    if (diffDays === 1) return t('common.tomorrow', 'Tomorrow');
    if (diffDays <= 7) {
        return diffDays === 1 ? t('common.inOneDay', 'In 1 day') : t('common.inDays', 'In {count} days', { count: diffDays });
    }
    if (diffDays <= 30) {
        const weeks = Math.ceil(diffDays / 7);
        return weeks === 1 ? t('common.inOneWeek', 'In 1 week') : t('common.inWeeks', 'In {count} weeks', { count: weeks });
    }
    return date.toLocaleDateString(locale, { month: 'short', day: 'numeric', year: 'numeric' });
}

function getStatusBadgeVariant(status: string) {
    switch (status) {
        case 'active':
            return 'default';
        case 'suspended':
            return 'secondary';
        case 'expired':
            return 'outline';
        case 'cancelled':
            return 'destructive';
        default:
            return 'outline';
    }
}

function getSubscriptionBadgeVariant(status: string) {
    switch (status) {
        case 'active':
        case 'trialing':
            return 'default';
        case 'canceled':
        case 'incomplete':
        case 'incomplete_expired':
            return 'destructive';
        case 'past_due':
        case 'unpaid':
            return 'secondary';
        default:
            return 'outline';
    }
}

function StatCard({
    title,
    value,
    subtitle,
    icon: Icon,
    href,
}: {
    title: string;
    value: string | number;
    subtitle: string;
    icon: React.ElementType;
    href?: string;
}) {
    const content = (
        <div className={`rounded-lg border bg-card p-4 ${href ? 'transition-colors hover:bg-muted/50' : ''}`}>
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-xs font-medium text-muted-foreground">{title}</p>
                    <p className="text-2xl font-bold tracking-tight">{value}</p>
                    <p className="text-xs text-muted-foreground">{subtitle}</p>
                </div>
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <Icon className="h-5 w-5 text-primary" />
                </div>
            </div>
        </div>
    );

    if (href) {
        return (
            <Link href={href} className="block">
                {content}
            </Link>
        );
    }

    return content;
}

function QuickActionCard({
    title,
    description,
    icon: Icon,
    href,
    variant = 'default',
}: {
    title: string;
    description: string;
    icon: React.ElementType;
    href: string;
    variant?: 'default' | 'primary';
}) {
    return (
        <Link
            href={href}
            className={`group flex items-center gap-3 rounded-lg border p-3 transition-colors ${variant === 'primary' ? 'border-primary/50 bg-primary/5 hover:bg-primary/10' : 'hover:bg-muted/50'}`}
        >
            <div
                className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${variant === 'primary' ? 'bg-primary text-primary-foreground' : 'bg-muted group-hover:bg-primary/10'}`}
            >
                <Icon className={`h-4 w-4 ${variant === 'default' ? 'group-hover:text-primary' : ''}`} />
            </div>
            <div className="min-w-0 flex-1">
                <p className="text-sm leading-none font-medium">{title}</p>
                <p className="mt-0.5 truncate text-xs text-muted-foreground">{description}</p>
            </div>
            <ArrowRight className="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5" />
        </Link>
    );
}

function LicenseRow({ license }: { license: License }) {
    const { t } = useTranslations();
    const daysUntilExpiry = license.expires_at ? Math.ceil((new Date(license.expires_at).getTime() - Date.now()) / (1000 * 60 * 60 * 24)) : null;
    const isExpiringSoon = daysUntilExpiry !== null && daysUntilExpiry <= 14 && daysUntilExpiry > 0;
    const isActive = license.status === 'active';

    return (
        <Link
            href={`/dashboard/licenses/${license.id}`}
            className="group flex items-center gap-4 rounded-lg border p-3 transition-colors hover:bg-muted/50"
        >
            <div
                className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-muted text-muted-foreground'}`}
            >
                <Key className="h-4 w-4" />
            </div>
            <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2">
                    <span className="truncate font-medium">{license.product.name}</span>
                    <Badge variant={getStatusBadgeVariant(license.status)} className="shrink-0 px-1.5 py-0 text-[10px]">
                        {license.status_label}
                    </Badge>
                    {isExpiringSoon && (
                        <Badge variant="outline" className="shrink-0 border-amber-500 px-1.5 py-0 text-[10px] text-amber-600">
                            {t('dashboard.overview.daysLeftShort', '{count}d left', { count: daysUntilExpiry })}
                        </Badge>
                    )}
                </div>
                <div className="flex items-center gap-3 text-xs text-muted-foreground">
                    <span>{license.package.name}</span>
                    <span>•</span>
                    <span>
                        {license.active_activations_count}
                        {license.domain_limit ? `/${license.domain_limit}` : ''} {t('dashboard.overview.domains', 'domains')}
                    </span>
                </div>
            </div>
            <ArrowRight className="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5" />
        </Link>
    );
}

function SubscriptionRow({ subscription }: { subscription: DashboardOverview['subscriptions'][0] }) {
    const { t, locale } = useTranslations();
    const isActive = subscription.stripe_status === 'active' || subscription.stripe_status === 'trialing';

    return (
        <Link href="/dashboard/subscriptions" className="group flex items-center gap-3 rounded-lg border p-3 transition-colors hover:bg-muted/50">
            <div
                className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-muted text-muted-foreground'}`}
            >
                <Package className="h-4 w-4" />
            </div>
            <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2">
                    <span className="truncate font-medium">
                        {subscription.product_name ?? t('dashboard.overview.subscriptionFallback', 'Subscription')}
                    </span>
                    <Badge variant={getSubscriptionBadgeVariant(subscription.stripe_status)} className="shrink-0 px-1.5 py-0 text-[10px] capitalize">
                        {subscription.stripe_status}
                    </Badge>
                </div>
                <div className="flex items-center gap-2 text-xs text-muted-foreground">
                    <span>{subscription.package_name ?? t('common.na', 'N/A')}</span>
                    {subscription.current_period_end && (
                        <>
                            <span>•</span>
                            <span>
                                {t('dashboard.overview.renews', 'Renews')} {formatRelativeDate(subscription.current_period_end, locale, t)}
                            </span>
                        </>
                    )}
                </div>
            </div>
            <ArrowRight className="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5" />
        </Link>
    );
}

export default function Overview({ user, stats, recent_licenses, subscriptions }: OverviewProps) {
    const { t, locale } = useTranslations();
    const hasSubscriptions = subscriptions.length > 0;
    const hasLicenses = recent_licenses.length > 0;
    const isNewUser = !hasSubscriptions && !hasLicenses;

    const hour = new Date().getHours();
    const greeting =
        hour < 12
            ? t('dashboard.overview.greeting.morning', 'Good morning')
            : hour < 18
              ? t('dashboard.overview.greeting.afternoon', 'Good afternoon')
              : t('dashboard.overview.greeting.evening', 'Good evening');

    return (
        <DashboardLayout>
            <Head title={t('dashboard.nav.dashboard', 'Dashboard')} />

            <div className="space-y-6">
                {/* Welcome Section */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            {greeting}, {user.name?.split(' ')[0] ?? t('dashboard.overview.there', 'there')}! 👋
                        </h1>
                        <p className="text-muted-foreground">
                            {isNewUser
                                ? t('dashboard.overview.welcomeNew', "Welcome! Let's get you started with your first subscription.")
                                : t('dashboard.overview.welcomeBack', "Here's what's happening with your account.")}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button asChild variant="outline" size="sm">
                            <Link href="/dashboard/settings">
                                <Settings className="mr-2 h-4 w-4" />
                                {t('dashboard.nav.settings', 'Settings')}
                            </Link>
                        </Button>
                        <Button asChild size="sm">
                            <Link href="/products">
                                <Plus className="mr-2 h-4 w-4" />
                                {t('dashboard.overview.newSubscription', 'New Subscription')}
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Quick Actions for New Users */}
                {isNewUser && (
                    <div className="rounded-lg border border-dashed border-primary/50 bg-gradient-to-br from-primary/5 to-transparent p-4">
                        <div className="mb-3 flex items-center gap-2">
                            <Zap className="h-4 w-4 text-primary" />
                            <h3 className="font-semibold">{t('dashboard.overview.gettingStarted', 'Getting Started')}</h3>
                        </div>
                        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <QuickActionCard
                                title={t('home.hero.browseProducts', 'Browse Products')}
                                description={t('dashboard.overview.quick.browseDescription', 'Explore our available products')}
                                icon={ShoppingBag}
                                href="/products"
                                variant="primary"
                            />
                            <QuickActionCard
                                title={t('dashboard.overview.quick.completeProfile', 'Complete Profile')}
                                description={t('dashboard.overview.quick.completeProfileDescription', 'Add your billing information')}
                                icon={CreditCard}
                                href="/dashboard/profile"
                            />
                            <QuickActionCard
                                title={t('dashboard.overview.quick.viewDocs', 'View Documentation')}
                                description={t('dashboard.overview.quick.viewDocsDescription', 'Learn how to use our products')}
                                icon={ExternalLink}
                                href="/docs"
                            />
                        </div>
                    </div>
                )}

                {/* Stats Cards */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title={t('dashboard.overview.stats.activeSubscriptions', 'Active Subscriptions')}
                        value={stats.active_subscriptions}
                        subtitle={t('dashboard.overview.stats.totalSubscriptions', '{count} total subscriptions', {
                            count: stats.total_subscriptions,
                        })}
                        icon={CreditCard}
                        href="/dashboard/subscriptions"
                    />
                    <StatCard
                        title={t('dashboard.overview.stats.activeLicenses', 'Active Licenses')}
                        value={stats.active_licenses}
                        subtitle={t('dashboard.overview.stats.totalLicenses', '{count} total licenses', { count: stats.total_licenses })}
                        icon={Key}
                        href="/dashboard/licenses"
                    />
                    <StatCard
                        title={t('dashboard.overview.stats.totalActivations', 'Total Activations')}
                        value={stats.total_activations}
                        subtitle={t('dashboard.overview.stats.acrossLicenses', 'Across all licenses')}
                        icon={RefreshCw}
                    />
                    <StatCard
                        title={t('dashboard.overview.stats.creditBalance', 'Credit Balance')}
                        value={stats.credit_balance}
                        subtitle={t('dashboard.overview.stats.appliedToFuture', 'Applied to future invoices')}
                        icon={Wallet}
                        href="/dashboard/invoices"
                    />
                </div>

                {/* Quick Actions */}
                {hasSubscriptions && (
                    <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        <QuickActionCard
                            title={t('dashboard.overview.quick.manageLicenses', 'Manage Licenses')}
                            description={t('dashboard.overview.quick.manageLicensesDescription', 'View and manage license keys')}
                            icon={Key}
                            href="/dashboard/licenses"
                        />
                        <QuickActionCard
                            title={t('dashboard.overview.quick.viewInvoices', 'View Invoices')}
                            description={t('dashboard.overview.quick.viewInvoicesDescription', 'Download invoices')}
                            icon={CreditCard}
                            href="/dashboard/invoices"
                        />
                        <QuickActionCard
                            title={t('dashboard.overview.quick.updateProfile', 'Update Profile')}
                            description={t('dashboard.overview.quick.updateProfileDescription', 'Manage account details')}
                            icon={Settings}
                            href="/dashboard/profile"
                        />
                        <QuickActionCard
                            title={t('home.hero.browseProducts', 'Browse Products')}
                            description={t('dashboard.overview.quick.browseMoreDescription', 'Subscribe to more')}
                            icon={ShoppingBag}
                            href="/products"
                        />
                    </div>
                )}

                {/* Main Content Grid */}
                <div className="grid gap-4 lg:grid-cols-2">
                    {/* Active Subscriptions */}
                    <div className="rounded-lg border bg-card">
                        <div className="flex items-center justify-between border-b px-4 py-3">
                            <div className="flex items-center gap-2">
                                <CreditCard className="h-4 w-4 text-muted-foreground" />
                                <h3 className="font-semibold">{t('dashboard.overview.activeSubscriptionsSection', 'Active Subscriptions')}</h3>
                            </div>
                            {hasSubscriptions && (
                                <Button asChild variant="ghost" size="sm" className="h-7 text-xs">
                                    <Link href="/dashboard/subscriptions">
                                        {t('common.viewAll', 'View All')}
                                        <ArrowRight className="ml-1 h-3 w-3" />
                                    </Link>
                                </Button>
                            )}
                        </div>
                        <div className="p-3">
                            {!hasSubscriptions ? (
                                <Empty className="min-h-[150px]">
                                    <EmptyHeader>
                                        <EmptyMedia variant="icon">
                                            <CreditCard className="h-5 w-5" />
                                        </EmptyMedia>
                                        <EmptyTitle className="text-sm">
                                            {t('dashboard.overview.emptyActiveSubscriptionsTitle', 'No active subscriptions')}
                                        </EmptyTitle>
                                        <EmptyDescription className="text-xs">
                                            {t('dashboard.overview.noActiveSubscriptions', 'Subscribe to a product to get started.')}
                                        </EmptyDescription>
                                    </EmptyHeader>
                                    <EmptyContent>
                                        <Button asChild size="sm">
                                            <Link href="/products">
                                                <ShoppingBag className="mr-1.5 h-3.5 w-3.5" />
                                                {t('home.hero.browseProducts', 'Browse Products')}
                                            </Link>
                                        </Button>
                                    </EmptyContent>
                                </Empty>
                            ) : (
                                <div className="space-y-2">
                                    {subscriptions.slice(0, 4).map((subscription) => (
                                        <SubscriptionRow key={subscription.id} subscription={subscription} />
                                    ))}
                                    {subscriptions.length > 4 && (
                                        <p className="pt-1 text-center text-xs text-muted-foreground">
                                            +{subscriptions.length - 4} {t('common.more', 'more')}
                                        </p>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Upcoming Renewals */}
                    <div className="rounded-lg border bg-card">
                        <div className="flex items-center gap-2 border-b px-4 py-3">
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                            <h3 className="font-semibold">{t('dashboard.overview.upcomingRenewals', 'Upcoming Renewals')}</h3>
                        </div>
                        <div className="p-3">
                            {!hasSubscriptions ? (
                                <Empty className="min-h-[150px]">
                                    <EmptyHeader>
                                        <EmptyMedia variant="icon">
                                            <Calendar className="h-5 w-5" />
                                        </EmptyMedia>
                                        <EmptyTitle className="text-sm">
                                            {t('dashboard.overview.emptyUpcomingRenewalsTitle', 'No upcoming renewals')}
                                        </EmptyTitle>
                                        <EmptyDescription className="text-xs">
                                            {t('dashboard.overview.noUpcomingRenewals', 'Subscription renewals will appear here.')}
                                        </EmptyDescription>
                                    </EmptyHeader>
                                </Empty>
                            ) : (
                                <div className="space-y-2">
                                    {subscriptions
                                        .filter((s) => s.current_period_end && (s.stripe_status === 'active' || s.stripe_status === 'trialing'))
                                        .sort((a, b) => new Date(a.current_period_end!).getTime() - new Date(b.current_period_end!).getTime())
                                        .slice(0, 4)
                                        .map((subscription) => (
                                            <div key={subscription.id} className="flex items-center justify-between rounded-lg border p-2.5">
                                                <div className="min-w-0 flex-1">
                                                    <p className="truncate text-sm font-medium">{subscription.product_name}</p>
                                                    <p className="text-xs text-muted-foreground">{subscription.package_name}</p>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-sm font-medium">
                                                        {formatRelativeDate(subscription.current_period_end ?? null, locale, t)}
                                                    </p>
                                                    <p className="text-xs text-muted-foreground">
                                                        {subscription.current_period_end &&
                                                            new Date(subscription.current_period_end).toLocaleDateString(locale, {
                                                                month: 'short',
                                                                day: 'numeric',
                                                            })}
                                                    </p>
                                                </div>
                                            </div>
                                        ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Recent Licenses */}
                <div className="rounded-lg border bg-card">
                    <div className="flex items-center justify-between border-b px-4 py-3">
                        <div className="flex items-center gap-2">
                            <Key className="h-4 w-4 text-muted-foreground" />
                            <h3 className="font-semibold">{t('dashboard.overview.recentLicenses', 'Recent Licenses')}</h3>
                        </div>
                        {hasLicenses && (
                            <Button asChild variant="ghost" size="sm" className="h-7 text-xs">
                                <Link href="/dashboard/licenses">
                                    {t('common.viewAll', 'View All')}
                                    <ArrowRight className="ml-1 h-3 w-3" />
                                </Link>
                            </Button>
                        )}
                    </div>
                    <div className="p-3">
                        {!hasLicenses ? (
                            <Empty className="min-h-[150px]">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Key className="h-5 w-5" />
                                    </EmptyMedia>
                                    <EmptyTitle className="text-sm">{t('dashboard.overview.emptyLicensesTitle', 'No licenses yet')}</EmptyTitle>
                                    <EmptyDescription className="text-xs">
                                        {t('dashboard.overview.noLicenses', 'Licenses are created when you subscribe to a product.')}
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button asChild variant="outline" size="sm">
                                        <Link href="/products">
                                            <ShoppingBag className="mr-1.5 h-3.5 w-3.5" />
                                            {t('home.hero.browseProducts', 'Browse Products')}
                                        </Link>
                                    </Button>
                                </EmptyContent>
                            </Empty>
                        ) : (
                            <div className="space-y-2">
                                {recent_licenses.slice(0, 5).map((license) => (
                                    <LicenseRow key={license.id} license={license} />
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}

Overview.layout = null;
