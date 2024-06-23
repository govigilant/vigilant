<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lighthouse_result_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LighthouseResult::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->string('audit')->index();
            $table->string('title', 1024);
            $table->string('explanation', 1024)->nullable();
            $table->text('description')->nullable();
            $table->float('score')->nullable();
            $table->string('scoreDisplayMode');
            $table->json('details')->nullable();
            $table->json('warnings')->nullable();
            $table->json('items')->nullable();
            $table->json('metricSavings')->nullable();
            $table->float('guidanceLevel')->nullable();

            $table->float('numericValue')->nullable();
            $table->string('numericUnit')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lighthouse_result_audits');
    }
};
