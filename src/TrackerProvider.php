<?php

namespace Delta4op\Laravel\Tracker;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Dump;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Error;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ClientRequest;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Event;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Log;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppRequest;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\CacheEvent;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleCommandLog;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleSchedule;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\DbQuery;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Mail;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\RedisEvent;
use Delta4op\Laravel\Tracker\Enums\AppEntryType;
use Delta4op\Laravel\Tracker\Support\BatchIdGenerator;
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
        $package->name('laravel-tracker-bot')
            ->hasConfigFile('tracker')
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
            AppEntryType::DUMP->value => Dump::class,
            AppEntryType::ERROR->value => Error::class,
            AppEntryType::REQUEST->value => AppRequest::class,
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
