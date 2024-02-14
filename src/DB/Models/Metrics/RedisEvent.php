<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

/**
 * @property ?string $connection
 * @property ?string $command
 * @property ?float $time
 */
class RedisEvent extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'redis_events';

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->connection ?? '') .
            ($this->command ?? '')
        );
    }
}
