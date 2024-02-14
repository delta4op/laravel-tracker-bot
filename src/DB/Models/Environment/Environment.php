<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Environment;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest\AppRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ?int $id
 * @property ?string $symbol
 *
 * @property Collection<AppEntry> $appEntries
 * @property Collection<AppRequest> $appRequests
 */
class Environment extends Model
{
    use HasTimestamps;

    protected $table = 'environments';

    /**
     * @return HasMany
     */
    public function appEntries(): HasMany
    {
        return $this->hasMany(AppEntry::class, 'env_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function appRequests(): HasMany
    {
        return $this->hasMany(AppRequest::class, 'env_id', 'id');
    }
}
