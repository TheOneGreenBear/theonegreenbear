<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mal_id')->unique();
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->string('media_type')->nullable();
            $table->unsignedInteger('num_episodes')->default(0)->comment('0 when unknown or still airing');
            $table->decimal('mean', 4, 2)->nullable()->comment('MAL community score');
            $table->string('status')->index()->comment('watching, completed, on_hold, dropped, plan_to_watch');
            $table->unsignedTinyInteger('score')->default(0)->comment('my score, 0 = not scored');
            $table->unsignedInteger('episodes_watched')->default(0);
            $table->boolean('is_rewatching')->default(false);
            $table->timestamp('list_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_entries');
    }
};
