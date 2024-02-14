<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;

/**
 * @property ?string $name
 * @property ?array $payload
 * @property ?array $listeners
 * @property ?mixed $broadcast
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
}
