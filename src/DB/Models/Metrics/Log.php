<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\LogEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\LogER;

/**
 * @property ?string $level
 * @property ?string $message
 * @property ?array $context
 *
 * @method static LogEB query()
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

    /**
     * @param $query
     * @return LogEB
     */
    public function newEloquentBuilder($query): LogEB
    {
        return new LogEB($query);
    }

    /**
     * @return LogER
     */
    public static function repository(): LogER
    {
        return new LogER;
    }
}
