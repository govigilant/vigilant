<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Healthchecks\Models\Healthcheck;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('healthcheck_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Healthcheck::class)->constrained()->onDelete('cascade');
            $table->integer('run_id')->nullable();

            $table->string('key');
            $table->string('status');
            $table->string('message')->nullable();
            $table->json('data')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthcheck_results');
    }
};
