<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lighthouse_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->string('url');

            $table->json('settings');
            $table->string('interval');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lighthouse_monitors');
    }
};
