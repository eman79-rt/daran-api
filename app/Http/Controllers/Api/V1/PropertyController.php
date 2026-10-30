<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyIndexRequest;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\V1\MessageResource;
use App\Http\Resources\V1\PropertyCardResource;
use App\Http\Resources\V1\PropertyDetailResource;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyController extends Controller
{
    public function __construct(private PropertyService $service) {}

    public function index(PropertyIndexRequest $request): AnonymousResourceCollection
    {
        $p = $this->service->listing($request, $request->boolean('infinite'));

        return PropertyCardResource::collection($p);
    }

    public function show(Property $property): PropertyDetailResource
    {
        return new PropertyDetailResource($this->service->detail($property));
    }

    public function store(StorePropertyRequest $request): PropertyDetailResource
    {
        return new PropertyDetailResource($this->service->create($request->validated(), $request));
    }

    public function update(UpdatePropertyRequest $request, Property $property): PropertyDetailResource
    {
        return new PropertyDetailResource($this->service->update($property, $request->validated(), $request));
    }

    public function destroy(Request $request, Property $property): MessageResource
    {
        $this->service->delete($property, $request);

        return new MessageResource(['message' => 'تم حذف العقار']);
    }
}
