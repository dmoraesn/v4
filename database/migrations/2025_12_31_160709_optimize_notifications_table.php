<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Executa a migration.
     *
     * Esta migration foi desativada porque o índice já existe no Laravel padrão.
     * Mantida apenas para não quebrar o histórico de migrations.
     */
    public function up(): void
    {
        // Índice já existente no framework — nada a fazer
    }

    /**
     * Reverte a migration.
     */
    public function down(): void
    {
        // Nada a reverter
    }
};