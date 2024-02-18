<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\DbQueryEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\DbQueryER;
use Delta4op\Laravel\Tracker\Enums\Database;

/**
 * @property ?string $connection
 * @property ?string $driver
 * @property ?Database $db
 * @property ?string $query
 * @property ?float $time
 * @property ?string $file
 * @property ?boolean $is_internal_file
 * @property ?int $line
 * @property ?array $bindings
 *
 * @method static DbQueryEB query()
 */
class DbQuery extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'db_queries';

    protected $casts = [
        'bindings' => 'array',
        'db' => Database::class,
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5($this->query ?? '');
    }

    /**
     * @param $query
     * @return DbQueryEB
     */
    public function newEloquentBuilder($query): DbQueryEB
    {
        return new DbQueryEB($query);
    }

    /**
     * @return DbQueryER
     */
    public static function repository(): DbQueryER
    {
        return new DbQueryER;
    }
}
