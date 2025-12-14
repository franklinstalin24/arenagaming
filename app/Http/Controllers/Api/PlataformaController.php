<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\BaseController; // Change this to the correct base controller if needed
// Removed duplicate import
use Illuminate\Routing\Controller; // Correct import for the base controller
use Illuminate\Http\Request;
 
use App\Models\Plataforma;
 
class PlataformaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 
    //GET
    public function index()
    {
        $plataformas = Plataforma::select('id', 'nombre', 'slug')->get();
 
        return response()->json($plataformas, 200);
        
    }
 
    /**
     * Store a newly created resource in storage.
     */
 
    //POST
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|unique:plataformas,slug'
        ]);
 
        $plataforma = Plataforma::create($request->all());
 
        return response()->json([
            'success' => true,
            'data' => $plataforma
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
 
    //PUT
    public function update(Request $request, string $id)
    {
        $plataforma = Plataforma::find($id);
 
        if (!$plataforma) {
            return response()->json(
                ['success' => false, 'message' => 'No encontrada']
                , 404);
        }
 
        $request -> validate([
            'nombre' => 'sometimes|string|max:100',
            'slug' => 'sometimes|string|unique:plataformas,slug,'.$id
        ]);
 
        $plataforma->update($request->all());
 
        return response()->json([
            'success' => true,
            'data' => $plataforma
        ], 200);
    }
 
    /**
     * Remove the specified resource from storage.
     */
 
    //DELETE    
    public function destroy(string $id)
    {
        $plataforma = Plataforma::find($id);
 
        if (!$plataforma) {
            return response()->json(
                ['success' => false, 'message' => 'No encontrada']
                , 404);
        }
 
        // Al borrar una plataforma, los juegos asociados
        // se quedan con plataforma_id = null
 
        $plataforma->delete();
 
        return response()->json([
            'success' => true,
            'message' => 'Plataforma eliminada'
        ], 200);
    }
}
