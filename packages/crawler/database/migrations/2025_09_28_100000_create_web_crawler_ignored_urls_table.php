<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vigilant\Crawler\Models\Crawler;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_crawler_ignored_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Crawler::class)->nullable()->constrained('web_crawlers')->onDelete('cascade');

            $table->string('url_hash');

            $table->timestamps();

            $table->unique(['crawler_id', 'url_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_crawler_ignored_urls');
    }
};
