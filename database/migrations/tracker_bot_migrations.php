<?php

use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppError;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\DbQuery;
use Delta4op\Laravel\TrackerBot\DB\Models\Source;
use Delta4op\Laravel\TrackerBot\Enums\HttpMethod;
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schemaBuilder()->dropIfExists((new AppEntry)->getTable());
        $this->schemaBuilder()->dropIfExists((new AppRequest)->getTable());
        $this->schemaBuilder()->dropIfExists((new AppError())->getTable());
        $this->schemaBuilder()->dropIfExists((new Environment)->getTable());
        $this->schemaBuilder()->dropIfExists((new Source)->getTable());
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
            $table->string('family_hash', 150);

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
            $table->string('controller_class')->nullable();
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
            $table->string('class');
            $table->string('file');
            $table->boolean('is_internal_file');
            $table->string('code');
            $table->unsignedInteger('line');
            $table->string('message');
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
     * @param $table
     * @return void
     */
    protected function commonTableConfigurationForMetrics(&$table): void
    {
        $table->bigIncrements('id');
        $table->uuid();
        $table->string('family_hash');

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
