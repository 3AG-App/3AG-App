import { Head, Link } from '@inertiajs/react';
import { ArrowDownToLine, Calendar, Check, CreditCard, DollarSign, ExternalLink, FileText, Receipt, ShoppingBag, XCircle } from 'lucide-react';

import { index as subscriptionsIndex } from '@/actions/App/Http/Controllers/Dashboard/SubscriptionController';
import { index as productsIndex } from '@/actions/App/Http/Controllers/ProductController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { useTranslations } from '@/hooks/use-translations';
import DashboardLayout from '@/layouts/dashboard-layout';
import type { Invoice } from '@/types';

type TranslateFn = (key: string, fallback?: string, params?: Record<string, string | number>) => string;

interface InvoicesIndexProps {
    invoices: Invoice[];
}

function getStatusBadgeVariant(status: string) {
    switch (status) {
        case 'paid':
            return 'default' as const;
        case 'open':
            return 'secondary' as const;
        case 'draft':
            return 'outline' as const;
        case 'void':
        case 'uncollectible':
            return 'destructive' as const;
        default:
            return 'outline' as const;
    }
}

function getStatusIcon(status: string) {
    switch (status) {
        case 'paid':
            return <Check className="h-3 w-3" />;
        case 'void':
        case 'uncollectible':
            return <XCircle className="h-3 w-3" />;
        default:
            return null;
    }
}

function getStatusLabel(status: string, t: TranslateFn): string {
    return t(`dashboard.invoices.status.${status}`, status.replaceAll('_', ' '));
}

function formatShortDate(dateString: string, locale: string): string {
    return new Date(dateString).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function InvoiceCard({ invoice }: { invoice: Invoice }) {
    const { t, locale } = useTranslations();
    const isPaid = invoice.status === 'paid';
    const isCreditNote = invoice.is_credit_note;

    return (
        <Card className={`transition-all hover:shadow-md ${!isPaid && !isCreditNote ? 'border-amber-200 dark:border-amber-900' : ''}`}>
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between gap-4">
                    <div className="flex items-start gap-3">
                        <div
                            className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${
                                isCreditNote
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : isPaid
                                      ? 'bg-primary/10 text-primary'
                                      : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                            }`}
                        >
                            {isCreditNote ? <CreditCard className="h-5 w-5" /> : <FileText className="h-5 w-5" />}
                        </div>
                        <div className="min-w-0">
                            <CardTitle className="text-base">
                                {isCreditNote ? t('dashboard.invoices.creditNote', 'Credit Note') : t('dashboard.invoices.invoice', 'Invoice')}
                            </CardTitle>
                            <CardDescription className="font-mono text-xs">{invoice.id.substring(0, 24)}...</CardDescription>
                        </div>
                    </div>
                    <Badge variant={getStatusBadgeVariant(invoice.status)} className="gap-1 capitalize">
                        {getStatusIcon(invoice.status)}
                        {getStatusLabel(invoice.status, t)}
                    </Badge>
                </div>
            </CardHeader>
            <CardContent className="space-y-3">
                {/* Amount */}
                <div className="flex items-center justify-between">
                    <span className="text-sm text-muted-foreground">{t('dashboard.invoices.amount', 'Amount')}</span>
                    <span className={`text-lg font-bold ${isCreditNote ? 'text-green-600 dark:text-green-400' : ''}`}>
                        {isCreditNote ? `+${invoice.credit_amount}` : invoice.subtotal}
                    </span>
                </div>

                {/* Payment Details */}
                {!isCreditNote && (
                    <div className="space-y-1.5 text-sm">
                        <div className="flex items-center justify-between">
                            <span className="text-muted-foreground">{t('dashboard.invoices.paid', 'Paid')}</span>
                            <span className="font-medium">{invoice.amount_paid}</span>
                        </div>
                        {invoice.credit_applied && (
                            <div className="flex items-center justify-between">
                                <span className="text-muted-foreground">{t('dashboard.invoices.creditApplied', 'Credit Applied')}</span>
                                <span className="font-medium text-green-600 dark:text-green-400">{invoice.credit_applied}</span>
                            </div>
                        )}
                    </div>
                )}

                {/* Date */}
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <Calendar className="h-3.5 w-3.5" />
                    {formatShortDate(invoice.date, locale)}
                </div>
            </CardContent>
            <CardFooter className="border-t bg-muted/30 px-4 py-3">
                <div className="flex w-full items-center justify-end gap-2">
                    {invoice.hosted_invoice_url && (
                        <>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button asChild variant="ghost" size="sm" className="text-xs">
                                        <a href={invoice.hosted_invoice_url} target="_blank" rel="noopener noreferrer">
                                            <ExternalLink className="mr-1.5 h-3.5 w-3.5" />
                                            {t('common.view', 'View')}
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>{t('dashboard.invoices.viewOnStripe', 'View invoice on Stripe')}</TooltipContent>
                            </Tooltip>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <Button asChild variant="outline" size="sm" className="text-xs">
                                        <a href={`${invoice.hosted_invoice_url}/pdf`} target="_blank" rel="noopener noreferrer">
                                            <ArrowDownToLine className="mr-1.5 h-3.5 w-3.5" />
                                            {t('dashboard.invoices.downloadPdf', 'Download PDF')}
                                        </a>
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>{t('dashboard.invoices.downloadPdfTooltip', 'Download PDF invoice')}</TooltipContent>
                            </Tooltip>
                        </>
                    )}
                </div>
            </CardFooter>
        </Card>
    );
}

export default function InvoicesIndex({ invoices }: InvoicesIndexProps) {
    const { t } = useTranslations();
    const paidInvoices = invoices.filter((i) => i.status === 'paid' && !i.is_credit_note);
    const creditNotes = invoices.filter((i) => i.is_credit_note);
    const pendingInvoices = invoices.filter((i) => i.status !== 'paid' && !i.is_credit_note);

    const totalPaid = paidInvoices.length;
    const totalCredits = creditNotes.length;
    const totalPending = pendingInvoices.length;

    return (
        <DashboardLayout breadcrumbs={[{ label: t('dashboard.nav.invoices', 'Invoices') }]}>
            <Head title={t('dashboard.nav.invoices', 'Invoices')} />

            <div className="space-y-6">
                {/* Page Header */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">{t('dashboard.invoices.title', 'Invoices')}</h1>
                        <p className="text-muted-foreground">
                            {t('dashboard.invoices.subtitle', 'View and download your billing history and invoices.')}
                        </p>
                    </div>
                    <Button asChild variant="outline">
                        <Link href={subscriptionsIndex.url()}>
                            <CreditCard className="mr-2 h-4 w-4" />
                            {t('dashboard.invoices.manageSubscriptions', 'Manage Subscriptions')}
                        </Link>
                    </Button>
                </div>

                {/* Stats Overview */}
                {invoices.length > 0 && (
                    <div className="grid gap-3 sm:grid-cols-3">
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <Check className="h-4 w-4" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{totalPaid}</p>
                                <p className="text-xs text-muted-foreground">{t('dashboard.invoices.paid', 'Paid')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10">
                                <DollarSign className="h-4 w-4 text-primary" />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{totalCredits}</p>
                                <p className="text-xs text-muted-foreground">{t('dashboard.invoices.credits', 'Credits')}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3 rounded-lg border bg-card p-3">
                            <div
                                className={`flex h-9 w-9 items-center justify-center rounded-lg ${totalPending > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-muted'}`}
                            >
                                <Receipt className={`h-4 w-4 ${totalPending === 0 ? 'text-muted-foreground' : ''}`} />
                            </div>
                            <div>
                                <p className="text-xl font-bold">{totalPending}</p>
                                <p className="text-xs text-muted-foreground">{t('dashboard.invoices.pending', 'Pending')}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Empty State */}
                {invoices.length === 0 && (
                    <Card>
                        <CardContent className="py-12">
                            <Empty>
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Receipt className="h-6 w-6" />
                                    </EmptyMedia>
                                    <EmptyTitle>{t('dashboard.invoices.emptyTitle', 'No invoices yet')}</EmptyTitle>
                                    <EmptyDescription>
                                        {t(
                                            'dashboard.invoices.empty',
                                            "You don't have any invoices yet. Once you make a purchase or subscribe to a plan, your invoices will appear here.",
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

                {/* Pending Invoices */}
                {pendingInvoices.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold text-amber-600">
                            <Receipt className="h-5 w-5" />
                            {t('dashboard.invoices.pendingSection', 'Pending Invoices')}
                            <Badge variant="secondary">{pendingInvoices.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {pendingInvoices.map((invoice) => (
                                <InvoiceCard key={invoice.id} invoice={invoice} />
                            ))}
                        </div>
                    </div>
                )}

                {/* Credit Notes */}
                {creditNotes.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold text-green-600">
                            <CreditCard className="h-5 w-5" />
                            {t('dashboard.invoices.creditNotesSection', 'Credit Notes')}
                            <Badge variant="secondary">{creditNotes.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {creditNotes.map((invoice) => (
                                <InvoiceCard key={invoice.id} invoice={invoice} />
                            ))}
                        </div>
                    </div>
                )}

                {/* Paid Invoices */}
                {paidInvoices.length > 0 && (
                    <div className="space-y-4">
                        <h2 className="flex items-center gap-2 text-lg font-semibold">
                            <Check className="h-5 w-5 text-green-600" />
                            {t('dashboard.invoices.paidSection', 'Paid Invoices')}
                            <Badge variant="secondary">{paidInvoices.length}</Badge>
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {paidInvoices.map((invoice) => (
                                <InvoiceCard key={invoice.id} invoice={invoice} />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}

InvoicesIndex.layout = null;
