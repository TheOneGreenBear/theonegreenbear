<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steam_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appid')->unique();
            $table->string('name');
            $table->unsignedInteger('playtime_forever')->default(0)->comment('minutes');
            $table->unsignedInteger('playtime_2weeks')->nullable()->comment('minutes; null when not played recently');
            $table->timestamp('last_played_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steam_games');
    }
};
