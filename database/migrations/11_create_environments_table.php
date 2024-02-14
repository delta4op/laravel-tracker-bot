<?php

use Delta4op\Laravel\TrackerBot\DB\Models\Environment\Environment;
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
        Schema::create((new Environment)->getTable(), function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('symbol');

            $table->timestamps();
        });
    }
};
