<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migração.
     *
     * Cria tabela local para armazenar partidos políticos únicos.
     * O SAPL possui model Partido separado, com sigla e nome.
     * Esta tabela serve como cache local para evitar duplicatas e permitir edição manual futura.
     */
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sapl_id')->nullable(); // ID do partido no SAPL (se disponível)
            $table->string('sigla', 20)->unique(); // Sigla oficial (ex: PP, PSDB)
            $table->string('nome', 150); // Nome completo do partido
            $table->timestamps();

            $table->index('sigla');
            $table->index('sapl_id');
        });
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};