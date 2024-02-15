<?php

namespace Delta4op\Laravel\Tracker\DB\Models;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\AppEntryEB;
use Delta4op\Laravel\Tracker\Support\BatchIdGenerator;
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
class Entry extends BaseModel
{
    use HasTimestamps;

    protected $table = 'entries';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Entry $entry) {

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
