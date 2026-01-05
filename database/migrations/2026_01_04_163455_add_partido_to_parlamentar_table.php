<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parlamentar', function (Blueprint $table) {
            $table->string('partido')->nullable()->after('nome_parlamentar');
            $table->index('partido');
        });
    }

    public function down(): void
    {
        Schema::table('parlamentar', function (Blueprint $table) {
            $table->dropColumn('partido');
        });
    }
};