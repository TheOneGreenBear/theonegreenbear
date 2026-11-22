<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // OAuth tokens for services the site reads on the owner's behalf (stored encrypted).
        Schema::create('service_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->unique();
            $table->text('access_token');
            $table->text('refresh_token');
            $table->timestamp('expires_at');
            $table->string('scopes')->nullable();
            $table->timestamps();
        });

        // Latest synced data for providers that don't need their own tables.
        Schema::create('provider_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->unique();
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_snapshots');
        Schema::dropIfExists('service_tokens');
    }
};
