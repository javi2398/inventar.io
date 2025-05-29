<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {
        // Validar la solicitud
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar al usuario
        $user = User::where('email', $request->email)->first();

        // Verificar las credenciales del usuario
        if ($user && Hash::check($request->password, $user->password)) {
            // Crear un token para el usuario
            $token = $user->createToken('MiAppToken')->plainTextToken;
            // Retornar el token como respuesta
            return response()->json(['token' => $token], 200);
        }

        // Si las credenciales son incorrectas
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Logout and invalidate the token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
