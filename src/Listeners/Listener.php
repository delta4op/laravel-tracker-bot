<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\MetricsModel;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Delta4op\Laravel\TrackerBot\Support\FetchesStackTrace;
use Illuminate\Support\Str;

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
        if(!$model->family_hash) {
            $model->setFamilyHash();
        }

        $source = TrackerBot::getSource();
        $environment = TrackerBot::getEnvironment();

        $entry = new AppEntry;
        $entry->source_id = $model->source_id;
        $entry->env_id = $model->env_id;
        $entry->family_hash = $model->family_hash;
        $entry->source()->associate($source);
        $entry->env()->associate($environment);
        $entry->save();

        $model->source()->associate($source);
        $model->env()->associate($environment);
        $model->entry_id = $entry->id;
        $model->entry_uuid = $entry->uuid;
        $model->batch_id = $entry->batchId;
        $model->save();

        $entry->metrics_model()->associate($model);
        $entry->save();

        return $entry;
    }

    /**
     * Determine whether a file should be considered internal.
     */
    protected function isInternalFile(string $file): bool
    {
        return Str::startsWith($file, base_path('vendor'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'pulse'))
            || Str::startsWith($file, base_path('vendor'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'framework'))
            || $file === base_path('artisan')
            || $file === public_path('index.php');
    }
    // todo fire events
}
