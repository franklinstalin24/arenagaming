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
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion_corta') ->nullable();
            $table->string('descripcion_larga') ->nullable();

            //precios
            $table->decimal('precio_normal', 8, 2)->nullable();
            $table->decimal('precio_oferta', 8, 2)->nullable();

            //firebase storage
            $table->string('imagen_url', 500)->nullable();

            //estados
            $table->boolean('es_destacado')->default(false);
            $table->boolean('activo')->default(true);

            //relacion con la plataforma(1 a muchos)
            $table->foreignId('plataforma_id')
            ->nullable()
            ->constrained('plataformas')
            ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juegos');
    }
};
