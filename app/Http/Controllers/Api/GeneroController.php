<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Genero;
use Illuminate\Http\Request;
 
class GeneroController extends Controller
{
    public function index()
    {
        return response()->json(Genero::all(), 200);
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|unique:generos,slug'
        ]);
 
        $genero = Genero::create($request->all());
 
        return response()->json(['success' => true, 'data' => $genero], 201);
    }
 
    public function show($id)
    {
        $genero = Genero::find($id);
        return $genero
            ? response()->json(['success' => true, 'data' => $genero], 200)
            : response()->json(['success' => false, 'message' => 'No encontrado'], 404);
    }
 
    public function update(Request $request, $id)
    {
        $genero = Genero::find($id);
        if (!$genero) return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
 
        $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'slug' => 'sometimes|string|unique:generos,slug,' . $id
        ]);
 
        $genero->update($request->all());
        return response()->json(['success' => true, 'data' => $genero], 200);
    }
 
    public function destroy($id)
    {
        $genero = Genero::find($id);
        if (!$genero) return response()->json(['success' => false, 'message' => 'No encontrado'], 404);
 
        // Al borrar el género, se borra la relación en la tabla pivote automáticamente
        // gracias al 'cascadeOnDelete' que pusimos en la migración juego_genero.
        $genero->delete();
 
        return response()->json(['success' => true, 'message' => 'Género eliminado'], 200);
    }
}