<?php

namespace Delta4op\Laravel\Tracker;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;

trait RegistersWatchers
{
    /**
     * The class names of the registered watchers.
     *
     * @var array
     */
    protected static array $watchers = [];

    /**
     * Determine if a given watcher has been registered.
     *
     * @param string $class
     * @return bool
     */
    public static function hasWatcher(string $class): bool
    {
        return in_array($class, static::$watchers);
    }

    /**
     * Register the configured Telescope watchers.
     *
     * @param Application $app
     * @return void
     * @throws BindingResolutionException
     */
    protected static function registerWatchers(Application $app): void
    {
        foreach (config('tracker.watchers') as $key => $watcher) {
            if (is_string($key) && $watcher === false) {
                continue;
            }

            if (is_array($watcher) && !($watcher['enabled'] ?? true)) {
                continue;
            }

            $watcher = $app->make(is_string($key) ? $key : $watcher, [
                'options' => is_array($watcher) ? $watcher : [],
            ]);

            static::$watchers[] = get_class($watcher);

            $watcher->register($app);
        }
    }
}
