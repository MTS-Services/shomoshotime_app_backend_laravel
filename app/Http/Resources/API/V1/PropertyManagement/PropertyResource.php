<?php

namespace App\Http\Resources\API\V1\PropertyManagement;

use App\Http\Resources\API\V1\AreaResource;
use App\Http\Resources\API\V1\UserManagement\UserResource;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusData = [];
        if (Auth::check()) {
            $statusData = [
                Property::STATUS_PENDING . ': ' . 'Pending',
                Property::STATUS_OPEN . ': ' . 'Open',
                Property::STATUS_ARCHIVE . ': ' . 'Archived',
                Property::STATUS_SOLD . ': ' . 'Sold',
                Property::STATUS_DELETED . ': ' . 'Deleted',
                Property::STATUS_EXPIRED . ': ' . 'Expired',
            ];
        } else {
            $statusData = [
                Property::STATUS_OPEN . ': ' . 'Open',
                Property::STATUS_ARCHIVE . ': ' . 'Archived',
                Property::STATUS_SOLD . ': ' . 'Sold',
            ];
        }

        return array_merge([
            'id' => $this->id ?? null,

            'user_id' => $this->user_id ?? null,
            'category_id' => $this->category?->id ?? null,
            'property_type_id' => $this->propertyType?->id ?? null,
            'area_id' => $this->area?->id ?? null,
            'title' => $this->title ?? 'N/A',
            'slug' => $this->slug ?? 'N/A',
            'description' => $this->description ?? 'N/A',
            'price' => $this->price ?? '0.00',
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_data' => "",
            'views' => $this->whenLoaded('views', fn() => $this->views->count()),
            'is_featured' => $this->is_featured ?? false,
            'expires_at' => $this->expires_at ?? 'N/A',
            'renew_count' => $this->renew_count ?? 0,
            'renew_at' => $this->renew_at ?? 'N/A',
            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
            'primary_image' => $this->whenLoaded('primaryImage') ? new PropertyImageResource($this->primaryImage) : 'N/A',
            'images' => $this->whenLoaded('nonPrimaryImages') ? PropertyImageResource::collection($this->nonPrimaryImages) : [],
            'category' => $this->whenLoaded('category') ? new CategoryResource($this->category) : 'N/A',
            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : 'N/A',
            'property_type' => $this->whenLoaded('propertyType') ? new PropertyTypeResource($this->propertyType) : 'N/A',
            'area' => $this->whenLoaded('area') ? new AreaResource($this->area) : 'N/A',
        ], [
            'status_data' => $statusData,
        ]);
    }
}
