<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'type' => $this->type, 'offer_type' => $this->offer_type->value, 'price' => ['amount' => (float) $this->price_amount, 'currency' => $this->currency->value], 'province' => $this->whenLoaded('province', fn () => ['id' => $this->province->id, 'name' => $this->province->name]), 'cover_image' => $this->whenLoaded('media', fn () => $this->media->first()?->path), 'agency' => $this->whenLoaded('agency', fn () => ['name' => $this->agency->name, 'logo' => $this->agency->logo?->path])];
    }
}
