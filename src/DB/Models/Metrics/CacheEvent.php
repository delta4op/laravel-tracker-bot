<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\Enums\CacheEventType;

/**
 * @property ?CacheEventType $type
 * @property ?string $key
 * @property ?string $value
 * @property ?float $expiration
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
}
