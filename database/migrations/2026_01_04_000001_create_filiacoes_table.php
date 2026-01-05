<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migração.
     *
     * Cria tabela local para armazenar filiações partidárias dos parlamentares.
     * O SAPL mantém partido em modelo separado (Filiacao), relacionado ao Parlamentar.
     * Esta tabela permite capturar a sigla do partido atual (ou mais recente) de cada parlamentar.
     */
    public function up(): void
    {
        Schema::create('filiacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained('cidades')->onDelete('cascade');
            $table->foreignId('parlamentar_id')->constrained('parlamentar')->onDelete('cascade');
            $table->unsignedBigInteger('sapl_id'); // ID da filiação no SAPL (útil para atualizações futuras)
            $table->string('partido_sigla', 20)->nullable(); // Sigla do partido (ex: PP, PSDB)
            $table->string('partido_nome', 100)->nullable(); // Nome completo do partido (opcional)
            $table->date('data_filiacao')->nullable();
            $table->date('data_desfiliacao')->nullable();
            $table->boolean('atual')->default(true); // Indica se é a filiação vigente
            $table->timestamps();

            // Índices para performance
            $table->unique(['parlamentar_id', 'atual']); // Garante uma única filiação atual por parlamentar
            $table->index(['cidade_id', 'partido_sigla']);
            $table->index('atual');
        });
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        Schema::dropIfExists('filiacoes');
    }
};