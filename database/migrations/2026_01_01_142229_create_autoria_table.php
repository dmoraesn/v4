<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     *
     * Tabela pivot Autoria fiel ao SAPL.
     */
    public function up(): void
    {
        Schema::create('autoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained('materia_legislativa')->onDelete('cascade');
            $table->foreignId('parlamentar_id')->constrained('parlamentar')->onDelete('cascade');
            $table->boolean('primeiro_autor')->default(false);
            $table->timestamps();

            $table->unique(['materia_id', 'parlamentar_id']);
            $table->index('primeiro_autor');
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('autoria');
    }
};