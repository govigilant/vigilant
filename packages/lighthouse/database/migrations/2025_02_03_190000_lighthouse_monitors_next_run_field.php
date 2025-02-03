<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighthouse_monitors', function (Blueprint $table): void {
            $table->dateTime('next_run')->after('interval');
            $table->integer('interval')->change();
        });
    }

    public function down(): void
    {
        Schema::dropColumns('lighthouse_monitors', ['next_run']);

        Schema::table('lighthouse_monitors', function (Blueprint $table): void {
            $table->string('interval')->change();
        });
    }
};
