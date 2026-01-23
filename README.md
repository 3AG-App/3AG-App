# 3AG App - WordPress Plugins & Themes Store

E-commerce platform for selling premium WordPress plugins and themes. Built with Laravel, React, and Stripe with domain-based license activation and subscription billing.

## Features

- **Product Catalog** - Showcase plugins and themes with detailed descriptions and pricing tiers
- **License Management** - Auto-generated license keys with domain activation limits
- **Subscription Billing** - Stripe-powered monthly/yearly subscriptions with automatic renewals
- **Customer Dashboard** - Self-service portal for downloads, license management, and billing
- **Admin Panel** - Filament-powered admin for managing products, orders, and customers
- **Automatic Updates** - License validation for WordPress plugin/theme update delivery

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: React 19, TypeScript, Inertia.js v2
- **Styling**: Tailwind CSS v4, shadcn/ui components
- **Admin**: Filament v5
- **Payments**: Stripe via Laravel Cashier
- **Testing**: Pest v4
- **Linting**: ESLint, Prettier, Laravel Pint

## Requirements

- PHP 8.4+
- Composer
- Node.js 18+
- MySQL 8.0+ or PostgreSQL
- Stripe Account

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd 3AG-App
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure environment variables

Update your `.env` file with your database and Stripe credentials:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Stripe
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Cashier
CASHIER_CURRENCY=chf
CASHIER_CURRENCY_LOCALE=de_CH
```

### 5. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 6. Build frontend assets

```bash
npm run build
```

### 7. Start the development server

```bash
# Using Laravel Sail (recommended)
./vendor/bin/sail up -d

# Or using built-in server
php artisan serve
npm run dev
```

## Stripe Webhook Setup

### Local Development

Use Stripe CLI to forward webhooks to your local environment:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

Copy the webhook signing secret to your `.env` file.

### Production

1. Go to Stripe Dashboard → Webhooks
2. Add endpoint: `https://yourdomain.com/stripe/webhook`
3. Select these events:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `customer.subscription.paused`
   - `customer.subscription.resumed`
   - `invoice.payment_failed`
   - `invoice.payment_succeeded`

## Project Structure

```
app/
├── Enums/
│   ├── LicenseStatus.php      # Active, Expired, Suspended, Paused
│   └── ProductType.php        # Plugin, Theme, SourceCode
├── Filament/
│   └── Resources/             # Admin panel resources
├── Http/
│   ├── Controllers/           # API and web controllers
│   └── Requests/              # Form request validation
├── Listeners/
│   ├── CreateLicenseOnSubscriptionCreated.php
│   └── SyncLicenseStatusOnSubscriptionChange.php
├── Models/
│   ├── License.php            # License with domain limit
│   ├── LicenseActivation.php  # Domain activations
│   ├── Package.php            # Pricing tiers (Starter, Pro, Agency)
│   ├── Product.php            # Products (plugins, themes, etc.)
│   └── User.php               # Customers with Billable trait
resources/
├── js/
│   ├── components/            # React/shadcn components
│   ├── layouts/               # App layouts
│   └── pages/                 # Inertia pages
database/
├── factories/                 # Model factories for testing
├── migrations/                # Database migrations
└── seeders/                   # Demo data seeders
tests/
├── Feature/                   # Feature tests
└── Unit/                      # Unit tests
```

## Key Models

### Product
A WordPress plugin or theme available for purchase.

### Package
Pricing tier for a product (e.g., Starter, Professional, Agency) with Stripe price ID and domain activation limit.

### License
Generated when a customer subscribes. Used for:
- Download authentication
- WordPress update API validation
- Domain activation tracking

### LicenseActivation
Records each domain where a license is activated.

## Testing

```bash
# Run all tests
php artisan test

# Run specific tests
php artisan test --filter=License

# Run with coverage
php artisan test --coverage
```

## Code Quality

```bash
# Format PHP code
./vendor/bin/pint

# Format JS/TS code
npm run lint
npm run format
```

## Commands

```bash
# Generate Wayfinder types (auto-runs with Vite)
php artisan wayfinder:generate

# Clear all caches
php artisan optimize:clear

# Create admin user
php artisan make:filament-user
```

## License

This project is proprietary software. All rights reserved.
