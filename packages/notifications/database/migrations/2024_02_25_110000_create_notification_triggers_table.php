<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Team::class);

            $table->string('notification')->index();

            $table->json('conditions');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_triggers');
    }
};
