<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Log;
use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Psr\Log\LogLevel;
use Throwable;

class LogWatcher extends Watcher
{
    protected const PRIORITIES = [
        LogLevel::DEBUG => 100,
        LogLevel::INFO => 200,
        LogLevel::NOTICE => 250,
        LogLevel::WARNING => 300,
        LogLevel::ERROR => 400,
        LogLevel::CRITICAL => 500,
        LogLevel::ALERT => 550,
        LogLevel::EMERGENCY => 600,
    ];

    /**
     * Register the watcher.
     *
     * @param  Application  $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(MessageLogged::class, [$this, 'recordLog']);
    }

    /**
     * @param MessageLogged $event
     * @return void
     */
    public function recordLog(MessageLogged $event): void
    {
        if (!Tracker::isRecording() || $this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareAppLog($event)
        );
    }

    /**
     * @param MessageLogged $event
     * @return Log
     */
    protected function prepareAppLog(MessageLogged $event): Log
    {
        $object = new Log;

        $object->level = $event->level;
        $object->message = $event->message;
        $object->context = Arr::except($event->context, ['trackerBot']);

        return $object;
    }

    /**
     * Determine if the event should be ignored.
     *
     * @param  MessageLogged  $event
     * @return bool
     */
    private function shouldIgnore(MessageLogged $event): bool
    {
        if (isset($event->context['exception']) && $event->context['exception'] instanceof Throwable) {
            return true;
        }

        if(!$this->isWatcherEnabled()) {
            return true;
        }

        $minimumTelescopeLogLevel = static::PRIORITIES[$this->options['level'] ?? 'debug']
            ?? static::PRIORITIES[LogLevel::DEBUG];

        $eventLogLevel = static::PRIORITIES[$event->level]
            ?? static::PRIORITIES[LogLevel::DEBUG];

        return $eventLogLevel < $minimumTelescopeLogLevel;
    }
}
