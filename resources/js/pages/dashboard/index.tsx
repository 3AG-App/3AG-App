import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    Calendar,
    Check,
    Copy,
    CreditCard,
    ExternalLink,
    Eye,
    EyeOff,
    Key,
    Package,
    Plus,
    RefreshCw,
    Settings,
    ShoppingBag,
    TrendingUp,
    Wallet,
    Zap,
} from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Progress } from '@/components/ui/progress';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
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

function LicenseKeyCell({ licenseKey }: { licenseKey: string }) {
    const [isVisible, setIsVisible] = useState(false);
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        await navigator.clipboard.writeText(licenseKey);
        setCopied(true);
        toast.success('License key copied to clipboard');
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div className="flex items-center gap-1.5">
            <code className="rounded bg-muted px-2 py-1 font-mono text-xs">
                {isVisible ? licenseKey.slice(0, 20) + '...' : `${licenseKey.slice(0, 8)}${'•'.repeat(8)}`}
            </code>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setIsVisible(!isVisible)}>
                        {isVisible ? <EyeOff className="h-3 w-3" /> : <Eye className="h-3 w-3" />}
                    </Button>
                </TooltipTrigger>
                <TooltipContent>{isVisible ? 'Hide key' : 'Show key'}</TooltipContent>
            </Tooltip>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="ghost" size="icon" className="h-6 w-6" onClick={handleCopy}>
                        {copied ? <Check className="h-3 w-3 text-green-500" /> : <Copy className="h-3 w-3" />}
                    </Button>
                </TooltipTrigger>
                <TooltipContent>{copied ? 'Copied!' : 'Copy key'}</TooltipContent>
            </Tooltip>
        </div>
    );
}

function ActivationProgress({ active, limit }: { active: number; limit: number | null }) {
    if (limit === null) {
        return (
            <div className="flex items-center gap-2">
                <span className="text-sm font-medium">{active}</span>
                <Badge variant="outline" className="text-xs">
                    Unlimited
                </Badge>
            </div>
        );
    }

    const percentage = (active / limit) * 100;
    const isNearLimit = percentage >= 80;
    const isAtLimit = percentage >= 100;

    return (
        <div className="w-32 space-y-1">
            <div className="flex items-center justify-between text-xs">
                <span className={isAtLimit ? 'font-medium text-destructive' : isNearLimit ? 'font-medium text-amber-600' : ''}>
                    {active} / {limit}
                </span>
            </div>
            <Progress
                value={Math.min(percentage, 100)}
                className={isAtLimit ? '[&>div]:bg-destructive' : isNearLimit ? '[&>div]:bg-amber-500' : ''}
            />
        </div>
    );
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
    trend,
    href,
}: {
    title: string;
    value: string | number;
    subtitle: string;
    icon: React.ElementType;
    trend?: { value: number; label: string };
    href?: string;
}) {
    const content = (
        <Card className={href ? 'transition-all hover:border-primary/50 hover:shadow-md' : ''}>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">{title}</CardTitle>
                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10">
                    <Icon className="h-4 w-4 text-primary" />
                </div>
            </CardHeader>
            <CardContent className="space-y-1">
                <div className="text-3xl font-bold tracking-tight">{value}</div>
                <div className="flex items-center gap-2">
                    <p className="text-xs text-muted-foreground">{subtitle}</p>
                    {trend && trend.value > 0 && (
                        <span className="flex items-center text-xs font-medium text-green-600">
                            <TrendingUp className="mr-0.5 h-3 w-3" />+{trend.value} {trend.label}
                        </span>
                    )}
                </div>
            </CardContent>
        </Card>
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
        <Link href={href} className="block">
            <Card
                className={`group h-full transition-all hover:shadow-md ${variant === 'primary' ? 'border-primary/50 bg-primary/5 hover:bg-primary/10' : 'hover:border-primary/50'}`}
            >
                <CardContent className="flex items-center gap-4 p-4">
                    <div
                        className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition-colors ${variant === 'primary' ? 'bg-primary text-primary-foreground' : 'bg-muted group-hover:bg-primary/10'}`}
                    >
                        <Icon className={`h-5 w-5 ${variant === 'default' ? 'group-hover:text-primary' : ''}`} />
                    </div>
                    <div className="min-w-0 flex-1">
                        <h3 className="leading-tight font-medium">{title}</h3>
                        <p className="truncate text-sm text-muted-foreground">{description}</p>
                    </div>
                    <ArrowRight className="h-4 w-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-1 group-hover:text-primary" />
                </CardContent>
            </Card>
        </Link>
    );
}

function LicenseCard({ license }: { license: License }) {
    return (
        <Card className="overflow-hidden transition-all hover:shadow-md">
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between gap-2">
                    <div className="min-w-0 flex-1">
                        <CardTitle className="truncate text-base">{license.product.name}</CardTitle>
                        <CardDescription className="text-xs">{license.package.name}</CardDescription>
                    </div>
                    <Badge variant={getStatusBadgeVariant(license.status)} className="shrink-0">
                        {license.status_label}
                    </Badge>
                </div>
            </CardHeader>
            <CardContent className="space-y-3 pb-3">
                <div>
                    <p className="mb-1 text-xs font-medium text-muted-foreground">License Key</p>
                    <LicenseKeyCell licenseKey={license.license_key} />
                </div>
                <div className="grid grid-cols-2 gap-3">
                    <div>
                        <p className="text-xs font-medium text-muted-foreground">Activations</p>
                        <ActivationProgress active={license.active_activations_count} limit={license.domain_limit} />
                    </div>
                    <div>
                        <p className="mb-1 text-xs font-medium text-muted-foreground">Expires</p>
                        <p className="text-sm font-medium">{formatRelativeDate(license.expires_at)}</p>
                    </div>
                </div>
            </CardContent>
            <CardFooter className="border-t bg-muted/30 px-4 py-2">
                <Button asChild variant="ghost" size="sm" className="ml-auto h-7 text-xs">
                    <Link href={`/dashboard/licenses/${license.id}`}>
                        Manage License
                        <ArrowRight className="ml-1 h-3 w-3" />
                    </Link>
                </Button>
            </CardFooter>
        </Card>
    );
}

function SubscriptionRow({ subscription }: { subscription: DashboardOverview['subscriptions'][0] }) {
    const isActive = subscription.stripe_status === 'active' || subscription.stripe_status === 'trialing';

    return (
        <div className="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50">
            <div
                className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-muted text-muted-foreground'}`}
            >
                <Package className="h-5 w-5" />
            </div>
            <div className="min-w-0 flex-1">
                <div className="flex items-center gap-2">
                    <h4 className="truncate font-medium">{subscription.product_name ?? 'Subscription'}</h4>
                    <Badge variant={getSubscriptionBadgeVariant(subscription.stripe_status)} className="shrink-0 capitalize">
                        {subscription.stripe_status}
                    </Badge>
                </div>
                <p className="text-sm text-muted-foreground">{subscription.package_name ?? 'N/A'}</p>
            </div>
            <div className="hidden text-right sm:block">
                <p className="text-xs font-medium text-muted-foreground">Next Billing</p>
                <p className="text-sm font-medium">{subscription.current_period_end ? formatRelativeDate(subscription.current_period_end) : 'N/A'}</p>
            </div>
            <Button asChild variant="ghost" size="icon" className="shrink-0">
                <Link href="/dashboard/subscriptions">
                    <ArrowRight className="h-4 w-4" />
                </Link>
            </Button>
        </div>
    );
}

export default function Overview({ user, stats, recent_licenses, subscriptions }: OverviewProps) {
    const hasSubscriptions = subscriptions.length > 0;
    const hasLicenses = recent_licenses.length > 0;
    const isNewUser = !hasSubscriptions && !hasLicenses;

    return (
        <DashboardLayout>
            <Head title="Dashboard" />

            <div className="space-y-8">
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
                    <Card className="border-dashed border-primary/50 bg-gradient-to-br from-primary/5 to-transparent">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Zap className="h-5 w-5 text-primary" />
                                Getting Started
                            </CardTitle>
                            <CardDescription>Complete these steps to start using our products</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
                        </CardContent>
                    </Card>
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
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <QuickActionCard
                            title="Manage Licenses"
                            description="View and manage your license keys"
                            icon={Key}
                            href="/dashboard/licenses"
                        />
                        <QuickActionCard
                            title="View Invoices"
                            description="Download invoices and receipts"
                            icon={CreditCard}
                            href="/dashboard/invoices"
                        />
                        <QuickActionCard title="Update Profile" description="Manage your account details" icon={Settings} href="/dashboard/profile" />
                        <QuickActionCard title="Browse Products" description="Subscribe to more products" icon={ShoppingBag} href="/products" />
                    </div>
                )}

                {/* Main Content Grid */}
                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Active Subscriptions */}
                    <Card className="lg:col-span-1">
                        <CardHeader className="flex flex-row items-center justify-between">
                            <div>
                                <CardTitle className="flex items-center gap-2">
                                    <CreditCard className="h-5 w-5 text-muted-foreground" />
                                    Active Subscriptions
                                </CardTitle>
                                <CardDescription>Your current subscription plans</CardDescription>
                            </div>
                            {hasSubscriptions && (
                                <Button asChild variant="ghost" size="sm">
                                    <Link href="/dashboard/subscriptions">
                                        View All
                                        <ArrowRight className="ml-1 h-4 w-4" />
                                    </Link>
                                </Button>
                            )}
                        </CardHeader>
                        <CardContent>
                            {!hasSubscriptions ? (
                                <Empty className="min-h-[200px]">
                                    <EmptyHeader>
                                        <EmptyMedia variant="icon">
                                            <CreditCard className="h-6 w-6" />
                                        </EmptyMedia>
                                        <EmptyTitle>No active subscriptions</EmptyTitle>
                                        <EmptyDescription>Subscribe to a product to get started and unlock powerful features.</EmptyDescription>
                                    </EmptyHeader>
                                    <EmptyContent>
                                        <Button asChild>
                                            <Link href="/products">
                                                <ShoppingBag className="mr-2 h-4 w-4" />
                                                Browse Products
                                            </Link>
                                        </Button>
                                    </EmptyContent>
                                </Empty>
                            ) : (
                                <div className="space-y-3">
                                    {subscriptions.slice(0, 3).map((subscription) => (
                                        <SubscriptionRow key={subscription.id} subscription={subscription} />
                                    ))}
                                    {subscriptions.length > 3 && (
                                        <p className="text-center text-sm text-muted-foreground">
                                            +{subscriptions.length - 3} more subscription{subscriptions.length - 3 > 1 ? 's' : ''}
                                        </p>
                                    )}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Upcoming Renewals */}
                    <Card className="lg:col-span-1">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Calendar className="h-5 w-5 text-muted-foreground" />
                                Upcoming Renewals
                            </CardTitle>
                            <CardDescription>Subscriptions renewing soon</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {!hasSubscriptions ? (
                                <Empty className="min-h-[200px]">
                                    <EmptyHeader>
                                        <EmptyMedia variant="icon">
                                            <Calendar className="h-6 w-6" />
                                        </EmptyMedia>
                                        <EmptyTitle>No upcoming renewals</EmptyTitle>
                                        <EmptyDescription>Your subscription renewals will appear here.</EmptyDescription>
                                    </EmptyHeader>
                                </Empty>
                            ) : (
                                <div className="space-y-3">
                                    {subscriptions
                                        .filter((s) => s.current_period_end && (s.stripe_status === 'active' || s.stripe_status === 'trialing'))
                                        .sort((a, b) => new Date(a.current_period_end!).getTime() - new Date(b.current_period_end!).getTime())
                                        .slice(0, 4)
                                        .map((subscription) => (
                                            <div key={subscription.id} className="flex items-center justify-between rounded-lg border p-3">
                                                <div className="min-w-0 flex-1">
                                                    <p className="truncate font-medium">{subscription.product_name}</p>
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
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Licenses */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle className="flex items-center gap-2">
                                <Key className="h-5 w-5 text-muted-foreground" />
                                Recent Licenses
                            </CardTitle>
                            <CardDescription>Your most recently created license keys</CardDescription>
                        </div>
                        {hasLicenses && (
                            <Button asChild variant="ghost" size="sm">
                                <Link href="/dashboard/licenses">
                                    View All Licenses
                                    <ArrowRight className="ml-1 h-4 w-4" />
                                </Link>
                            </Button>
                        )}
                    </CardHeader>
                    <CardContent>
                        {!hasLicenses ? (
                            <Empty className="min-h-[200px]">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Key className="h-6 w-6" />
                                    </EmptyMedia>
                                    <EmptyTitle>No licenses yet</EmptyTitle>
                                    <EmptyDescription>
                                        Licenses are automatically created when you subscribe to a product. Browse our products to get started.
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button asChild variant="outline">
                                        <Link href="/products">
                                            <ShoppingBag className="mr-2 h-4 w-4" />
                                            Browse Products
                                        </Link>
                                    </Button>
                                </EmptyContent>
                            </Empty>
                        ) : (
                            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                {recent_licenses.slice(0, 6).map((license) => (
                                    <LicenseCard key={license.id} license={license} />
                                ))}
                            </div>
                        )}
                    </CardContent>
                    {hasLicenses && recent_licenses.length > 6 && (
                        <CardFooter className="justify-center border-t pt-4">
                            <Button asChild variant="outline">
                                <Link href="/dashboard/licenses">
                                    View All {recent_licenses.length} Licenses
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                        </CardFooter>
                    )}
                </Card>
            </div>
        </DashboardLayout>
    );
}

Overview.layout = null;
