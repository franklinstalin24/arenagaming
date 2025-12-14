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
        $pc = \App\Models\Plataforma::firstOrCreate(['slug' => 'pc'], ['nombre' => 'PC']);
        $ps5 = \App\Models\Plataforma::firstOrCreate(['slug' => 'ps5'], ['nombre' => 'PlayStation 5']);

        // 2. Crear Géneros
        $accion = \App\Models\Genero::firstOrCreate(['slug' => 'accion'], ['nombre' => 'Acción']);
        $rpg = \App\Models\Genero::firstOrCreate(['slug' => 'rpg'], ['nombre' => 'RPG']);

        // 3. Crear Juegos demo con imagenes (usar URLs públicas de demo)
        $j1 = \App\Models\Juego::firstOrCreate(
            ['titulo' => 'The Legend of Laravel'],
            [
                'descripcion_corta' => 'Aprende backend jugando',
                'precio_normal' => 50.00,
                'precio_oferta' => 39.99,
                'plataforma_id' => $pc->id,
                'imagen_url' => 'https://picsum.photos/seed/laravel/800/600',
                'activo' => true,
            ]
        );
        $j2 = \App\Models\Juego::firstOrCreate(
            ['titulo' => 'PHP Adventures'],
            [
                'descripcion_corta' => 'Explora patrones de diseño',
                'precio_normal' => 59.99,
                'plataforma_id' => $ps5->id,
                'imagen_url' => 'https://picsum.photos/seed/php/800/600',
                'activo' => true,
            ]
        );

        // 4. Vincular géneros (Tabla Pivote)
        $j1->generos()->syncWithoutDetaching([$accion->id, $rpg->id]);
        $j2->generos()->syncWithoutDetaching([$accion->id]);

        // 5. Usuario Admin demo
        User::firstOrCreate(
            ['email' => 'franklin@arenagaming.com'],
            [
                'name' => 'franklin',
                'password' => bcrypt('securepassword'),
                'role' => 'admin',
            ]
        );
    }
}