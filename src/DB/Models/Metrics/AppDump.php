<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

/**
 * @property ?string $content
 */
class AppDump extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'app_dumps';

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5($this->content ?? '');
    }
}
