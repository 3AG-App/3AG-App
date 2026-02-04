import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Calendar, CreditCard, ExternalLink, Key, Package, Plus, RefreshCw, Settings, ShoppingBag, Wallet, Zap } from 'lucide-react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { DashboardOverview, License } from '@/types';

interface OverviewProps {
    user: DashboardOverview['user'];
    stats: DashboardOverview['stats'];
    recent_licenses: DashboardOverview['recent_licenses'];
    subscriptions: DashboardOverview['subscriptions'];
}

function getGreeting(): string {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
}

function formatRelativeDate(dateString: string | null): string {
    if (!dateString) return 'Never';
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = date.getTime() - now.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return 'Expired';
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Tomorrow';
    if (diffDays <= 7) return `In ${diffDays} days`;
    if (diffDays <= 30) return `In ${Math.ceil(diffDays / 7)} weeks`;
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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
                            {daysUntilExpiry}d left
                        </Badge>
                    )}
                </div>
                <div className="flex items-center gap-3 text-xs text-muted-foreground">
                    <span>{license.package.name}</span>
                    <span>•</span>
                    <span>
                        {license.active_activations_count}
                        {license.domain_limit ? `/${license.domain_limit}` : ''} domains
                    </span>
                </div>
            </div>
            <ArrowRight className="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5" />
        </Link>
    );
}

function SubscriptionRow({ subscription }: { subscription: DashboardOverview['subscriptions'][0] }) {
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
                    <span className="truncate font-medium">{subscription.product_name ?? 'Subscription'}</span>
                    <Badge variant={getSubscriptionBadgeVariant(subscription.stripe_status)} className="shrink-0 px-1.5 py-0 text-[10px] capitalize">
                        {subscription.stripe_status}
                    </Badge>
                </div>
                <div className="flex items-center gap-2 text-xs text-muted-foreground">
                    <span>{subscription.package_name ?? 'N/A'}</span>
                    {subscription.current_period_end && (
                        <>
                            <span>•</span>
                            <span>Renews {formatRelativeDate(subscription.current_period_end)}</span>
                        </>
                    )}
                </div>
            </div>
            <ArrowRight className="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5" />
        </Link>
    );
}

export default function Overview({ user, stats, recent_licenses, subscriptions }: OverviewProps) {
    const hasSubscriptions = subscriptions.length > 0;
    const hasLicenses = recent_licenses.length > 0;
    const isNewUser = !hasSubscriptions && !hasLicenses;

    return (
        <DashboardLayout>
            <Head title="Dashboard" />

            <div className="space-y-6">
                {/* Welcome Section */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            {getGreeting()}, {user.name?.split(' ')[0] ?? 'there'}! 👋
                        </h1>
                        <p className="text-muted-foreground">
                            {isNewUser
                                ? "Welcome! Let's get you started with your first subscription."
                                : "Here's what's happening with your account."}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button asChild variant="outline" size="sm">
                            <Link href="/dashboard/settings">
                                <Settings className="mr-2 h-4 w-4" />
                                Settings
                            </Link>
                        </Button>
                        <Button asChild size="sm">
                            <Link href="/products">
                                <Plus className="mr-2 h-4 w-4" />
                                New Subscription
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Quick Actions for New Users */}
                {isNewUser && (
                    <div className="rounded-lg border border-dashed border-primary/50 bg-gradient-to-br from-primary/5 to-transparent p-4">
                        <div className="mb-3 flex items-center gap-2">
                            <Zap className="h-4 w-4 text-primary" />
                            <h3 className="font-semibold">Getting Started</h3>
                        </div>
                        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <QuickActionCard
                                title="Browse Products"
                                description="Explore our available products"
                                icon={ShoppingBag}
                                href="/products"
                                variant="primary"
                            />
                            <QuickActionCard
                                title="Complete Profile"
                                description="Add your billing information"
                                icon={CreditCard}
                                href="/dashboard/profile"
                            />
                            <QuickActionCard
                                title="View Documentation"
                                description="Learn how to use our products"
                                icon={ExternalLink}
                                href="/docs"
                            />
                        </div>
                    </div>
                )}

                {/* Stats Cards */}
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title="Active Subscriptions"
                        value={stats.active_subscriptions}
                        subtitle={`${stats.total_subscriptions} total subscriptions`}
                        icon={CreditCard}
                        href="/dashboard/subscriptions"
                    />
                    <StatCard
                        title="Active Licenses"
                        value={stats.active_licenses}
                        subtitle={`${stats.total_licenses} total licenses`}
                        icon={Key}
                        href="/dashboard/licenses"
                    />
                    <StatCard title="Total Activations" value={stats.total_activations} subtitle="Across all licenses" icon={RefreshCw} />
                    <StatCard
                        title="Credit Balance"
                        value={stats.credit_balance}
                        subtitle="Applied to future invoices"
                        icon={Wallet}
                        href="/dashboard/invoices"
                    />
                </div>

                {/* Quick Actions */}
                {hasSubscriptions && (
                    <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        <QuickActionCard title="Manage Licenses" description="View and manage license keys" icon={Key} href="/dashboard/licenses" />
                        <QuickActionCard title="View Invoices" description="Download invoices" icon={CreditCard} href="/dashboard/invoices" />
                        <QuickActionCard title="Update Profile" description="Manage account details" icon={Settings} href="/dashboard/profile" />
                        <QuickActionCard title="Browse Products" description="Subscribe to more" icon={ShoppingBag} href="/products" />
                    </div>
                )}

                {/* Main Content Grid */}
                <div className="grid gap-4 lg:grid-cols-2">
                    {/* Active Subscriptions */}
                    <div className="rounded-lg border bg-card">
                        <div className="flex items-center justify-between border-b px-4 py-3">
                            <div className="flex items-center gap-2">
                                <CreditCard className="h-4 w-4 text-muted-foreground" />
                                <h3 className="font-semibold">Active Subscriptions</h3>
                            </div>
                            {hasSubscriptions && (
                                <Button asChild variant="ghost" size="sm" className="h-7 text-xs">
                                    <Link href="/dashboard/subscriptions">
                                        View All
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
                                        <EmptyTitle className="text-sm">No active subscriptions</EmptyTitle>
                                        <EmptyDescription className="text-xs">Subscribe to a product to get started.</EmptyDescription>
                                    </EmptyHeader>
                                    <EmptyContent>
                                        <Button asChild size="sm">
                                            <Link href="/products">
                                                <ShoppingBag className="mr-1.5 h-3.5 w-3.5" />
                                                Browse Products
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
                                        <p className="pt-1 text-center text-xs text-muted-foreground">+{subscriptions.length - 4} more</p>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Upcoming Renewals */}
                    <div className="rounded-lg border bg-card">
                        <div className="flex items-center gap-2 border-b px-4 py-3">
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                            <h3 className="font-semibold">Upcoming Renewals</h3>
                        </div>
                        <div className="p-3">
                            {!hasSubscriptions ? (
                                <Empty className="min-h-[150px]">
                                    <EmptyHeader>
                                        <EmptyMedia variant="icon">
                                            <Calendar className="h-5 w-5" />
                                        </EmptyMedia>
                                        <EmptyTitle className="text-sm">No upcoming renewals</EmptyTitle>
                                        <EmptyDescription className="text-xs">Subscription renewals will appear here.</EmptyDescription>
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
                                                        {formatRelativeDate(subscription.current_period_end ?? null)}
                                                    </p>
                                                    <p className="text-xs text-muted-foreground">
                                                        {subscription.current_period_end &&
                                                            new Date(subscription.current_period_end).toLocaleDateString('en-US', {
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
                            <h3 className="font-semibold">Recent Licenses</h3>
                        </div>
                        {hasLicenses && (
                            <Button asChild variant="ghost" size="sm" className="h-7 text-xs">
                                <Link href="/dashboard/licenses">
                                    View All
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
                                    <EmptyTitle className="text-sm">No licenses yet</EmptyTitle>
                                    <EmptyDescription className="text-xs">Licenses are created when you subscribe to a product.</EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button asChild variant="outline" size="sm">
                                        <Link href="/products">
                                            <ShoppingBag className="mr-1.5 h-3.5 w-3.5" />
                                            Browse Products
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
