<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Foundation\Application;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleCommandLog;

class ConsoleCommandWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param Application $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(CommandFinished::class, [$this, 'recordCommand']);
    }

    public function recordCommand(CommandFinished $event): void
    {
        if (!Tracker::isRecording() || $this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareConsoleCommandLog($event)
        );
    }

    /**
     * @param CommandFinished $event
     * @return ConsoleCommandLog
     */
    protected function prepareConsoleCommandLog(CommandFinished $event): ConsoleCommandLog
    {
        $object = new ConsoleCommandLog;

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
        return
            !$this->isWatcherEnabled() ||
            in_array($event->command, array_merge($this->options['ignore'] ?? [], [
                'schedule:run',
                'schedule:finish',
                'package:discover',
            ]));
    }
}
