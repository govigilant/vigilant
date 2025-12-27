<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('uptime_monitors')
            ->where('type', '=', 'ping')
            ->update(['type' => 'tcp']);
    }

    public function down(): void
    {
        DB::table('uptime_monitors')
            ->where('type', '=', 'icmp')
            ->update(['type' => 'ping']);
    }
};
