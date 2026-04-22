<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les statistiques
        $stagiairesCount = $user->stagiaires()->count();
        $evaluationsCount = $user->evaluationsDonnees()->count();
        $experience = $user->experience ?? '5-10';
        
        return view('tuteur.profil', compact('user', 'stagiairesCount', 'evaluationsCount', 'experience'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:100',
            'universite' => 'nullable|string|max:255',
            'bureau' => 'nullable|string|max:100',
            'max_stagiaires' => 'nullable|integer|min:1|max:20',
            'experience' => 'nullable|string|max:20',
            'linkedin' => 'nullable|string|max:255',
            'disponibilites' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'expertises' => 'nullable|json',
        ]);

        // Récupérer uniquement les champs qui existent dans la table
        $allowedFields = ['first_name', 'last_name', 'phone', 'address', 'bio'];
        
        // Vérifier si les colonnes existent avant de les ajouter
        if (Schema::hasColumn('users', 'departement')) {
            $allowedFields[] = 'departement';
        }
        if (Schema::hasColumn('users', 'poste')) {
            $allowedFields[] = 'poste';
        }
        if (Schema::hasColumn('users', 'universite')) {
            $allowedFields[] = 'universite';
        }
        if (Schema::hasColumn('users', 'bureau')) {
            $allowedFields[] = 'bureau';
        }
        if (Schema::hasColumn('users', 'max_stagiaires')) {
            $allowedFields[] = 'max_stagiaires';
        }
        if (Schema::hasColumn('users', 'experience')) {
            $allowedFields[] = 'experience';
        }
        if (Schema::hasColumn('users', 'linkedin')) {
            $allowedFields[] = 'linkedin';
        }
        if (Schema::hasColumn('users', 'disponibilites')) {
            $allowedFields[] = 'disponibilites';
        }
        if (Schema::hasColumn('users', 'expertises')) {
            $allowedFields[] = 'expertises';
        }
        
        // Ne garder que les champs autorisés
        $dataToUpdate = $request->only($allowedFields);
        
        // Traiter les expertises (JSON)
        if ($request->has('expertises') && is_string($request->expertises)) {
            $dataToUpdate['expertises'] = $request->expertises;
        }
        
        $user->update($dataToUpdate);

        return redirect()->route('tuteur.profil')
                         ->with('success', 'Profil mis à jour avec succès');
    }

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

        return redirect()->route('tuteur.profil')
                         ->with('success', 'Avatar mis à jour avec succès');
    }
}
