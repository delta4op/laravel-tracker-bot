<?php

namespace Delta4op\Laravel\TrackerBot\DB\Concerns;

use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\BaseModel;
use Delta4op\Laravel\TrackerBot\DB\Models\Environment\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest;
use Delta4op\Laravel\TrackerBot\DB\Models\Source\Source;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

/**
 * @property ?string $id
 * @property ?string $uuid
 * @property ?string $family_hash
 *
 * @property ?int $entry_id
 * @property ?string $entry_uuid
 * @property ?string $batch_id
 * @property ?int $source_id
 * @property ?int $env_id
 *
 * @property ?AppEntry $appEntry
 * @property ?Source $source
 * @property ?Environment $env
 */
abstract class MetricsModel extends BaseModel
{
    use HasTimestamps;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (AppRequest $appRequest) {
            if(!isset($appRequest->uuid)) {
                $appRequest->uuid = Str::orderedUuid()->toString();
            }

            if(!isset($appRequest->family_hash)) {
                $appRequest->setFamilyHash();
            }
        });
    }

    public function appEntry(): MorphOne
    {
        return $this->morphOne(AppEntry::class, 'entry_id', 'model_key', 'model_id');
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
        return $this->belongsTo(Source::class, 'env_id', 'id');
    }

    /**
     * @return $this
     */
    public function setFamilyHash(): static
    {
        $this->family_hash = $this->calculateFamilyHash();
        return $this;
    }

    /**
     * @return string
     */
    public abstract function calculateFamilyHash(): string;
}
