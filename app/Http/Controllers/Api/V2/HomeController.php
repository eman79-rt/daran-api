<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\HomeResource;
use App\Services\HomeService;

class HomeController extends Controller
{
    public function __invoke(HomeService $home): HomeResource
    {
        return new HomeResource($home->data());
    }
}
