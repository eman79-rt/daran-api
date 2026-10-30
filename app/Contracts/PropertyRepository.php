<?php

namespace App\Contracts;

use App\Models\Property;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface PropertyRepository
{
    public function paginate(Request $request, bool $cursor = false): LengthAwarePaginator|CursorPaginator;

    public function findVisible(Property $property): Property;
}
