<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
           $table->string('timezone')->after('personal_team')->default('UTC');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('teams', ['timezone']);
    }
};
