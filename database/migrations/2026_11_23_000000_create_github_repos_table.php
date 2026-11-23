<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('github_repos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('github_id')->unique();
            $table->string('name');
            $table->string('full_name');
            $table->text('description')->nullable();
            $table->string('html_url');
            $table->string('homepage')->nullable();
            $table->string('language')->nullable()->index();
            $table->unsignedInteger('stars')->default(0);
            $table->unsignedInteger('forks')->default(0);
            $table->json('topics');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('pushed_at')->nullable();
            $table->timestamp('repo_created_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('github_repos');
    }
};
