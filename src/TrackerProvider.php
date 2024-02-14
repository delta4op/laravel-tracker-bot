<?php

namespace Delta4op\Laravel\TrackerBot;

use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppDump;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppError;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\ClientRequest;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\Event;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\Log;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\CacheEvent;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\ConsoleCommandLog;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\ConsoleSchedule;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\DbQuery;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\Mail;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\RedisEvent;
use Delta4op\Laravel\TrackerBot\Enums\AppEntryType;
use Delta4op\Laravel\TrackerBot\Support\BatchIdGenerator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TrackerProvider extends PackageServiceProvider
{
    /**
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package->name('tracker-bot')
            ->hasConfigFile('tracker-bot')
            ->hasMigration('tracker_migrations');
    }

    /**
     * @return void
     */
    public function bootingPackage(): void
    {
        $this->enableAppEntryTableMorphMaps();

        Tracker::start($this->app);
    }

    /**
     * @return void
     */
    public function enableAppEntryTableMorphMaps(): void
    {
        Relation::morphMap([
            AppEntryType::APP_DUMP->value => AppDump::class,
            AppEntryType::APP_ERROR->value => AppError::class,
            AppEntryType::APP_REQUEST->value => AppRequest::class,
            AppEntryType::CACHE_EVENT->value => CacheEvent::class,
            AppEntryType::CLIENT_REQUEST->value => ClientRequest::class,
            AppEntryType::CONSOLE_COMMAND->value => ConsoleCommandLog::class,
            AppEntryType::CONSOLE_SCHEDULE->value => ConsoleSchedule::class,
            AppEntryType::DB_QUERY->value => DbQuery::class,
            AppEntryType::EVENT->value => Event::class,
            AppEntryType::LOG->value => Log::class,
            AppEntryType::MAIL->value => Mail::class,
            AppEntryType::REDIS_EVENT->value => RedisEvent::class,
        ]);
    }

    /**
     * @return void
     */
    public function registeringPackage(): void
    {
        $this->app->singleton(BatchIdGenerator::class, function(){
            return new BatchIdGenerator(Str::uuid());
        });
    }
}
