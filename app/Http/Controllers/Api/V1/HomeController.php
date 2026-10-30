<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\HomeResource;
use App\Services\HomeService;

class HomeController extends Controller
{
    public function __invoke(HomeService $home): HomeResource
    {
        return new HomeResource($home->data());
    }
}
