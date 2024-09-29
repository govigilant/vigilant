<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_onboarding_step', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Team::class)->index();

            $table->string('step')->nullable();
            $table->dateTime('finished_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_onboarding_step');
    }
};
