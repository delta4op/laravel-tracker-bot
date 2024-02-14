<?php

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
        Schema::create((new Source)->getTable(), function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('symbol');

            $table->timestamps();
        });
    }
};
