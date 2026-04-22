<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TuteurController extends Controller
{
    /**
     * Afficher la liste des tuteurs
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'tuteur');
        
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
        } elseif ($sortField == 'experience') {
            $query->orderBy('experience');
        } elseif ($sortField == 'stagiaires') {
            $query->orderBy('stagiaires_count');
        }
        
        $tuteurs = $query->paginate(10);
        
        // Calculer les statistiques pour chaque tuteur
        foreach ($tuteurs as $tuteur) {
            $tuteur->stagiaires_count = User::where('role', 'candidat')
                                            ->where('tuteur_id', $tuteur->id)
                                            ->count();
            $tuteur->evaluations_count = \App\Models\Evaluation::where('evaluateur_id', $tuteur->id)->count();
            $tuteur->satisfaction = rand(85, 98);
        }
        
        // Statistiques globales
        $totalTuteurs = User::where('role', 'tuteur')->count();
        $tuteursActifs = User::where('role', 'tuteur')->where('is_active', true)->count();
        $tuteursOccupes = User::where('role', 'tuteur')
                              ->whereHas('stagiaires', function($q) {
                                  $q->whereNotNull('tuteur_id');
                              })->count();
        $tuteursDisponibles = $tuteursActifs - $tuteursOccupes;
        $pourcentageActifs = $totalTuteurs > 0 ? round(($tuteursActifs / $totalTuteurs) * 100) : 0;
        $totalStagiairesEncadres = User::where('role', 'candidat')->whereNotNull('tuteur_id')->count();
        $nouveauxTuteurs = User::where('role', 'tuteur')->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $nouveauxStagiairesEncadres = User::where('role', 'candidat')
                                          ->whereNotNull('tuteur_id')
                                          ->where('created_at', '>=', Carbon::now()->subDays(30))
                                          ->count();
        $moyenneParTuteur = $totalTuteurs > 0 ? round($totalStagiairesEncadres / $totalTuteurs, 1) : 0;
        
        // Départements distincts pour les filtres
        $departements = User::where('role', 'tuteur')
                            ->whereNotNull('departement')
                            ->distinct()
                            ->pluck('departement');
        
        // Stagiaires sans tuteur pour l'assignation
        $stagiairesSansTuteur = User::where('role', 'candidat')
                                    ->whereNull('tuteur_id')
                                    ->orderBy('first_name')
                                    ->get();
        
        return view('responsable.tuteurs', compact(
            'tuteurs', 
            'totalTuteurs', 
            'tuteursActifs', 
            'tuteursOccupes', 
            'tuteursDisponibles',
            'pourcentageActifs', 
            'totalStagiairesEncadres', 
            'nouveauxTuteurs',
            'nouveauxStagiairesEncadres', 
            'moyenneParTuteur', 
            'departements',
            'stagiairesSansTuteur'
        ));
    }
    
    /**
     * Afficher le formulaire de création d'un tuteur
     */
    public function create()
    {
        return view('responsable.tuteurs-create');
    }
    
    /**
     * Enregistrer un nouveau tuteur
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
            'universite' => 'nullable|string|max:255',
            'bureau' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:50',
            'entreprise' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'disponibilites' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'expertises' => 'nullable|json',
        ]);
        
        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'tuteur',
            'departement' => $request->departement,
            'poste' => $request->poste,
            'universite' => $request->universite,
            'bureau' => $request->bureau,
            'experience' => $request->experience,
            'entreprise' => $request->entreprise,
            'linkedin' => $request->linkedin,
            'disponibilites' => $request->disponibilites,
            'bio' => $request->bio,
            'expertises' => $request->expertises,
            'is_active' => true,
        ]);
        
        return redirect()->route('responsable.tuteurs')
                         ->with('success', 'Tuteur ajouté avec succès');
    }
    
    /**
     * Afficher les détails d'un tuteur
     */
    public function show($id)
    {
        $tuteur = User::where('role', 'tuteur')->with('stagiaires')->findOrFail($id);
        $tuteur->stagiaires_count = $tuteur->stagiaires->count();
        $tuteur->evaluations_count = \App\Models\Evaluation::where('evaluateur_id', $id)->count();
        
        return view('responsable.tuteurs-show', compact('tuteur'));
    }
    
    /**
     * Afficher le formulaire d'édition d'un tuteur
     */
    public function edit($id)
    {
        $tuteur = User::where('role', 'tuteur')->findOrFail($id);
        return view('responsable.tuteurs-edit', compact('tuteur'));
    }
    
    /**
     * Mettre à jour un tuteur
     */
    public function update(Request $request, $id)
    {
        $tuteur = User::where('role', 'tuteur')->findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'departement' => 'nullable|string|max:100',
            'poste' => 'nullable|string|max:100',
            'universite' => 'nullable|string|max:255',
            'bureau' => 'nullable|string|max:100',
            'experience' => 'nullable|string|max:50',
            'entreprise' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'disponibilites' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'expertises' => 'nullable|json',
        ]);
        
        $tuteur->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'phone' => $request->phone,
            'departement' => $request->departement,
            'poste' => $request->poste,
            'universite' => $request->universite,
            'bureau' => $request->bureau,
            'experience' => $request->experience,
            'entreprise' => $request->entreprise,
            'linkedin' => $request->linkedin,
            'disponibilites' => $request->disponibilites,
            'bio' => $request->bio,
            'expertises' => $request->expertises,
        ]);
        
        return redirect()->route('responsable.tuteurs')
                         ->with('success', 'Tuteur modifié avec succès');
    }
    
    /**
     * Supprimer un tuteur
     */
    public function destroy($id)
    {
        $tuteur = User::where('role', 'tuteur')->findOrFail($id);
        
        // Vérifier si le tuteur a des stagiaires
        $stagiairesCount = User::where('role', 'candidat')->where('tuteur_id', $id)->count();
        if ($stagiairesCount > 0) {
            return redirect()->route('responsable.tuteurs')
                             ->with('error', 'Impossible de supprimer ce tuteur car il encadre ' . $stagiairesCount . ' stagiaire(s).');
        }
        
        $tuteur->delete();
        
        return redirect()->route('responsable.tuteurs')
                         ->with('success', 'Tuteur supprimé avec succès');
    }
    
    /**
     * Assigner un stagiaire à un tuteur
     */
    public function assignStagiaire(Request $request)
    {
        $request->validate([
            'tuteur_id' => 'required|exists:users,id',
            'stagiaire_id' => 'required|exists:users,id',
            'date_assignation' => 'nullable|date',
            'commentaire' => 'nullable|string|max:500',
        ]);
        
        $tuteur = User::where('role', 'tuteur')->findOrFail($request->tuteur_id);
        $stagiaire = User::where('role', 'candidat')->findOrFail($request->stagiaire_id);
        
        // Vérifier si le stagiaire a déjà un tuteur
        if ($stagiaire->tuteur_id) {
            return redirect()->route('responsable.tuteurs')
                             ->with('error', 'Ce stagiaire a déjà un tuteur.');
        }
        
        // Vérifier si le tuteur n'a pas déjà 3 stagiaires
        $stagiairesCount = User::where('role', 'candidat')->where('tuteur_id', $tuteur->id)->count();
        if ($stagiairesCount >= 3) {
            return redirect()->route('responsable.tuteurs')
                             ->with('error', 'Ce tuteur a déjà 3 stagiaires. Maximum atteint.');
        }
        
        $stagiaire->update([
            'tuteur_id' => $tuteur->id,
        ]);
        
        return redirect()->route('responsable.tuteurs')
                         ->with('success', 'Stagiaire assigné au tuteur avec succès.');
    }
    
    /**
     * Désassigner un stagiaire d'un tuteur
     */
    public function desassignStagiaire($tuteurId, $stagiaireId)
    {
        $tuteur = User::where('role', 'tuteur')->findOrFail($tuteurId);
        $stagiaire = User::where('role', 'candidat')->where('tuteur_id', $tuteurId)->findOrFail($stagiaireId);
        
        $stagiaire->update(['tuteur_id' => null]);
        
        return redirect()->route('responsable.tuteurs')
                         ->with('success', 'Stagiaire désassigné avec succès.');
    }
    
    /**
     * Afficher les statistiques d'un tuteur
     */
    public function statistiques($id)
    {
        $tuteur = User::where('role', 'tuteur')->findOrFail($id);
        
        // Récupérer les stagiaires du tuteur
        $stagiaires = User::where('role', 'candidat')
                          ->where('tuteur_id', $id)
                          ->get();
        
        // Récupérer les évaluations
        $evaluations = \App\Models\Evaluation::where('evaluateur_id', $id)
                                             ->with('candidat')
                                             ->orderBy('created_at', 'desc')
                                             ->get();
        
        // Calculer la moyenne des notes
        $moyenneNotes = $evaluations->avg('note') ?? 0;
        
        // Statistiques par mois
        $evaluationsParMois = \App\Models\Evaluation::where('evaluateur_id', $id)
                                                     ->selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
                                                     ->whereYear('created_at', Carbon::now()->year)
                                                     ->groupBy('mois')
                                                     ->orderBy('mois')
                                                     ->get()
                                                     ->mapWithKeys(function($item) {
                                                         $mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'][$item->mois - 1];
                                                         return [$mois => $item->total];
                                                     });
        
        return view('responsable.tuteurs-statistiques', compact('tuteur', 'stagiaires', 'evaluations', 'moyenneNotes', 'evaluationsParMois'));
    }
}
