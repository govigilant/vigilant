<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Uptime\Models\Outpost;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->foreignIdFor(Outpost::class, 'closest_outpost_id')->after('team_id')->nullable()->constrained('uptime_outposts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('uptime_monitors', function (Blueprint $table): void {
            $table->dropForeign(['closest_outpost_id']);
            $table->dropColumn('closest_outpost_id');
        });
    }
};
