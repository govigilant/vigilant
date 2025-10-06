<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uptime_outposts', function (Blueprint $table): void {
            $table->id();

            $table->string('ip');
            $table->integer('port');
            $table->string('external_ip');

            $table->string('status')->index();

            $table->string('country')->nullable();
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();

            $table->dateTime('last_available_at');
            $table->timestamps();

            $table->unique(['ip', 'port']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uptime_outposts');
    }
};
