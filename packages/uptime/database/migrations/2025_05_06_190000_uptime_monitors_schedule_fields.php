<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropColumns('uptime_monitors', ['interval']);

        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->timestamp('next_run')->nullable()->after('settings');
            $table->integer('interval')->default(60)->after('next_run');
            $table->string('state')->default('up')->after('name');
            $table->integer('try')->default(0)->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->dropColumn(['next_run', 'interval', 'state']);
        });

        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->string('interval')->default('* * * * *')->after('settings');
        });
    }
};
