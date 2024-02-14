<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\DB\Concerns\MetricsModel;
use Illuminate\Support\Str;

/**
 * @property ?string $class
 * @property ?string $file
 * @property ?string $code
 * @property ?int $line
 * @property ?string $message
 * @property ?array $context
 * @property ?array $trace
 * @property ?array $linePreview
 */
class AppError extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'app_errors';

    protected $casts = [
        'context' => 'array',
        'trace' => 'array',
        'linePreview' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (AppError $error) {
            $error->uuid = Str::orderedUuid()->toString();
            $error->setFamilyHash();
        });
    }

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return 'unset';
    }
}
