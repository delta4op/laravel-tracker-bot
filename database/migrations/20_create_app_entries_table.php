<?php

use Delta4op\Laravel\TrackerBot\DB\Models\Environment\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Source\Source;
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
        Schema::create((new Delta4op\Laravel\TrackerBot\DB\Models\AppEntry\AppEntry)->getTable(), function (Blueprint $table) {

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
};
