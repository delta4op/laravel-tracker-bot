<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects;

use Delta4op\Laravel\TrackerBot\Enums\CacheEventType;

/**
 * @property ?CacheEventType $type
 * @property ?string $key
 * @property ?string $value
 * @property ?float $expiration
 */
class CacheObject extends EntryObject
{
    protected $casts = [
        'type' => CacheEventType::class,
    ];
}
