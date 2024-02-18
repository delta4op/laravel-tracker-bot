<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\RedisEventEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\RedisEventER;

/**
 * @property ?string $connection
 * @property ?string $command
 * @property ?float $time
 *
 * @method static RedisEventEB query()
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

    /**
     * @param $query
     * @return RedisEventEB
     */
    public function newEloquentBuilder($query): RedisEventEB
    {
        return new RedisEventEB($query);
    }

    /**
     * @return RedisEventER
     */
    public static function repository(): RedisEventER
    {
        return new RedisEventER;
    }
}
