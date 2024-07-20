<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dns_monitor_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DnsMonitor::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->string('type');
            $table->string('value');
            $table->json('geoip')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dns_monitor_histories');
    }
};
