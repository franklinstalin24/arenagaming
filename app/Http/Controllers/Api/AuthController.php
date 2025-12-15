<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user||!Hash::check($request->password, $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        //token creation

        $token = $user->createToken($user->role)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Autenticación exitosa',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    //register user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'cliente', // rol por defecto
        ]);

        $token = $user->createToken('cliente')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    //logout token

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cierre de sesión exitoso'
        ], 200);
    }

}