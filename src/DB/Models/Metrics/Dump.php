<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;

/**
 * @property ?string $content
 */
class Dump extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'dumps';

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5($this->content ?? '');
    }
}
