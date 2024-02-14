<?php

namespace Delta4op\Laravel\TrackerBot;

use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest\AppRequest;
use Delta4op\Laravel\TrackerBot\Enums\AppEntryType;
use Delta4op\Laravel\TrackerBot\Listeners\AppErrorListener;
use Delta4op\Laravel\TrackerBot\Listeners\AppRequestListener;
use Delta4op\Laravel\TrackerBot\Listeners\CacheListener;
use Delta4op\Laravel\TrackerBot\Listeners\ClientRequestListener;
use Delta4op\Laravel\TrackerBot\Listeners\ConsoleCommandListener;
use Delta4op\Laravel\TrackerBot\Listeners\DbQueryListener;
use Delta4op\Laravel\TrackerBot\Listeners\EventListener;
use Delta4op\Laravel\TrackerBot\Listeners\MailListener;
use Delta4op\Laravel\TrackerBot\Listeners\RedisListener;
use Delta4op\Laravel\TrackerBot\Listeners\ScheduleListener;
use Delta4op\Laravel\TrackerBot\Support\BatchIdGenerator;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TrackerBotProvider extends PackageServiceProvider
{
    public function bootingPackage(): void
    {
        $this->enableAppEntryTableMorphMaps();

        $this->listenAppErrors();

        $this->listenAppRequests();

        $this->listenCache();

        $this->listenClientRequests();

        $this->listenConsole();

        $this->listenDbQueries();

        $this->listenMails();

        $this->listenEvents();

        if ($this->app->bound('redis')) {
            $this->listenRedis();
        }
    }

    /**
     * @return void
     */
    public function enableAppEntryTableMorphMaps(): void
    {
        Relation::morphMap([
            AppEntryType::APP_REQUEST->value => AppRequest::class,
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

    /**
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package->name('tracker-bot')
            ->hasConfigFile('tracker-bot')
            ->hasMigration();
    }

    /**
     * @return void
     */
    protected function listenAppErrors(): void
    {
        Event::listen(MessageLogged::class, AppErrorListener::class);
    }

    /**
     * @return void
     */
    protected function listenAppRequests(): void
    {
        Event::listen(RequestHandled::class, AppRequestListener::class);
    }

    /**
     * @return void
     */
    protected function listenCache(): void
    {
        Event::listen(CacheHit::class, CacheListener::class);
        Event::listen(CacheMissed::class, CacheListener::class);
        Event::listen(KeyWritten::class, CacheListener::class);
        Event::listen(KeyForgotten::class, CacheListener::class);
    }

    /**
     * @return void
     */
    protected function listenClientRequests(): void
    {
        Event::listen(ConnectionFailed::class, ClientRequestListener::class);
        Event::listen(ResponseReceived::class, ClientRequestListener::class);
    }

    /**
     * @return void
     */
    protected function listenConsole(): void
    {
        Event::listen(CommandFinished::class, ConsoleCommandListener::class);
        Event::listen(CommandStarting::class, ScheduleListener::class);
    }

    /**
     * @return void
     */
    protected function listenDbQueries(): void
    {
        Event::listen(QueryExecuted::class, DbQueryListener::class);
    }

    /**
     * @return void
     */
    protected function listenMails(): void
    {
        Event::listen(MessageSent::class, MailListener::class);
    }

    /**
     * @return void
     */
    protected function listenRedis(): void
    {
        Event::listen(CommandExecuted::class, RedisListener::class);

        foreach ((array) $this->app['redis']->connections() as $connection) {
            $connection->setEventDispatcher($this->app['events']);
        }

        $this->app['redis']->enableEvents();
    }

    /**
     * @return void
     */
    protected function listenEvents(): void
    {
        $this->app['events']->listen('*', [EventListener::class, 'handle']);
    }
}
