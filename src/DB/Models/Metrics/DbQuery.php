<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

/**
 * @property ?string $connection
 * @property ?string $query
 * @property ?float $time
 * @property ?string $file
 * @property ?boolean $is_internal_file
 * @property ?int $line
 * @property ?array $bindings
 */
class DbQuery extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'db_queries';

    protected $casts = [
        'bindings' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5($this->query ?? '');
    }
}
