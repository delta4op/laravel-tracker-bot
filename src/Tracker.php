<?php

namespace Delta4op\Laravel\Tracker;

use Delta4op\Laravel\Tracker\DB\Models\Entry;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\MetricsModel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Testing\Fakes\EventFake;

class Tracker
{
    use RegistersWatchers;
    use ProvidesSource;
    use ProvidesEnvironment;

    /**
     * Indicates if Telescope should record entries.
     *
     * @var bool
     */
    public static $shouldRecord = false;

    /**
     * The list of hidden response parameters.
     *
     * @var array
     */
    public static array $hiddenResponseParameters = [];

    /**
     * Indicates if Telescope should ignore events fired by Laravel.
     *
     * @var bool
     */
    public static bool $ignoreFrameworkEvents = true;

    /**
     * The callback executed after queuing a new entry.
     *
     * @var \Closure
     */
    public static $afterRecordingHook;

    /**
     * @return array
     */
    public static function config(): array
    {
        return config('tracker');
    }

    /**
     * @param Application $app
     * @return void
     */
    public static function start(Application $app): void
    {
        if (!filter_var(config('tracker.enabled', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        static::registerWatchers($app);

        static::startRecording();
    }

    /**
     * @param MetricsModel $model
     * @return Entry
     */
    public static function recordEntry(MetricsModel $model): Entry
    {
        // set family hash if not set already
        if (!$model->family_hash) {
            $model->setFamilyHash();
        }

        $source = Tracker::getSource();
        $environment = Tracker::getEnvironment();

        $entry = new Entry;
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

        // execute hook
        if (static::$afterRecordingHook) {
            call_user_func(static::$afterRecordingHook, new static, $entry);
        }

        return $entry;
    }

    /**
     * @return void
     */
    public static function startRecording(): void
    {
        static::$shouldRecord = true;
    }

    /**
     * @return void
     */
    public static function stopRecording(): void
    {
        static::$shouldRecord = false;
    }

    /**
     * Execute the given callback without recording Telescope entries.
     *
     * @param callable $callback
     * @return mixed
     */
    public static function withoutRecording(callable $callback): mixed
    {
        $shouldRecord = static::$shouldRecord;

        static::$shouldRecord = false;

        try {
            return call_user_func($callback);
        } finally {
            static::$shouldRecord = $shouldRecord;
        }
    }

    /**
     * Determine if the application is running an approved command.
     *
     * @param Application $app
     * @return bool
     */
    protected static function runningApprovedArtisanCommand(Application $app): bool
    {
        return $app->runningInConsole() && !in_array(
                $_SERVER['argv'][1] ?? null,
                array_merge([
                    // 'migrate',
                    'migrate:rollback',
                    'migrate:fresh',
                    // 'migrate:refresh',
                    'migrate:reset',
                    'migrate:install',
                    'package:discover',
                    'queue:listen',
                    'queue:work',
                    'horizon',
                    'horizon:work',
                    'horizon:supervisor',
                ], config('telescope.ignoreCommands', []), config('telescope.ignore_commands', []))
            );
    }

    /**
     * @return bool
     */
    public static function isRecording(): bool
    {
        return self::$shouldRecord && !app('events') instanceof EventFake;
    }

    /**
     * Hide the given response parameters.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function hideResponseParameters(array $attributes): static
    {
        static::$hiddenResponseParameters = array_values(array_unique(array_merge(
            static::$hiddenResponseParameters,
            $attributes
        )));

        return new static;
    }


    /**
     * @return string
     */
    public static function dbConnection(): string
    {
        return static::config()['storage']['database']['connection'] ?? '';
    }
}
