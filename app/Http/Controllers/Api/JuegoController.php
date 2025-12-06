<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Juego::with(['plataforma', 'generos']) ->where('activo', true);

        if ($request->has('buscar')) {
            $termino = $request->input('buscar');
            $query->where('titulo', 'like', '%' . $termino . '%');
        }

        $juegos = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'cantidad' => $juegos->count(),
            'data' => $juegos,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion_corta' => 'nullable|string',
            'descripcion_larga' => 'nullable|string',
            'precio_normal' => 'rquired|numeric',
            'precio_oferta' => 'nullable|numeric|lt:precio_normal',
            'imagen_url' => 'nullable|string',
            'es_destacado' => 'boolean',
            'activo' => 'boolean',
            'plataforma_id' => 'required|exists:plataformas,id',
            'generos' => 'array',
            'generos.*' => 'exists:generos,id',
        ]);

        $juego = Juego::create($request->all());

        if($request->has('generos')){
            $juego->generos()->sync($request->input('generos'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Juego creado exitosamente',
            'data' => $juego -> load('generos')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
