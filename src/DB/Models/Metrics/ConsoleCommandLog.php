<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

/**
 * @property ?string $command
 * @property ?string $command_class
 * @property ?int $exitCode
 * @property ?array $arguments
 * @property ?array $options
 */
class ConsoleCommandLog extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'console_command_logs';

    protected $casts = [
        'arguments' => 'array',
        'options' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5($this->command ?? '');
    }
}
