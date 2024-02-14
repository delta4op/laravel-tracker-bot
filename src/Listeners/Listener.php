<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Concerns\MetricsModel;
use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry\AppEntry;
use Delta4op\Laravel\TrackerBot\Support\FetchesStackTrace;

abstract class Listener
{
    use FetchesStackTrace;

    protected array $options = [];

    public function __construct()
    {
        $this->options = config('tracker-bot.listeners.'.get_class(), []);
    }

    /**
     * @param MetricsModel $model
     * @return AppEntry
     */
    protected function recordEntry(MetricsModel $model): AppEntry
    {
        $entry = new AppEntry;
        $entry->source_id = $model->source_id;
        $entry->env_id = $model->env_id;
        $entry->family_hash = $model->family_hash;
        $entry->save();

        $model->save();
        $model->entry_id = $entry->id;
        $model->entry_uuid = $entry->uuid;
        $model->batch_id = $entry->batchId;
        $entry->metrics_model()->associate($model);

        return $entry;
    }

    // todo fire events
}
