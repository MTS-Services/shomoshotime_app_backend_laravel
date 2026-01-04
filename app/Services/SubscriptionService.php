<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionService
{
     public function getAllSubscriptions( string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Subscription::orderBy($orderBy, $order)->active()->latest();
        
    }
}
