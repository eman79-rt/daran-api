<?php

namespace App\Services;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\Province;

class HomeService
{
    public function __construct(private PropertyCache $cache) {}

    public function data(): array
    {
        $featured = $this->cache->featured(fn () => Property::query()->where('status', PropertyStatus::Available)->whereNotNull('approved_at')->where('is_featured', true)->with(['province', 'agency.logo', 'media'])->orderByDesc('id')->limit(10)->get());
        $counts = Province::query()->get(['id', 'name'])->mapWithKeys(fn ($province) => [$province->id => $this->cache->provinceCount($province->id, fn () => Property::query()->where('province_id', $province->id)->where('status', PropertyStatus::Available)->whereNotNull('approved_at')->count())]);

        return ['featured' => $featured, 'available_by_province' => $counts];
    }
}
