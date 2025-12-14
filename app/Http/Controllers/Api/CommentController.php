<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    // Listar comentarios de un juego (público - no requiere auth)
    public function index($juegoId)
    {
        try {
            $comments = Comment::where('juego_id', $juegoId)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($c) {
                    return [
                        'id' => $c->id,
                        'user_name' => $c->user ? $c->user->name : 'Usuario',
                        'comment' => $c->comment,
                        'created_at' => $c->created_at->diffForHumans(),
                    ];
                });

            return response()->json($comments);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    // Crear comentario (requiere autenticación)
    public function store(Request $request)
    {
        $request->validate([
            'juego_id' => 'required|exists:juegos,id',
            'comment' => 'required|string|max:200',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'juego_id' => $request->juego_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comentario creado',
            'data' => $comment,
        ], 201);
    }

    // Eliminar comentario (solo el autor o admin)
    public function destroy(Request $request, $commentId)
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado',
            ], 404);
        }

        // Solo el autor o un admin puede eliminar
        if ($request->user()->id !== $comment->user_id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado',
        ]);
    }
}
