<?php

namespace Delta4op\Laravel\Tracker\DB\Models;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ?int $id
 * @property ?string $symbol
 *
 * @property Collection<Entry> $appEntries
 * @property Collection<AppRequest> $appRequests
 */
class Environment extends BaseModel
{
    use HasTimestamps;

    protected $table = 'environments';

    protected $fillable = ['symbol'];

    /**
     * @return HasMany
     */
    public function appEntries(): HasMany
    {
        return $this->hasMany(Entry::class, 'env_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function appRequests(): HasMany
    {
        return $this->hasMany(AppRequest::class, 'env_id', 'id');
    }
}
