<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppError;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Delta4op\Laravel\TrackerBot\Support\ExceptionContext;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Throwable;

class AppErrorListener extends Listener
{
    /**
     * @param MessageLogged $event
     * @return void
     */
    public function handle(MessageLogged $event): void
    {
        if (!TrackerBot::isEnabled() || $this->options['enabled'] !== true || $this->shouldIgnore($event)) {
            return;
        }

        $this->recordEntry($this->prepareAppError($event));
    }

    /**
     * @param MessageLogged $event
     * @return AppError|null
     */
    protected function prepareAppError(MessageLogged $event): ?AppError
    {
        $appError = new AppError;

        /** @var Throwable $exception */
        $exception = $event->context['exception'];

        $trace = collect($exception->getTrace())->map(function ($item) {
            return Arr::only($item, ['file', 'line']);
        })->toArray();

        $appError->class = get_class($exception);
        $appError->file = $exception->getFile();
        $appError->is_internal_file = $this->isInternalFile($exception->getFile());
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
