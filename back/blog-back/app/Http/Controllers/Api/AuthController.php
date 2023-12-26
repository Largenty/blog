<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'logout']);
    }

    public function updateUser(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
        ]);

        $user->update($validatedData);

        return response()->json(['message' => 'Utilisateur mis à jour avec succès.']);
    }

    public function modifyPassword(Request $request)
    {

        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect.'], 403);
        }

        $user->password = Hash::make($validatedData['new_password']);
        $user->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }

}
