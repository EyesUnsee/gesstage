<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StagiaireController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'candidat')->with('tuteur', 'stage');
        
        // Filtre par recherche
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('entreprise', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filtre par département
        if ($request->departement) {
            $query->where('departement', $request->departement);
        }
        
        // Filtre par statut
        if ($request->statut) {
            $query->whereHas('stage', function($q) use ($request) {
                $q->where('statut', $request->statut);
            });
        }
        
        // Filtre par tuteur
        if ($request->tuteur) {
            $query->where('tuteur_id', $request->tuteur);
        }
        
        // Tri
        $sortField = $request->sort ?? 'nom';
        if ($sortField == 'nom') {
            $query->orderBy('last_name');
        } elseif ($sortField == 'date_debut') {
            $query->orderBy('stage_debut');
        } elseif ($sortField == 'date_fin') {
            $query->orderBy('stage_fin');
        }
        
        $stagiaires = $query->paginate(10);
        
        // Calculer la progression pour chaque stagiaire
        foreach ($stagiaires as $stagiaire) {
            if ($stagiaire->stage) {
                $total = $stagiaire->stage->date_debut->diffInDays($stagiaire->stage->date_fin);
                $ecoule = $stagiaire->stage->date_debut->diffInDays(Carbon::now());
                $stagiaire->progression = $total > 0 ? round(($ecoule / $total) * 100) : 0;
                $stagiaire->stage_statut = $stagiaire->stage->statut;
                $stagiaire->stage_debut = $stagiaire->stage->date_debut;
                $stagiaire->stage_fin = $stagiaire->stage->date_fin;
            } else {
                $stagiaire->progression = 0;
                $stagiaire->stage_statut = 'a_venir';
            }
        }
        
        // Statistiques
        $totalStagiaires = User::where('role', 'candidat')->count();
        $stagiairesActifs = User::where('role', 'candidat')->whereHas('stage', function($q) {
            $q->where('statut', 'en_cours');
        })->count();
        $stagiairesTermines = User::where('role', 'candidat')->whereHas('stage', function($q) {
            $q->where('statut', 'termine');
        })->count();
        $nouveauxStagiaires = User::where('role', 'candidat')->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $stagesEnCours = $stagiairesActifs;
        $satisfactionMoyenne = 4.5;
        
        // Données pour les filtres
        $departements = User::where('role', 'candidat')->whereNotNull('departement')->distinct()->pluck('departement');
        $tuteurs = User::where('role', 'tuteur')->orderBy('first_name')->get();
        
        return view('responsable.stagiaires', compact(
            'stagiaires',
            'totalStagiaires',
            'stagiairesActifs',
            'stagiairesTermines',
            'nouveauxStagiaires',
            'stagesEnCours',
            'satisfactionMoyenne',
            'departements',
            'tuteurs'
        ));
    }
    
    /**
     * Afficher le formulaire de création d'un stagiaire
     */
    public function create()
    {
        $tuteurs = User::where('role', 'tuteur')->orderBy('first_name')->get();
        return view('responsable.stagiaires-create', compact('tuteurs'));
    }
    
    /**
     * Enregistrer un nouveau stagiaire
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'tuteur_id' => 'nullable|exists:users,id',
            'entreprise' => 'nullable|string|max:255',
            'formation' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:100',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'candidat',
            'tuteur_id' => $request->tuteur_id,
            'entreprise' => $request->entreprise,
            'formation' => $request->formation,
            'departement' => $request->departement,
            'is_active' => true,
        ]);
        
        // Créer le stage si les dates sont fournies
        if ($request->date_debut && $request->date_fin) {
            Stage::create([
                'candidat_id' => $user->id,
                'tuteur_id' => $request->tuteur_id,
                'titre' => 'Stage de ' . $user->first_name . ' ' . $user->last_name,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'statut' => 'a_venir',
                'entreprise' => $request->entreprise,
            ]);
        }
        
        return redirect()->route('responsable.stagiaires')
                         ->with('success', 'Stagiaire ajouté avec succès');
    }
    
    public function show($id)
    {
        $stagiaire = User::where('role', 'candidat')->with('tuteur', 'stage')->findOrFail($id);
        return view('responsable.stagiaires-show', compact('stagiaire'));
    }
    
    public function edit($id)
    {
        $stagiaire = User::where('role', 'candidat')->with('tuteur', 'stage')->findOrFail($id);
        $tuteurs = User::where('role', 'tuteur')->orderBy('first_name')->get();
        return view('responsable.stagiaires-edit', compact('stagiaire', 'tuteurs'));
    }
    
    /**
     * Mettre à jour un stagiaire
     */
    public function update(Request $request, $id)
    {
        $stagiaire = User::where('role', 'candidat')->findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'tuteur_id' => 'nullable|exists:users,id',
            'entreprise' => 'nullable|string|max:255',
            'formation' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'stage_statut' => 'nullable|in:a_venir,en_cours,termine',
        ]);
        
        $stagiaire->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'phone' => $request->phone,
            'tuteur_id' => $request->tuteur_id,
            'entreprise' => $request->entreprise,
            'formation' => $request->formation,
            'departement' => $request->departement,
            'address' => $request->address,
            'bio' => $request->bio,
        ]);
        
        // Mettre à jour le stage
        $stage = $stagiaire->stage;
        if ($stage) {
            $stage->update([
                'tuteur_id' => $request->tuteur_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'statut' => $request->stage_statut ?? $stage->statut,
                'entreprise' => $request->entreprise,
            ]);
        } elseif ($request->date_debut && $request->date_fin) {
            // Créer un stage s'il n'existe pas
            Stage::create([
                'candidat_id' => $stagiaire->id,
                'tuteur_id' => $request->tuteur_id,
                'titre' => 'Stage de ' . $stagiaire->first_name . ' ' . $stagiaire->last_name,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'statut' => $request->stage_statut ?? 'a_venir',
                'entreprise' => $request->entreprise,
            ]);
        }
        
        return redirect()->route('responsable.stagiaires')
                         ->with('success', 'Stagiaire modifié avec succès');
    }
    
    public function destroy($id)
    {
        $stagiaire = User::where('role', 'candidat')->findOrFail($id);
        $stagiaire->delete();
        
        return redirect()->route('responsable.stagiaires')
                         ->with('success', 'Stagiaire supprimé avec succès');
    }
}
