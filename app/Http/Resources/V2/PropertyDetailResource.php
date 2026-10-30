<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return (new PropertyCardResource($this->resource))->resolve($request) + ['description' => $this->description, 'address' => $this->address, 'bedrooms' => $this->bedrooms, 'bathrooms' => $this->bathrooms, 'area_sqm' => (float) $this->area_sqm, 'status' => $this->status->value, 'images' => $this->whenLoaded('media', fn () => $this->media->pluck('path')->values()), 'contact_phone' => $request->user() ? $this->owner_phone : null, 'created_at' => $this->created_at?->toISOString()];
    }
}
