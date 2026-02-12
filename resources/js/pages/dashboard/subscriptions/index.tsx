import { Head, Link, router } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowRight,
    Calendar,
    Check,
    CreditCard,
    ExternalLink,
    Info,
    MoreHorizontal,
    Package,
    Play,
    RefreshCw,
    ShoppingBag,
    XCircle,
} from 'lucide-react';
import { useState } from 'react';

import { index as licensesIndex } from '@/actions/App/Http/Controllers/Dashboard/LicenseController';
import { cancel as cancelSubscription, resume as resumeSubscription } from '@/actions/App/Http/Controllers/Dashboard/SubscriptionController';
import { index as productsIndex } from '@/actions/App/Http/Controllers/ProductController';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Progress } from '@/components/ui/progress';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useTranslations } from '@/hooks/use-translations';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { Subscription } from '@/types';

type TranslateFn = (key: string, fallback?: string, params?: Record<string, string | number>) => string;

interface SubscriptionsIndexProps {
    subscriptions: Subscription[];
    billing_portal_url: string | null;
}

function getStatusBadgeVariant(status: string) {
    switch (status) {
        case 'active':
        case 'trialing':
            return 'default' as const;
        case 'canceled':
        case 'incomplete':
        case 'incomplete_expired':
            return 'destructive' as const;
        case 'past_due':
        case 'unpaid':
            return 'secondary' as const;
        default:
            return 'outline' as const;
    }
}

function getStatusIcon(status: string) {
    switch (status) {
        case 'active':
            return <Check className="h-4 w-4" />;
        case 'trialing':
            return <Play className="h-4 w-4" />;
        case 'canceled':
        case 'incomplete':
        case 'incomplete_expired':
            return <XCircle className="h-4 w-4" />;
        case 'past_due':
        case 'unpaid':
            return <AlertCircle className="h-4 w-4" />;
        default:
            return <Info className="h-4 w-4" />;
    }
}

function getStatusLabel(status: string, t: TranslateFn): string {
    const key = `dashboard.subscriptions.status.${status}`;

    return t(key, status.replaceAll('_', ' '));
}

function formatDate(dateString: string | null, locale: string, t: TranslateFn): string {
    if (!dateString) return t('common.na', 'N/A');
    return new Date(dateString).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function formatRelativeDate(dateString: string | null, locale: string, t: TranslateFn): string {
    if (!dateString) return t('common.na', 'N/A');
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
    return formatDate(dateString, locale, t);
}

function getDaysUntilRenewal(dateString: string | null): number | null {
    if (!dateString) return null;
    const date = new Date(dateString);
    const now = new Date();
    return Math.ceil((date.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
}

function SubscriptionCard({
    subscription,
    billingPortalUrl,
    onCancel,
    onResume,
}: {
    subscription: Subscription;
    billingPortalUrl: string | null;
    onCancel: () => void;
    onResume: () => void;
}) {
    const { t, locale } = useTranslations();
    const isActive = subscription.stripe_status === 'active' || subscription.stripe_status === 'trialing';
    const daysUntilRenewal = getDaysUntilRenewal(subscription.current_period_end ?? null);
    const isRenewingSoon = daysUntilRenewal !== null && daysUntilRenewal <= 7 && daysUntilRenewal >= 0;

    return (
        <Card className={`gap-0 overflow-hidden p-0 transition-all hover:shadow-md ${isActive ? '' : 'opacity-75'}`}>
            <CardHeader className="px-6 pt-6 pb-3">
                <div className="flex items-start justify-between gap-4">
                    <div className="flex items-start gap-3">
                        <div
                            className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-muted text-muted-foreground'}`}
                        >
                            <Package className="h-5 w-5" />
                        </div>
                        <div className="min-w-0">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                {subscription.product_name ?? t('dashboard.subscriptions.unknownProduct', 'Unknown Product')}
                            </CardTitle>
                            <CardDescription>
                                {subscription.package_name ?? t('dashboard.subscriptions.unknownPackage', 'Unknown Package')}
                            </CardDescription>
                        </div>
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" className="shrink-0">
                                <MoreHorizontal className="h-4 w-4" />
                                <span className="sr-only">{t('common.actions', 'Actions')}</span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {billingPortalUrl && (
                                <DropdownMenuItem asChild>
                                    <a href={billingPortalUrl} target="_blank" rel="noopener noreferrer">
                                        <ExternalLink className="mr-2 h-4 w-4" />
                                        {t('dashboard.subscriptions.manageInStripe', 'Manage in Stripe')}
                                    </a>
                                </DropdownMenuItem>
                            )}
                            {subscription.is_on_grace_period ? (
                                <DropdownMenuItem onClick={onResume}>
                                    <RefreshCw className="mr-2 h-4 w-4" />
                                    {t('dashboard.subscriptions.resume', 'Resume Subscription')}
                                </DropdownMenuItem>
                            ) : (
                                subscription.is_active && (
                                    <>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem className="text-destructive" onClick={onCancel}>
                                            <XCircle className="mr-2 h-4 w-4" />
                                            {t('dashboard.subscriptions.cancel', 'Cancel Subscription')}
                                        </DropdownMenuItem>
                                    </>
                                )
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>
            <CardContent className="space-y-4 px-6 pb-6">
                {/* Status Badges */}
                <div className="flex flex-wrap items-center gap-2">
                    <Badge variant={getStatusBadgeVariant(subscription.stripe_status)} className="gap-1 capitalize">
                        {getStatusIcon(subscription.stripe_status)}
                        {getStatusLabel(subscription.stripe_status, t)}
                    </Badge>
                    {subscription.is_on_trial && (
                        <Badge variant="secondary" className="gap-1">
                            <Play className="h-3 w-3" />
                            {t('dashboard.subscriptions.trialEnds', 'Trial ends')} {formatRelativeDate(subscription.trial_ends_at, locale, t)}
                        </Badge>
                    )}
                    {subscription.is_on_grace_period && (
                        <Badge variant="outline" className="gap-1 border-amber-500 text-amber-600">
                            <Calendar className="h-3 w-3" />
                            {t('dashboard.subscriptions.cancels', 'Cancels')} {formatRelativeDate(subscription.ends_at, locale, t)}
                        </Badge>
                    )}
                </div>

                {/* Billing Period Progress */}
                {isActive && subscription.current_period_end && daysUntilRenewal !== null && (
                    <div className="space-y-2">
                        <div className="flex items-center justify-between text-sm">
                            <span className="text-muted-foreground">{t('dashboard.subscriptions.billingPeriod', 'Billing Period')}</span>
                            <span className={`font-medium ${isRenewingSoon ? 'text-amber-600' : ''}`}>
                                {isRenewingSoon && <Calendar className="mr-1 inline h-3 w-3" />}
                                {t('dashboard.subscriptions.renews', 'Renews')} {formatRelativeDate(subscription.current_period_end, locale, t)}
                            </span>
                        </div>
                        <Tooltip>
                            <TooltipTrigger asChild>
                                <div>
                                    <Progress
                                        value={Math.max(0, 100 - (daysUntilRenewal / 30) * 100)}
                                        className={`h-1.5 ${isRenewingSoon ? '[&>div]:bg-amber-500' : ''}`}
                                    />
                                </div>
                            </TooltipTrigger>
                            <TooltipContent>
                                {t('dashboard.subscriptions.daysUntilRenewal', '{count} days until renewal on {date}', {
                                    count: daysUntilRenewal,
                                    date: formatDate(subscription.current_period_end, locale, t),
                                })}
                            </TooltipContent>
                        </Tooltip>
                    </div>
                )}

                {/* Dates */}
                <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p className="text-muted-foreground">{t('dashboard.subscriptions.started', 'Started')}</p>
                        <p className="font-medium">{formatDate(subscription.created_at, locale, t)}</p>
                    </div>
                    <div>
                        <p className="text-muted-foreground">{t('dashboard.subscriptions.nextBilling', 'Next Billing')}</p>
                        <p className="font-medium">
                            {subscription.is_canceled
                                ? t('dashboard.subscriptions.cancelled', 'Cancelled')
                                : formatDate(subscription.current_period_end ?? null, locale, t)}
                        </p>
                    </div>
                </div>
            </CardContent>
            <CardFooter className="border-t bg-muted/30 px-4 py-3">
                <div className="flex w-full items-center justify-between">
                    {billingPortalUrl ? (
                        <Button asChild variant="ghost" size="sm" className="text-xs">
                            <a href={billingPortalUrl} target="_blank" rel="noopener noreferrer">
                                <CreditCard className="mr-1.5 h-3.5 w-3.5" />
                                {t('dashboard.subscriptions.billingPortal', 'Billing Portal')}
                            </a>
                        </Button>
                    ) : (
                        <span />
                    )}
                    <Button asChild variant="ghost" size="sm" className="text-xs">
                        <Link href={licensesIndex.url()}>
                            {t('dashboard.subscriptions.viewLicenses', 'View Licenses')}
                            <ArrowRight className="ml-1.5 h-3.5 w-3.5" />
                        </Link>
                    </Button>
                </div>
            </CardFooter>
        </Card>
    );
}

export default function SubscriptionsIndex({ subscriptions, billing_portal_url }: SubscriptionsIndexProps) {
    const { t } = useTranslations();
    const [cancelDialogOpen, setCancelDialogOpen] = useState(false);
    const [selectedSubscription, setSelectedSubscription] = useState<Subscription | null>(null);
    const [processing, setProcessing] = useState(false);

    const activeSubscriptions = subscriptions.filter((s) => s.stripe_status === 'active' || s.stripe_status === 'trialing');
    const inactiveSubscriptions = subscriptions.filter((s) => s.stripe_status !== 'active' && s.stripe_status !== 'trialing');

    const handleCancelSubscription = () => {
        if (!selectedSubscription) return;

        setProcessing(true);
        router.post(
            cancelSubscription.url({ subscription: selectedSubscription.id }),
            {},
            {
                onFinish: () => {
                    setProcessing(false);
                    setCancelDialogOpen(false);
                    setSelectedSubscription(null);
                },
            },
        );
    };

    const handleResumeSubscription = (subscription: Subscription) => {
        router.post(resumeSubscription.url({ subscription: subscription.id }));
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: t('dashboard.nav.subscriptions', 'Subscriptions') }]}>
            <Head title={t('dashboard.nav.subscriptions', 'Subscriptions')} />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">{t('dashboard.subscriptions.title', 'Subscriptions')}</h1>
                        <p className="text-muted-foreground">
                            {t('dashboard.subscriptions.subtitle', 'Manage your subscription plans and billing settings.')}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        {billing_portal_url && (
                            <Button asChild variant="outline">
                                <a href={billing_portal_url} target="_blank" rel="noopener noreferrer">
                                    <CreditCard className="mr-2 h-4 w-4" />
                                    {t('dashboard.subscriptions.billingPortal', 'Billing Portal')}
                                    <ExternalLink className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                        )}
                        <Button asChild>
                            <Link href={productsIndex.url()}>
                                <ShoppingBag className="mr-2 h-4 w-4" />
                                {t('dashboard.subscriptions.addSubscription', 'Add Subscription')}
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Stats Overview */}
                {subscriptions.length > 0 && (
                    <div className="grid gap-3 sm:grid-cols-3">
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <Check className="h-4 w-4" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{activeSubscriptions.length}</p>
                                <p className="text-xs text-muted-foreground">{t('common.active', 'Active')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-muted">
                                <XCircle className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{inactiveSubscriptions.length}</p>
                                <p className="text-xs text-muted-foreground">{t('common.inactive', 'Inactive')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10">
                                <Calendar className="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">
                                    {
                                        activeSubscriptions.filter((s) => {
                                            const days = getDaysUntilRenewal(s.current_period_end ?? null);
                                            return days !== null && days <= 7 && days >= 0;
                                        }).length
                                    }
                                </p>
                                <p className="text-xs text-muted-foreground">{t('dashboard.subscriptions.renewingSoon', 'Renewing Soon')}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Empty State */}
                {subscriptions.length === 0 && (
                    <Card>
                        <CardContent className="py-12">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <CreditCard className="h-6 w-6" />
                                    </EmptyMedia>
                                    <EmptyTitle>{t('dashboard.subscriptions.emptyTitle', 'No subscriptions yet')}</EmptyTitle>
                                    <EmptyDescription>
                                        {t(
                                            'dashboard.subscriptions.empty',
                                            "You don't have any active subscriptions. Browse our products and choose a plan that fits your needs.",
                                        )}
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button asChild>
                                        <Link href={productsIndex.url()}>
                                            <ShoppingBag className="mr-2 h-4 w-4" />
                                            {t('home.hero.browseProducts', 'Browse Products')}
                                        </Link>
                                    </Button>
                                </EmptyContent>
                            </Empty>
                        </CardContent>
                    </Card>
                )}

                {/* Active Subscriptions */}
                {activeSubscriptions.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold">
                            <Check className="h-5 w-5 text-green-600" />
                            {t('dashboard.subscriptions.activeSection', 'Active Subscriptions')}
                            <Badge variant="secondary">{activeSubscriptions.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2">
                            {activeSubscriptions.map((subscription) => (
                                <SubscriptionCard
                                    key={subscription.id}
                                    subscription={subscription}
                                    billingPortalUrl={billing_portal_url}
                                    onCancel={() => {
                                        setSelectedSubscription(subscription);
                                        setCancelDialogOpen(true);
                                    }}
                                    onResume={() => handleResumeSubscription(subscription)}
                                />
                            ))}
                        </div>
                    </div>
                )}

                {/* Inactive Subscriptions */}
                {inactiveSubscriptions.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold text-muted-foreground">
                            <XCircle className="h-5 w-5" />
                            {t('dashboard.subscriptions.inactiveSection', 'Inactive Subscriptions')}
                            <Badge variant="outline">{inactiveSubscriptions.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2">
                            {inactiveSubscriptions.map((subscription) => (
                                <SubscriptionCard
                                    key={subscription.id}
                                    subscription={subscription}
                                    billingPortalUrl={billing_portal_url}
                                    onCancel={() => {
                                        setSelectedSubscription(subscription);
                                        setCancelDialogOpen(true);
                                    }}
                                    onResume={() => handleResumeSubscription(subscription)}
                                />
                            ))}
                        </div>
                    </div>
                )}

                {/* Info Card */}
                <Card className="border-blue-200 bg-blue-50/50 dark:border-blue-900 dark:bg-blue-950/50">
                    <CardContent className="flex items-start gap-4 p-4">
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/50">
                            <Info className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 className="font-medium text-blue-900 dark:text-blue-100">
                                {t('dashboard.subscriptions.billingInfo.title', 'Billing Information')}
                            </h3>
                            <p className="text-sm text-blue-800 dark:text-blue-200">
                                {t(
                                    'dashboard.subscriptions.billingInfo.description',
                                    'Your billing is managed securely through Stripe. Use the Billing Portal to update your payment method, view invoices, or download receipts.',
                                )}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Cancel Subscription Dialog */}
            <AlertDialog open={cancelDialogOpen} onOpenChange={setCancelDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{t('dashboard.subscriptions.cancelDialog.title', 'Cancel Subscription?')}</AlertDialogTitle>
                        <AlertDialogDescription>
                            {t(
                                'dashboard.subscriptions.cancelDialog.description',
                                "Are you sure you want to cancel your subscription to {product}? You'll continue to have access until the end of your current billing period.",
                                { product: selectedSubscription?.product_name ?? '' },
                            )}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel disabled={processing}>
                            {t('dashboard.subscriptions.cancelDialog.keep', 'Keep Subscription')}
                        </AlertDialogCancel>
                        <AlertDialogAction
                            onClick={handleCancelSubscription}
                            disabled={processing}
                            className="text-destructive-foreground bg-destructive hover:bg-destructive/90"
                        >
                            {processing
                                ? t('dashboard.subscriptions.cancelDialog.cancelling', 'Cancelling...')
                                : t('dashboard.subscriptions.cancelDialog.confirm', 'Yes, Cancel')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DashboardLayout>
    );
}

SubscriptionsIndex.layout = null;
