<?php

namespace App\Console\Commands;

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Notifications\LicenseExpiryAlert;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLicenseExpiryAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-license-expiry-alerts
                            {--days=7 : Days before expiry to send alert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send license expiry alert emails to users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysBeforeExpiry = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($daysBeforeExpiry);

        $this->info("Looking for licenses expiring within {$daysBeforeExpiry} days...");

        $licenses = License::query()
            ->with(['user', 'user.preference', 'product'])
            ->where('status', LicenseStatus::Active)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [Carbon::now(), $targetDate])
            ->get();

        $sentCount = 0;

        foreach ($licenses as $license) {
            $user = $license->user;

            if (! $user) {
                continue;
            }

            $preference = $user->preference;

            if ($preference && (! $preference->notifications_enabled || ! $preference->license_expiry_alerts)) {
                $this->line("Skipping {$user->email} - notifications disabled");

                continue;
            }

            $daysUntilExpiry = Carbon::now()->diffInDays($license->expires_at, false);

            if ($daysUntilExpiry < 0) {
                continue;
            }

            $productName = $license->product?->name ?? 'License';
            $user->notify(new LicenseExpiryAlert($license, (int) $daysUntilExpiry));
            $sentCount++;
            $this->line("Sent alert to {$user->email} for {$productName} (expires in {$daysUntilExpiry} days)");
        }

        $this->info("Sent {$sentCount} license expiry alert(s).");

        return self::SUCCESS;
    }
}
