<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\Support\FetchesStackTrace;
use Illuminate\Foundation\Application;

abstract class Watcher
{
    use FetchesStackTrace;

    protected array $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Register the watcher.
     *
     * @param Application $app
     */
    abstract public function register(Application $app);

    /**
     * @return mixed
     */
    protected function isWatcherEnabled(): bool
    {
        return filter_var(
            $this->options['enabled'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
