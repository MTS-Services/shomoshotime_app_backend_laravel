<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function getAllSubscriptions(string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Subscription::orderBy($orderBy, $order)->active()->latest();

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
}
