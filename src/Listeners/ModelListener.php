<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\AppErrorObject;
use Delta4op\Laravel\TrackerBot\Enums\AppEntryType;
use Delta4op\Laravel\TrackerBot\Support\ExceptionContext;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Throwable;

class ModelListener extends Listener
{
    public function handle($event): void
    {
//        if (!$this->shouldRecord($event)) {
//            return;
//        }
//
//        if ($object = $this->prepareEventObject($event)) {
//            $this->logEntry(
//                EntryType::APP_ERROR,
//                $object
//            );
//        }
    }
//
//    protected function prepareEventObject(string $event): ?AppErrorObject
//    {
//        $object = new AppErrorObject;
//
//        /** @var Throwable $exception */
//        $exception = $event->context['exception'];
//
//        $trace = collect($exception->getTrace())->map(function ($item) {
//            return Arr::only($item, ['file', 'line']);
//        })->toArray();
//
//        $object->class = get_class($exception);
//        $object->file = $exception->getFile();
//        $object->line = $exception->getLine();
//        $object->message = $exception->getMessage();
//        $object->trace = $trace;
//        $object->linePreview = ExceptionContext::get($exception);
//        $object->context = transform(Arr::except($event->context, ['exception', 'trackerbot']), function ($context) {
//            return ! empty($context) ? $context : null;
//        });
//
//        return $object;
//    }
//
//    /**
//     * Record model hydrations.
//     *
//     * @param  array  $data
//     * @return void
//     */
//    public function recordHydrations($data)
//    {
//        if (! ($this->options['hydrations'] ?? false)
//            || ! $this->shouldRecordHydration($modelClass = get_class($data['model'] ?? $data[0]))) {
//            return;
//        }
//
//        if (! isset($this->hydrationEntries[$modelClass])) {
//            $this->hydrationEntries[$modelClass] = IncomingEntry::make([
//                'action' => 'retrieved',
//                'model' => $modelClass,
//                'count' => 1,
//            ])->tags([$modelClass]);
//
//            Telescope::recordModelEvent($this->hydrationEntries[$modelClass]);
//        } else {
//            $entry = $this->hydrationEntries[$modelClass];
//
//            if (is_string($this->hydrationEntries[$modelClass]->content)) {
//                $entry->content = json_decode($entry->content, true);
//            }
//
//            $entry->content['count']++;
//        }
//    }
//
//    /**
//     * Flush the cached entries.
//     *
//     * @return void
//     */
//    public function flush()
//    {
//        $this->hydrationEntries = [];
//    }
//
//    /**
//     * Extract the Eloquent action from the given event.
//     *
//     * @param  string  $event
//     * @return mixed
//     */
//    private function action($event)
//    {
//        preg_match('/\.(.*):/', $event, $matches);
//
//        return $matches[1];
//    }
//
//    /**
//     * Determine if the Eloquent event should be recorded.
//     *
//     * @param  string  $eventName
//     * @return bool
//     */
//    private function shouldRecord($eventName)
//    {
//        return Str::is([
//            '*created*', '*updated*', '*restored*', '*deleted*', '*retrieved*',
//        ], $eventName);
//    }
//
//    /**
//     * Determine if the hydration should be recorded for the model class.
//     *
//     * @param  string  $modelClass
//     * @return bool
//     */
//    private function shouldRecordHydration(string $modelClass): bool
//    {
//        return collect($this->options['ignore'] ?? [EntryModel::class])
//            ->every(function ($class) use ($modelClass) {
//                return $modelClass !== $class && ! is_subclass_of($modelClass, $class);
//            });
//    }
}
