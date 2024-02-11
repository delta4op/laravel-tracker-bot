<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\EventEntry;

use Carbon\Carbon;
use Delta4op\Laravel\TrackerBot\DB\EloquentBuilders\AppEntryEB;
use Delta4op\Laravel\TrackerBot\DB\EloquentRepositories\AppEntryER;
use Delta4op\Laravel\TrackerBot\DB\Models\BaseModel;
use Delta4op\Laravel\TrackerBot\DB\Models\common\PlatformDetails;
use Delta4op\Laravel\TrackerBot\DB\Models\common\Server;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\AppErrorObject;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\AppRequestObject;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\ClientRequestObject;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\ConsoleCommandObject;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\DbQueryObject;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\EntryObject;
use Delta4op\Laravel\TrackerBot\DB\Models\objects\ScheduleObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Enums\Platform;
use Delta4op\Laravel\TrackerBot\Support\BatchIdGenerator;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Relations\EmbedsOne;

/**
 * @property ?string $_id
 * @property ?string $source
 * @property ?Platform $platform
 * @property ?string $uuid
 * @property ?string $batchId
 * @property ?string $env
 * @property ?string $familyHash
 * @property ?EntryType $type
 * @property ?EntryObject $entryObject
 * @property ?Server $server
 * @property ?Carbon $recordedAt
 *
 * @method AppEntryEB query()
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

            $platform = new PlatformDetails;
            $platform->type = Platform::PHP_LARAVEL;
            $entry->platform()->associate($platform);
        });
    }

    /**
     * @return EmbedsOne
     */
    public function server(): EmbedsOne
    {
        return $this->embedsOne(Server::class);
    }

    /**
     * @return EmbedsOne
     */
    public function platform(): EmbedsOne
    {
        return $this->embedsOne(PlatformDetails::class);
    }

    /**
     * @return EmbedsOne
     */
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

    /**
     * @param $query
     * @return AppEntryEB
     */
    public function newEloquentBuilder($query): AppEntryEB
    {
        return new AppEntryEB($query);
    }

    /**
     * @return AppEntryER
     */
    public static function repository(): AppEntryER
    {
        return new AppEntryER;
    }
}
