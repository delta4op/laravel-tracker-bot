<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\ConsoleScheduleEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\ConsoleScheduleER;

/**
 * @property ?string $command
 * @property ?string $description
 * @property ?string $expression
 * @property ?string $timezone
 * @property ?string $output
 * @property ?array $config
 *
 * @method static ConsoleScheduleEB query()
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

    /**
     * @param $query
     * @return ConsoleScheduleEB
     */
    public function newEloquentBuilder($query): ConsoleScheduleEB
    {
        return new ConsoleScheduleEB($query);
    }

    /**
     * @return ConsoleScheduleER
     */
    public static function repository(): ConsoleScheduleER
    {
        return new ConsoleScheduleER;
    }
}
