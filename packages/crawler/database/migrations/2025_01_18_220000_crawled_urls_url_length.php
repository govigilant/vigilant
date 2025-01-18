<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_crawled_urls', function (Blueprint $table) {
            $table->string('url', 8192)->change();
        });
    }
};
