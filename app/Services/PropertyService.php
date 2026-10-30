<?php

namespace App\Services;

use App\Contracts\PropertyRepository;
use App\Enums\PropertyStatus;
use App\Exceptions\ApiException;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public function __construct(private PropertyRepository $properties, private PropertyCache $cache) {}

    public function listing(Request $r, bool $cursor = false)
    {
        return $this->properties->paginate($r, $cursor);
    }

    public function detail(Property $property): Property
    {
        return $this->cache->detail($property->id, function () use ($property) {
            return $this->properties->findVisible($property);
        });
    }

    public function create(array $data, Request $r): Property
    {
        $agencyId = $r->user()?->agency_id;
        if (! $agencyId) {
            throw new ApiException('حساب المستخدم غير مرتبط بمكتب عقاري');
        }

        return DB::transaction(function () use ($data, $agencyId) {
            $data['agency_id'] = $agencyId;
            $data['status'] = PropertyStatus::Pending;
            $data['approved_at'] = null;
            $images = $data['images'] ?? [];
            unset($data['images']);
            $p = Property::create($data);
            foreach ($images as $i => $path) {
                $p->media()->create(['path' => $path, 'collection' => 'property', 'sort_order' => $i]);
            }

return $p->load(['province', 'agency.logo', 'media']);
        });
    }

    public function update(Property $property, array $data, Request $r): Property
    {
        $this->assertOwner($property, $r);

        return DB::transaction(function () use ($property, $data) {
            $images = $data['images'] ?? null;
            unset($data['images']);
            $property->fill($data)->save();
            if ($images !== null) {
                $property->media()->delete();
                foreach ($images as $i => $path) {
                    $property->media()->create(['path' => $path, 'collection' => 'property', 'sort_order' => $i]);
                }
            }

return $property->fresh(['province', 'agency.logo', 'media']);
        });
    }

    public function delete(Property $property, Request $r): void
    {
        $this->assertOwner($property, $r);
        $property->delete();
    }

    private function assertOwner(Property $p, Request $r): void
    {
        if (! $r->user() || $p->agency_id !== $r->user()->agency_id) {
            abort(403, 'لا تملك صلاحية هذا العقار');
        }
    }
}
