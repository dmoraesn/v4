<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            if (!Schema::hasColumn('cidades', 'brasao')) {
                $table
                    ->string('brasao')
                    ->nullable()
                    ->after('sapl');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            if (Schema::hasColumn('cidades', 'brasao')) {
                $table->dropColumn('brasao');
            }
        });
    }
};
