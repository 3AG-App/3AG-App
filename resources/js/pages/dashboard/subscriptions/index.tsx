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
import DashboardLayout from '@/layouts/dashboard-layout';
import type { Subscription } from '@/types';

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

function formatDate(dateString: string | null): string {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function formatRelativeDate(dateString: string | null): string {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = date.getTime() - now.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return 'Expired';
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Tomorrow';
    if (diffDays <= 7) return `In ${diffDays} days`;
    if (diffDays <= 30) return `In ${Math.ceil(diffDays / 7)} weeks`;
    return formatDate(dateString);
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
    const isActive = subscription.stripe_status === 'active' || subscription.stripe_status === 'trialing';
    const daysUntilRenewal = getDaysUntilRenewal(subscription.current_period_end ?? null);
    const isRenewingSoon = daysUntilRenewal !== null && daysUntilRenewal <= 7 && daysUntilRenewal >= 0;

    return (
        <Card className={`transition-all hover:shadow-md ${isActive ? '' : 'opacity-75'}`}>
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between gap-4">
                    <div className="flex items-start gap-3">
                        <div
                            className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-muted text-muted-foreground'}`}
                        >
                            <Package className="h-5 w-5" />
                        </div>
                        <div className="min-w-0">
                            <CardTitle className="flex items-center gap-2 text-lg">{subscription.product_name ?? 'Unknown Product'}</CardTitle>
                            <CardDescription>{subscription.package_name ?? 'Unknown Package'}</CardDescription>
                        </div>
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" className="shrink-0">
                                <MoreHorizontal className="h-4 w-4" />
                                <span className="sr-only">Actions</span>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {billingPortalUrl && (
                                <DropdownMenuItem asChild>
                                    <a href={billingPortalUrl} target="_blank" rel="noopener noreferrer">
                                        <ExternalLink className="mr-2 h-4 w-4" />
                                        Manage in Stripe
                                    </a>
                                </DropdownMenuItem>
                            )}
                            {subscription.is_on_grace_period ? (
                                <DropdownMenuItem onClick={onResume}>
                                    <RefreshCw className="mr-2 h-4 w-4" />
                                    Resume Subscription
                                </DropdownMenuItem>
                            ) : (
                                subscription.is_active && (
                                    <>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem className="text-destructive" onClick={onCancel}>
                                            <XCircle className="mr-2 h-4 w-4" />
                                            Cancel Subscription
                                        </DropdownMenuItem>
                                    </>
                                )
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>
            <CardContent className="space-y-4">
                {/* Status Badges */}
                <div className="flex flex-wrap items-center gap-2">
                    <Badge variant={getStatusBadgeVariant(subscription.stripe_status)} className="gap-1 capitalize">
                        {getStatusIcon(subscription.stripe_status)}
                        {subscription.stripe_status}
                    </Badge>
                    {subscription.is_on_trial && (
                        <Badge variant="secondary" className="gap-1">
                            <Play className="h-3 w-3" />
                            Trial ends {formatRelativeDate(subscription.trial_ends_at)}
                        </Badge>
                    )}
                    {subscription.is_on_grace_period && (
                        <Badge variant="outline" className="gap-1 border-amber-500 text-amber-600">
                            <Calendar className="h-3 w-3" />
                            Cancels {formatRelativeDate(subscription.ends_at)}
                        </Badge>
                    )}
                </div>

                {/* Billing Period Progress */}
                {isActive && subscription.current_period_end && daysUntilRenewal !== null && (
                    <div className="space-y-2">
                        <div className="flex items-center justify-between text-sm">
                            <span className="text-muted-foreground">Billing Period</span>
                            <span className={`font-medium ${isRenewingSoon ? 'text-amber-600' : ''}`}>
                                {isRenewingSoon && <Calendar className="mr-1 inline h-3 w-3" />}
                                Renews {formatRelativeDate(subscription.current_period_end)}
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
                                {daysUntilRenewal} days until renewal on {formatDate(subscription.current_period_end)}
                            </TooltipContent>
                        </Tooltip>
                    </div>
                )}

                {/* Dates */}
                <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p className="text-muted-foreground">Started</p>
                        <p className="font-medium">{formatDate(subscription.created_at)}</p>
                    </div>
                    <div>
                        <p className="text-muted-foreground">Next Billing</p>
                        <p className="font-medium">{subscription.is_canceled ? 'Cancelled' : formatDate(subscription.current_period_end ?? null)}</p>
                    </div>
                </div>
            </CardContent>
            <CardFooter className="border-t bg-muted/30 px-4 py-3">
                <div className="flex w-full items-center justify-between">
                    {billingPortalUrl ? (
                        <Button asChild variant="ghost" size="sm" className="text-xs">
                            <a href={billingPortalUrl} target="_blank" rel="noopener noreferrer">
                                <CreditCard className="mr-1.5 h-3.5 w-3.5" />
                                Billing Portal
                            </a>
                        </Button>
                    ) : (
                        <span />
                    )}
                    <Button asChild variant="ghost" size="sm" className="text-xs">
                        <Link href="/dashboard/licenses">
                            View Licenses
                            <ArrowRight className="ml-1.5 h-3.5 w-3.5" />
                        </Link>
                    </Button>
                </div>
            </CardFooter>
        </Card>
    );
}

export default function SubscriptionsIndex({ subscriptions, billing_portal_url }: SubscriptionsIndexProps) {
    const [cancelDialogOpen, setCancelDialogOpen] = useState(false);
    const [selectedSubscription, setSelectedSubscription] = useState<Subscription | null>(null);
    const [processing, setProcessing] = useState(false);

    const activeSubscriptions = subscriptions.filter((s) => s.stripe_status === 'active' || s.stripe_status === 'trialing');
    const inactiveSubscriptions = subscriptions.filter((s) => s.stripe_status !== 'active' && s.stripe_status !== 'trialing');

    const handleCancelSubscription = () => {
        if (!selectedSubscription) return;

        setProcessing(true);
        router.post(
            `/dashboard/subscriptions/${selectedSubscription.id}/cancel`,
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
        router.post(`/dashboard/subscriptions/${subscription.id}/resume`);
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: 'Subscriptions' }]}>
            <Head title="Subscriptions" />

            <div className="space-y-8">
                {/* Page Header */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Subscriptions</h1>
                        <p className="text-muted-foreground">Manage your subscription plans and billing settings.</p>
                    </div>
                    <div className="flex gap-2">
                        {billing_portal_url && (
                            <Button asChild variant="outline">
                                <a href={billing_portal_url} target="_blank" rel="noopener noreferrer">
                                    <CreditCard className="mr-2 h-4 w-4" />
                                    Billing Portal
                                    <ExternalLink className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                        )}
                        <Button asChild>
                            <Link href="/products">
                                <ShoppingBag className="mr-2 h-4 w-4" />
                                Add Subscription
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Stats Overview */}
                {subscriptions.length > 0 && (
                    <div className="grid gap-4 sm:grid-cols-3">
                        <Card>
                            <CardContent className="flex items-center gap-4 p-4">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <Check className="h-5 w-5" />
                                </div>
                                <div>
                                    <p className="text-2xl font-bold">{activeSubscriptions.length}</p>
                                    <p className="text-sm text-muted-foreground">Active Subscriptions</p>
                                </div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent className="flex items-center gap-4 p-4">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                    <XCircle className="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div>
                                    <p className="text-2xl font-bold">{inactiveSubscriptions.length}</p>
                                    <p className="text-sm text-muted-foreground">Inactive Subscriptions</p>
                                </div>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent className="flex items-center gap-4 p-4">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                    <Calendar className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <p className="text-2xl font-bold">
                                        {
                                            activeSubscriptions.filter((s) => {
                                                const days = getDaysUntilRenewal(s.current_period_end ?? null);
                                                return days !== null && days <= 7 && days >= 0;
                                            }).length
                                        }
                                    </p>
                                    <p className="text-sm text-muted-foreground">Renewing This Week</p>
                                </div>
                            </CardContent>
                        </Card>
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
                                    <EmptyTitle>No subscriptions yet</EmptyTitle>
                                    <EmptyDescription>
                                        You don't have any active subscriptions. Browse our products and choose a plan that fits your needs.
                                    </EmptyDescription>
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
                        </CardContent>
                    </Card>
                )}

                {/* Active Subscriptions */}
                {activeSubscriptions.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold">
                            <Check className="h-5 w-5 text-green-600" />
                            Active Subscriptions
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
                            Inactive Subscriptions
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
                            <h3 className="font-medium text-blue-900 dark:text-blue-100">Billing Information</h3>
                            <p className="text-sm text-blue-800 dark:text-blue-200">
                                Your billing is managed securely through Stripe. Use the Billing Portal to update your payment method, view invoices,
                                or download receipts.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Cancel Subscription Dialog */}
            <AlertDialog open={cancelDialogOpen} onOpenChange={setCancelDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Cancel Subscription?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to cancel your subscription to <strong>{selectedSubscription?.product_name}</strong>? You'll
                            continue to have access until the end of your current billing period.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel disabled={processing}>Keep Subscription</AlertDialogCancel>
                        <AlertDialogAction
                            onClick={handleCancelSubscription}
                            disabled={processing}
                            className="text-destructive-foreground bg-destructive hover:bg-destructive/90"
                        >
                            {processing ? 'Cancelling...' : 'Yes, Cancel'}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DashboardLayout>
    );
}

SubscriptionsIndex.layout = null;
