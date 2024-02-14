<?php

namespace Delta4op\Laravel\Tracker\DB\Models\objects;

use Delta4op\Laravel\Tracker\Enums\CacheEventType;

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
