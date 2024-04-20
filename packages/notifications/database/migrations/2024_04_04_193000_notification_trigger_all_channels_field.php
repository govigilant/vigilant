<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_triggers', function (Blueprint $table): void {
            $table->boolean('all_channels')->default(false)->after('conditions');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('notification_triggers', ['all_channels']);
    }
};
