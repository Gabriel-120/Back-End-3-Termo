<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->unique()->constrained('produtos')->cascadeOnDelete();
            $table->integer('quantidade')->default(0);
            $table->integer('quantidade_minima')->default(10);
            $table->string('localizacao')->nullable();
            $table->string('lote')->nullable();
            $table->date('data_validade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};
