<?php

declare(strict_types=1);

return [
    'brand_name' => 'Admin 3AG',

    'navigation' => [
        'dashboard' => 'Tableau de bord',
        'shop_management' => 'Gestion de la boutique',
        'license_management' => 'Gestion des licences',
        'user_management' => 'Gestion des utilisateurs',
    ],

    'common' => [
        'active' => 'Actif',
        'activated' => 'Activé',
        'all' => 'Tous',
        'copied' => 'Copié !',
        'created' => 'Créé',
        'customer' => 'Client',
        'deactivated' => 'Désactivé',
        'domains' => 'Domaines',
        'email' => 'E-mail',
        'expires' => 'Expire',
        'inactive' => 'Inactif',
        'last_check' => 'Dernière vérification',
        'last_updated' => 'Dernière mise à jour',
        'na' => 'N/D',
        'name' => 'Nom',
        'never' => 'Jamais',
        'order' => 'Ordre',
        'package' => 'Pack',
        'product' => 'Produit',
        'status' => 'Statut',
        'type' => 'Type',
        'unknown' => 'Inconnu',
    ],

    'enums' => [
        'license_status' => [
            'active' => 'Actif',
            'paused' => 'En pause',
            'suspended' => 'Suspendu',
            'expired' => 'Expiré',
            'cancelled' => 'Annulé',
        ],
        'product_type' => [
            'plugin' => 'Plugin',
            'theme' => 'Thème',
            'source_code' => 'Code source',
        ],
        'csv_upload_status' => [
            'pending' => 'En attente',
            'completed' => 'Terminé',
            'failed' => 'Échec',
        ],
        'nalda_csv_type' => [
            'orders' => 'Commandes',
            'products' => 'Produits',
        ],
    ],

    'widgets' => [
        'latest_licenses' => [
            'heading' => 'Licences récentes',
            'description' => 'Les 10 licences les plus récemment créées',
            'columns' => [
                'license_key' => 'Clé de licence',
            ],
        ],
        'license_status_overview' => [
            'heading' => 'Licences par statut',
        ],
        'revenue_chart' => [
            'heading' => 'Croissance dans le temps',
            'filters' => [
                'last_30_days' => '30 derniers jours',
                'last_6_months' => '6 derniers mois',
                'last_12_months' => '12 derniers mois',
            ],
            'datasets' => [
                'new_users' => 'Nouveaux utilisateurs',
                'new_licenses' => 'Nouvelles licences',
            ],
        ],
        'stats_overview' => [
            'total_users' => 'Utilisateurs totaux',
            'new_this_month' => ':count nouveaux ce mois-ci',
            'active_licenses' => 'Licences actives',
            'total_licenses' => ':count licences totales',
            'paying_customers' => 'Clients payants',
            'conversion' => ':percentage% de conversion',
            'active_products' => 'Produits actifs',
            'available_for_purchase' => 'Disponible à l’achat',
        ],
    ],

    'resources' => [
        'users' => [
            'navigation_label' => 'Utilisateurs',
            'model_label' => 'Utilisateur',
            'plural_model_label' => 'Utilisateurs',
            'navigation_badge_tooltip' => 'Nombre total d’utilisateurs inscrits',
            'status' => [
                'subscribed' => 'Abonné',
                'free' => 'Gratuit',
            ],
            'sections' => [
                'account_information' => 'Informations du compte',
                'subscription_billing' => 'Abonnement & facturation',
                'statistics' => 'Statistiques',
            ],
            'fields' => [
                'verified' => 'Vérifié',
                'stripe_customer_id' => 'ID client Stripe',
                'payment_method' => 'Moyen de paiement',
                'trial_ends' => 'Fin d’essai',
                'total_licenses' => 'Licences totales',
                'active_licenses' => 'Licences actives',
                'member_since' => 'Membre depuis',
            ],
            'placeholders' => [
                'no_stripe_account' => 'Aucun compte Stripe',
                'no_payment_method' => 'Aucun moyen de paiement',
                'no_trial' => 'Aucun essai',
            ],
            'form' => [
                'fields' => [
                    'email_address' => 'Adresse e-mail',
                    'email_verified_at' => 'E-mail vérifié le',
                    'trial_ends_at' => 'Fin d’essai le',
                    'last_4_digits' => '4 derniers chiffres',
                ],
                'help' => [
                    'password_edit_blank' => 'Laissez vide pour conserver le mot de passe actuel lors de la modification.',
                ],
                'description' => [
                    'stripe_subscription_information' => 'Informations d’abonnement Stripe',
                ],
            ],
            'table' => [
                'email_address' => 'Adresse e-mail',
                'email_copied' => 'E-mail copié',
                'stripe_customer' => 'Client Stripe',
                'filters' => [
                    'email_verified' => 'E-mail vérifié',
                    'has_subscription' => 'A un abonnement',
                ],
                'placeholders' => [
                    'no_subscription' => 'Aucun abonnement',
                    'no_card' => 'Aucune carte',
                ],
            ],
            'list' => [
                'actions' => [
                    'new_user' => 'Nouvel utilisateur',
                ],
                'tabs' => [
                    'all_users' => 'Tous les utilisateurs',
                    'verified' => 'Vérifiés',
                    'unverified' => 'Non vérifiés',
                    'subscribed' => 'Abonnés',
                ],
            ],
            'relation_licenses' => [
                'title' => 'Licences',
                'columns' => [
                    'license_key' => 'Clé de licence',
                ],
                'empty' => [
                    'heading' => 'Aucune licence',
                    'description' => 'Cet utilisateur n’a aucune licence. Créez-en une pour commencer.',
                ],
            ],
        ],

        'subscriptions' => [
            'navigation_label' => 'Abonnements',
            'model_label' => 'Abonnement',
            'plural_model_label' => 'Abonnements',
            'table' => [
                'stripe_id' => 'ID Stripe',
                'stripe_price' => 'Prix Stripe',
                'trial_ends' => 'Fin d’essai',
                'ends' => 'Fin',
                'ended' => 'Terminé',
                'placeholders' => [
                    'multiple_prices' => 'Plusieurs prix',
                    'no_trial' => 'Aucun essai',
                ],
            ],
            'infolist' => [
                'sections' => [
                    'subscription' => 'Abonnement',
                    'dates' => 'Dates',
                ],
            ],
        ],

        'licenses' => [
            'navigation_label' => 'Licences',
            'model_label' => 'Licence',
            'plural_model_label' => 'Licences',
            'navigation_badge_tooltip' => 'Licences actives',
            'sections' => [
                'license_information' => 'Informations de licence',
                'customer_product' => 'Client & produit',
                'usage_limits' => 'Utilisation & limites',
                'subscription' => 'Abonnement',
                'timestamps' => 'Horodatage',
            ],
            'fields' => [
                'license_key' => 'Clé de licence',
                'domain_limit' => 'Limite de domaines',
                'active_domains' => 'Domaines actifs',
                'last_validated' => 'Dernière validation',
                'stripe_status' => 'Statut Stripe',
            ],
            'placeholders' => [
                'unlimited' => '∞ Illimité',
                'no_subscription' => 'Aucun abonnement',
            ],
            'notifications' => [
                'license_key_copied' => 'Clé de licence copiée !',
            ],
            'form' => [
                'sections' => [
                    'license_details' => 'Détails de la licence',
                    'subscription_limits' => 'Abonnement & limites',
                    'status_expiry' => 'Statut & expiration',
                ],
                'help' => [
                    'auto_generate' => 'Laisser vide pour générer automatiquement',
                    'optional_stripe_subscription' => 'Optionnel - lier à un abonnement Stripe',
                    'copy_from_package' => 'Copié depuis le pack si laissé vide',
                    'no_expiration' => 'Laisser vide pour aucune expiration',
                ],
                'placeholders' => [
                    'license_key_example' => 'ex. XXXX-XXXX-XXXX-XXXX',
                    'unlimited' => 'Laisser vide pour illimité',
                ],
            ],
            'table' => [
                'filters' => [
                    'expiration' => 'Expiration',
                    'expired' => 'Expiré',
                    'not_expired' => 'Non expiré',
                ],
                'notifications' => [
                    'license_suspended' => 'Licence suspendue',
                    'license_activated' => 'Licence activée',
                    'licenses_activated' => ':count licences activées',
                    'licenses_suspended' => ':count licences suspendues',
                ],
            ],
            'list' => [
                'actions' => [
                    'new_license' => 'Nouvelle licence',
                ],
                'tabs' => [
                    'all_licenses' => 'Toutes les licences',
                    'active' => 'Actives',
                    'paused' => 'En pause',
                    'suspended' => 'Suspendues',
                    'expired' => 'Expirées',
                    'cancelled' => 'Annulées',
                ],
            ],
            'view' => [
                'actions' => [
                    'copy_key' => 'Copier la clé',
                    'suspend' => 'Suspendre',
                    'activate' => 'Activer',
                ],
                'modals' => [
                    'suspend' => [
                        'heading' => 'Suspendre la licence',
                        'description' => 'Cela suspendra la licence et empêchera son utilisation. Êtes-vous sûr ?',
                    ],
                    'activate' => [
                        'heading' => 'Activer la licence',
                        'description' => 'Cela activera la licence et autorisera son utilisation.',
                    ],
                ],
                'notifications' => [
                    'copied' => 'Clé de licence copiée dans le presse-papiers !',
                    'suspended' => 'Licence suspendue',
                    'activated' => 'Licence activée',
                ],
            ],
            'relation_activations' => [
                'title' => 'Activations de domaine',
                'columns' => [
                    'domain' => 'Domaine',
                    'ip_address' => 'Adresse IP',
                    'browser' => 'Navigateur',
                ],
                'notifications' => [
                    'domain_copied' => 'Domaine copié !',
                ],
                'modals' => [
                    'deactivate' => [
                        'heading' => 'Désactiver le domaine',
                        'description' => 'Voulez-vous vraiment désactiver ce domaine ? La licence ne fonctionnera plus sur ce domaine.',
                    ],
                    'reactivate' => [
                        'heading' => 'Réactiver le domaine',
                        'description' => 'Cela réactivera le domaine. Vérifiez que la licence dispose d’emplacements disponibles.',
                    ],
                ],
                'empty' => [
                    'heading' => 'Aucune activation',
                    'description' => 'Cette licence n’a été activée sur aucun domaine.',
                ],
            ],
        ],

        'products' => [
            'navigation_label' => 'Produits',
            'model_label' => 'Produit',
            'plural_model_label' => 'Produits',
            'navigation_badge_tooltip' => 'Produits actifs',
            'global_search' => [
                'packages_suffix' => 'packs',
            ],
            'sections' => [
                'product_details' => 'Détails du produit',
                'statistics' => 'Statistiques',
            ],
            'fields' => [
                'short_description' => 'Description courte',
                'long_description' => 'Description longue',
                'total_packages' => 'Total des packs',
                'active_packages' => 'Packs actifs',
                'sort_order' => 'Ordre de tri',
            ],
            'placeholders' => [
                'no_short_description' => 'Aucune description courte',
                'no_long_description' => 'Aucune description longue',
            ],
            'form' => [
                'sections' => [
                    'product_information' => 'Informations produit',
                    'settings' => 'Paramètres',
                    'banner_image' => 'Image de bannière',
                    'screenshots' => 'Captures d’écran',
                ],
                'fields' => [
                    'banner_image' => 'Image de bannière',
                    'screenshots' => 'Captures d’écran',
                ],
                'help' => [
                    'slug' => 'Identifiant compatible URL. Généré automatiquement à partir du nom à la création.',
                    'inactive_hidden' => 'Les produits inactifs sont masqués de la vitrine.',
                    'sort_order' => 'Les nombres les plus faibles apparaissent en premier.',
                    'banner_upload' => 'Téléversez une image de bannière pour le héros produit (max 5 Mo). Recommandé : 1920x1080.',
                    'screenshots_upload' => 'Téléversez des captures produit (max 5 Mo chacune). Glissez pour réorganiser.',
                ],
                'placeholders' => [
                    'short_description' => 'Brève description du produit...',
                    'long_description' => 'Description détaillée du produit...',
                ],
            ],
            'table' => [
                'packages' => 'Packs',
                'filters' => [
                    'active_only' => 'Actifs uniquement',
                    'inactive_only' => 'Inactifs uniquement',
                ],
            ],
            'relation_packages' => [
                'title' => 'Packs',
                'empty' => [
                    'heading' => 'Aucun pack',
                    'description' => 'Créez des packs avec des niveaux de prix pour ce produit.',
                ],
            ],
            'relation_releases' => [
                'title' => 'Versions',
                'fields' => [
                    'version' => 'Version',
                    'release_notes' => 'Notes de version',
                    'zip' => 'ZIP de version',
                ],
                'columns' => [
                    'zip_file' => 'Fichier ZIP',
                ],
                'empty' => [
                    'heading' => 'Aucune version',
                    'description' => 'Créez une version et téléversez un package ZIP pour ce produit.',
                ],
            ],
            'packages' => [
                'navigation_label' => 'Packs',
                'model_label' => 'Pack',
                'plural_model_label' => 'Packs',
                'form' => [
                    'sections' => [
                        'package_information' => 'Informations du pack',
                        'pricing' => 'Tarification',
                        'features' => 'Fonctionnalités',
                    ],
                    'fields' => [
                        'features' => 'Fonctionnalités',
                        'stripe_monthly_price_id' => 'ID prix mensuel Stripe',
                        'stripe_yearly_price_id' => 'ID prix annuel Stripe',
                    ],
                    'help' => [
                        'slug' => 'Identifiant compatible URL. Généré automatiquement à partir du nom à la création.',
                        'inactive_hidden' => 'Les packs inactifs sont masqués de la vitrine.',
                        'unlimited_domains' => 'Laissez vide pour des domaines illimités.',
                        'monthly_price' => 'Prix de l’abonnement mensuel.',
                        'yearly_price' => 'Prix de l’abonnement annuel (généralement réduit).',
                        'stripe_monthly_price_id' => 'ID prix Stripe pour la facturation mensuelle.',
                        'stripe_yearly_price_id' => 'ID prix Stripe pour la facturation annuelle.',
                        'features' => 'Listez les fonctionnalités incluses dans ce pack. Appuyez sur Entrée après chaque fonctionnalité.',
                    ],
                    'placeholders' => [
                        'description' => 'Brève description de ce niveau de pack...',
                        'unlimited' => 'Illimité',
                        'amount' => '0.00',
                        'stripe_price' => 'price_...',
                        'add_feature' => 'Ajouter une fonctionnalité...',
                    ],
                ],
            ],
        ],

        'nalda_csv_uploads' => [
            'model_label' => 'Import CSV Nalda',
            'plural_model_label' => 'Imports CSV Nalda',
            'navigation_label' => 'Imports CSV Nalda',
            'form' => [
                'sections' => [
                    'upload_details' => 'Détails du téléversement',
                    'sftp_configuration' => 'Configuration SFTP',
                ],
                'fields' => [
                    'csv_file' => 'Fichier CSV',
                    'sftp_host' => 'Hôte SFTP',
                    'sftp_port' => 'Port SFTP',
                    'sftp_username' => 'Nom d’utilisateur SFTP',
                    'sftp_path' => 'Chemin SFTP',
                    'uploaded_at' => 'Téléversé le',
                    'error_message' => 'Message d’erreur',
                ],
                'help' => [
                    'csv_upload' => 'Téléverser un fichier CSV (max 10 Mo)',
                ],
                'status' => [
                    'pending' => 'En attente',
                    'processing' => 'En traitement',
                    'completed' => 'Terminé',
                    'failed' => 'Échec',
                ],
                'placeholders' => [
                    'domain' => 'exemple.fr',
                    'sftp_host' => 'sftp.exemple.fr',
                    'sftp_path' => '/uploads/csv',
                    'uploaded_at_auto' => 'Défini automatiquement à la fin',
                    'error_message' => 'Les détails de l’erreur apparaîtront ici si l’envoi échoue',
                ],
            ],
            'table' => [
                'placeholders' => [
                    'not_uploaded' => 'Non téléversé',
                ],
            ],
        ],

        'license_activations' => [
            'navigation_label' => 'Activations de domaine',
            'model_label' => 'Activation de domaine',
            'plural_model_label' => 'Activations de domaine',
            'table' => [
                'license' => 'Licence',
                'user' => 'Utilisateur',
                'modals' => [
                    'deactivate_domain' => 'Désactiver le domaine',
                    'remove_activation_for' => 'Supprimer l’activation pour :domain ?',
                ],
                'actions' => [
                    'deactivate_selected' => 'Désactiver la sélection',
                ],
            ],
        ],
    ],
];
