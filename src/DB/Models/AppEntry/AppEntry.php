<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\AppEntry;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\DB\EloquentBuilders\AppEntryEB;
use Delta4op\Laravel\TrackerBot\DB\Models\Source\Source;
use Delta4op\Laravel\TrackerBot\Support\BatchIdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property ?string $_id
 * @property ?string $uuid
 * @property ?string $batchId
 * @property ?int $source_id
 * @property ?int $env_id
 * @property ?string $model_key
 * @property ?string $model_id
 * @property ?string $familyHash
 *
 * @method AppEntryEB query()
 */
class AppEntry extends Model
{
    use HasTimestamps;

    protected $table = 'app_entries';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (AppEntry $entry) {

            $entry->uuid = Str::orderedUuid()->toString();

            $entry->batchId = app(BatchIdGenerator::class)->getUuid();
        });
    }

    /**
     * @return MorphTo
     */
    public function metrics_model(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'model_key', 'model_id');
    }

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function environment(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'env_id', 'id');
    }
}
