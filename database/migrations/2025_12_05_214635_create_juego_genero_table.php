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
        Schema::create('juego_genero', function (Blueprint $table) {
            $table->id();

            //fk juego
            $table->foreignId('juego_id')
            ->constrained('juegos')
            ->cascadeOnDelete();

            //fk genero
            $table->foreignId('genero_id')
            ->constrained('generos')
            ->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juego_genero');
    }
};
