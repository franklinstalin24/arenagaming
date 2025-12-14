<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genero extends Model
{
    use HasFactory;

    //mapeo de la tabla
    protected $table = 'generos';

    //asignacion masiva
    protected $fillable = ['nombre', 'slug',];

    //relacion uno a muchos
    public function juegos()
    {
        //relacion muchos a muchos con la tabla pivote 'juego_genero'
        return $this->belongsToMany(Juego::class, 'juego_genero', 'genero_id', 'juego_id');
    }
}
