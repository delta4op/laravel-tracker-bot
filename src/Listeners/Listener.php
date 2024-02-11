<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\common\Server;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\EntryObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Support\FetchesStackTrace;

abstract class Listener
{
    use FetchesStackTrace;

    protected array $options = [];

    public function __construct()
    {
        $this->options = config("tracker-bot.listeners." . get_class(), []);
    }

    /**
     * @param EntryType $type
     * @param EntryObject $eventObject
     * @return AppEntry
     */
    protected function logEntry(EntryType $type, EntryObject $eventObject): AppEntry
    {
        $entry = new AppEntry;
        $entry->type = $type;
        $entry->server()->associate($this->getServer());
        $entry->entryObject()->associate($eventObject);
        $entry->save();

        $this->fireEntryLoggedEvent($entry);

        return $entry;
    }

    /**
     * @return Server
     */
    protected function getServer(): Server
    {
        $server = new Server;
        $server->processId = getmypid();

        return $server;
    }

    /**
     * @param $entry
     * @return void
     */
    protected function fireEntryLoggedEvent($entry)
    {
        // todo
    }
}
