<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserSubscriptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HandleSubscriptionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        try {
            $now = Carbon::now();
            $processedCount = 0;
            $deactivatedCount = 0;
            $usersUpdated = [];

            DB::transaction(function () use ($now, &$processedCount, &$deactivatedCount, &$usersUpdated) {
                
                // Find all active subscriptions that have expired
                $expiredSubscriptions = UserSubscriptions::where('is_active', true)
                    ->where('ends_at', '<=', $now)
                    ->with('user')
                    ->get();

                foreach ($expiredSubscriptions as $subscription) {
                    // Deactivate expired subscription
                    $subscription->update([
                        'is_active' => false,
                        'updated_by' => null,
                    ]);

                    $deactivatedCount++;
                    $processedCount++;

                    if (!in_array($subscription->user_id, $usersUpdated)) {
                        $usersUpdated[] = $subscription->user_id;
                    }
                }

                // Update user is_premium status for all affected users
                foreach ($usersUpdated as $userId) {
                    $this->updateUserPremiumStatus($userId, $now);
                }

                // Sync user premium status
                $this->syncUserPremiumStatus($now);
            });

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function updateUserPremiumStatus(int $userId, Carbon $now): void
    {
        $hasActiveSubscription = UserSubscriptions::where('user_id', $userId)
            ->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->exists();

        $user = User::find($userId);
        
        if (!$user) {
            return;
        }

        $oldStatus = $user->is_premium;
        $newStatus = $hasActiveSubscription;

        if ($oldStatus !== $newStatus) {
            $user->update(['is_premium' => $newStatus]);
        }
    }

    private function syncUserPremiumStatus(Carbon $now): void
    {
        // Users with active subscriptions but is_premium = false
        $usersWithActiveSubscriptions = UserSubscriptions::where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->whereHas('user', function ($query) {
                $query->where('is_premium', false);
            })
            ->pluck('user_id')
            ->unique();

        if ($usersWithActiveSubscriptions->isNotEmpty()) {
            User::whereIn('id', $usersWithActiveSubscriptions)
                ->update(['is_premium' => true]);
        }

        // Users with is_premium = true but no active subscriptions
        $usersWithoutActiveSubscriptions = User::where('is_premium', true)
            ->whereDoesntHave('subscriptions', function ($query) use ($now) {
                $query->where('is_active', true)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>', $now);
            })
            ->pluck('id');

        if ($usersWithoutActiveSubscriptions->isNotEmpty()) {
            User::whereIn('id', $usersWithoutActiveSubscriptions)
                ->update(['is_premium' => false]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        // Handle job failure
    }
}