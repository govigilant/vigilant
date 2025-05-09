<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_crawled_urls', function (Blueprint $table): void {
            $table->string('url_hash')->after('url');

            $table->index(['crawler_id', 'url_hash']);
        });
    }
};
