import { Head, Link } from '@inertiajs/react';

import { show } from '@/actions/App/Http/Controllers/ProductController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { CustomPagination } from '@/components/ui/custom-pagination';
import { useTranslations } from '@/hooks/use-translations';
import type { PaginatedData, Product } from '@/types';

interface Props {
    products: PaginatedData<Product>;
}

const typeColors: Record<Product['type'], 'default' | 'secondary' | 'outline'> = {
    plugin: 'default',
    theme: 'secondary',
    source_code: 'outline',
};

export default function ProductsIndex({ products }: Props) {
    const { t } = useTranslations();

    return (
        <>
            <Head title={t('products.title', 'Products')} />

            <div className="container mx-auto px-4 py-12">
                <div className="mb-8 text-center">
                    <h1 className="mb-4 text-4xl font-bold">{t('products.heading', 'Our Products')}</h1>
                    <p className="mx-auto max-w-2xl text-muted-foreground">
                        {t(
                            'products.subheading',
                            'Explore our collection of premium plugins, themes, and source code packages built for developers.',
                        )}
                    </p>
                </div>

                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {products.data.map((product) => (
                        <Card key={product.id} className="flex flex-col overflow-hidden pt-0">
                            <Link href={show.url({ product: product.slug })} className="block">
                                <div className="relative aspect-video w-full bg-muted">
                                    <img
                                        src={product.banner_url}
                                        alt={product.name}
                                        loading="lazy"
                                        className="absolute inset-0 h-full w-full object-cover"
                                    />
                                </div>
                            </Link>
                            <CardHeader className="flex-1">
                                <Badge variant={typeColors[product.type]} className="mb-2 w-fit">
                                    {product.type_label}
                                </Badge>
                                <CardTitle className="text-xl">
                                    <Link href={show.url({ product: product.slug })} className="transition-colors hover:text-primary">
                                        {product.name}
                                    </Link>
                                </CardTitle>
                                <CardDescription className="line-clamp-3">{product.short_description}</CardDescription>
                            </CardHeader>
                            <CardFooter>
                                <Link href={show.url({ product: product.slug })} className="w-full">
                                    <Button variant="outline" className="w-full">
                                        {t('products.viewDetails', 'View Details')}
                                    </Button>
                                </Link>
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {products.data.length === 0 && (
                    <div className="py-12 text-center">
                        <p className="text-muted-foreground">{t('products.empty', 'No products available at the moment.')}</p>
                    </div>
                )}

                <div className="mt-8">
                    <CustomPagination currentPage={products.current_page} totalPages={products.last_page} />
                </div>
            </div>
        </>
    );
}
