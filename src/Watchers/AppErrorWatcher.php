<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Error;
use Delta4op\Laravel\Tracker\Helpers\FileHelpers;
use Delta4op\Laravel\Tracker\Support\ExceptionContext;
use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Throwable;

class AppErrorWatcher extends Watcher
{
    /**
     * @param Application $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(MessageLogged::class, [$this, 'recordAppError']);
    }

    /**
     * @param MessageLogged $event
     * @return void
     */
    public function recordAppError(MessageLogged $event): void
    {
        if (!Tracker::isRecording()|| $this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareAppError($event)
        );
    }

    /**
     * @param MessageLogged $event
     * @return Error|null
     */
    protected function prepareAppError(MessageLogged $event): ?Error
    {
        $appError = new Error;

        /** @var Throwable $exception */
        $exception = $event->context['exception'];

        $trace = collect($exception->getTrace())->map(function ($item) {
            return Arr::only($item, ['file', 'line']);
        })->toArray();

        $appError->class = get_class($exception);
        $appError->file = $exception->getFile();
        $appError->is_internal_file = FileHelpers::isInternalFile($exception->getFile());
        $appError->line = $exception->getLine();
        $appError->code = (string) $exception->getCode();
        $appError->message = $exception->getMessage();
        $appError->trace = $trace;
        $appError->linePreview = ExceptionContext::get($exception);
        $appError->context = transform(Arr::except($event->context, ['exception', 'trackerbot']), function ($context) {
            return ! empty($context) ? $context : null;
        }) ?? [];

        return $appError;
    }

    /**
     * Determine if the event should be ignored.
     */
    private function shouldIgnore(MessageLogged $event): bool
    {
        return ! isset($event->context['exception']);
    }
}
