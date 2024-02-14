<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

/**
 * @property ?string $command
 * @property ?string $description
 * @property ?string $expression
 * @property ?string $timezone
 * @property ?string $output
 * @property ?array $config
 */
class ConsoleSchedule extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'console_schedules';

    protected $casts = [
        'config' => 'array'
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->command ?? '') .
            ($this->expression ?? '')
        );
    }
}
