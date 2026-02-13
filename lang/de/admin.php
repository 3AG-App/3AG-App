<?php

declare(strict_types=1);

return [
    'brand_name' => '3AG Admin',

    'navigation' => [
        'dashboard' => 'Dashboard',
        'shop_management' => 'Shop-Verwaltung',
        'license_management' => 'Lizenzverwaltung',
        'user_management' => 'Benutzerverwaltung',
    ],

    'common' => [
        'active' => 'Aktiv',
        'activated' => 'Aktiviert',
        'all' => 'Alle',
        'copied' => 'Kopiert!',
        'created' => 'Erstellt',
        'customer' => 'Kunde',
        'deactivated' => 'Deaktiviert',
        'domains' => 'Domains',
        'email' => 'E-Mail',
        'expires' => 'Läuft ab',
        'inactive' => 'Inaktiv',
        'last_check' => 'Letzte Prüfung',
        'last_updated' => 'Zuletzt aktualisiert',
        'na' => 'k. A.',
        'name' => 'Name',
        'never' => 'Nie',
        'order' => 'Reihenfolge',
        'package' => 'Paket',
        'product' => 'Produkt',
        'status' => 'Status',
        'type' => 'Typ',
        'unknown' => 'Unbekannt',
    ],

    'widgets' => [
        'latest_licenses' => [
            'heading' => 'Neueste Lizenzen',
            'description' => 'Die 10 zuletzt erstellten Lizenzen',
            'columns' => [
                'license_key' => 'Lizenzschlüssel',
            ],
        ],
        'license_status_overview' => [
            'heading' => 'Lizenzen nach Status',
        ],
        'revenue_chart' => [
            'heading' => 'Wachstum im Zeitverlauf',
            'filters' => [
                'last_30_days' => 'Letzte 30 Tage',
                'last_6_months' => 'Letzte 6 Monate',
                'last_12_months' => 'Letzte 12 Monate',
            ],
            'datasets' => [
                'new_users' => 'Neue Benutzer',
                'new_licenses' => 'Neue Lizenzen',
            ],
        ],
        'stats_overview' => [
            'total_users' => 'Benutzer gesamt',
            'new_this_month' => ':count neu in diesem Monat',
            'active_licenses' => 'Aktive Lizenzen',
            'total_licenses' => ':count Lizenzen gesamt',
            'paying_customers' => 'Zahlende Kunden',
            'conversion' => ':percentage% Konversion',
            'active_products' => 'Aktive Produkte',
            'available_for_purchase' => 'Zum Kauf verfügbar',
        ],
    ],

    'resources' => [
        'users' => [
            'navigation_badge_tooltip' => 'Gesamtzahl registrierter Benutzer',
            'status' => [
                'subscribed' => 'Abonniert',
                'free' => 'Kostenlos',
            ],
            'sections' => [
                'account_information' => 'Kontoinformationen',
                'subscription_billing' => 'Abonnement & Abrechnung',
                'statistics' => 'Statistiken',
            ],
            'fields' => [
                'verified' => 'Verifiziert',
                'stripe_customer_id' => 'Stripe-Kunden-ID',
                'payment_method' => 'Zahlungsmethode',
                'trial_ends' => 'Test endet',
                'total_licenses' => 'Lizenzen gesamt',
                'active_licenses' => 'Aktive Lizenzen',
                'member_since' => 'Mitglied seit',
            ],
            'placeholders' => [
                'no_stripe_account' => 'Kein Stripe-Konto',
                'no_payment_method' => 'Keine Zahlungsmethode',
                'no_trial' => 'Kein Testzeitraum',
            ],
            'form' => [
                'fields' => [
                    'email_address' => 'E-Mail-Adresse',
                    'email_verified_at' => 'E-Mail verifiziert am',
                    'trial_ends_at' => 'Test endet am',
                    'last_4_digits' => 'Letzte 4 Ziffern',
                ],
                'help' => [
                    'password_edit_blank' => 'Leer lassen, um das aktuelle Passwort beim Bearbeiten beizubehalten.',
                ],
                'description' => [
                    'stripe_subscription_information' => 'Stripe-Abonnementinformationen',
                ],
            ],
            'table' => [
                'email_address' => 'E-Mail-Adresse',
                'email_copied' => 'E-Mail kopiert',
                'stripe_customer' => 'Stripe-Kunde',
                'filters' => [
                    'email_verified' => 'E-Mail verifiziert',
                    'has_subscription' => 'Hat Abonnement',
                ],
                'placeholders' => [
                    'no_subscription' => 'Kein Abonnement',
                    'no_card' => 'Keine Karte',
                ],
            ],
            'list' => [
                'actions' => [
                    'new_user' => 'Neuer Benutzer',
                ],
                'tabs' => [
                    'all_users' => 'Alle Benutzer',
                    'verified' => 'Verifiziert',
                    'unverified' => 'Nicht verifiziert',
                    'subscribed' => 'Abonniert',
                ],
            ],
            'relation_licenses' => [
                'title' => 'Lizenzen',
                'columns' => [
                    'license_key' => 'Lizenzschlüssel',
                ],
                'empty' => [
                    'heading' => 'Noch keine Lizenzen',
                    'description' => 'Dieser Benutzer hat keine Lizenzen. Erstellen Sie eine, um zu beginnen.',
                ],
            ],
        ],

        'subscriptions' => [
            'table' => [
                'stripe_id' => 'Stripe-ID',
                'stripe_price' => 'Stripe-Preis',
                'trial_ends' => 'Test endet',
                'ends' => 'Endet',
                'ended' => 'Beendet',
                'placeholders' => [
                    'multiple_prices' => 'Mehrere Preise',
                    'no_trial' => 'Kein Testzeitraum',
                ],
            ],
            'infolist' => [
                'sections' => [
                    'subscription' => 'Abonnement',
                    'dates' => 'Daten',
                ],
            ],
        ],

        'licenses' => [
            'navigation_badge_tooltip' => 'Aktive Lizenzen',
            'sections' => [
                'license_information' => 'Lizenzinformationen',
                'customer_product' => 'Kunde & Produkt',
                'usage_limits' => 'Nutzung & Limits',
                'subscription' => 'Abonnement',
                'timestamps' => 'Zeitstempel',
            ],
            'fields' => [
                'license_key' => 'Lizenzschlüssel',
                'domain_limit' => 'Domain-Limit',
                'active_domains' => 'Aktive Domains',
                'last_validated' => 'Zuletzt validiert',
                'stripe_status' => 'Stripe-Status',
            ],
            'placeholders' => [
                'unlimited' => '∞ Unbegrenzt',
                'no_subscription' => 'Kein Abonnement',
            ],
            'notifications' => [
                'license_key_copied' => 'Lizenzschlüssel kopiert!',
            ],
            'form' => [
                'sections' => [
                    'license_details' => 'Lizenzdetails',
                    'subscription_limits' => 'Abonnement & Limits',
                    'status_expiry' => 'Status & Ablauf',
                ],
                'help' => [
                    'auto_generate' => 'Leer lassen, um automatisch zu generieren',
                    'optional_stripe_subscription' => 'Optional – mit Stripe-Abonnement verknüpfen',
                    'copy_from_package' => 'Wird aus dem Paket übernommen, wenn leer',
                    'no_expiration' => 'Leer lassen für kein Ablaufdatum',
                ],
                'placeholders' => [
                    'license_key_example' => 'z. B. XXXX-XXXX-XXXX-XXXX',
                    'unlimited' => 'Leer lassen für unbegrenzt',
                ],
            ],
            'table' => [
                'filters' => [
                    'expiration' => 'Ablauf',
                    'expired' => 'Abgelaufen',
                    'not_expired' => 'Nicht abgelaufen',
                ],
                'notifications' => [
                    'license_suspended' => 'Lizenz pausiert',
                    'license_activated' => 'Lizenz aktiviert',
                    'licenses_activated' => ':count Lizenzen aktiviert',
                    'licenses_suspended' => ':count Lizenzen pausiert',
                ],
            ],
            'list' => [
                'actions' => [
                    'new_license' => 'Neue Lizenz',
                ],
                'tabs' => [
                    'all_licenses' => 'Alle Lizenzen',
                    'active' => 'Aktiv',
                    'paused' => 'Pausiert',
                    'suspended' => 'Ausgesetzt',
                    'expired' => 'Abgelaufen',
                    'cancelled' => 'Gekündigt',
                ],
            ],
            'view' => [
                'actions' => [
                    'copy_key' => 'Schlüssel kopieren',
                    'suspend' => 'Aussetzen',
                    'activate' => 'Aktivieren',
                ],
                'modals' => [
                    'suspend' => [
                        'heading' => 'Lizenz aussetzen',
                        'description' => 'Dadurch wird die Lizenz ausgesetzt und kann nicht mehr verwendet werden. Sind Sie sicher?',
                    ],
                    'activate' => [
                        'heading' => 'Lizenz aktivieren',
                        'description' => 'Dadurch wird die Lizenz aktiviert und kann wieder verwendet werden.',
                    ],
                ],
                'notifications' => [
                    'copied' => 'Lizenzschlüssel in die Zwischenablage kopiert!',
                    'suspended' => 'Lizenz ausgesetzt',
                    'activated' => 'Lizenz aktiviert',
                ],
            ],
            'relation_activations' => [
                'title' => 'Domain-Aktivierungen',
                'columns' => [
                    'domain' => 'Domain',
                    'ip_address' => 'IP-Adresse',
                    'browser' => 'Browser',
                ],
                'notifications' => [
                    'domain_copied' => 'Domain kopiert!',
                ],
                'modals' => [
                    'deactivate' => [
                        'heading' => 'Domain deaktivieren',
                        'description' => 'Möchten Sie diese Domain wirklich deaktivieren? Die Lizenz funktioniert dann auf dieser Domain nicht mehr.',
                    ],
                    'reactivate' => [
                        'heading' => 'Domain reaktivieren',
                        'description' => 'Dadurch wird die Domain reaktiviert. Stellen Sie sicher, dass die Lizenz verfügbare Slots hat.',
                    ],
                ],
                'empty' => [
                    'heading' => 'Noch keine Aktivierungen',
                    'description' => 'Diese Lizenz wurde auf keiner Domain aktiviert.',
                ],
            ],
        ],

        'products' => [
            'navigation_badge_tooltip' => 'Aktive Produkte',
            'global_search' => [
                'packages_suffix' => 'Pakete',
            ],
            'sections' => [
                'product_details' => 'Produktdetails',
                'statistics' => 'Statistiken',
            ],
            'fields' => [
                'short_description' => 'Kurzbeschreibung',
                'long_description' => 'Langbeschreibung',
                'total_packages' => 'Pakete gesamt',
                'active_packages' => 'Aktive Pakete',
                'sort_order' => 'Sortierung',
            ],
            'placeholders' => [
                'no_short_description' => 'Keine Kurzbeschreibung',
                'no_long_description' => 'Keine Langbeschreibung',
            ],
            'form' => [
                'sections' => [
                    'product_information' => 'Produktinformationen',
                    'settings' => 'Einstellungen',
                    'banner_image' => 'Bannerbild',
                    'screenshots' => 'Screenshots',
                ],
                'help' => [
                    'slug' => 'URL-freundlicher Bezeichner. Beim Erstellen automatisch aus dem Namen erzeugt.',
                    'inactive_hidden' => 'Inaktive Produkte sind im Storefront ausgeblendet.',
                    'sort_order' => 'Kleinere Werte erscheinen zuerst.',
                    'banner_upload' => 'Bannerbild für den Hero-Bereich hochladen (max. 5 MB). Empfohlen: 1920x1080.',
                    'screenshots_upload' => 'Produktscreenshots hochladen (je max. 5 MB). Zum Neuordnen ziehen.',
                ],
                'placeholders' => [
                    'short_description' => 'Kurze Produktbeschreibung...',
                    'long_description' => 'Ausführliche Produktbeschreibung...',
                ],
            ],
            'table' => [
                'packages' => 'Pakete',
                'filters' => [
                    'active_only' => 'Nur aktiv',
                    'inactive_only' => 'Nur inaktiv',
                ],
            ],
            'relation_packages' => [
                'title' => 'Pakete',
                'empty' => [
                    'heading' => 'Noch keine Pakete',
                    'description' => 'Erstellen Sie Pakete mit Preisstufen für dieses Produkt.',
                ],
            ],
            'packages' => [
                'form' => [
                    'sections' => [
                        'package_information' => 'Paketinformationen',
                        'pricing' => 'Preise',
                        'features' => 'Funktionen',
                    ],
                    'fields' => [
                        'stripe_monthly_price_id' => 'Stripe-Monatspreis-ID',
                        'stripe_yearly_price_id' => 'Stripe-Jahrespreis-ID',
                    ],
                    'help' => [
                        'slug' => 'URL-freundlicher Bezeichner. Beim Erstellen automatisch aus dem Namen erzeugt.',
                        'inactive_hidden' => 'Inaktive Pakete sind im Storefront ausgeblendet.',
                        'unlimited_domains' => 'Leer lassen für unbegrenzte Domains.',
                        'monthly_price' => 'Monatlicher Abonnementpreis.',
                        'yearly_price' => 'Jährlicher Abonnementpreis (meist mit Rabatt).',
                        'stripe_monthly_price_id' => 'Stripe-Preis-ID für monatliche Abrechnung.',
                        'stripe_yearly_price_id' => 'Stripe-Preis-ID für jährliche Abrechnung.',
                        'features' => 'Funktionen dieses Pakets auflisten. Nach jeder Funktion Enter drücken.',
                    ],
                    'placeholders' => [
                        'description' => 'Kurze Beschreibung dieser Paketstufe...',
                        'unlimited' => 'Unbegrenzt',
                        'amount' => '0.00',
                        'stripe_price' => 'price_...',
                        'add_feature' => 'Funktion hinzufügen...',
                    ],
                ],
            ],
        ],

        'nalda_csv_uploads' => [
            'navigation_label' => 'Nalda-CSV-Uploads',
            'form' => [
                'sections' => [
                    'upload_details' => 'Upload-Details',
                    'sftp_configuration' => 'SFTP-Konfiguration',
                ],
                'help' => [
                    'csv_upload' => 'CSV-Datei hochladen (max. 10 MB)',
                ],
                'status' => [
                    'pending' => 'Ausstehend',
                    'processing' => 'Wird verarbeitet',
                    'completed' => 'Abgeschlossen',
                    'failed' => 'Fehlgeschlagen',
                ],
                'placeholders' => [
                    'domain' => 'beispiel.de',
                    'sftp_host' => 'sftp.beispiel.de',
                    'sftp_path' => '/uploads/csv',
                    'uploaded_at_auto' => 'Wird nach Abschluss automatisch gesetzt',
                    'error_message' => 'Fehlerdetails erscheinen hier, falls der Upload fehlschlägt',
                ],
            ],
            'table' => [
                'placeholders' => [
                    'not_uploaded' => 'Nicht hochgeladen',
                ],
            ],
        ],

        'license_activations' => [
            'model_label' => 'Domain-Aktivierung',
            'plural_model_label' => 'Domain-Aktivierungen',
            'table' => [
                'license' => 'Lizenz',
                'user' => 'Benutzer',
                'modals' => [
                    'deactivate_domain' => 'Domain deaktivieren',
                    'remove_activation_for' => 'Aktivierung für :domain entfernen?',
                ],
                'actions' => [
                    'deactivate_selected' => 'Ausgewählte deaktivieren',
                ],
            ],
        ],
    ],
];
