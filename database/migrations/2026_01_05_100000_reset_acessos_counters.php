<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Zera os contadores de acesso em matérias e parlamentares.
     *
     * @return void
     */
    public function up(): void
    {
        // Zera o contador de acessos em todas as matérias
        DB::table('materia_legislativa')
            ->update(['acessos' => 0]);

        // Zera o contador de acessos em todos os parlamentares
        DB::table('parlamentar')
            ->update(['acessos' => 0]);
    }

    /**
     * Reverte a migração (não aplicável neste caso, pois é apenas reset).
     *
     * @return void
     */
    public function down(): void
    {
        // Não há reversão prática para zerar contadores
    }
};