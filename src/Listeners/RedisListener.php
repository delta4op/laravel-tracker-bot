<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\RedisObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Redis\Events\CommandExecuted;

class RedisListener extends Listener
{
    public function handle(CommandExecuted $event): void
    {
        if ($this->shouldIgnore($event)) {
            return;
        }

        $this->logEntry(
            EntryType::REDIS,
            $this->prepareEventObject($event)
        );
    }

    protected function prepareEventObject(CommandExecuted $event): RedisObject
    {
        $object = new RedisObject;
        $object->connection = $event->connectionName;
        $object->command = $this->formatCommand($event->command, $event->parameters);
        $object->time = number_format($event->time, 2, '.', '');

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

        return "{$command} {$parameters}";
    }

    /**
     * Determine if the event should be ignored.
     */
    private function shouldIgnore(mixed $event): bool
    {
        return in_array($event->command, [
            'pipeline', 'transaction',
        ]);
    }
}
