<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Closure;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\EventObject;
use Delta4op\Laravel\TrackerBot\Support\ExtractProperties;
use Delta4op\Laravel\TrackerBot\Support\FormatsClosure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionFunction;

class EventListener extends Listener
{
    use FormatsClosure;

    /**
     * @param $eventName
     * @param $payload
     * @return void
     * @throws ReflectionException
     */
    public function handle($eventName, $payload): void
    {
//        if ($this->shouldIgnore($eventName)) {
//            return;
//        }

        $this->logEntry(
            EntryType::EVENT,
            $this->prepareEventObject($eventName, $payload)
        );
    }

    /**
     * @param $eventName
     * @param $payload
     * @return EventObject|null
     * @throws ReflectionException
     */
    protected function prepareEventObject($eventName, $payload): ?EventObject
    {
        $formattedPayload = $this->extractPayload($eventName, $payload);

        $object = new EventObject;

        $object->name = $eventName;
        $object->payload = $formattedPayload;
        $object->listeners = $this->formatListeners($eventName);
        $object->broadcast = class_exists($eventName) && in_array(ShouldBroadcast::class, (array)class_implements($eventName));

        return $object;
    }

    /**
     * Extract the payload and tags from the event.
     *
     * @param string $eventName
     * @param array $payload
     * @return array
     * @throws ReflectionException
     */
    protected function extractPayload(string $eventName, mixed $payload): array
    {
        if (class_exists($eventName) && isset($payload[0]) && is_object($payload[0])) {
            return ExtractProperties::from($payload[0]);
        }

        return collect($payload)->map(function ($value) {
            return is_object($value) ? [
                'class' => get_class($value),
                'properties' => json_decode(json_encode($value), true),
            ] : $value;
        })->toArray();
    }

    /**
     * Format list of event listeners.
     *
     * @param string $eventName
     * @return array
     */
    protected function formatListeners(string $eventName): array
    {
        return collect(app('events')->getListeners($eventName))
            ->map(function ($listener) {
                $listener = (new ReflectionFunction($listener))
                    ->getStaticVariables()['listener'];

                if (is_string($listener)) {
                    return Str::contains($listener, '@') ? $listener : $listener . '@handle';
                } elseif (is_array($listener) && is_string($listener[0])) {
                    return $listener[0] . '@' . $listener[1];
                } elseif (is_array($listener) && is_object($listener[0])) {
                    return get_class($listener[0]) . '@' . $listener[1];
                } elseif (is_object($listener) && is_callable($listener) && !$listener instanceof Closure) {
                    return get_class((object)$listener) . '@__invoke';
                }

                return $this->formatClosureListener($listener);
            })->reject(function ($listener) {
                return Str::contains($listener, 'Laravel\\Telescope');
            })->map(function ($listener) {
                if (Str::contains($listener, '@')) {
                    $queued = in_array(ShouldQueue::class, class_implements(Str::beforeLast($listener, '@')));
                }

                return [
                    'name' => $listener,
                    'queued' => $queued ?? false,
                ];
            })->values()->toArray();
    }

    /**
     * Determine if the event should be ignored.
     *
     * @param string $eventName
     * @return bool
     */
    protected function shouldIgnore(string $eventName): bool
    {
        return $this->eventIsIgnored($eventName) || $this->eventIsFiredByTrackerBot($eventName);
    }

    /**
     * Determine if the event was fired internally by Laravel.
     *
     * @param string $eventName
     * @return bool
     */
    protected function eventIsFiredByTheFramework(string $eventName): bool
    {
        return Str::is(
            [
                'Illuminate\*',
                'Laravel\Octane\*',
                'Laravel\Scout\Events\ModelsImported',
                'eloquent*',
                'bootstrapped*',
                'bootstrapping*',
                'creating*',
                'composing*',
            ],
            $eventName
        );
    }

    protected function eventIsFiredByTrackerBot($eventName): bool
    {
        return Str::is(
            ['trackerBot*'],
            $eventName
        );
    }

    /**
     * Determine if the event is ignored manually.
     *
     * @param string $eventName
     * @return bool
     */
    protected function eventIsIgnored(string $eventName): bool
    {
        return Str::is($this->options['ignore'] ?? [], $eventName);
    }
}