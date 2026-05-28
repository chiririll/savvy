<?php

namespace App\Services\Health\Checks;

use App\Services\Health\HealthCheck;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseConnectivityCheck implements HealthCheck
{
    public function run(): array
    {
        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            return [
                'component' => 'sqlite',
                'measurement' => 'connectivity',
                'type' => 'datastore',
                'ok' => false,
                'output' => $e->getMessage(),
            ];
        }

        return [
            'component' => 'sqlite',
            'measurement' => 'connectivity',
            'type' => 'datastore',
            'ok' => true,
        ];
    }
}
