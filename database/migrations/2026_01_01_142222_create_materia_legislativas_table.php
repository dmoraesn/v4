<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Cria tabela fiel ao modelo MateriaLegislativa do SAPL.
     */
    public function up(): void
    {
        Schema::create('materia_legislativa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained('cidades')->onDelete('cascade');
            $table->unsignedBigInteger('sapl_id'); // ID original no SAPL
            $table->string('tipo_sigla', 10);
            $table->string('tipo_descricao', 100);
            $table->unsignedInteger('numero');
            $table->unsignedInteger('ano');
            $table->date('data_apresentacao')->nullable();
            $table->date('data_publicacao')->nullable();
            $table->text('ementa');
            $table->string('texto_integral')->nullable(); // URL do PDF
            $table->boolean('em_tramitacao')->default(true);
            $table->string('regime_tramitacao', 50)->nullable();
            $table->text('indexacao')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['cidade_id', 'sapl_id']);
            $table->index(['cidade_id', 'ano', 'numero']);
            $table->index('em_tramitacao');

            // Índice FULLTEXT para busca local rápida (MySQL)
            $table->fullText(['ementa', 'indexacao', 'observacao']);
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('materia_legislativa');
    }
};