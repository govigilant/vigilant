<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Healthchecks\Models\Healthcheck;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('healthcheck_metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Healthcheck::class)->constrained()->onDelete('cascade');

            $table->string('key');
            $table->decimal('value');
            $table->string('unit')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthcheck_metrics');
    }
};
