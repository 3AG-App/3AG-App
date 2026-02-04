<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiryAlert extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public License $license,
        public int $daysUntilExpiry
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
        $productName = $this->license->product?->name ?? 'your product';
        $expiryDate = $this->license->expires_at->format('F j, Y');

        $message = (new MailMessage)
            ->subject("License Expiring Soon: {$productName}")
            ->greeting("Hello {$notifiable->name}!");

        if ($this->daysUntilExpiry === 0) {
            $message->line("Your license for **{$productName}** expires today!");
        } elseif ($this->daysUntilExpiry === 1) {
            $message->line("Your license for **{$productName}** expires tomorrow on {$expiryDate}.");
        } else {
            $message->line("Your license for **{$productName}** will expire in {$this->daysUntilExpiry} days on {$expiryDate}.");
        }

        return $message
            ->line('To continue using this product without interruption, please renew your subscription.')
            ->action('Renew Now', url('/dashboard/billing'))
            ->line('Thank you for your continued support!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'license_id' => $this->license->id,
            'product_name' => $this->license->product?->name,
            'days_until_expiry' => $this->daysUntilExpiry,
            'expires_at' => $this->license->expires_at->toISOString(),
        ];
    }
}
