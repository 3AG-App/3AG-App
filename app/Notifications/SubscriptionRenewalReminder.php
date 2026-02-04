<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Laravel\Cashier\Subscription;

class SubscriptionRenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Subscription $subscription,
        public int $daysUntilRenewal
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $renewalDate = $this->subscription->asStripeSubscription()->current_period_end;
        $formattedDate = date('F j, Y', $renewalDate);

        return (new MailMessage)
            ->subject('Your Subscription Renews Soon')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your subscription will automatically renew in {$this->daysUntilRenewal} days on {$formattedDate}.")
            ->line('If you wish to make any changes to your subscription, please visit your account settings.')
            ->action('Manage Subscription', url('/dashboard/billing'))
            ->line('Thank you for being a valued customer!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'days_until_renewal' => $this->daysUntilRenewal,
        ];
    }
}
