<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController; // Solución: Usar el controlador base de Laravel si el de la app no está disponible
use Illuminate\Http\Request;
use App\Models\Juego;
use App\Services\FirebaseService;

class JuegoController extends BaseController // Se extiende del alias BaseController
{
    protected $firebaseService;
    
    // Corregido: Asignar la instancia inyectada por Laravel
    public function __construct(FirebaseService $firebaseService)
    {
        // En un entorno de producción con DI, solo se usaría $this->firebaseService = $firebaseService;
        // Sin embargo, si el servicio no está registrado correctamente, la línea de abajo puede ser un fallback temporal.
        // Pero idealmente, deberíamos usar la inyección:
        $this->firebaseService = $firebaseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Juego::with(['plataforma', 'generos'])->where('activo', true);

        if ($request->has('buscar')) {
            $termino = $request->input('buscar');
            $query->where('titulo', 'like', '%' . $termino . '%');
        }

        $perPage = (int) $request->input('per_page', 12);
        $juegos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'cantidad' => $juegos->total(),
            'data' => $juegos->items(),
            'pagination' => [
                'current_page' => $juegos->currentPage(),
                'per_page' => $juegos->perPage(),
                'last_page' => $juegos->lastPage(),
                'total' => $juegos->total(),
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion_corta' => 'nullable|string',
            'descripcion_larga' => 'nullable|string',
            'precio_normal' => 'required|numeric',
            'precio_oferta' => 'nullable|numeric|lt:precio_normal',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'es_destacado' => 'boolean',
            'activo' => 'boolean',
            'plataforma_id' => 'required|exists:plataformas,id',
            'generos' => 'array',
            'generos.*' => 'exists:generos,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $url = $this->firebaseService->uploadImage($request->file('imagen'), 'juegos');
            $data['imagen_url'] = $url; // guarda la url en el campo imagen_url
        }

        $juego = Juego::create($data);

        if($request->has('generos')){
            $juego->generos()->sync($request->input('generos'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Juego creado exitosamente',
            'data' => $juego->load('generos')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $juego = Juego::with(['plataforma', 'generos'])->where('activo', true)->find($id);

        if (!$juego) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $juego,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $juego = Juego::find($id);

        if (!$juego) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado',
            ], 404);
        }
        $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion_corta' => 'sometimes|nullable|string',
            'descripcion_larga' => 'nullable|string',
            'precio_normal' => 'sometimes|required|numeric',
            'precio_oferta' => 'nullable|numeric|lt:precio_normal',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'es_destacado' => 'sometimes|boolean',
            'activo' => 'sometimes|boolean',
            'plataforma_id' => 'sometimes|required|exists:plataformas,id',
            'generos' => 'sometimes|array',
            'generos.*' => 'exists:generos,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $url = $this->firebaseService->uploadImage($request->file('imagen'), 'juegos');
            $data['imagen_url'] = $url; 
        }
        
        $juego->update($data);

        if($request->has('generos')){
            $juego->generos()->sync($request->input('generos'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Juego actualizado exitosamente',
            'data' => $juego->load('generos'),
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $juego = Juego::find($id);

        if (!$juego) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado',
            ], 404);
        }
        
        $juego->delete();    

        return response()->json([
            'success' => true,
            'message' => 'Juego eliminado exitosamente',
        ], 200);
    }
}