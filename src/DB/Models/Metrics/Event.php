<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\EventEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\EventER;

/**
 * @property ?string $name
 * @property ?array $payload
 * @property ?array $listeners
 * @property ?mixed $broadcast
 *
 * @method static EventEB query()
 */
class Event extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'events';

    protected $casts = [
        'context' => 'array',
        'trace' => 'array',
        'linePreview' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->file ?? '') .
            ($this->line ?? '') .
            ($this->message ?? '') .
            ($this->code ?? '')
        );
    }

    /**
     * @param $query
     * @return EventEB
     */
    public function newEloquentBuilder($query): EventEB
    {
        return new EventEB($query);
    }

    /**
     * @return EventER
     */
    public static function repository(): EventER
    {
        return new EventER;
    }
}
