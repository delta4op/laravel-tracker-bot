<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\ScheduleObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Console\Events\CommandStarting;

class ScheduleListener extends Listener
{
    /**
     * @param CommandStarting $event
     * @return void
     */
    public function handle(CommandStarting $event): void
    {
        if ($event->command !== 'schedule:run' &&
            $event->command !== 'schedule:finish') {
            return;
        }

        if ($object = $this->prepareEventObject($event)) {
            $this->logEntry(
                EntryType::COMMAND_SCHEDULE,
                $object
            );
        }
    }

    /**
     * @param CommandStarting $event
     * @return ScheduleObject|null
     */
    protected function prepareEventObject(CommandStarting $event): ?ScheduleObject
    {
        $object = new ScheduleObject;

        $object->command = $event->command;

        return $object;
    }
}
