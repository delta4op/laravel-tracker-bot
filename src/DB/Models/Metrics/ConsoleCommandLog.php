<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\ConsoleCommandEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\ConsoleCommandER;

/**
 * @property ?string $command
 * @property ?string $command_class
 * @property ?int $exitCode
 * @property ?array $arguments
 * @property ?array $options
 *
 * @method static ConsoleCommandEB query()
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

    /**
     * @param $query
     * @return ConsoleCommandEB
     */
    public function newEloquentBuilder($query): ConsoleCommandEB
    {
        return new ConsoleCommandEB($query);
    }

    /**
     * @return ConsoleCommandER
     */
    public static function repository(): ConsoleCommandER
    {
        return new ConsoleCommandER;
    }
}
