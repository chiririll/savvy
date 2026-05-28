<?php

namespace App\Services\Health;

interface HealthCheck
{
    /**
     * @return array<string, mixed>
     */
    public function run(): array;
}
