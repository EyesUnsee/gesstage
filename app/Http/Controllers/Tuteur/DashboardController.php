<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Stage;
use App\Models\Evaluation;
use App\Models\Journal;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord du tuteur
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les stagiaires (candidats) assignés à ce tuteur
        $stagiaires = User::where('role', 'candidat')
                          ->where('tuteur_id', $user->id)
                          ->get();
        
        // Récupérer les IDs des stagiaires
        $stagiaireIds = $stagiaires->pluck('id')->toArray();
        
        // Calculer les statistiques
        $stagiairesActifs = $stagiaires->count();
        $nouveauxStagiaires = User::where('role', 'candidat')
                                  ->where('tuteur_id', $user->id)
                                  ->where('created_at', '>=', now()->subDays(7))
                                  ->count();
        
        // Évaluations à faire
        $evaluationsAFaire = Evaluation::where('evaluateur_id', $user->id)
                                       ->where('statut', 'en_attente')
                                       ->count();
        
        // Évaluations faites
        $evaluationsFaites = Evaluation::where('evaluateur_id', $user->id)
                                       ->where('statut', 'publie')
                                       ->count();
        
        $nouvellesEvaluations = Evaluation::where('evaluateur_id', $user->id)
                                          ->where('created_at', '>=', now()->subDays(30))
                                          ->where('statut', 'publie')
                                          ->count();
        
        // Journaux à valider - Version corrigée sans relation user()
        $journauxAValider = Journal::whereIn('user_id', $stagiaireIds)
                                   ->where('statut', 'en_attente')
                                   ->count();
        
        // Évaluations en attente avec détails
        $evaluationsEnAttente = Evaluation::where('evaluateur_id', $user->id)
                                          ->where('statut', 'en_attente')
                                          ->with('candidat')
                                          ->get();
        
        // Pour chaque stagiaire, calculer la progression
        foreach ($stagiaires as $stagiaire) {
            $totalJournaux = Journal::where('user_id', $stagiaire->id)->count();
            $journauxValides = Journal::where('user_id', $stagiaire->id)
                                      ->where('statut', 'valide')
                                      ->count();
            $stagiaire->progression = $totalJournaux > 0 ? round(($journauxValides / $totalJournaux) * 100) : 0;
            $stagiaire->formation = $stagiaire->formation ?? 'Stagiaire';
            $stagiaire->entreprise_nom = $stagiaire->entreprise ?? 'Entreprise';
        }
        
        return view('tuteur.dashboard', compact(
            'stagiaires',
            'stagiairesActifs',
            'nouveauxStagiaires',
            'evaluationsAFaire',
            'evaluationsFaites',
            'nouvellesEvaluations',
            'journauxAValider',
            'evaluationsEnAttente'
        ));
    }
}
