<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plataforma extends Model
{
    use HasFactory;

    //mapeo de la tabla
    protected $table = 'plataformas';

    //asignacion masiva
    protected $fillable = ['nombre', 'slug',];

    //relacion uno a muchos
    public function juegos()
    {
        //modelo fk de la otra tabla
        return $this->hasMany(Juego::class, 'plataforma_id');
    }
}
