<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Adiciona campos para controle de sincronização local dos dados do SAPL.
     */
    public function up(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable()->after('total_leis');
            $table->integer('total_leis_local')->default(0)->after('last_sync_at');
            $table->index('last_sync_at');
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::table('cidades', function (Blueprint $table) {
            $table->dropColumn(['last_sync_at', 'total_leis_local']);
        });
    }
};