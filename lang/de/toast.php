<?php

declare(strict_types=1);

return [
    'email_verified_successfully' => 'E-Mail erfolgreich verifiziert!',
    'verification_link_sent' => 'Bestätigungslink gesendet!',

    'auth' => [
        'welcome_back' => 'Willkommen zurück!',
        'logged_out' => 'Sie wurden abgemeldet.',
        'account_created' => 'Konto erfolgreich erstellt. Willkommen!',
    ],

    'dashboard' => [
        'profile_updated' => [
            'message' => 'Profil aktualisiert',
            'description' => 'Ihre Profilinformationen wurden gespeichert.',
        ],
        'password_updated' => [
            'message' => 'Passwort aktualisiert',
            'description' => 'Ihr Passwort wurde erfolgreich geändert.',
        ],
        'settings_saved' => [
            'message' => 'Einstellungen gespeichert',
            'description' => 'Ihre Einstellungen wurden aktualisiert.',
        ],
        'subscription_cancelled' => [
            'message' => 'Abonnement gekündigt',
            'description' => 'Ihr Abonnement bleibt bis zum Ende des Abrechnungszeitraums aktiv.',
        ],
        'subscription_cancel_failed' => [
            'message' => 'Abonnement konnte nicht gekündigt werden',
        ],
        'subscription_resumed' => [
            'message' => 'Abonnement fortgesetzt',
            'description' => 'Ihr Abonnement wurde reaktiviert.',
        ],
        'subscription_resume_failed' => [
            'message' => 'Abonnement konnte nicht fortgesetzt werden',
        ],
        'domains_deactivated' => [
            'message' => 'Domains deaktiviert',
            'description' => ':count Domain(s) wurden für diese Lizenz deaktiviert.',
        ],
        'domain_deactivated' => [
            'message' => 'Domain deaktiviert',
            'description' => 'Die Lizenz wurde für :domain deaktiviert.',
        ],
    ],

    'product' => [
        'pricing_not_available' => [
            'message' => 'Preis nicht verfügbar',
            'description' => 'Dieses Abrechnungsintervall ist für dieses Paket nicht verfügbar.',
        ],
        'already_subscribed' => [
            'message' => 'Bereits abonniert',
            'description' => 'Sie haben bereits ein aktives Abonnement für dieses Produkt. Nutzen Sie den Planwechsel, um Pläne zu ändern.',
        ],
        'unable_to_process_subscription' => [
            'message' => 'Abonnement kann nicht verarbeitet werden',
            'description' => 'Es gab ein Problem mit der Preiskonfiguration. Bitte kontaktieren Sie den Support.',
        ],
        'no_active_subscription' => [
            'message' => 'Kein aktives Abonnement',
            'description' => 'Sie haben kein aktives Abonnement für dieses Produkt.',
        ],
        'no_change_needed' => [
            'message' => 'Keine Änderung erforderlich',
            'description' => 'Sie sind bereits in diesem Plan.',
        ],
        'cannot_downgrade' => [
            'message' => 'Downgrade nicht möglich',
            'description' => 'Sie haben :active aktive Domains, aber der neue Plan erlaubt nur :limit. Bitte deaktivieren Sie zuerst einige Domains.',
        ],
        'subscription_updated' => [
            'message' => 'Abonnement aktualisiert',
            'description' => 'Ihr Abonnement wurde auf :plan geändert.',
        ],
        'subscription_update_failed' => [
            'message' => 'Abonnement konnte nicht aktualisiert werden',
            'description' => 'Beim Aktualisieren Ihres Abonnements ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
        ],
    ],

    'dev' => [
        'flash_test' => [
            'success' => [
                'message' => 'Vorgang erfolgreich abgeschlossen!',
                'description' => 'Ihre Änderungen wurden gespeichert.',
            ],
            'error' => [
                'message' => 'Etwas ist schiefgelaufen!',
                'description' => 'Bitte versuchen Sie es erneut oder kontaktieren Sie den Support.',
            ],
            'warning' => [
                'message' => 'Bitte gehen Sie vorsichtig vor.',
            ],
            'info' => [
                'message' => 'Hier sind einige nützliche Informationen.',
            ],
            'default' => [
                'message' => 'Testnachricht',
            ],
        ],
    ],
];
