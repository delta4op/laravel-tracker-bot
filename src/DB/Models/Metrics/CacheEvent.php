<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\CacheEventEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\CacheEventER;
use Delta4op\Laravel\Tracker\Enums\CacheEventType;

/**
 * @property ?CacheEventType $type
 * @property ?string $key
 * @property ?string $value
 * @property ?float $expiration
 *
 * @method static CacheEventEB query()
 */
class CacheEvent extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'cache_events';

    protected $casts = [
        'type' => CacheEventType::class,
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->key ?? '') .
            ($this->value ?? '')
        );
    }

    /**
     * @param $query
     * @return CacheEventEB
     */
    public function newEloquentBuilder($query): CacheEventEB
    {
        return new CacheEventEB($query);
    }

    /**
     * @return CacheEventER
     */
    public static function repository(): CacheEventER
    {
        return new CacheEventER;
    }
}
