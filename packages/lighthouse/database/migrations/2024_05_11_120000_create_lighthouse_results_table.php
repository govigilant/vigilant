<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lighthouse_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lighthouse_site_id');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->float('performance');
            $table->float('accessibility');
            $table->float('best_practices');
            $table->float('seo');

            $table->timestamps();

            $table->foreign('lighthouse_site_id')
                ->references('id')
                ->on('lighthouse_monitors')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lighthouse_results');
    }
};
