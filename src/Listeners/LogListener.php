<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\LogObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Psr\Log\LogLevel;
use Throwable;

class LogListener extends Listener
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
     * @param MessageLogged $event
     * @return void
     */
    public function handle(MessageLogged $event): void
    {
        if (!TrackerBot::isEnabled() || $this->shouldIgnore($event)) {
            return;
        }

        $this->logEntry(
            EntryType::APP_LOG,
            $this->prepareEventObject($event)
        );
    }

    /**
     * @param MessageLogged $event
     * @return LogObject
     */
    protected function prepareEventObject(MessageLogged $event): LogObject
    {
        $object = new LogObject;

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

        $minimumTelescopeLogLevel = static::PRIORITIES[$this->options['level'] ?? 'debug']
            ?? static::PRIORITIES[LogLevel::DEBUG];

        $eventLogLevel = static::PRIORITIES[$event->level]
            ?? static::PRIORITIES[LogLevel::DEBUG];

        return $eventLogLevel < $minimumTelescopeLogLevel;
    }
}
