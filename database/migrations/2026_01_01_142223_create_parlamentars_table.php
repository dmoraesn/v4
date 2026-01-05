<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Tabela fiel ao modelo Parlamentar do SAPL.
     */
    public function up(): void
    {
        Schema::create('parlamentar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained('cidades')->onDelete('cascade');
            $table->unsignedBigInteger('sapl_id');
            $table->string('nome_parlamentar', 150);
            $table->boolean('ativo')->default(true);
            $table->string('fotografia')->nullable(); // URL ou caminho da foto
            $table->timestamps();

            $table->unique(['cidade_id', 'sapl_id']);
            $table->index('nome_parlamentar');
            $table->index('ativo');
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('parlamentar');
    }
};