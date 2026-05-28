<?php

namespace App\DTOs;

readonly class HealthReportData
{
    /**
     * @param  list<array<string, mixed>>  $checks
     */
    public function __construct(
        public string $status,
        public array $checks = [],
    ) {}

    public static function up(): self
    {
        return new self('pass');
    }

    /**
     * @param  list<array<string, mixed>>  $checks
     */
    public static function fromChecks(array $checks): self
    {
        $passing = array_reduce(
            $checks,
            fn (bool $carry, array $check): bool => $carry && $check['ok'],
            true,
        );

        return new self($passing ? 'pass' : 'fail', $checks);
    }

    public function isPassing(): bool
    {
        return $this->status === 'pass';
    }
}
