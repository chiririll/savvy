<?php

namespace App\Http\Resources;

use App\DTOs\HealthReportData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var HealthReportData $report */
        $report = $this->resource;

        $payload = [
            'status' => $report->status,
            'releaseId' => config('app.version'),
        ];

        if ($report->checks !== []) {
            $payload['checks'] = $this->checks($report->checks);
        }

        return $payload;
    }

    public function withResponse(Request $request, JsonResponse $response): void
    {
        /** @var HealthReportData $report */
        $report = $this->resource;

        $response->setStatusCode($report->isPassing() ? 200 : 503);
        $response->headers->set('Content-Type', 'application/health+json');
    }

    /**
     * @param  list<array<string, mixed>>  $checks
     */
    private function checks(array $checks): array
    {
        $now = now()->toRfc3339String();
        $mapped = [];

        foreach ($checks as $check) {
            $entry = [
                'componentType' => $check['type'],
                'status' => $check['ok'] ? 'pass' : 'fail',
                'time' => $now,
            ];

            if (isset($check['observedValue'])) {
                $entry['observedValue'] = $check['observedValue'];
                $entry['observedUnit'] = $check['observedUnit'];
            }

            if (isset($check['output'])) {
                $entry['output'] = $check['output'];
            }

            $mapped["{$check['component']}:{$check['measurement']}"] = [$entry];
        }

        return $mapped;
    }
}
