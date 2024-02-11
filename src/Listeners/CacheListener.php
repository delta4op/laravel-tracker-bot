<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\CacheObject;
use Delta4op\Laravel\TrackerBot\Enums\CacheEventType;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

class CacheListener extends Listener
{
    /**
     * @param CacheHit|CacheMissed|KeyWritten|KeyForgotten $event
     * @return void
     */
    public function handle(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): void
    {
        if ($this->shouldIgnore($event)) {
            return;
        }

        if ($object = $this->prepareEventObject($event)) {
            $this->logEntry(
                EntryType::APP_ERROR,
                $object
            );
        }
    }

    /**
     * @param CacheHit|CacheMissed|KeyWritten|KeyForgotten $event
     * @return CacheObject
     */
    protected function prepareEventObject(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): CacheObject
    {
        $object = new CacheObject;

        if($event instanceof CacheHit) {
            $object->type = CacheEventType::SET;
            $object->key = $event->key;
            $object->value = $this->formatValue($event);
//            $object->expiration = $this->formatExpiration($event);
        }

        if($event instanceof CacheMissed) {
            $object->type = CacheEventType::MISSED;
            $object->key = $event->key;
        }

        if($event instanceof KeyWritten) {
            $object->type = CacheEventType::SET;
            $object->key = $event->key;
            $object->value = $this->formatValue($event);
//            $object->expiration = $this->formatExpiration($event);
        }

        if($event instanceof KeyForgotten) {
            $object->type = CacheEventType::FORGET;
            $object->key = $event->key;
        }

        return $object;
    }


    /**
     * Record a cache key was forgotten / removed.
     *
     * @param  \Illuminate\Cache\Events\KeyForgotten  $event
     * @return void
     */
    public function recordKeyForgotten(KeyForgotten $event)
    {
        if (! Telescope::isRecording() || $this->shouldIgnore($event)) {
            return;
        }

        Telescope::recordCache(IncomingEntry::make([
            'type' => 'forget',
            'key' => $event->key,
        ]));
    }

    /**
     * Determine the value of an event.
     *
     * @param  mixed  $event
     * @return mixed
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
     * @return bool
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
     *
     * @param CacheHit|CacheMissed|KeyWritten|KeyForgotten $event
     * @return bool
     */
    private function shouldIgnore(CacheHit|CacheMissed|KeyWritten|KeyForgotten $event): bool
    {
        return Str::is([
            'illuminate:queue:restart',
            'framework/schedule*',
            'telescope:*',
        ], $event->key);
    }
}
