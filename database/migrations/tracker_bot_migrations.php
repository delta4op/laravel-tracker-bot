<?php

use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\Environment\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest\AppRequest;
use Delta4op\Laravel\TrackerBot\DB\Models\Source\Source;
use Delta4op\Laravel\TrackerBot\Enums\HttpContentType;
use Delta4op\Laravel\TrackerBot\Enums\HttpMethod;
use Delta4op\Laravel\TrackerBot\Enums\RequestProtocol;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->dropIfExists((new AppEntry)->getTable());
        $schema->dropIfExists((new AppRequest)->getTable());
        $schema->dropIfExists((new Environment)->getTable());
        $schema->dropIfExists((new Source)->getTable());
    }

    /**
     * @return void
     */
    protected function sourceSchema(): void
    {
        Schema::create((new Source)->getTable(), function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('symbol');

            $table->timestamps();
        });
    }

    protected function environmentSchema(): void
    {
        Schema::create((new Environment)->getTable(), function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('symbol');

            $table->timestamps();
        });
    }

    protected function appEntriesSchema(): void
    {
        Schema::create((new AppEntry)->getTable(), function (Blueprint $table) {

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

            $table->

            $table->timestamps();
        });
    }

    protected function appRequestsSchema(): void
    {
        Schema::create((new AppRequest())->getTable(), function (Blueprint $table) {

            // ids
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

            // request data
            $table->enum('protocol', RequestProtocol::values())->nullable();
            $table->enum('method', HttpMethod::values());
            $table->enum('content_type', HttpContentType::values());
            $table->string('host');
            $table->string('path');
            $table->string('url');
            $table->ipAddress('ip');
            $table->jsonb('ips');
            $table->jsonb('middleware');
            $table->jsonb('headers');
            $table->string('content');
            $table->jsonb('session');
            $table->jsonb('cookies');

            // response data
            $table->unsignedSmallInteger('response_status');
            $table->string('response_content');
            $table->enum('response_content_type', HttpContentType::values());
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
};
