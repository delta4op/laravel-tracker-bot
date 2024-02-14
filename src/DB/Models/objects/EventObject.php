<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\objects;

/**
 * @property ?string $name
 * @property ?array $payload
 * @property ?array $listeners
 * @property ?boolean $broadcast
 */
class EventObject extends EntryObject
{
    protected $casts = [
        'payload' => 'array',
        'listeners' => 'array',
    ];
}
