<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\ConsoleCommandObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Illuminate\Console\Events\CommandFinished;

class ConsoleCommandListener extends Listener
{
    public function handle(CommandFinished $event): void
    {
        if (!TrackerBot::isEnabled() || $this->shouldIgnore($event)) {
            return;
        }

        $this->logEntry(
            EntryType::CONSOLE_COMMAND,
            $this->prepareEventObject($event)
        );
    }

    /**
     * @param CommandFinished $event
     * @return ConsoleCommandObject
     */
    protected function prepareEventObject(CommandFinished $event): ConsoleCommandObject
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
