<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\ErrorEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\ErrorER;

/**
 * @property ?string $class
 * @property ?string $file
 * @property ?boolean $is_internal_file
 * @property ?string $code
 * @property ?int $line
 * @property ?string $message
 * @property ?array $context
 * @property ?array $trace
 * @property ?array $linePreview
 *
 * @method static ErrorEB query()
 */
class Error extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'errors';

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
     * @return ErrorEB
     */
    public function newEloquentBuilder($query): ErrorEB
    {
        return new ErrorEB($query);
    }

    /**
     * @return ErrorER
     */
    public static function repository(): ErrorER
    {
        return new ErrorER;
    }
}
