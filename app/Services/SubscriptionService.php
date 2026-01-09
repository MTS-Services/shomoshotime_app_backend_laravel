<?php

namespace App\Services;

use App\Models\Subscription;
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
