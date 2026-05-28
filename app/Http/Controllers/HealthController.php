<?php

namespace App\Http\Controllers;

use App\Http\Resources\HealthResource;
use App\Services\HealthService;

class HealthController extends Controller
{
    public function live(HealthService $health): HealthResource
    {
        return new HealthResource($health->liveness());
    }

    public function ready(HealthService $health): HealthResource
    {
        return new HealthResource($health->readiness());
    }
}
