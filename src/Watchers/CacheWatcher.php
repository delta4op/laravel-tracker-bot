<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\CacheEvent;
use Delta4op\Laravel\Tracker\Enums\CacheEventType;
use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class CacheWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param Application $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(CacheHit::class, [$this, 'recordCache']);
        $app['events']->listen(CacheMissed::class, [$this, 'recordCache']);
        $app['events']->listen(KeyWritten::class, [$this, 'recordCache']);
        $app['events']->listen(KeyForgotten::class, [$this, 'recordCache']);
    }

    /**
     * @param CacheHit|CacheMissed|KeyWritten|KeyForgotten $event
     * @return void
     */
    public function recordCache(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): void
    {
        if (!Tracker::isRecording() || $this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareCacheEvent($event)
        );
    }

    /**
     * @param CacheHit|CacheMissed|KeyWritten|KeyForgotten $event
     * @return CacheEvent
     */
    protected function prepareCacheEvent(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): CacheEvent
    {
        $cacheEvent = new CacheEvent;

        if ($event instanceof CacheHit) {
            $cacheEvent->type = CacheEventType::SET;
            $cacheEvent->key = $event->key;
            $cacheEvent->value = $this->formatValue($event);
        }

        if ($event instanceof CacheMissed) {
            $cacheEvent->type = CacheEventType::MISSED;
            $cacheEvent->key = $event->key;
        }

        if ($event instanceof KeyWritten) {
            $cacheEvent->type = CacheEventType::SET;
            $cacheEvent->key = $event->key;
            $cacheEvent->value = $this->formatValue($event);
        }

        if ($event instanceof KeyForgotten) {
            $cacheEvent->type = CacheEventType::FORGET;
            $cacheEvent->key = $event->key;
        }

        return $cacheEvent;
    }

    /**
     * Determine the value of an event.
     *
     * @param mixed $event
     */
    private function formatValue($event): mixed
    {
        return (!$this->shouldHideValue($event))
            ? $event->value
            : '********';
    }

    /**
     * Determine if the event value should be ignored.
     *
     * @param CacheHit|CacheMissed|KeyWritten|KeyForgotten $event
     * @return bool
     */
    private function shouldHideValue(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): bool
    {
        return Str::is(
            $this->options['hidden'] ?? [],
            $event->key
        );
    }

    /**
     * Determine if the event should be ignored.
     */
    private function shouldIgnore(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): bool
    {
        return !$this->isWatcherEnabled() ||
            Str::is([
                'illuminate:queue:restart',
                'framework/schedule*',
                'trackerBot:*',
            ], $event->key);
    }
}
