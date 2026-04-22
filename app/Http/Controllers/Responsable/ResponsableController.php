<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ResponsableController extends Controller
{
    /**
     * Afficher la liste des responsables
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'responsable');
        
        // Recherche
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('departement', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filtre par département
        if ($request->departement) {
            $query->where('departement', $request->departement);
        }
        
        // Filtre par statut
        if ($request->statut == 'actif') {
            $query->where('is_active', true);
        } elseif ($request->statut == 'inactif') {
            $query->where('is_active', false);
        }
        
        // Tri
        $sortField = $request->sort ?? 'nom';
        if ($sortField == 'nom') {
            $query->orderBy('last_name');
        } elseif ($sortField == 'departement') {
            $query->orderBy('departement');
        }
        
        $responsables = $query->paginate(10);
        
        // Statistiques globales
        $totalResponsables = User::where('role', 'responsable')->count();
        $responsablesActifs = User::where('role', 'responsable')->where('is_active', true)->count();
        $pourcentageActifs = $totalResponsables > 0 ? round(($responsablesActifs / $totalResponsables) * 100) : 0;
        $nouveauxResponsables = User::where('role', 'responsable')->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Départements distincts pour les filtres
        $departements = User::where('role', 'responsable')
                            ->whereNotNull('departement')
                            ->distinct()
                            ->pluck('departement');
        
        return view('responsable.responsables', compact(
            'responsables',
            'totalResponsables',
            'responsablesActifs',
            'pourcentageActifs',
            'nouveauxResponsables',
            'departements'
        ));
    }
    
    /**
     * Afficher le formulaire de création d'un responsable
     */
    public function create()
    {
        return view('responsable.responsables-create');
    }
    
    /**
     * Enregistrer un nouveau responsable
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'departement' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'responsable',
            'departement' => $request->departement,
            'poste' => $request->poste,
            'bio' => $request->bio,
            'is_active' => true,
        ]);
        
        return redirect()->route('responsable.responsables.index')
                         ->with('success', 'Responsable ajouté avec succès');
    }
    
    /**
     * Afficher les détails d'un responsable
     */
    public function show($id)
    {
        $responsable = User::where('role', 'responsable')->findOrFail($id);
        return view('responsable.responsables-show', compact('responsable'));
    }
    
    /**
     * Afficher le formulaire d'édition d'un responsable
     */
    public function edit($id)
    {
        $responsable = User::where('role', 'responsable')->findOrFail($id);
        return view('responsable.responsables-edit', compact('responsable'));
    }
    
    /**
     * Mettre à jour un responsable
     */
    public function update(Request $request, $id)
    {
        $responsable = User::where('role', 'responsable')->findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        $responsable->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'phone' => $request->phone,
            'departement' => $request->departement,
            'poste' => $request->poste,
            'bio' => $request->bio,
        ]);
        
        return redirect()->route('responsable.responsables.index')
                         ->with('success', 'Responsable modifié avec succès');
    }
    
    /**
     * Supprimer un responsable
     */
    public function destroy($id)
    {
        $responsable = User::where('role', 'responsable')->findOrFail($id);
        
        // Empêcher la suppression de son propre compte
        if (auth()->id() == $id) {
            return redirect()->route('responsable.responsables.index')
                             ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        $responsable->delete();
        
        return redirect()->route('responsable.responsables.index')
                         ->with('success', 'Responsable supprimé avec succès');
    }
}
