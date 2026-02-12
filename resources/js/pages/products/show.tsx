import { Head, Link, router } from '@inertiajs/react';
import { ArrowRightIcon, CheckIcon, CreditCardIcon } from 'lucide-react';
import { useCallback, useRef, useState } from 'react';
import Lightbox from 'yet-another-react-lightbox';
import Counter from 'yet-another-react-lightbox/plugins/counter';
import 'yet-another-react-lightbox/plugins/counter.css';
import Zoom from 'yet-another-react-lightbox/plugins/zoom';
import 'yet-another-react-lightbox/styles.css';

import { index as productsIndex, subscribe, swap } from '@/actions/App/Http/Controllers/ProductController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { useTranslations } from '@/hooks/use-translations';
import { cn } from '@/lib/utils';
import type { CurrentSubscription, Package, ProductDetail, Screenshot } from '@/types';

interface Props {
    product: ProductDetail;
    currentSubscription: CurrentSubscription | null;
}

function formatPrice(price: string): string {
    return new Intl.NumberFormat('de-CH', {
        style: 'currency',
        currency: 'CHF',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(parseFloat(price));
}

function SubscriptionNotice({ currentSubscription }: { currentSubscription: CurrentSubscription }) {
    const { t } = useTranslations();

    const isWarning = currentSubscription.requires_payment;

    return (
        <div
            className={cn(
                'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm',
                isWarning
                    ? 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200'
                    : 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200',
            )}
        >
            <CreditCardIcon className="h-3.5 w-3.5" />
            <span>
                {isWarning
                    ? t('productShow.subscription.paymentRequired', 'Payment required')
                    : t('productShow.subscription.subscribed', 'Subscribed')}
                : <strong>{currentSubscription.package_name}</strong> (
                {currentSubscription.is_yearly ? t('productShow.yearly', 'Yearly') : t('productShow.monthly', 'Monthly')})
            </span>
            {currentSubscription.on_grace_period && (
                <span className="text-xs opacity-75">- {t('productShow.subscription.cancelsAtPeriodEnd', 'Cancels at period end')}</span>
            )}
        </div>
    );
}

function PricingCard({
    pkg,
    isYearly,
    isPopular,
    currentSubscription,
}: {
    pkg: Package;
    isYearly: boolean;
    isPopular: boolean;
    currentSubscription: CurrentSubscription | null;
}) {
    const { t } = useTranslations();

    const price = isYearly ? pkg.yearly_price : pkg.monthly_price;
    const period = isYearly ? '/yr' : '/mo';

    const isCurrentPlan = currentSubscription?.package_id === pkg.id;
    const isCurrentBillingInterval = currentSubscription?.is_yearly === isYearly;
    const isExactCurrentPlan = isCurrentPlan && isCurrentBillingInterval;
    const hasSubscription = currentSubscription !== null;

    const handleClick = () => {
        if (isExactCurrentPlan) return;

        const data = { billing_interval: isYearly ? 'yearly' : 'monthly' };

        if (hasSubscription) {
            router.post(swap.url({ package: pkg.id }), data);
        } else {
            router.post(subscribe.url({ package: pkg.id }), data);
        }
    };

    const getButtonText = () => {
        if (isExactCurrentPlan) return t('productShow.pricing.currentPlan', 'Current Plan');
        if (hasSubscription) {
            if (isCurrentPlan) {
                return isYearly
                    ? t('productShow.pricing.switchToYearly', 'Switch to Yearly')
                    : t('productShow.pricing.switchToMonthly', 'Switch to Monthly');
            }
            return t('productShow.pricing.switchPlan', 'Switch Plan');
        }
        return t('nav.getStarted', 'Get Started');
    };

    return (
        <Card
            className={cn(
                'relative flex flex-col transition-shadow',
                isPopular && !isCurrentPlan && 'scale-[1.02] border-primary shadow-lg',
                isCurrentPlan && 'ring-2 ring-primary',
            )}
        >
            {isCurrentPlan && (
                <div className="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-green-600 px-3 py-1 text-xs font-medium whitespace-nowrap text-white">
                    {t('productShow.pricing.yourPlan', 'Your Plan')}
                </div>
            )}
            {!isCurrentPlan && isPopular && (
                <div className="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-primary px-3 py-1 text-xs font-medium whitespace-nowrap text-primary-foreground">
                    {t('productShow.pricing.mostPopular', 'Most Popular')}
                </div>
            )}
            <CardHeader className="text-center">
                <CardTitle className="text-xl">{pkg.name}</CardTitle>
                <CardDescription>{pkg.description}</CardDescription>
                <div className="mt-4">
                    <span className="text-4xl font-bold tracking-tight">{formatPrice(price)}</span>
                    <span className="text-muted-foreground">{period}</span>
                </div>
                {!hasSubscription && (
                    <p className="mt-2 text-sm font-medium text-primary">{t('productShow.pricing.trialOffer', '10-day free trial')}</p>
                )}
                {isYearly && (
                    <p className="text-sm font-medium text-primary">
                        {t('productShow.pricing.savePrefix', 'Save')}{' '}
                        {formatPrice(String(parseFloat(pkg.monthly_price) * 12 - parseFloat(pkg.yearly_price)))}{' '}
                        {t('productShow.pricing.savePerYearSuffix', '/ year')}
                    </p>
                )}
            </CardHeader>
            <CardContent className="flex-1">
                <ul className="space-y-3">
                    {pkg.features.map((feature, index) => (
                        <li key={index} className="flex items-start gap-2">
                            <CheckIcon className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                            <span className="text-sm">{feature}</span>
                        </li>
                    ))}
                    <li className="flex items-start gap-2">
                        <CheckIcon className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                        <span className="text-sm">
                            {pkg.domain_limit ? `${pkg.domain_limit} site license${pkg.domain_limit > 1 ? 's' : ''}` : 'Unlimited sites'}
                        </span>
                    </li>
                </ul>
            </CardContent>
            <CardFooter>
                <Button
                    onClick={handleClick}
                    className={cn('w-full', isPopular && !isExactCurrentPlan && 'shadow-sm')}
                    size="lg"
                    variant={isExactCurrentPlan ? 'secondary' : isPopular ? 'default' : 'outline'}
                    disabled={isExactCurrentPlan}
                >
                    {getButtonText()}
                </Button>
            </CardFooter>
        </Card>
    );
}

function ScreenshotGrid({ screenshots, onImageClick }: { screenshots: Screenshot[]; onImageClick: (index: number) => void }) {
    if (screenshots.length === 0) return null;

    return (
        <div className="grid grid-cols-2 gap-2 sm:grid-cols-3">
            {screenshots.map((screenshot, index) => (
                <button
                    key={screenshot.id}
                    type="button"
                    onClick={() => onImageClick(index)}
                    className={cn(
                        'group relative overflow-hidden rounded-lg bg-muted',
                        'focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none',
                    )}
                >
                    <div className="relative aspect-video w-full">
                        <img
                            src={screenshot.thumbnail}
                            alt={screenshot.alt}
                            loading="lazy"
                            className="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                        />
                    </div>
                    <div className="pointer-events-none absolute inset-0 transition-colors duration-300 group-hover:bg-black/5 dark:group-hover:bg-white/5" />
                </button>
            ))}
        </div>
    );
}

export default function ProductShow({ product, currentSubscription }: Props) {
    const { t } = useTranslations();

    const [isYearly, setIsYearly] = useState(currentSubscription?.is_yearly ?? false);
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(0);
    const pricingRef = useRef<HTMLDivElement>(null);

    const packages = product.packages ?? [];
    const screenshots = product.screenshots ?? [];
    const heroImage = product.banner.optimized;
    const heroImageFull = product.banner.original;
    const remainingScreenshots = screenshots;

    const maxYearlySavings = packages.reduce((max, pkg) => {
        const savings = parseFloat(pkg.monthly_price) * 12 - parseFloat(pkg.yearly_price);
        return savings > max ? savings : max;
    }, 0);

    const minMonthlyPrice = packages.reduce((min, pkg) => {
        const price = parseFloat(pkg.monthly_price);
        return price < min ? price : min;
    }, Number.POSITIVE_INFINITY);

    const scrollToPricing = () => {
        pricingRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const openLightbox = useCallback((index: number) => {
        setLightboxIndex(index);
        setLightboxOpen(true);
    }, []);

    const slides = [
        { src: product.banner.original, alt: product.name, width: 1920, height: 1080 },
        ...screenshots.map((s) => ({
            src: s.original,
            alt: s.alt,
            width: 1920,
            height: 1080,
        })),
    ];

    const screenshotLightboxOffset = 1;

    return (
        <>
            <Head title={product.name} />

            {/* ── Hero ── */}
            <section className="border-b bg-gradient-to-b from-muted/40 to-background">
                <div className="container mx-auto px-4 py-6">
                    <nav className="mb-8 text-sm text-muted-foreground">
                        <Link href={productsIndex.url()} className="hover:text-foreground">
                            {t('nav.products', 'Products')}
                        </Link>
                        <span className="mx-2">/</span>
                        <span className="text-foreground">{product.name}</span>
                    </nav>

                    <div className={cn('grid items-center gap-10', heroImage ? 'lg:grid-cols-2' : 'mx-auto max-w-2xl text-center')}>
                        <div className={cn('flex flex-col gap-6', !heroImage && 'items-center')}>
                            <Badge variant="secondary">{product.type_label}</Badge>

                            <h1 className="text-4xl font-bold tracking-tight lg:text-5xl">{product.name}</h1>
                            {product.short_description && <p className="max-w-lg text-lg text-muted-foreground">{product.short_description}</p>}

                            {currentSubscription && <SubscriptionNotice currentSubscription={currentSubscription} />}

                            {packages.length > 0 && (
                                <div className="flex items-center gap-3">
                                    <Button size="lg" onClick={scrollToPricing}>
                                        {t('productShow.viewPricing', 'View Pricing')}
                                        <ArrowRightIcon className="ml-2 h-4 w-4" />
                                    </Button>
                                    {Number.isFinite(minMonthlyPrice) && (
                                        <span className="text-sm text-muted-foreground">
                                            {t('productShow.startingFrom', 'Starting from')}{' '}
                                            <span className="font-medium text-primary">{formatPrice(String(minMonthlyPrice))}/mo</span>
                                            <span className="mx-2">•</span>
                                            <span className="font-medium text-primary">
                                                {t('productShow.pricing.trialOffer', '10-day free trial')}
                                            </span>
                                        </span>
                                    )}
                                </div>
                            )}
                        </div>

                        {heroImage && (
                            <button
                                type="button"
                                onClick={() => {
                                    if (heroImageFull) {
                                        setLightboxIndex(0);
                                        setLightboxOpen(true);
                                    }
                                }}
                                className="group relative overflow-hidden rounded-xl shadow-lg ring-1 ring-border focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none"
                            >
                                <div className="relative aspect-[16/10] w-full bg-muted">
                                    <img
                                        src={heroImage}
                                        alt={product.name}
                                        className="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.01]"
                                    />
                                </div>
                                <div className="pointer-events-none absolute inset-0 transition-colors duration-300 group-hover:bg-black/5 dark:group-hover:bg-white/5" />
                            </button>
                        )}
                    </div>
                </div>
            </section>

            {product.long_description && (
                <section className="border-b bg-background py-12">
                    <div className="container mx-auto px-4">
                        <div className="mx-auto max-w-3xl">
                            <div
                                className="prose max-w-none prose-neutral dark:prose-invert"
                                dangerouslySetInnerHTML={{ __html: product.long_description }}
                            />
                        </div>
                    </div>
                </section>
            )}

            {/* ── Pricing ── */}
            {packages.length > 0 && (
                <section ref={pricingRef} className="scroll-mt-8 py-16 lg:py-20">
                    <div className="container mx-auto px-4">
                        <div className="mb-10 text-center">
                            <h2 className="mb-2 text-3xl font-bold tracking-tight">{t('productShow.choosePlan', 'Choose your plan')}</h2>
                            <p className="text-muted-foreground">
                                {t('productShow.choosePlanSubheading', 'Pick the plan that fits your needs. Upgrade or downgrade anytime.')}
                            </p>
                        </div>

                        <div className="mb-10 flex items-center justify-center gap-4">
                            <span className={cn('text-sm transition-colors', !isYearly ? 'font-medium text-foreground' : 'text-muted-foreground')}>
                                {t('productShow.monthly', 'Monthly')}
                            </span>
                            <Switch checked={isYearly} onCheckedChange={setIsYearly} />
                            <span className={cn('text-sm transition-colors', isYearly ? 'font-medium text-foreground' : 'text-muted-foreground')}>
                                {t('productShow.yearly', 'Yearly')}
                                {maxYearlySavings > 0 && (
                                    <Badge variant="secondary" className="ml-2 text-xs font-normal text-primary">
                                        {t('productShow.pricing.saveUpTo', 'Save up to')} {formatPrice(String(maxYearlySavings))}
                                    </Badge>
                                )}
                            </span>
                        </div>

                        <div
                            className={cn(
                                'mx-auto grid gap-6',
                                packages.length === 1 && 'max-w-md',
                                packages.length === 2 && 'max-w-3xl md:grid-cols-2',
                                packages.length >= 3 && 'max-w-5xl md:grid-cols-3',
                            )}
                        >
                            {packages.map((pkg, index) => (
                                <PricingCard
                                    key={pkg.id}
                                    pkg={pkg}
                                    isYearly={isYearly}
                                    isPopular={packages.length >= 3 ? index === 1 : index === packages.length - 1}
                                    currentSubscription={currentSubscription}
                                />
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {packages.length === 0 && (
                <section className="py-16">
                    <div className="container mx-auto px-4 text-center">
                        <p className="text-muted-foreground">No pricing packages available at the moment.</p>
                    </div>
                </section>
            )}

            {/* ── More screenshots ── */}
            {remainingScreenshots.length > 0 && (
                <section className="border-t bg-muted/30 py-16">
                    <div className="container mx-auto px-4">
                        <h2 className="mb-8 text-center text-2xl font-bold tracking-tight">{t('productShow.screenshots', 'Screenshots')}</h2>
                        <div className="mx-auto max-w-4xl">
                            <ScreenshotGrid screenshots={remainingScreenshots} onImageClick={(i) => openLightbox(i + screenshotLightboxOffset)} />
                        </div>
                    </div>
                </section>
            )}

            {/* ── Footer nav ── */}
            <div className="container mx-auto px-4 py-8">
                <Link href={productsIndex.url()} className="text-sm text-muted-foreground transition-colors hover:text-foreground">
                    ← {t('productShow.backToAllProducts', 'Back to all products')}
                </Link>
            </div>

            {/* ── Lightbox ── */}
            {slides.length > 0 && (
                <Lightbox
                    open={lightboxOpen}
                    close={() => setLightboxOpen(false)}
                    index={lightboxIndex}
                    slides={slides}
                    plugins={[Zoom, Counter]}
                    animation={{ fade: 200, swipe: 300 }}
                    carousel={{ finite: slides.length <= 5 }}
                    zoom={{ maxZoomPixelRatio: 3, scrollToZoom: true }}
                    controller={{ closeOnBackdropClick: true }}
                    styles={{ container: { backgroundColor: 'rgba(0, 0, 0, 0.92)' } }}
                    render={{
                        buttonPrev: slides.length <= 1 ? () => null : undefined,
                        buttonNext: slides.length <= 1 ? () => null : undefined,
                    }}
                />
            )}
        </>
    );
}
