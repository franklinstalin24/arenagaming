<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class UploadController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Upload de imagen a Firebase Storage
     * POST /api/upload/image
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
        ]);

        try {
            $file = $request->file('image');
            $folder = $request->input('folder', 'juegos'); // default: juegos
            
            $url = $this->firebase->uploadImage($file, $folder);

            return response()->json([
                'success' => true,
                'message' => 'Imagen subida correctamente',
                'url' => $url,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir imagen: ' . $e->getMessage(),
            ], 500);
        }
    }
}
