<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channel_notification_trigger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_channel_id');
            $table->unsignedBigInteger('notification_trigger_id');
            $table->timestamps();

            $table->foreign('notification_channel_id', 'channel_id')
                ->references('id')->on('notification_channels')->onDelete('cascade');
            $table->foreign('notification_trigger_id', 'trigger_id')
                ->references('id')->on('notification_triggers')->onDelete('cascade');

            $table->unique(['notification_channel_id', 'notification_trigger_id'], 'unique_notification_channel_trigger');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_channel_notification_trigger');
    }
};
