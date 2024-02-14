<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\DB\EloquentBuilders\AppEntryEB;
use Delta4op\Laravel\TrackerBot\Support\BatchIdGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property ?string $id
 * @property ?string $uuid
 * @property ?string $batchId
 * @property ?int $source_id
 * @property ?int $env_id
 * @property ?string $model_key
 * @property ?string $model_id
 * @property ?string $family_hash
 * @property ?Source $source
 * @property ?Environment $env
 * @method AppEntryEB query()
 */
class AppEntry extends BaseModel
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
    public function env(): BelongsTo
    {
        return $this->belongsTo(Environment::class, 'env_id', 'id');
    }
}
