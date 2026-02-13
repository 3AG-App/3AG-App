<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SubscriptionRenewalReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Cashier\Subscription;

class SendSubscriptionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-subscription-reminders
                            {--days=3 : Days before renewal to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription renewal reminder emails to users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysBeforeRenewal = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($daysBeforeRenewal);

        $this->info("Looking for subscriptions renewing around {$targetDate->toDateString()}...");

        $subscriptions = Subscription::query()
            ->whereNull('ends_at')
            ->whereNotNull('stripe_status')
            ->active()
            ->get();

        $sentCount = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if (! $user instanceof User) {
                continue;
            }

            $preference = $user->preference;

            if ($preference && (! $preference->notifications_enabled || ! $preference->subscription_reminders)) {
                $this->line("Skipping {$user->email} - notifications disabled");

                continue;
            }

            try {
                $stripeSubscription = $subscription->asStripeSubscription();
                $periodEndTimestamp = $stripeSubscription->current_period_end ?? null;

                if ($periodEndTimestamp === null) {
                    $this->line("Skipping subscription {$subscription->id} - missing period end");

                    continue;
                }

                $periodEnd = Carbon::createFromTimestamp($periodEndTimestamp);

                $daysUntilRenewal = Carbon::now()->diffInDays($periodEnd, false);

                if ($daysUntilRenewal >= 0 && $daysUntilRenewal <= $daysBeforeRenewal) {
                    $user->notify(new SubscriptionRenewalReminder($subscription, (int) $daysUntilRenewal));
                    $sentCount++;
                    $this->line("Sent reminder to {$user->email} (renews in {$daysUntilRenewal} days)");
                }
            } catch (\Throwable $e) {
                $this->error("Failed to process subscription {$subscription->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$sentCount} subscription renewal reminder(s).");

        return self::SUCCESS;
    }
}
