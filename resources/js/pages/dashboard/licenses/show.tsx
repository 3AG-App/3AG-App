import { Head, Link, router } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowLeft,
    Calendar,
    Check,
    Clock,
    Copy,
    Eye,
    EyeOff,
    Globe,
    Key,
    Monitor,
    MoreHorizontal,
    Shield,
    Trash2,
    XCircle,
} from 'lucide-react';
import { useState } from 'react';
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
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Progress } from '@/components/ui/progress';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { LicenseActivation, LicenseWithActivations } from '@/types';

interface LicenseShowProps {
    license: LicenseWithActivations;
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

function formatDate(dateString: string | null): string {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatShortDate(dateString: string | null): string {
    if (!dateString) return 'Never';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
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
    return formatShortDate(dateString);
}

function getDaysUntilExpiry(dateString: string | null): number | null {
    if (!dateString) return null;
    const date = new Date(dateString);
    const now = new Date();
    return Math.ceil((date.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
}

function LicenseKeyDisplay({ licenseKey }: { licenseKey: string }) {
    const [isVisible, setIsVisible] = useState(false);
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        await navigator.clipboard.writeText(licenseKey);
        setCopied(true);
        toast.success('License key copied to clipboard');
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div className="flex items-center gap-2">
            <code className="flex-1 truncate rounded bg-muted px-3 py-2 font-mono text-sm">
                {isVisible ? licenseKey : `${licenseKey.slice(0, 12)}${'•'.repeat(24)}${licenseKey.slice(-4)}`}
            </code>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="outline" size="icon" onClick={() => setIsVisible(!isVisible)}>
                        {isVisible ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                    </Button>
                </TooltipTrigger>
                <TooltipContent>{isVisible ? 'Hide' : 'Show'} license key</TooltipContent>
            </Tooltip>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="outline" size="icon" onClick={handleCopy}>
                        {copied ? <Check className="h-4 w-4 text-green-500" /> : <Copy className="h-4 w-4" />}
                    </Button>
                </TooltipTrigger>
                <TooltipContent>{copied ? 'Copied!' : 'Copy license key'}</TooltipContent>
            </Tooltip>
        </div>
    );
}

function ActivationCard({ activation, onDeactivate }: { activation: LicenseActivation; onDeactivate: () => void }) {
    const isActive = !activation.deactivated_at;

    return (
        <Card className={`transition-all ${!isActive ? 'opacity-60' : 'hover:shadow-md'}`}>
            <CardContent className="p-4">
                <div className="flex items-start justify-between gap-4">
                    <div className="flex items-start gap-3">
                        <div
                            className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${
                                isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-muted text-muted-foreground'
                            }`}
                        >
                            <Globe className="h-5 w-5" />
                        </div>
                        <div className="min-w-0">
                            <p className="truncate font-medium">{activation.domain}</p>
                            <p className="text-sm text-muted-foreground">
                                {activation.ip_address && <span className="font-mono">{activation.ip_address}</span>}
                            </p>
                        </div>
                    </div>
                    {isActive && (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="icon" className="shrink-0">
                                    <MoreHorizontal className="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem className="text-destructive" onClick={onDeactivate}>
                                    <Trash2 className="mr-2 h-4 w-4" />
                                    Deactivate Domain
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    )}
                </div>
                <div className="mt-3 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p className="text-muted-foreground">Activated</p>
                        <p className="font-medium">{formatShortDate(activation.activated_at)}</p>
                    </div>
                    <div>
                        <p className="text-muted-foreground">{isActive ? 'Last Checked' : 'Deactivated'}</p>
                        <p className="font-medium">
                            {isActive ? formatShortDate(activation.last_checked_at) : formatShortDate(activation.deactivated_at)}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}

export default function LicenseShow({ license }: LicenseShowProps) {
    const [deactivateDialogOpen, setDeactivateDialogOpen] = useState(false);
    const [selectedActivation, setSelectedActivation] = useState<LicenseActivation | null>(null);
    const [processing, setProcessing] = useState(false);

    const activeActivations = license.activations.filter((a) => !a.deactivated_at);
    const inactiveActivations = license.activations.filter((a) => a.deactivated_at);

    const activationsUsed = activeActivations.length;
    const activationsLimit = license.domain_limit;
    const activationsPercentage = activationsLimit ? (activationsUsed / activationsLimit) * 100 : 0;
    const isNearLimit = activationsLimit !== null && activationsPercentage >= 80;
    const daysUntilExpiry = getDaysUntilExpiry(license.expires_at);
    const isExpiringSoon = daysUntilExpiry !== null && daysUntilExpiry <= 14 && daysUntilExpiry > 0;
    const isExpired = daysUntilExpiry !== null && daysUntilExpiry <= 0;

    const handleDeactivate = () => {
        if (!selectedActivation) return;

        setProcessing(true);
        router.delete(`/dashboard/licenses/${license.id}/activations/${selectedActivation.id}`, {
            onFinish: () => {
                setProcessing(false);
                setDeactivateDialogOpen(false);
                setSelectedActivation(null);
            },
        });
    };

    return (
        <DashboardLayout breadcrumbs={[{ label: 'Licenses', href: '/dashboard/licenses' }, { label: license.product.name }]}>
            <Head title={`License - ${license.product.name}`} />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="flex items-start gap-4">
                    <Button asChild variant="ghost" size="icon" className="mt-1 shrink-0">
                        <Link href="/dashboard/licenses">
                            <ArrowLeft className="h-4 w-4" />
                        </Link>
                    </Button>
                    <div className="min-w-0 flex-1">
                        <div className="flex flex-wrap items-center gap-2">
                            <h1 className="text-3xl font-bold tracking-tight">{license.product.name}</h1>
                            <Badge variant={getStatusBadgeVariant(license.status)} className="gap-1 capitalize">
                                {getStatusIcon(license.status)}
                                {license.status_label}
                            </Badge>
                            {isExpiringSoon && (
                                <Badge variant="outline" className="gap-1 border-amber-500 text-amber-600">
                                    <AlertTriangle className="h-3 w-3" />
                                    Expires {formatRelativeDate(license.expires_at)}
                                </Badge>
                            )}
                            {isExpired && (
                                <Badge variant="destructive" className="gap-1">
                                    <XCircle className="h-3 w-3" />
                                    Expired
                                </Badge>
                            )}
                        </div>
                        <p className="text-muted-foreground">{license.package.name}</p>
                    </div>
                </div>

                {/* Stats Overview */}
                <div className="grid gap-3 sm:grid-cols-4">
                    <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                            <Globe className="h-4 w-4" />
                        </div>
                        <div>
                            <p className="text-xl font-bold">{activationsUsed}</p>
                            <p className="text-xs text-muted-foreground">Active</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                        <div
                            className={`flex h-9 w-9 items-center justify-center rounded-lg ${isNearLimit ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-primary/10'}`}
                        >
                            <Key className={`h-4 w-4 ${isNearLimit ? '' : 'text-primary'}`} />
                        </div>
                        <div>
                            <p className="text-xl font-bold">{activationsLimit !== null ? `${activationsLimit - activationsUsed}` : '∞'}</p>
                            <p className="text-xs text-muted-foreground">Remaining</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                        <div
                            className={`flex h-9 w-9 items-center justify-center rounded-lg ${
                                isExpiringSoon
                                    ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                    : isExpired
                                      ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                      : 'bg-muted'
                            }`}
                        >
                            <Calendar className={`h-4 w-4 ${!isExpiringSoon && !isExpired ? 'text-muted-foreground' : ''}`} />
                        </div>
                        <div>
                            <p className={`text-xl font-bold ${isExpiringSoon ? 'text-amber-600' : ''} ${isExpired ? 'text-red-600' : ''}`}>
                                {daysUntilExpiry !== null ? (daysUntilExpiry > 0 ? daysUntilExpiry : 0) : '∞'}
                            </p>
                            <p className="text-xs text-muted-foreground">Days Left</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-muted">
                            <Clock className="h-4 w-4 text-muted-foreground" />
                        </div>
                        <div>
                            <p className="text-sm font-medium">{formatShortDate(license.last_validated_at)}</p>
                            <p className="text-xs text-muted-foreground">Last Validated</p>
                        </div>
                    </div>
                </div>

                {/* License Key Card */}
                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Shield className="h-5 w-5" />
                            License Key
                        </CardTitle>
                        <CardDescription>Use this key to activate your software on allowed domains</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <LicenseKeyDisplay licenseKey={license.license_key} />

                        {/* Domain Usage Progress */}
                        {activationsLimit !== null && (
                            <div className="space-y-2">
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-muted-foreground">Domain Usage</span>
                                    <span className={`font-medium ${isNearLimit ? 'text-amber-600' : ''}`}>
                                        {activationsUsed} / {activationsLimit} domains used
                                    </span>
                                </div>
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <div>
                                            <Progress value={activationsPercentage} className={`h-2 ${isNearLimit ? '[&>div]:bg-amber-500' : ''}`} />
                                        </div>
                                    </TooltipTrigger>
                                    <TooltipContent>{activationsLimit - activationsUsed} activation slots remaining</TooltipContent>
                                </Tooltip>
                            </div>
                        )}
                    </CardContent>
                    <CardFooter className="border-t bg-muted/30 px-6 py-3">
                        <div className="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-muted-foreground">
                            <span>
                                Created: <span className="font-medium text-foreground">{formatShortDate(license.created_at)}</span>
                            </span>
                            <span>
                                Expires:{' '}
                                <span
                                    className={`font-medium ${isExpiringSoon ? 'text-amber-600' : ''} ${isExpired ? 'text-red-600' : 'text-foreground'}`}
                                >
                                    {formatShortDate(license.expires_at)}
                                </span>
                            </span>
                        </div>
                    </CardFooter>
                </Card>

                {/* Active Activations */}
                <div className="space-y-4">
                    <h2 className="flex items-center gap-2 text-lg font-semibold">
                        <Globe className="h-5 w-5 text-green-600" />
                        Active Domains
                        <Badge variant="secondary">{activeActivations.length}</Badge>
                    </h2>

                    {activeActivations.length === 0 ? (
                        <Card>
                            <CardContent className="py-12">
                                <Empty>
                                    <EmptyHeader>
                                        <EmptyMedia variant="icon">
                                            <Globe className="h-6 w-6" />
                                        </EmptyMedia>
                                        <EmptyTitle>No active domains</EmptyTitle>
                                        <EmptyDescription>
                                            This license hasn't been activated on any domain yet. Use the license key to activate on your website.
                                        </EmptyDescription>
                                    </EmptyHeader>
                                </Empty>
                            </CardContent>
                        </Card>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2">
                            {activeActivations.map((activation) => (
                                <ActivationCard
                                    key={activation.id}
                                    activation={activation}
                                    onDeactivate={() => {
                                        setSelectedActivation(activation);
                                        setDeactivateDialogOpen(true);
                                    }}
                                />
                            ))}
                        </div>
                    )}
                </div>

                {/* Inactive Activations */}
                {inactiveActivations.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold text-muted-foreground">
                            <Monitor className="h-5 w-5" />
                            Deactivated Domains
                            <Badge variant="outline">{inactiveActivations.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2">
                            {inactiveActivations.map((activation) => (
                                <ActivationCard key={activation.id} activation={activation} onDeactivate={() => {}} />
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* Deactivate Dialog */}
            <AlertDialog open={deactivateDialogOpen} onOpenChange={setDeactivateDialogOpen}>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Deactivate Domain?</AlertDialogTitle>
                        <AlertDialogDescription>
                            This will deactivate the license on <strong>{selectedActivation?.domain}</strong>. The domain will need to reactivate the
                            license to continue using it.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel disabled={processing}>Cancel</AlertDialogCancel>
                        <AlertDialogAction
                            onClick={handleDeactivate}
                            disabled={processing}
                            className="text-destructive-foreground bg-destructive hover:bg-destructive/90"
                        >
                            {processing ? 'Deactivating...' : 'Deactivate'}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </DashboardLayout>
    );
}

LicenseShow.layout = null;
