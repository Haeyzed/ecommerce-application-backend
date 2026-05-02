<?php

namespace App\Console\Commands\Central;

use App\Enums\Central\SubscriptionStatus;
use App\Models\Central\Subscription;
use App\Notifications\Central\DynamicTemplateNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

#[Signature('app:notify-expiring-subscriptions {--days=3 : Number of days before expiration to notify}')]
#[Description('Sends plan-expiring notifications to tenants whose subscriptions expire within the given window.')]
class NotifyExpiringSubscriptions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $threshold = now()->addDays($days);

        $subscriptions = Subscription::query()
            ->with(['tenant', 'plan'])
            ->whereIn('status', [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::TRIAL,
            ])
            ->where(function ($query) use ($threshold) {
                $query->where('current_period_end', '<=', $threshold)
                    ->orWhere('trial_ends_at', '<=', $threshold);
            })
            ->where(function ($query) {
                $query->where('current_period_end', '>', now())
                    ->orWhere('trial_ends_at', '>', now());
            })
            ->get();

        $notifiedCount = 0;

        foreach ($subscriptions as $subscription) {
            $tenant = $subscription->tenant;
            $plan = $subscription->plan;

            if (! $tenant || ! $plan || empty($tenant->owner_email)) {
                continue;
            }

            $expirationDate = $subscription->trial_ends_at ?? $subscription->current_period_end;

            Notification::route('mail', $tenant->owner_email)
                ->notify(new DynamicTemplateNotification(
                    event: 'plan_expiring',
                    templateData: [
                        'tenant_name' => $tenant->name,
                        'plan_name' => $plan->name,
                        'expiration_date' => $expirationDate?->format('F j, Y') ?? 'N/A',
                    ]
                ));

            $notifiedCount++;
        }

        $this->info("Sent plan-expiring notifications to {$notifiedCount} tenant(s).");

        return self::SUCCESS;
    }
}
