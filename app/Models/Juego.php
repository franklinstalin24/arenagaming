<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Juego extends Model
{
    use HasFactory;
    
    //mapeo de la tabla
    protected $table = 'juegos';

    //asignacion masiva
    protected $fillable = [
        'titulo', 
        'descripcion_corta', 
        'descripcion_larga', 
        'precio_normal',
        'precio_oferta', 
        'imagen_url', 
        'es_destacado', 
        'activo',
        'plataforma_id',];

    //relacion muchos a uno
    public function plataforma()
    {
        //modelo fk de la otra tabla
        return $this->belongsTo(Plataforma::class, 'plataforma_id');
    }

    //relacion muchos a muchos
    public function generos()
    {
        //modelo fk de la otra tabla
        return $this->belongsToMany(Genero::class, 'juego_genero', 'juego_id', 'genero_id');
    }
}
