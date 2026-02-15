<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscriptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function getAllSubs(string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Subscription::orderBy($orderBy, $order)->active()->latest();

    }

    public function getAllSubscriptions(string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Subscription::orderBy($orderBy, $order)->latest();

    }

    public function findData($id): ?Subscription
    {
        $model = Subscription::findOrFail($id);
        if (! $model) {
            throw new \Exception('Data not found');
        }

        return $model;
    }

    public function createSubscription(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();

            return Subscription::create($data);
        });
    }

    public function createUserSubscription(array $data)
    {
        return DB::transaction(function () use ($data) {

            $data['created_by'] = Auth::id();

            UserSubscriptions::where('user_id', $data['user_id'])
                ->update(['is_active' => false]);

            $data['is_active'] = true;
            $data['is_cancel'] = false;

            return UserSubscriptions::create($data);
        });
    }

    public function cancelUserSubscription(int $userId): ?UserSubscriptions
    {
        return DB::transaction(function () use ($userId) {
            $subscription = UserSubscriptions::where('user_id', $userId)
                ->where('is_active', true)
                ->first();

            if (! $subscription) {
                return null;
            }

            $subscription->update([
                'is_active' => false,
                'is_cancel' => true,
                'ends_at' => $subscription->ends_at ?? now(),
                'updated_by' => Auth::id(),
            ]);

            User::whereKey($userId)->update(['is_premium' => false]);

            return $subscription->fresh(['user', 'subscription']);
        });
    }

    public function updateSubscription(Subscription $subscription, array $data): Subscription
    {
        return DB::transaction(function () use ($subscription, $data) {

            $data['updated_by'] = Auth::id();
            $subscription->update($data);

            return $subscription;
        });
    }

    public function deleteSubscription(Subscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $subscription->forceDelete();
        });
    }
}
