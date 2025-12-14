<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // Listar favoritos del usuario autenticado
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->with('juego')->get();
        return response()->json([
            'success' => true,
            'data' => $favorites,
        ]);
    }

    // Agregar a favoritos
    public function store(Request $request)
    {
        $request->validate([
            'juego_id' => 'required|exists:juegos,id',
        ]);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'juego_id' => $request->juego_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agregado a favoritos',
            'data' => $favorite,
        ], 201);
    }

    // Eliminar de favoritos
    public function destroy(Request $request, $juegoId)
    {
        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('juego_id', $juegoId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Eliminado de favoritos',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No encontrado en favoritos',
        ], 404);
    }
}
