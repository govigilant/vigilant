<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_history', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Channel::class)
                ->index()
                ->constrained('notification_channels')
                ->cascadeOnDelete();

            $table->foreignIdFor(Trigger::class)
                ->nullable()
                ->index()
                ->constrained('notification_triggers')
                ->cascadeOnDelete();

            $table->string('notification');
            $table->string('uniqueId');
            $table->json('data');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_history');
    }
};
