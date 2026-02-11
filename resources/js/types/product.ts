export interface Package {
    id: number;
    name: string;
    slug: string;
    description: string;
    domain_limit: number | null;
    monthly_price: string;
    yearly_price: string;
    features: string[];
}

export interface Screenshot {
    id: number;
    original: string;
    thumbnail: string;
    alt: string;
}

export interface Banner {
    original: string;
    optimized: string;
}

export interface Product {
    id: number;
    name: string;
    slug: string;
    description: string;
    type: 'plugin' | 'theme' | 'source_code';
    type_label: string;
    banner_url: string;
}

export interface ProductDetail extends Product {
    packages: Package[];
    screenshots: Screenshot[];
    banner: Banner;
}

export interface CurrentSubscription {
    id: number;
    package_id: number | null;
    package_slug: string;
    package_name: string;
    stripe_price: string;
    is_yearly: boolean;
    ends_at: string | null;
    on_grace_period: boolean;
    requires_payment: boolean;
}
