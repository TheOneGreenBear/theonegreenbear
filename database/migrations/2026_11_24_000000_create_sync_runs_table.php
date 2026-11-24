<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per provider sync attempt, so the dashboard can show when each last ran and why it failed.
        Schema::create('sync_runs', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('status');
            $table->unsignedInteger('items')->nullable();
            $table->text('error')->nullable();
            $table->unsignedInteger('duration_ms');
            $table->timestamps();

            $table->index(['provider', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_runs');
    }
};
