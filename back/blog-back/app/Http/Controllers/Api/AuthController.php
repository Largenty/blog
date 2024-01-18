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
        // Valider les données de requête. Ici, on s'assure que l'email et le mot de passe sont fournis.
        $request->validate([
            'email' => 'required|email', // L'email doit être un email valide et ne doit pas être vide.
            'password' => 'required' // Le mot de passe ne doit pas être vide.
        ]);

        // Tente de récupérer l'utilisateur par son email. Si aucun utilisateur n'est trouvé, une exception est levée.
        $user = User::where('email', $request->email)->firstOrFail();

        // Vérifie si le mot de passe fourni correspond au mot de passe hashé enregistré dans la base de données.
        if (!Hash::check($request->password, $user->password)) {
            // Si le mot de passe ne correspond pas, une exception est levée avec un message d'erreur.
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        // Crée un nouveau jeton d'API pour l'utilisateur et le retourne en texte brut.
        $token = $user->createToken('api-token')->plainTextToken;

        // Renvoie une réponse JSON contenant le jeton d'API.
        return response()->json([
            'token' => $token
        ]);
    }


    public function register(Request $request)
    {
        // Valider les données de requête pour s'assurer que toutes les informations nécessaires sont fournies et valides.
        $request->validate([
            'name' => 'required|string|max:255', // Le nom est obligatoire, doit être une chaîne de caractères et de longueur maximale 255.
            'email' => 'required|string|email|max:255|unique:users', // L'email est obligatoire, doit être valide, unique dans la table 'users' et de longueur maximale 255.
            'password' => 'required|string|min:6|confirmed', // Le mot de passe est obligatoire, doit être une chaîne de caractères d'au moins 6 caractères et doit être confirmé (c'est-à-dire qu'un champ 'password_confirmation' correspondant est présent).
        ]);

        // Créer un nouvel utilisateur en utilisant les données validées.
        $user = User::create([
            'name' => $request->name, // Utilise le nom fourni dans la requête.
            'email' => $request->email, // Utilise l'email fourni dans la requête.
            'password' => Hash::make($request->password), // Hash le mot de passe avant de le stocker pour des raisons de sécurité.
        ]);

        // Crée un nouveau jeton d'API pour l'utilisateur et le retourne en texte brut.
        $token = $user->createToken('api-token')->plainTextToken;

        // Renvoie une réponse JSON contenant les informations de l'utilisateur et son jeton d'API.
        // Le code de statut HTTP 201 indique qu'une ressource a été créée avec succès.
        return response()->json([
            'user' => $user, // Inclut l'objet utilisateur dans la réponse.
            'token' => $token // Inclut le jeton d'API dans la réponse.
        ], 201);
    }


    public function logout(Request $request)
    {
        // Supprimer tous les tokens de l'utilisateur actuellement authentifié.
        // Cela déconnecte l'utilisateur en invalidant tous les tokens associés à son compte.
        $request->user()->tokens()->delete();

        // Renvoyer une réponse JSON indiquant que la déconnexion a été effectuée avec succès.
        // Le message 'logout' confirme que l'utilisateur est maintenant déconnecté.
        return response()->json(['message' => 'logout']);
    }


    public function updateUser(Request $request)
    {
        // Récupérer l'utilisateur actuellement authentifié.
        $user = auth()->user();

        // Valider les données entrantes de la requête.
        // Les champs 'name' et 'email' sont optionnels ('sometimes'),
        // mais s'ils sont fournis, 'name' doit être une chaîne de caractères ne dépassant pas 255 caractères,
        // et 'email' doit être une adresse email valide.
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|string|max:255|unique:users',
        ]);

        // Mettre à jour l'utilisateur avec les données validées.
        // La méthode update() ne mettra à jour que les champs fournis dans $validatedData.
        $user->update($validatedData);

        // Renvoyer une réponse JSON indiquant que la mise à jour de l'utilisateur a été réussie.
        return response()->json(['message' => 'Utilisateur mis à jour avec succès.']);
    }
    public function modifyPassword(Request $request)
    {
        // Valider les données de la requête.
        // 'current_password' et 'new_password' sont requis.
        // 'new_password' doit être une chaîne d'au moins 6 caractères.
        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        // Récupérer l'utilisateur actuellement authentifié.
        $user = auth()->user();

        // Vérifier si le 'current_password' correspond au mot de passe de l'utilisateur.
        // Hash::check compare le mot de passe donné avec celui stocké dans la base de données.
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            // Si le mot de passe actuel ne correspond pas, renvoyer une erreur 403 avec un message.
            return response()->json(['message' => 'Le mot de passe actuel est incorrect.'], 403);
        }

        // Mettre à jour le mot de passe de l'utilisateur avec le 'new_password'.
        // Hash::make crypte le nouveau mot de passe avant de le stocker.
        $user->password = Hash::make($validatedData['new_password']);
        $user->save(); // Sauvegarder les modifications dans la base de données.

        // Renvoyer une réponse JSON indiquant que le mot de passe a été mis à jour avec succès.
        return response()->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }


}
