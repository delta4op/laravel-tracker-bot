<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleSchedule;
use Illuminate\Contracts\Foundation\Application;

class ConsoleScheduleWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param  Application  $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(ScheduledTaskStarting::class, [$this, 'recordCommand']);
    }

    /**
     * @param ScheduledTaskStarting $event
     * @return void
     */
    public function recordCommand(ScheduledTaskStarting $event): void
    {
        if(!Tracker::isRecording() || $this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareConsoleSchedule($event)
        );
    }

    /**
     * @param ScheduledTaskStarting $event
     * @return ConsoleSchedule|null
     */
    protected function prepareConsoleSchedule(ScheduledTaskStarting $event): ?ConsoleSchedule
    {
        $object = new ConsoleSchedule;

        $object->command = $event->task->command;
        $object->description = $event->task->description;
        $object->expression = $event->task->expression;
        $object->timezone = (string) $event->task->timezone;
        $object->config = [
            'environments' => $event->task->environments,
            'even_in_maintenance_mode' => $event->task->evenInMaintenanceMode,
            'without_over_lapping' => $event->task->withoutOverlapping,
            'on_one_server' => $event->task->onOneServer,
            'expires_at' => $event->task->expiresAt,
            'run_in_background' => $event->task->runInBackground,
        ];

        return $object;
    }

    /**
     * @param ScheduledTaskStarting $event
     * @return bool
     */
    protected function shouldIgnore(ScheduledTaskStarting $event): bool
    {
        return !$this->isWatcherEnabled() ||
            in_array($event->task->command, [
                'schedule:run',
                'schedule:finish'
            ]);
    }
}
