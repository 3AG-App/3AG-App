import { Head, Link, router } from '@inertiajs/react';
import { AlertTriangle, ArrowRight, Check, Copy, Eye, EyeOff, Globe, Key, MoreHorizontal, Search, ShoppingBag, Trash2, XCircle } from 'lucide-react';
import { useMemo, useState } from 'react';
import { toast } from 'sonner';

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
import { Input } from '@/components/ui/input';
import { Progress } from '@/components/ui/progress';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useTranslations } from '@/hooks/use-translations';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { License } from '@/types';

type TranslateFn = (key: string, fallback?: string, params?: Record<string, string | number>) => string;

interface LicensesIndexProps {
    licenses: License[];
}

function getStatusBadgeVariant(status: string) {
    switch (status) {
        case 'active':
            return 'default' as const;
        case 'suspended':
            return 'secondary' as const;
        case 'expired':
            return 'outline' as const;
        case 'cancelled':
            return 'destructive' as const;
        default:
            return 'outline' as const;
    }
}

function getStatusIcon(status: string) {
    switch (status) {
        case 'active':
            return <Check className="h-3 w-3" />;
        case 'suspended':
        case 'expired':
            return <AlertTriangle className="h-3 w-3" />;
        case 'cancelled':
            return <XCircle className="h-3 w-3" />;
        default:
            return null;
    }
}

function formatDate(dateString: string | null, locale: string, t: TranslateFn): string {
    if (!dateString) return t('common.never', 'Never');
    return new Date(dateString).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function getDaysUntilExpiry(dateString: string | null): number | null {
    if (!dateString) return null;
    const date = new Date(dateString);
    const now = new Date();
    return Math.ceil((date.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
}

function LicenseKeyDisplay({ licenseKey }: { licenseKey: string }) {
    const { t } = useTranslations();
    const [isVisible, setIsVisible] = useState(false);
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        await navigator.clipboard.writeText(licenseKey);
        setCopied(true);
        toast.success(t('dashboard.licenses.copiedToast', 'License key copied to clipboard'));
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div className="flex items-center gap-1">
            <code className="flex-1 truncate rounded bg-muted px-2 py-1 font-mono text-xs">
                {isVisible ? licenseKey : `${licenseKey.slice(0, 8)}${'•'.repeat(16)}${licenseKey.slice(-4)}`}
            </code>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="ghost" size="icon" className="h-7 w-7 shrink-0" onClick={() => setIsVisible(!isVisible)}>
                        {isVisible ? <EyeOff className="h-3.5 w-3.5" /> : <Eye className="h-3.5 w-3.5" />}
                    </Button>
                </TooltipTrigger>
                <TooltipContent>
                    {isVisible
                        ? t('dashboard.licenses.hideLicenseKey', 'Hide license key')
                        : t('dashboard.licenses.showLicenseKey', 'Show license key')}
                </TooltipContent>
            </Tooltip>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="ghost" size="icon" className="h-7 w-7 shrink-0" onClick={handleCopy}>
                        {copied ? <Check className="h-3.5 w-3.5 text-green-500" /> : <Copy className="h-3.5 w-3.5" />}
                    </Button>
                </TooltipTrigger>
                <TooltipContent>{copied ? t('common.copied', 'Copied!') : t('dashboard.licenses.copyLicenseKey', 'Copy license key')}</TooltipContent>
            </Tooltip>
        </div>
    );
}

function LicenseCard({ license, onDeactivateAll }: { license: License; onDeactivateAll: () => void }) {
    const { t, locale } = useTranslations();
    const activationsUsed = license.active_activations_count;
    const activationsLimit = license.domain_limit;
    const activationsPercentage = activationsLimit ? (activationsUsed / activationsLimit) * 100 : 0;
    const isNearLimit = activationsLimit !== null && activationsPercentage >= 80;
    const daysUntilExpiry = getDaysUntilExpiry(license.expires_at);
    const isExpiringSoon = daysUntilExpiry !== null && daysUntilExpiry <= 14 && daysUntilExpiry > 0;
    const isExpired = daysUntilExpiry !== null && daysUntilExpiry <= 0;

    return (
        <Card className={`transition-all hover:shadow-md ${license.status !== 'active' ? 'opacity-75' : ''}`}>
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between gap-4">
                    <div className="flex items-start gap-3">
                        <div
                            className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${
                                license.status === 'active'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-muted text-muted-foreground'
                            }`}
                        >
                            <Key className="h-5 w-5" />
                        </div>
                        <div className="min-w-0">
                            <CardTitle className="flex items-center gap-2 text-lg">{license.product.name}</CardTitle>
                            <CardDescription>{license.package.name}</CardDescription>
                        </div>
                    </div>
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" className="shrink-0">
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem asChild>
                                <Link href={`/dashboard/licenses/${license.id}`}>
                                    <Globe className="mr-2 h-4 w-4" />
                                    View Details
                                </Link>
                            </DropdownMenuItem>
                            {license.active_activations_count > 0 && (
                                <>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem className="text-destructive" onClick={onDeactivateAll}>
                                        <Trash2 className="mr-2 h-4 w-4" />
                                        {t('dashboard.licenses.deactivateAllDomains', 'Deactivate All Domains')}
                                    </DropdownMenuItem>
                                </>
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>
            <CardContent className="space-y-4">
                {/* Status and Expiry Badges */}
                <div className="flex flex-wrap items-center gap-2">
                    <Badge variant={getStatusBadgeVariant(license.status)} className="gap-1 capitalize">
                        {getStatusIcon(license.status)}
                        {license.status_label}
                    </Badge>
                    {isExpiringSoon && (
                        <Badge variant="outline" className="gap-1 border-amber-500 text-amber-600">
                            <AlertTriangle className="h-3 w-3" />
                            {t('dashboard.licenses.expiresInDays', 'Expires in {count} days', { count: daysUntilExpiry })}
                        </Badge>
                    )}
                    {isExpired && (
                        <Badge variant="destructive" className="gap-1">
                            <XCircle className="h-3 w-3" />
                            {t('common.expired', 'Expired')}
                        </Badge>
                    )}
                </div>

                {/* License Key */}
                <div>
                    <label className="mb-1.5 block text-xs font-medium text-muted-foreground">
                        {t('dashboard.licenses.licenseKey', 'License Key')}
                    </label>
                    <LicenseKeyDisplay licenseKey={license.license_key} />
                </div>

                {/* Domain Usage */}
                <div>
                    <div className="mb-1.5 flex items-center justify-between text-sm">
                        <span className="text-muted-foreground">{t('dashboard.licenses.domainActivations', 'Domain Activations')}</span>
                        <span className={`font-medium ${isNearLimit ? 'text-amber-600' : ''}`}>
                            {activationsUsed}
                            {activationsLimit !== null ? ` / ${activationsLimit}` : ''}
                            {activationsLimit === null && <span className="ml-1 text-muted-foreground">({t('common.unlimited', 'Unlimited')})</span>}
                        </span>
                    </div>
                    {activationsLimit !== null && (
                        <Tooltip>
                            <TooltipTrigger asChild>
                                <div>
                                    <Progress value={activationsPercentage} className={`h-1.5 ${isNearLimit ? '[&>div]:bg-amber-500' : ''}`} />
                                </div>
                            </TooltipTrigger>
                            <TooltipContent>
                                {t('dashboard.licenses.activationsRemaining', '{count} activations remaining', {
                                    count: activationsLimit - activationsUsed,
                                })}
                            </TooltipContent>
                        </Tooltip>
                    )}
                </div>

                {/* Dates */}
                <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p className="text-muted-foreground">{t('common.created', 'Created')}</p>
                        <p className="font-medium">{formatDate(license.created_at, locale, t)}</p>
                    </div>
                    <div>
                        <p className="text-muted-foreground">{t('common.expires', 'Expires')}</p>
                        <p className={`font-medium ${isExpiringSoon ? 'text-amber-600' : ''} ${isExpired ? 'text-destructive' : ''}`}>
                            {formatDate(license.expires_at, locale, t)}
                        </p>
                    </div>
                </div>
            </CardContent>
            <CardFooter className="border-t bg-muted/30 px-4 py-3">
                <Button asChild variant="ghost" size="sm" className="ml-auto text-xs">
                    <Link href={`/dashboard/licenses/${license.id}`}>
                        {t('dashboard.licenses.manageActivations', 'Manage Activations')}
                        <ArrowRight className="ml-1.5 h-3.5 w-3.5" />
                    </Link>
                </Button>
            </CardFooter>
        </Card>
    );
}

export default function LicensesIndex({ licenses }: LicensesIndexProps) {
    const { t } = useTranslations();
    const [deactivateDialogOpen, setDeactivateDialogOpen] = useState(false);
    const [selectedLicense, setSelectedLicense] = useState<License | null>(null);
    const [processing, setProcessing] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');

    const filteredLicenses = useMemo(() => {
        if (!searchQuery.trim()) return licenses;
        const query = searchQuery.toLowerCase();
        return licenses.filter(
            (license) =>
                license.product.name.toLowerCase().includes(query) ||
                license.package.name.toLowerCase().includes(query) ||
                license.license_key.toLowerCase().includes(query),
        );
    }, [licenses, searchQuery]);

    const activeLicenses = filteredLicenses.filter((l) => l.status === 'active');
    const inactiveLicenses = filteredLicenses.filter((l) => l.status !== 'active');
    const totalActivations = licenses.reduce((sum, l) => sum + l.active_activations_count, 0);
    const expiringSoon = licenses.filter((l) => {
        const days = getDaysUntilExpiry(l.expires_at);
        return days !== null && days <= 14 && days > 0;
    }).length;

    const handleDeactivateAll = () => {
        if (!selectedLicense) return;

        setProcessing(true);
        router.post(
            `/dashboard/licenses/${selectedLicense.id}/deactivate-all`,
            {},
            {
                onFinish: () => {
                    setProcessing(false);
                    setDeactivateDialogOpen(false);
                    setSelectedLicense(null);
                },
            },
        );
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: t('dashboard.nav.licenses', 'Licenses') }]}>
            <Head title={t('dashboard.nav.licenses', 'Licenses')} />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">{t('dashboard.licenses.title', 'Licenses')}</h1>
                        <p className="text-muted-foreground">
                            {t('dashboard.licenses.subtitle', 'View and manage your software licenses and domain activations.')}
                        </p>
                    </div>
                    <Button asChild>
                        <Link href="/products">
                            <ShoppingBag className="mr-2 h-4 w-4" />
                            {t('dashboard.licenses.getMore', 'Get More Licenses')}
                        </Link>
                    </Button>
                </div>

                {/* Stats Overview */}
                {licenses.length > 0 && (
                    <div className="grid gap-3 sm:grid-cols-4">
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <Key className="h-4 w-4" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{activeLicenses.length}</p>
                                <p className="text-xs text-muted-foreground">{t('common.active', 'Active')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10">
                                <Globe className="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{totalActivations}</p>
                                <p className="text-xs text-muted-foreground">{t('dashboard.licenses.activations', 'Activations')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                <AlertTriangle className="h-4 w-4" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{expiringSoon}</p>
                                <p className="text-xs text-muted-foreground">{t('dashboard.licenses.expiring', 'Expiring')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-muted">
                                <XCircle className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{inactiveLicenses.length}</p>
                                <p className="text-xs text-muted-foreground">{t('common.inactive', 'Inactive')}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Search */}
                {licenses.length > 0 && (
                    <div className="relative max-w-md">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            type="search"
                            placeholder={t('dashboard.licenses.searchPlaceholder', 'Search licenses by product, package, or key...')}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                )}

                {/* Empty State */}
                {licenses.length === 0 && (
                    <Card>
                        <CardContent className="py-12">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Key className="h-6 w-6" />
                                    </EmptyMedia>
                                    <EmptyTitle>{t('dashboard.licenses.emptyTitle', 'No licenses yet')}</EmptyTitle>
                                    <EmptyDescription>
                                        {t(
                                            'dashboard.licenses.emptyDescription',
                                            "You don't have any licenses. Subscribe to a product to receive your license keys.",
                                        )}
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button asChild>
                                        <Link href="/products">
                                            <ShoppingBag className="mr-2 h-4 w-4" />
                                            {t('home.hero.browseProducts', 'Browse Products')}
                                        </Link>
                                    </Button>
                                </EmptyContent>
                            </Empty>
                        </CardContent>
                    </Card>
                )}

                {/* No Search Results */}
                {licenses.length > 0 && filteredLicenses.length === 0 && (
                    <Card>
                        <CardContent className="py-12">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Search className="h-6 w-6" />
                                    </EmptyMedia>
                                    <EmptyTitle>{t('dashboard.licenses.noResultsTitle', 'No licenses found')}</EmptyTitle>
                                    <EmptyDescription>
                                        {t(
                                            'dashboard.licenses.noResultsDescription',
                                            'No licenses match your search criteria. Try adjusting your search.',
                                        )}
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Button variant="outline" onClick={() => setSearchQuery('')}>
                                        {t('dashboard.licenses.clearSearch', 'Clear Search')}
                                    </Button>
                                </EmptyContent>
                            </Empty>
                        </CardContent>
                    </Card>
                )}

                {/* Active Licenses */}
                {activeLicenses.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold">
                            <Check className="h-5 w-5 text-green-600" />
                            {t('dashboard.licenses.activeSection', 'Active Licenses')}
                            <Badge variant="secondary">{activeLicenses.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2">
                            {activeLicenses.map((license) => (
                                <LicenseCard
                                    key={license.id}
                                    license={license}
                                    onDeactivateAll={() => {
                                        setSelectedLicense(license);
                                        setDeactivateDialogOpen(true);
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                )}

                {/* Inactive Licenses */}
                {inactiveLicenses.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold text-muted-foreground">
                            <XCircle className="h-5 w-5" />
                            {t('dashboard.licenses.inactiveSection', 'Inactive Licenses')}
                            <Badge variant="outline">{inactiveLicenses.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2">
                            {inactiveLicenses.map((license) => (
                                <LicenseCard
                                    key={license.id}
                                    license={license}
                                    onDeactivateAll={() => {
                                        setSelectedLicense(license);
                                        setDeactivateDialogOpen(true);
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* Deactivate All Dialog */}
            <AlertDialog open={deactivateDialogOpen} onOpenChange={setDeactivateDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>{t('dashboard.licenses.deactivateDialog.title', 'Deactivate All Domains?')}</AlertDialogTitle>
                        <AlertDialogDescription>
                            {t(
                                'dashboard.licenses.deactivateDialog.description',
                                'This will deactivate all {count} active domain activations for this license. The domains will need to be reactivated to use the license again.',
                                { count: selectedLicense?.active_activations_count ?? 0 },
                            )}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel disabled={processing}>{t('common.cancel', 'Cancel')}</AlertDialogCancel>
                        <AlertDialogAction
                            onClick={handleDeactivateAll}
                            disabled={processing}
                            className="text-destructive-foreground bg-destructive hover:bg-destructive/90"
                        >
                            {processing
                                ? t('dashboard.licenses.deactivateDialog.deactivating', 'Deactivating...')
                                : t('dashboard.licenses.deactivateDialog.confirm', 'Deactivate All')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DashboardLayout>
    );
}

LicensesIndex.layout = null;
