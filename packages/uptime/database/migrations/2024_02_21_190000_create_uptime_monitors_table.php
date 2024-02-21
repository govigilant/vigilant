<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Sites\Models\Site;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uptime_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable();

            $table->string('name');
            $table->string('type');

            $table->json('settings');
            $table->string('interval');

            $table->integer('retries');
            $table->integer('timeout');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uptime_monitors');
    }
};
