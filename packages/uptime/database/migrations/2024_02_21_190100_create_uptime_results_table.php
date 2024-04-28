<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Uptime\Models\Monitor;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uptime_results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Monitor::class)->index()->constrained()->onDelete('cascade');

            $table->float('total_time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uptime_results');
    }
};
