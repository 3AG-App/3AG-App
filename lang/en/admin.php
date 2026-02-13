<?php

declare(strict_types=1);

return [
    'brand_name' => '3AG Admin',

    'navigation' => [
        'dashboard' => 'Dashboard',
        'shop_management' => 'Shop Management',
        'license_management' => 'License Management',
        'user_management' => 'User Management',
    ],

    'common' => [
        'active' => 'Active',
        'activated' => 'Activated',
        'all' => 'All',
        'copied' => 'Copied!',
        'created' => 'Created',
        'customer' => 'Customer',
        'deactivated' => 'Deactivated',
        'domains' => 'Domains',
        'email' => 'Email',
        'expires' => 'Expires',
        'inactive' => 'Inactive',
        'last_check' => 'Last Check',
        'last_updated' => 'Last Updated',
        'na' => 'N/A',
        'name' => 'Name',
        'never' => 'Never',
        'order' => 'Order',
        'package' => 'Package',
        'product' => 'Product',
        'status' => 'Status',
        'type' => 'Type',
        'unknown' => 'Unknown',
    ],

    'widgets' => [
        'latest_licenses' => [
            'heading' => 'Recent Licenses',
            'description' => 'The 10 most recently created licenses',
            'columns' => [
                'license_key' => 'License Key',
            ],
        ],
        'license_status_overview' => [
            'heading' => 'Licenses by Status',
        ],
        'revenue_chart' => [
            'heading' => 'Growth Over Time',
            'filters' => [
                'last_30_days' => 'Last 30 days',
                'last_6_months' => 'Last 6 months',
                'last_12_months' => 'Last 12 months',
            ],
            'datasets' => [
                'new_users' => 'New Users',
                'new_licenses' => 'New Licenses',
            ],
        ],
        'stats_overview' => [
            'total_users' => 'Total Users',
            'new_this_month' => ':count new this month',
            'active_licenses' => 'Active Licenses',
            'total_licenses' => ':count total licenses',
            'paying_customers' => 'Paying Customers',
            'conversion' => ':percentage% conversion',
            'active_products' => 'Active Products',
            'available_for_purchase' => 'Available for purchase',
        ],
    ],

    'resources' => [
        'users' => [
            'navigation_badge_tooltip' => 'Total registered users',
            'status' => [
                'subscribed' => 'Subscribed',
                'free' => 'Free',
            ],
            'sections' => [
                'account_information' => 'Account Information',
                'subscription_billing' => 'Subscription & Billing',
                'statistics' => 'Statistics',
            ],
            'fields' => [
                'verified' => 'Verified',
                'stripe_customer_id' => 'Stripe Customer ID',
                'payment_method' => 'Payment Method',
                'trial_ends' => 'Trial Ends',
                'total_licenses' => 'Total Licenses',
                'active_licenses' => 'Active Licenses',
                'member_since' => 'Member Since',
            ],
            'placeholders' => [
                'no_stripe_account' => 'No Stripe account',
                'no_payment_method' => 'No payment method',
                'no_trial' => 'No trial',
            ],
            'form' => [
                'fields' => [
                    'email_address' => 'Email Address',
                    'email_verified_at' => 'Email Verified At',
                    'trial_ends_at' => 'Trial Ends At',
                    'last_4_digits' => 'Last 4 Digits',
                ],
                'help' => [
                    'password_edit_blank' => 'Leave blank to keep current password when editing.',
                ],
                'description' => [
                    'stripe_subscription_information' => 'Stripe subscription information',
                ],
            ],
            'table' => [
                'email_address' => 'Email Address',
                'email_copied' => 'Email copied',
                'stripe_customer' => 'Stripe Customer',
                'filters' => [
                    'email_verified' => 'Email Verified',
                    'has_subscription' => 'Has Subscription',
                ],
                'placeholders' => [
                    'no_subscription' => 'No subscription',
                    'no_card' => 'No card',
                ],
            ],
            'list' => [
                'actions' => [
                    'new_user' => 'New User',
                ],
                'tabs' => [
                    'all_users' => 'All Users',
                    'verified' => 'Verified',
                    'unverified' => 'Unverified',
                    'subscribed' => 'Subscribed',
                ],
            ],
            'relation_licenses' => [
                'title' => 'Licenses',
                'columns' => [
                    'license_key' => 'License Key',
                ],
                'empty' => [
                    'heading' => 'No licenses yet',
                    'description' => 'This user has no licenses. Create one to get started.',
                ],
            ],
        ],

        'subscriptions' => [
            'table' => [
                'stripe_id' => 'Stripe ID',
                'stripe_price' => 'Stripe Price',
                'trial_ends' => 'Trial Ends',
                'ends' => 'Ends',
                'ended' => 'Ended',
                'placeholders' => [
                    'multiple_prices' => 'Multiple prices',
                    'no_trial' => 'No trial',
                ],
            ],
            'infolist' => [
                'sections' => [
                    'subscription' => 'Subscription',
                    'dates' => 'Dates',
                ],
            ],
        ],

        'licenses' => [
            'navigation_badge_tooltip' => 'Active licenses',
            'sections' => [
                'license_information' => 'License Information',
                'customer_product' => 'Customer & Product',
                'usage_limits' => 'Usage & Limits',
                'subscription' => 'Subscription',
                'timestamps' => 'Timestamps',
            ],
            'fields' => [
                'license_key' => 'License Key',
                'domain_limit' => 'Domain Limit',
                'active_domains' => 'Active Domains',
                'last_validated' => 'Last Validated',
                'stripe_status' => 'Stripe Status',
            ],
            'placeholders' => [
                'unlimited' => '∞ Unlimited',
                'no_subscription' => 'No subscription',
            ],
            'notifications' => [
                'license_key_copied' => 'License key copied!',
            ],
            'form' => [
                'sections' => [
                    'license_details' => 'License Details',
                    'subscription_limits' => 'Subscription & Limits',
                    'status_expiry' => 'Status & Expiry',
                ],
                'help' => [
                    'auto_generate' => 'Leave empty to auto-generate',
                    'optional_stripe_subscription' => 'Optional - link to Stripe subscription',
                    'copy_from_package' => 'Copied from package if left empty',
                    'no_expiration' => 'Leave empty for no expiration',
                ],
                'placeholders' => [
                    'license_key_example' => 'e.g. XXXX-XXXX-XXXX-XXXX',
                    'unlimited' => 'Leave empty for unlimited',
                ],
            ],
            'table' => [
                'filters' => [
                    'expiration' => 'Expiration',
                    'expired' => 'Expired',
                    'not_expired' => 'Not expired',
                ],
                'notifications' => [
                    'license_suspended' => 'License suspended',
                    'license_activated' => 'License activated',
                    'licenses_activated' => ':count licenses activated',
                    'licenses_suspended' => ':count licenses suspended',
                ],
            ],
            'list' => [
                'actions' => [
                    'new_license' => 'New License',
                ],
                'tabs' => [
                    'all_licenses' => 'All Licenses',
                    'active' => 'Active',
                    'paused' => 'Paused',
                    'suspended' => 'Suspended',
                    'expired' => 'Expired',
                    'cancelled' => 'Cancelled',
                ],
            ],
            'view' => [
                'actions' => [
                    'copy_key' => 'Copy Key',
                    'suspend' => 'Suspend',
                    'activate' => 'Activate',
                ],
                'modals' => [
                    'suspend' => [
                        'heading' => 'Suspend License',
                        'description' => 'This will suspend the license and prevent it from being used. Are you sure?',
                    ],
                    'activate' => [
                        'heading' => 'Activate License',
                        'description' => 'This will activate the license and allow it to be used.',
                    ],
                ],
                'notifications' => [
                    'copied' => 'License key copied to clipboard!',
                    'suspended' => 'License suspended',
                    'activated' => 'License activated',
                ],
            ],
            'relation_activations' => [
                'title' => 'Domain Activations',
                'columns' => [
                    'domain' => 'Domain',
                    'ip_address' => 'IP Address',
                    'browser' => 'Browser',
                ],
                'notifications' => [
                    'domain_copied' => 'Domain copied!',
                ],
                'modals' => [
                    'deactivate' => [
                        'heading' => 'Deactivate Domain',
                        'description' => 'Are you sure you want to deactivate this domain? The license will no longer work on this domain.',
                    ],
                    'reactivate' => [
                        'heading' => 'Reactivate Domain',
                        'description' => 'This will reactivate the domain. Make sure the license has available slots.',
                    ],
                ],
                'empty' => [
                    'heading' => 'No activations yet',
                    'description' => 'This license has not been activated on any domains.',
                ],
            ],
        ],

        'products' => [
            'navigation_badge_tooltip' => 'Active products',
            'global_search' => [
                'packages_suffix' => 'packages',
            ],
            'sections' => [
                'product_details' => 'Product Details',
                'statistics' => 'Statistics',
            ],
            'fields' => [
                'short_description' => 'Short description',
                'long_description' => 'Long description',
                'total_packages' => 'Total Packages',
                'active_packages' => 'Active Packages',
                'sort_order' => 'Sort Order',
            ],
            'placeholders' => [
                'no_short_description' => 'No short description',
                'no_long_description' => 'No long description',
            ],
            'form' => [
                'sections' => [
                    'product_information' => 'Product Information',
                    'settings' => 'Settings',
                    'banner_image' => 'Banner Image',
                    'screenshots' => 'Screenshots',
                ],
                'help' => [
                    'slug' => 'URL-friendly identifier. Auto-generated from name on creation.',
                    'inactive_hidden' => 'Inactive products are hidden from the storefront.',
                    'sort_order' => 'Lower numbers appear first.',
                    'banner_upload' => 'Upload a banner image for the product page hero (max 5MB). Recommended: 1920x1080.',
                    'screenshots_upload' => 'Upload product screenshots (max 5MB each). Drag to reorder.',
                ],
                'placeholders' => [
                    'short_description' => 'Brief description of the product...',
                    'long_description' => 'Detailed product description...',
                ],
            ],
            'table' => [
                'packages' => 'Packages',
                'filters' => [
                    'active_only' => 'Active only',
                    'inactive_only' => 'Inactive only',
                ],
            ],
            'relation_packages' => [
                'title' => 'Packages',
                'empty' => [
                    'heading' => 'No packages yet',
                    'description' => 'Create packages with pricing tiers for this product.',
                ],
            ],
            'packages' => [
                'form' => [
                    'sections' => [
                        'package_information' => 'Package Information',
                        'pricing' => 'Pricing',
                        'features' => 'Features',
                    ],
                    'fields' => [
                        'stripe_monthly_price_id' => 'Stripe Monthly Price ID',
                        'stripe_yearly_price_id' => 'Stripe Yearly Price ID',
                    ],
                    'help' => [
                        'slug' => 'URL-friendly identifier. Auto-generated from name on creation.',
                        'inactive_hidden' => 'Inactive packages are hidden from the storefront.',
                        'unlimited_domains' => 'Leave empty for unlimited domains.',
                        'monthly_price' => 'Monthly subscription price.',
                        'yearly_price' => 'Yearly subscription price (typically discounted).',
                        'stripe_monthly_price_id' => 'Stripe price ID for monthly billing.',
                        'stripe_yearly_price_id' => 'Stripe price ID for yearly billing.',
                        'features' => 'List the features included in this package. Press Enter after each feature.',
                    ],
                    'placeholders' => [
                        'description' => 'Brief description of this package tier...',
                        'unlimited' => 'Unlimited',
                        'amount' => '0.00',
                        'stripe_price' => 'price_...',
                        'add_feature' => 'Add a feature...',
                    ],
                ],
            ],
        ],

        'nalda_csv_uploads' => [
            'navigation_label' => 'Nalda CSV Uploads',
            'form' => [
                'sections' => [
                    'upload_details' => 'Upload Details',
                    'sftp_configuration' => 'SFTP Configuration',
                ],
                'help' => [
                    'csv_upload' => 'Upload a CSV file (max 10MB)',
                ],
                'status' => [
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                ],
                'placeholders' => [
                    'domain' => 'example.com',
                    'sftp_host' => 'sftp.example.com',
                    'sftp_path' => '/uploads/csv',
                    'uploaded_at_auto' => 'Set automatically on completion',
                    'error_message' => 'Error details will appear here if upload fails',
                ],
            ],
            'table' => [
                'placeholders' => [
                    'not_uploaded' => 'Not uploaded',
                ],
            ],
        ],

        'license_activations' => [
            'model_label' => 'Domain Activation',
            'plural_model_label' => 'Domain Activations',
            'table' => [
                'license' => 'License',
                'user' => 'User',
                'modals' => [
                    'deactivate_domain' => 'Deactivate Domain',
                    'remove_activation_for' => 'Remove activation for :domain?',
                ],
                'actions' => [
                    'deactivate_selected' => 'Deactivate Selected',
                ],
            ],
        ],
    ],
];
