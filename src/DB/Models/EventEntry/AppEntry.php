<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\EventEntry;

use Carbon\Carbon;
use Delta4op\Laravel\TrackerBot\BatchIdGenerator;
use Delta4op\Laravel\TrackerBot\DB\Models\BaseModel;
use Delta4op\Laravel\TrackerBot\DB\Models\common\Server;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\AppErrorObject;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\AppRequestObject;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\ClientRequestObject;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\ConsoleCommandObject;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\DbQueryObject;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\EntryObject;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\ScheduleObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Relations\EmbedsOne;

/**
 * @property ?string $_id
 * @property ?string $source
 * @property ?string $uuid
 * @property ?string $batchId
 * @property ?string $env
 * @property ?string $familyHash
 * @property ?EntryType $type
 * @property ?EntryObject $entryObject
 * @property ?Server $server
 * @property ?Carbon $recordedAt
 */
class AppEntry extends BaseModel
{
    protected $collection = 'appEntries';

    const CREATED_AT = 'recordedAt';

    protected $casts = [
        'type' => EntryType::class,
        'recordedAt' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (AppEntry $entry) {

            $entry->env = config('app.env');

            $entry->source = config('tracker-bot.source');

            $entry->uuid = Str::orderedUuid()->toString();

            $entry->batchId = app(BatchIdGenerator::class)->getUuid();
        });
    }

    public function server(): EmbedsOne
    {
        return $this->embedsOne(Server::class);
    }

    public function entryObject(): EmbedsOne
    {
        $eventTypeString = $this->attributes['eventType'] ?? null;

        return $this->embedsOne(match ($eventTypeString) {
            EntryType::APP_REQUEST->value => AppRequestObject::class,
            EntryType::DB_QUERY->value => DbQueryObject::class,
            EntryType::APP_ERROR->value => AppErrorObject::class,
            EntryType::CONSOLE_COMMAND->value => ConsoleCommandObject::class,
            EntryType::CLIENT_REQUEST->value => ClientRequestObject::class,
            EntryType::COMMAND_SCHEDULE->value => ScheduleObject::class,
            default => EntryObject::class
        });
    }
}
