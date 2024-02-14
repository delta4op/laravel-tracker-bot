<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

/**
 * @property ?string $level
 * @property ?string $message
 * @property ?array $context
 */
class Log extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'logs';

    protected $casts = [
        'context' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->level ?? '') .
            ($this->message ?? '')
        );
    }
}
