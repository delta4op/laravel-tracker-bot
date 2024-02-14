<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

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
 */
class AppEvent extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'app_errors';

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
