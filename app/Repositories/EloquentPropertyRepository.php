<?php

namespace App\Repositories;

use App\Contracts\PropertyRepository;
use App\Models\Property;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class EloquentPropertyRepository implements PropertyRepository
{
    public function paginate(Request $request, bool $cursor = false): LengthAwarePaginator|CursorPaginator
    {
        $user = $request->user();
        $q = Property::query()->with(['province', 'agency.logo', 'media']);
        if ($user?->agency_id) {
            $q->withoutGlobalScope('publicly_visible')->where('agency_id', $user->agency_id);
        }
        $q->inProvince($request->integer('province_id') ?: null)->ofOfferType($request->input('offer_type'))
            ->when($request->input('type'), fn ($b, $v) => $b->where('type', $v))
            ->priceRange($request->input('price_min'), $request->input('price_max'));
        if ($cursor) {
            return $q->orderByDesc('created_at')->orderByDesc('id')->cursorPaginate(min($request->integer('per_page', 15), 50));
        }

        return $q->orderByDesc('created_at')->orderByDesc('id')->paginate(min($request->integer('per_page', 15), 100));
    }

    public function findVisible(Property $property): Property
    {
        $property->loadMissing(['province', 'agency.logo', 'media']);

        return $property;
    }
}
