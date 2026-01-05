<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Adiciona colunas de contagem de acessos às tabelas corretas:
     * - materia_legislativa (nome real da tabela de matérias)
     * - parlamentar (nome real da tabela de parlamentares)
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('materia_legislativa', function (Blueprint $table) {
            $table->unsignedBigInteger('acessos')->default(0)->after('observacao');
            $table->index('acessos');
        });

        Schema::table('parlamentar', function (Blueprint $table) {
            $table->unsignedBigInteger('acessos')->default(0)->after('fotografia');
            $table->index('acessos');
        });
    }

    /**
     * Reverte as migrações.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('materia_legislativa', function (Blueprint $table) {
            $table->dropIndex(['acessos']);
            $table->dropColumn('acessos');
        });

        Schema::table('parlamentar', function (Blueprint $table) {
            $table->dropIndex(['acessos']);
            $table->dropColumn('acessos');
        });
    }
};