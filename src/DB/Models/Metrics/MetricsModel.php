<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\Models\Entry;
use Delta4op\Laravel\Tracker\DB\Models\BaseModel;
use Delta4op\Laravel\Tracker\DB\Models\Environment;
use Delta4op\Laravel\Tracker\DB\Models\Source;
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
 * @property ?Entry $appEntry
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
        static::creating(function (MetricsModel $metricsModel) {
            if(!isset($metricsModel->uuid)) {
                $metricsModel->uuid = Str::orderedUuid()->toString();
            }

            if(!isset($metricsModel->family_hash)) {
                $metricsModel->setFamilyHash();
            }
        });
    }

    public function appEntry(): MorphOne
    {
        return $this->morphOne(Entry::class, 'entry_id', 'model_key', 'model_id');
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
