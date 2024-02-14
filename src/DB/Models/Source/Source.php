<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Source;

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
class Source extends Model
{
    use HasTimestamps;

    protected $table = 'sources';

    protected $fillable = ['symbol'];

    /**
     * @return HasMany
     */
    public function appEntries(): HasMany
    {
        return $this->hasMany(AppEntry::class, 'source_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function appRequests(): HasMany
    {
        return $this->hasMany(AppRequest::class, 'source_id', 'id');
    }
}
