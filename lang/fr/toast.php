<?php

declare(strict_types=1);

return [
    'email_verified_successfully' => 'E-mail vérifié avec succès !',
    'verification_link_sent' => 'Lien de vérification envoyé !',

    'auth' => [
        'welcome_back' => 'Bon retour !',
        'logged_out' => 'Vous avez été déconnecté.',
        'account_created' => 'Compte créé avec succès. Bienvenue !',
    ],

    'dashboard' => [
        'profile_updated' => [
            'message' => 'Profil mis à jour',
            'description' => 'Les informations de votre profil ont été enregistrées.',
        ],
        'password_updated' => [
            'message' => 'Mot de passe mis à jour',
            'description' => 'Votre mot de passe a été modifié avec succès.',
        ],
        'settings_saved' => [
            'message' => 'Paramètres enregistrés',
            'description' => 'Vos préférences ont été mises à jour.',
        ],
        'subscription_cancelled' => [
            'message' => 'Abonnement annulé',
            'description' => 'Votre abonnement restera actif jusqu’à la fin de la période de facturation.',
        ],
        'subscription_cancel_failed' => [
            'message' => 'Impossible d’annuler l’abonnement',
        ],
        'subscription_resumed' => [
            'message' => 'Abonnement repris',
            'description' => 'Votre abonnement a été réactivé.',
        ],
        'subscription_resume_failed' => [
            'message' => 'Impossible de reprendre l’abonnement',
        ],
        'domains_deactivated' => [
            'message' => 'Domaines désactivés',
            'description' => ':count domaine(s) ont été désactivé(s) pour cette licence.',
        ],
        'domain_deactivated' => [
            'message' => 'Domaine désactivé',
            'description' => 'La licence a été désactivée pour :domain.',
        ],
    ],

    'product' => [
        'pricing_not_available' => [
            'message' => 'Tarification indisponible',
            'description' => 'Cet intervalle de facturation n’est pas disponible pour ce pack.',
        ],
        'already_subscribed' => [
            'message' => 'Déjà abonné',
            'description' => 'Vous avez déjà un abonnement actif pour ce produit. Utilisez le changement d’offre pour modifier votre plan.',
        ],
        'unable_to_process_subscription' => [
            'message' => 'Impossible de traiter l’abonnement',
            'description' => 'Un problème est survenu avec la configuration des prix. Veuillez contacter le support.',
        ],
        'no_active_subscription' => [
            'message' => 'Aucun abonnement actif',
            'description' => 'Vous n’avez pas d’abonnement actif pour ce produit.',
        ],
        'no_change_needed' => [
            'message' => 'Aucun changement nécessaire',
            'description' => 'Vous êtes déjà sur ce plan.',
        ],
        'cannot_downgrade' => [
            'message' => 'Impossible de rétrograder',
            'description' => 'Vous avez :active domaines actifs mais le nouveau plan n’autorise que :limit. Veuillez d’abord désactiver certains domaines.',
        ],
        'subscription_updated' => [
            'message' => 'Abonnement mis à jour',
            'description' => 'Votre abonnement a été changé vers :plan.',
        ],
        'subscription_update_failed' => [
            'message' => 'Échec de la mise à jour de l’abonnement',
            'description' => 'Une erreur est survenue lors de la mise à jour de votre abonnement. Veuillez réessayer.',
        ],
    ],

    'dev' => [
        'flash_test' => [
            'success' => [
                'message' => 'Opération terminée avec succès !',
                'description' => 'Vos modifications ont été enregistrées.',
            ],
            'error' => [
                'message' => 'Une erreur est survenue !',
                'description' => 'Veuillez réessayer ou contacter le support.',
            ],
            'warning' => [
                'message' => 'Veuillez procéder avec prudence.',
            ],
            'info' => [
                'message' => 'Voici des informations utiles.',
            ],
            'default' => [
                'message' => 'Message de test',
            ],
        ],
    ],
];
