<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\AppErrorObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Support\ExceptionContext;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Throwable;

class AppErrorListener extends Listener
{
    public function handle(MessageLogged $event): void
    {
        if ($this->shouldIgnore($event)) {
            return;
        }

        if ($object = $this->prepareEventObject($event)) {
            $this->logEntry(
                EntryType::APP_ERROR,
                $object
            );
        }
    }

    protected function prepareEventObject(MessageLogged $event): ?AppErrorObject
    {
        $object = new AppErrorObject;

        /** @var Throwable $exception */
        $exception = $event->context['exception'];

        $trace = collect($exception->getTrace())->map(function ($item) {
            return Arr::only($item, ['file', 'line']);
        })->toArray();

        $object->class = get_class($exception);
        $object->file = $exception->getFile();
        $object->line = $exception->getLine();
        $object->message = $exception->getMessage();
        $object->trace = $trace;
        $object->linePreview = ExceptionContext::get($exception);
        $object->context = transform(Arr::except($event->context, ['exception', 'trackerbot']), function ($context) {
            return ! empty($context) ? $context : null;
        });

        return $object;
    }

    /**
     * Determine if the event should be ignored.
     */
    private function shouldIgnore(MessageLogged $event): bool
    {
        return ! isset($event->context['exception']);
    }
}
