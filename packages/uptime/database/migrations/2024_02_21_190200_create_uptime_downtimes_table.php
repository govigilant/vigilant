<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Uptime\Models\Monitor;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uptime_downtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Monitor::class)->index()->constrained('uptime_monitors')->onDelete('cascade');

            $table->dateTime('start');
            $table->dateTime('end')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uptime_downtimes');
    }
};
