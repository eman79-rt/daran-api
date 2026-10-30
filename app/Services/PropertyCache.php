<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

class PropertyCache
{
    private function store()
    {
        return Cache::store();
    }

    private function rememberTagged(array $tags, string $key, $ttl, callable $loader): mixed
    {
        $store = $this->store();

        return $store instanceof TaggableStore
            ? $store->tags($tags)->remember($key, $ttl, $loader)
            : $loader();
    }

    public function forgetProperty(Property $property, ?int $oldProvinceId = null): void
    {
        $store = $this->store();
        if ($store instanceof TaggableStore) {
            $provinceIds = array_unique(array_filter([(int) $property->province_id, $oldProvinceId]));
            foreach ($provinceIds as $provinceId) {
                $store->tags(['province:'.$provinceId])->flush();
            }
            $store->tags(['properties'])->flush();
        } else {
            $store->forget('featured:10');
        }
        $store->forget('property:'.$property->id);
    }

    public function detail(int $id, callable $loader): mixed
    {
        return $this->store()->remember('property:'.$id, now()->addMinutes(5), $loader);
    }

    public function featured(callable $loader): mixed
    {
        return $this->rememberTagged(['properties'], 'featured:10', now()->addMinutes(2), $loader);
    }

    public function provinceCount(int $id, callable $loader): mixed
    {
        return $this->rememberTagged(['province:'.$id], 'count:'.$id, now()->addMinutes(2), $loader);
    }
}
