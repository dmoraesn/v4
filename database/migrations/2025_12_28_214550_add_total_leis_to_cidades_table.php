<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            $table->unsignedInteger('total_leis')->default(0)->after('brasao');
            $table->index('total_leis');
        });
    }

    public function down(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            $table->dropIndex(['total_leis']);
            $table->dropColumn('total_leis');
        });
    }
};