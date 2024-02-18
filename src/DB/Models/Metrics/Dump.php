<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\DumpEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\DumpER;

/**
 * @property ?string $content
 *
 * @method static DumpEB query()
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

    /**
     * @param $query
     * @return DumpEB
     */
    public function newEloquentBuilder($query): DumpEB
    {
        return new DumpEB($query);
    }

    /**
     * @return DumpER
     */
    public static function repository(): DumpER
    {
        return new DumpER;
    }
}
