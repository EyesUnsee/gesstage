<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Candidature;
use App\Models\Stage;

class ProfilController extends Controller
{
    /**
     * Afficher le profil du responsable
     */
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques pour le dashboard du profil
        $stagiairesCount = User::where('role', 'candidat')->count();
        $tuteursCount = User::where('role', 'tuteur')->count();
        $candidaturesCount = Candidature::count();
        $stagesCount = Stage::count();
        
        // Années d'expérience (exemple - à adapter selon votre logique)
        $anneesExperience = 3;
        
        // Taux de satisfaction (exemple)
        $satisfaction = 98;
        
        return view('responsable.profil', compact(
            'user', 
            'stagiairesCount', 
            'tuteursCount', 
            'candidaturesCount', 
            'stagesCount',
            'anneesExperience',
            'satisfaction'
        ));
    }

    /**
     * Mettre à jour le profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'departement' => 'nullable|string|max:100',
            'bureau' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
        ]);

        // Récupérer uniquement les champs qui existent dans la table
        $allowedFields = ['first_name', 'last_name', 'phone', 'address', 'bio'];
        
        if (Schema::hasColumn('users', 'birth_date')) {
            $allowedFields[] = 'birth_date';
        }
        if (Schema::hasColumn('users', 'departement')) {
            $allowedFields[] = 'departement';
        }
        if (Schema::hasColumn('users', 'bureau')) {
            $allowedFields[] = 'bureau';
        }
        
        $dataToUpdate = $request->only($allowedFields);
        $user->update($dataToUpdate);

        return redirect()->route('responsable.profil')
                         ->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Mettre à jour l'avatar
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Stocker le nouvel avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        return redirect()->route('responsable.profil')
                         ->with('success', 'Avatar mis à jour avec succès');
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Mettre à jour la date de dernière connexion
        $user->update(['last_login_at' => now()]);

        return redirect()->route('responsable.profil')
                         ->with('success', 'Mot de passe modifié avec succès');
    }
}
