<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_channels', function (Blueprint $table): void {
            $table->string('name')->nullable()->after('channel');
        });
    }

    public function down(): void
    {
        Schema::table('notification_channels', function (Blueprint $table): void {
            $table->dropColumn('name');
        });
    }
};
