<?php

declare(strict_types=1);

return [
    'email_verified_successfully' => 'Email verified successfully!',
    'verification_link_sent' => 'Verification link sent!',

    'auth' => [
        'welcome_back' => 'Welcome back!',
        'logged_out' => 'You have been logged out.',
        'account_created' => 'Account created successfully. Welcome!',
    ],

    'dashboard' => [
        'profile_updated' => [
            'message' => 'Profile updated',
            'description' => 'Your profile information has been saved.',
        ],
        'password_updated' => [
            'message' => 'Password updated',
            'description' => 'Your password has been changed successfully.',
        ],
        'settings_saved' => [
            'message' => 'Settings saved',
            'description' => 'Your preferences have been updated.',
        ],
        'subscription_cancelled' => [
            'message' => 'Subscription cancelled',
            'description' => 'Your subscription will remain active until the end of the billing period.',
        ],
        'subscription_cancel_failed' => [
            'message' => 'Failed to cancel subscription',
        ],
        'subscription_resumed' => [
            'message' => 'Subscription resumed',
            'description' => 'Your subscription has been reactivated.',
        ],
        'subscription_resume_failed' => [
            'message' => 'Failed to resume subscription',
        ],
        'domains_deactivated' => [
            'message' => 'Domains deactivated',
            'description' => ':count domain(s) have been deactivated from this license.',
        ],
        'domain_deactivated' => [
            'message' => 'Domain deactivated',
            'description' => 'The license has been deactivated from :domain.',
        ],
    ],

    'product' => [
        'pricing_not_available' => [
            'message' => 'Pricing not available',
            'description' => 'This billing interval is not available for this package.',
        ],
        'already_subscribed' => [
            'message' => 'Already subscribed',
            'description' => 'You already have an active subscription for this product. Use swap to change plans.',
        ],
        'unable_to_process_subscription' => [
            'message' => 'Unable to process subscription',
            'description' => 'There was an issue with the pricing configuration. Please contact support.',
        ],
        'no_active_subscription' => [
            'message' => 'No active subscription',
            'description' => "You don't have an active subscription for this product.",
        ],
        'no_change_needed' => [
            'message' => 'No change needed',
            'description' => 'You are already on this plan.',
        ],
        'cannot_downgrade' => [
            'message' => 'Cannot downgrade',
            'description' => 'You have :active active domains but the new plan only allows :limit. Please deactivate some domains first.',
        ],
        'subscription_updated' => [
            'message' => 'Subscription updated',
            'description' => 'Your subscription has been changed to :plan.',
        ],
        'subscription_update_failed' => [
            'message' => 'Failed to update subscription',
            'description' => 'An error occurred while updating your subscription. Please try again.',
        ],
    ],

    'dev' => [
        'flash_test' => [
            'success' => [
                'message' => 'Operation completed successfully!',
                'description' => 'Your changes have been saved.',
            ],
            'error' => [
                'message' => 'Something went wrong!',
                'description' => 'Please try again or contact support.',
            ],
            'warning' => [
                'message' => 'Please proceed with caution.',
            ],
            'info' => [
                'message' => 'Here is some useful information.',
            ],
            'default' => [
                'message' => 'Test message',
            ],
        ],
    ],
];
