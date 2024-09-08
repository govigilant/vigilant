<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Crawler\Enums\State;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_crawlers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');

            $table->string('state')->default(State::Pending->value);
            $table->string('start_url');
            $table->json('sitemaps')->nullable();

            $table->json('crawler_stats')->nullable();
            $table->json('settings')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_crawlers');
    }
};
