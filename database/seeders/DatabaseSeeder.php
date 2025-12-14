<?php
 
namespace Database\Seeders;
 
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Plataformas
    $pc = \App\Models\Plataforma::create(['nombre' => 'PC', 'slug' => 'pc']);
    $ps5 = \App\Models\Plataforma::create(['nombre' => 'PlayStation 5', 'slug' => 'ps5']);
 
    // 2. Crear Géneros
    $accion = \App\Models\Genero::create(['nombre' => 'Acción', 'slug' => 'accion']);
    $rpg = \App\Models\Genero::create(['nombre' => 'RPG', 'slug' => 'rpg']);
 
    // 3. Crear Juego vinculado
    $juego = \App\Models\Juego::create([
        'titulo' => 'The Legend of Laravel',
        'descripcion_corta' => 'Aprende backend jugando',
        'precio_normal' => 50.00,
        'plataforma_id' => $pc->id, // Usamos el ID del objeto creado arriba
        'activo' => true
    ]);
 
    // 4. Vincular géneros (Tabla Pivote)
    // Esto prueba que la relación N:N funciona
    $juego->generos()->attach([$accion->id, $rpg->id]);
    
    // 5. Usuario Admin
    \App\Models\User::create([
        'name' => 'franklin',
        'email' => 'franklin@arenagaming.com',
        'password', => bcrypt('securepassword'),
        'role' => 'admin'
    ]);
    }
}