<?php

use Delta4op\Laravel\Tracker\DB\Models\AppEntry;
use Delta4op\Laravel\Tracker\DB\Models\Environment;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppDump;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppError;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Log;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppRequest;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\CacheEvent;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleCommandLog;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleSchedule;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\DbQuery;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Event;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\Mail;
use Delta4op\Laravel\Tracker\DB\Models\Metrics\RedisEvent;
use Delta4op\Laravel\Tracker\DB\Models\Source;
use Delta4op\Laravel\Tracker\Enums\CacheEventType;
use Delta4op\Laravel\Tracker\Enums\HttpMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->sourceSchema();
        $this->environmentSchema();
        $this->appEntriesSchema();
        $this->appRequestsSchema();
        $this->appErrorSchema();
        $this->appDbQuerySchema();
        $this->appCacheEventSchema();
        $this->redisEventSchema();
        $this->appLogSchema();
        $this->consoleCommandLogSchema();
        $this->consoleScheduleSchema();
        $this->appDumpsSchema();
        $this->eventsSchema();
        $this->mailsSchema();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schemaBuilder()->dropIfExists((new ConsoleSchedule)->getTable());
        $this->schemaBuilder()->dropIfExists((new AppRequest)->getTable());
        $this->schemaBuilder()->dropIfExists((new AppError)->getTable());
        $this->schemaBuilder()->dropIfExists((new DbQuery)->getTable());
        $this->schemaBuilder()->dropIfExists((new CacheEvent)->getTable());
        $this->schemaBuilder()->dropIfExists((new ConsoleCommandLog)->getTable());
        $this->schemaBuilder()->dropIfExists((new AppEntry)->getTable());
        $this->schemaBuilder()->dropIfExists((new Environment)->getTable());
        $this->schemaBuilder()->dropIfExists((new Source)->getTable());
        $this->schemaBuilder()->dropIfExists((new RedisEvent)->getTable());
        $this->schemaBuilder()->dropIfExists((new Log)->getTable());
        $this->schemaBuilder()->dropIfExists((new AppDump)->getTable());
        $this->schemaBuilder()->dropIfExists((new Event)->getTable());
        $this->schemaBuilder()->dropIfExists((new Mail)->getTable());
    }

    /**
     * @return void
     */
    protected function sourceSchema(): void
    {
        $this->schemaBuilder()->create((new Source)->getTable(), function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('symbol');

            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function environmentSchema(): void
    {
        $this->schemaBuilder()->create((new Environment)->getTable(), function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('symbol');

            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appEntriesSchema(): void
    {
        $this->schemaBuilder()->create((new AppEntry)->getTable(), function (Blueprint $table) {

            // ids
            $table->bigIncrements('id');
            $table->uuid();
            $table->uuid('batchId');

            // relation - sources
            $table->unsignedTinyInteger('source_id');
            $table->foreign('source_id')->references('id')->on((new Source)->getTable());

            // relation - environments
            $table->unsignedTinyInteger('env_id');
            $table->foreign('env_id')->references('id')->on((new Environment)->getTable());

            // hash
            $table->string('family_hash', 32);

            $table->string('model_key', 150)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appRequestsSchema(): void
    {
        $this->schemaBuilder()->create((new AppRequest)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // request data
            $table->boolean('secure');
            $table->string('protocol');
            $table->enum('method', HttpMethod::values());
            $table->string('host');
            $table->string('path');
            $table->string('url');
            $table->text('full_url');
            $table->text('query_string')->nullable();
            $table->ipAddress('ip');
            $table->jsonb('ips');
            $table->jsonb('middleware');
            $table->jsonb('headers');
            $table->text('content');
            $table->jsonb('session');
            $table->jsonb('cookies');

            // response data
            $table->unsignedSmallInteger('response_status');
            $table->text('response_content');
            $table->jsonb('response_headers');

            // metrics
            $table->float('duration')->unsigned();
            $table->float('memory')->unsigned();

            // extras
            $table->text('controller_class')->nullable();
            $table->string('controller_action')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appErrorSchema(): void
    {
        $this->schemaBuilder()->create((new AppError)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // request data
            $table->text('class');
            $table->string('file');
            $table->boolean('is_internal_file');
            $table->string('code');
            $table->unsignedInteger('line');
            $table->text('message');
            $table->jsonb('context');
            $table->jsonb('trace');
            $table->jsonb('linePreview');

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appDbQuerySchema(): void
    {
        $this->schemaBuilder()->create((new DbQuery)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->string('connection');
            $table->text('query');
            $table->float('time');
            $table->string('file')->nullable();
            $table->boolean('is_internal_file')->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->jsonb('bindings')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appCacheEventSchema(): void
    {
        $this->schemaBuilder()->create((new CacheEvent)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->enum('type', CacheEventType::values());
            $table->string('key');
            $table->text('value')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function redisEventSchema(): void
    {
        $this->schemaBuilder()->create((new RedisEvent)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->string('connection');
            $table->string('command');
            $table->string('time')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appLogSchema(): void
    {
        $this->schemaBuilder()->create((new Log)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->string('level');
            $table->text('message')->nullable();
            $table->jsonb('context')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function consoleCommandLogSchema(): void
    {
        $this->schemaBuilder()->create((new ConsoleCommandLog)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->string('command');
            $table->text('command_class');
            $table->unsignedSmallInteger('exitCode');
            $table->jsonb('arguments')->nullable();
            $table->jsonb('options')->nullable();
            $table->text('output')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function consoleScheduleSchema(): void
    {
        $this->schemaBuilder()->create((new ConsoleSchedule)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->string('command');
            $table->text('description');
            $table->string('expression');
            $table->string('timezone');
            $table->jsonb('config')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function appDumpsSchema(): void
    {
        $this->schemaBuilder()->create((new AppDump)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // query data
            $table->text('content');

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function eventsSchema(): void
    {
        $this->schemaBuilder()->create((new Event)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // event data
            $table->string('name');
            $table->jsonb('payload')->nullable();
            $table->jsonb('listeners')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    protected function mailsSchema(): void
    {
        $this->schemaBuilder()->create((new Mail)->getTable(), function (Blueprint $table) {

            $this->commonTableConfigurationForMetrics($table);

            // event data
            $table->string('subject');
            $table->text('mailable')->nullable();
            $table->boolean('queued')->nullable();
            $table->jsonb('payload')->nullable();
            $table->jsonb('from');
            $table->jsonb('replyTo');
            $table->jsonb('to');
            $table->jsonb('cc');
            $table->jsonb('bcc');
            $table->text('html')->nullable();
            $table->text('html')->nullable();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * @param Blueprint $table
     * @return void
     */
    protected function commonTableConfigurationForMetrics(Blueprint $table): void
    {
        $table->bigIncrements('id');
        $table->uuid();
        $table->string('family_hash', 32);

        // relation - app_entries
        $table->bigInteger('entry_id');
        $table->foreign('entry_id')->references('id')->on((new AppEntry)->getTable());
        $table->uuid('entry_uuid');
        $table->uuid('batch_id');

        // relation - sources
        $table->unsignedTinyInteger('source_id');
        $table->foreign('source_id')->references('id')->on((new Source)->getTable());

        // relation - environments
        $table->unsignedTinyInteger('env_id');
        $table->foreign('env_id')->references('id')->on((new Environment)->getTable());
    }

    /**
     * @return Builder
     */
    protected function schemaBuilder(): Builder
    {
        return Schema::connection(
            config('tracker-bot.storage.database.connection')
        );
    }
};
