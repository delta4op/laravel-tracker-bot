<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\CacheObject;
use Delta4op\Laravel\TrackerBot\Enums\CacheEventType;
use Delta4op\Laravel\TrackerBot\Enums\AppEntryType;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Support\Str;

class CacheListener extends Listener
{
    public function handle(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): void
    {
        if (!TrackerBot::isEnabled() || $this->shouldIgnore($event)) {
            return;
        }

        $this->recordEntry(
            AppEntryType::CACHE,
            $this->prepareEventObject($event)
        );
    }

    protected function prepareEventObject(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): CacheObject
    {
        $object = new CacheObject;

        if ($event instanceof CacheHit) {
            $object->type = CacheEventType::SET;
            $object->key = $event->key;
            $object->value = $this->formatValue($event);
            //            $object->expiration = $this->formatExpiration($event);
        }

        if ($event instanceof CacheMissed) {
            $object->type = CacheEventType::MISSED;
            $object->key = $event->key;
        }

        if ($event instanceof KeyWritten) {
            $object->type = CacheEventType::SET;
            $object->key = $event->key;
            $object->value = $this->formatValue($event);
            //            $object->expiration = $this->formatExpiration($event);
        }

        if ($event instanceof KeyForgotten) {
            $object->type = CacheEventType::FORGET;
            $object->key = $event->key;
        }

        return $object;
    }

    /**
     * Determine the value of an event.
     *
     * @param  mixed  $event
     */
    private function formatValue($event): mixed
    {
        return (! $this->shouldHideValue($event))
            ? $event->value
            : '********';
    }

    /**
     * Determine if the event value should be ignored.
     *
     * @param  mixed  $event
     */
    private function shouldHideValue($event): bool
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
        return Str::is([
            'illuminate:queue:restart',
            'framework/schedule*',
            'trackerBot:*',
        ], $event->key);
    }
}
