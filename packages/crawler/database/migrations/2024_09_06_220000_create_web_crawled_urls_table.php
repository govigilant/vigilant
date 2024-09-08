<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Users\Models\Team;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_crawled_urls', function (Blueprint $table) {
            $table->uuid();
            $table->foreignIdFor(Crawler::class)->nullable()->constrained('web_crawlers')->onDelete('cascade');
            $table->foreignIdFor(Team::class)->constrained()->onDelete('cascade');
            $table->uuid('found_on_id')->nullable();

            $table->boolean('crawled')->default(false);
            $table->string('url', 2048);
            $table->integer('status')->nullable();

            $table->timestamps();

            $table->index(['crawler_id', 'crawled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_crawled_urls');
    }
};
