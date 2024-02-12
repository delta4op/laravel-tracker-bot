<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Illuminate\Console\Events\CommandStarting;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\ScheduleObject;

class ScheduleListener extends Listener
{
    /**
     * @param CommandStarting $event
     * @return void
     */
    public function handle(CommandStarting $event): void
    {
        if(!TrackerBot::isEnabled() || $this->shouldIgnore($event)) {
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
     * @return bool
     */
    protected function shouldIgnore(CommandStarting $event): bool
    {
        return in_array($event->command, [
            'schedule:run',
            'schedule:finish'
        ]);
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
