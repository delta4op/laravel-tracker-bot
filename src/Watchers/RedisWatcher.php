<?php

namespace Delta4op\Laravel\TrackerBot\Watchers;

use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\RedisEvent;
use Delta4op\Laravel\TrackerBot\Tracker;
use Illuminate\Redis\Events\CommandExecuted;

class RedisWatcher extends Watcher
{
    /**
     * @param CommandExecuted $event
     * @return void
     */
    public function handle(CommandExecuted $event): void
    {
        if ($this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareRedisEvent($event)
        );
    }

    /**
     * @param CommandExecuted $event
     * @return RedisEvent
     */
    protected function prepareRedisEvent(CommandExecuted $event): RedisEvent
    {
        $object = new RedisEvent;
        $object->connection = $event->connectionName;
        $object->command = $this->formatCommand($event->command, $event->parameters);
        $object->time = round($event->time, 2);

        return $object;
    }

    /**
     * Format the given Redis command.
     */
    private function formatCommand(string $command, array $parameters): string
    {
        $parameters = collect($parameters)->map(function ($parameter) {
            if (is_array($parameter)) {
                return collect($parameter)->map(function ($value, $key) {
                    if (is_array($value)) {
                        return json_encode($value);
                    }

                    return is_int($key) ? $value : "{$key} {$value}";
                })->implode(' ');
            }

            return $parameter;
        })->implode(' ');

        return "$command $parameters";
    }

    /**
     * Determine if the event should be ignored.
     */
    private function shouldIgnore(mixed $event): bool
    {
        return
            !Tracker::isRecording() ||
            !$this->isWatcherEnabled() ||
            in_array($event->command, [
                'pipeline', 'transaction',
            ]);
    }
}
