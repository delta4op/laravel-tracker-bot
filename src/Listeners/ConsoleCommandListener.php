<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Console\Events\CommandFinished;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\ConsoleCommandObject;

class ConsoleCommandListener extends Listener
{
    /**
     * @param CommandFinished $event
     * @return void
     */
    public function handle(CommandFinished $event): void
    {
        if($this->shouldIgnore($event)) {
            return;
        }

        if($object = $this->prepareEventObject($event)) {
            $this->logEntry(
                EntryType::CONSOLE_COMMAND,
                $object
            );
        }
    }

    /**
     * @param CommandFinished $event
     * @return ConsoleCommandObject|null
     */
    protected function prepareEventObject(CommandFinished $event): ConsoleCommandObject|null
    {
        $object = new ConsoleCommandObject;

        $object->command = $event->command;
        $object->exitCode = $event->exitCode;
        $object->arguments = $event->input->getArguments();
        $object->options = $event->input->getOptions();

        return $object;
    }

    /**
     * Determine if the event should be ignored.
     *
     * @param  CommandFinished $event
     * @return bool
     */
    private function shouldIgnore(CommandFinished $event): bool
    {
        return in_array($event->command, array_merge($this->options['ignore'] ?? [], [
            'schedule:run',
            'schedule:finish',
            'package:discover',
        ]));
    }
}
