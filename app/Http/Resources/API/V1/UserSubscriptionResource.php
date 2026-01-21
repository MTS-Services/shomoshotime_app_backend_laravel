<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'payment_id'    => $this->payment_id,
            'subscription_id' => $this->subscription_id,

            'starts_at'       => $this->starts_at,
            'ends_at'         => $this->ends_at,

            'is_active'       => (bool) $this->is_active,

            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',

          'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),

           'subscription' => $this->whenLoaded('subscription', function () {
                return new SubscriptionResource($this->subscription);
            }),

        ];
    }
}
