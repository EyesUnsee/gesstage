<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfilController extends Controller
{
    /**
     * Afficher le profil du candidat
     */
    public function index()
    {
        // Récupérer l'utilisateur connecté
        $candidat = Auth::user();
        
        // Si l'utilisateur n'est pas un candidat, rediriger
        if ($candidat->role !== 'candidat') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }
        
        // Passer le candidat à la vue
        return view('candidat.profil', compact('candidat'));
    }
    
    /**
     * Mettre à jour le profil du candidat
     */
    public function update(Request $request)
    {
        $candidat = Auth::user();
        
        // Validation des données
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        // Filtrer uniquement les colonnes qui existent dans la table
        $allowedColumns = ['first_name', 'last_name', 'phone', 'address'];
        
        // Vérifier si la colonne birth_date existe
        if (Schema::hasColumn('users', 'birth_date')) {
            $allowedColumns[] = 'birth_date';
        } else {
            // Si la colonne n'existe pas, la retirer des données validées
            unset($validated['birth_date']);
        }
        
        // Vérifier si la colonne bio existe
        if (Schema::hasColumn('users', 'bio')) {
            $allowedColumns[] = 'bio';
        } else {
            // Si la colonne n'existe pas, la retirer des données validées
            unset($validated['bio']);
        }
        
        // Ne garder que les colonnes autorisées
        $dataToUpdate = array_intersect_key($validated, array_flip($allowedColumns));
        
        // Mettre à jour le profil
        $candidat->update($dataToUpdate);
        
        return redirect()->route('candidat.profil')
                         ->with('success', 'Profil mis à jour avec succès');
    }
    
    /**
     * Mettre à jour l'avatar du candidat
     */
    public function updateAvatar(Request $request)
    {
        $candidat = Auth::user();
        
        // Validation de l'image
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($candidat->avatar && Storage::disk('public')->exists($candidat->avatar)) {
                Storage::disk('public')->delete($candidat->avatar);
            }
            
            // Stocker le nouvel avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            
            // Mettre à jour l'utilisateur
            $candidat->update(['avatar' => $avatarPath]);
        }
        
        return redirect()->route('candidat.profil')
                         ->with('success', 'Avatar mis à jour avec succès');
    }
    
    /**
     * Supprimer l'avatar du candidat
     */
    public function deleteAvatar()
    {
        $candidat = Auth::user();
        
        // Supprimer l'avatar s'il existe
        if ($candidat->avatar && Storage::disk('public')->exists($candidat->avatar)) {
            Storage::disk('public')->delete($candidat->avatar);
            $candidat->update(['avatar' => null]);
        }
        
        return redirect()->route('candidat.profil')
                         ->with('success', 'Avatar supprimé avec succès');
    }
    
    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $candidat = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|string|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $candidat->update([
            'password' => bcrypt($validated['new_password'])
        ]);
        
        return redirect()->route('candidat.profil')
                         ->with('success', 'Mot de passe modifié avec succès');
    }
}
