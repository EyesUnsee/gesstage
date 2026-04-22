<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Journal;
use Carbon\Carbon;

class JournalController extends Controller
{
    /**
     * Afficher les journaux des stagiaires
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'tuteur') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }

        // Récupérer les stagiaires du tuteur
        $stagiaires = User::where('tuteur_id', $user->id)
                          ->where('role', 'candidat')
                          ->get();

        $stagiairesIds = $stagiaires->pluck('id')->toArray();

        // Si pas de stagiaires, afficher vide
        if (empty($stagiairesIds)) {
            return view('tuteur.journaux', [
                'journaux' => collect([]),
                'stagiaires' => collect([]),
                'enAttente' => 0,
                'valides' => 0,
                'rejetes' => 0,
                'total' => 0
            ]);
        }

        // Récupérer les journaux des stagiaires
        $journaux = Journal::whereIn('user_id', $stagiairesIds)
                          ->with('user')
                          ->orderBy('created_at', 'desc')
                          ->get();

        // Calculer les statistiques
        $enAttente = $journaux->where('statut', 'en_attente')->count();
        $valides = $journaux->where('statut', 'valide')->count();
        $rejetes = $journaux->where('statut', 'rejete')->count();
        $total = $journaux->count();

        return view('tuteur.journaux', compact(
            'journaux',
            'stagiaires',
            'enAttente',
            'valides',
            'rejetes',
            'total'
        ));
    }

    /**
     * Valider un journal
     */
    public function valider(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $journal = Journal::where('id', $id)
                              ->whereHas('user', function($query) use ($user) {
                                  $query->where('tuteur_id', $user->id);
                              })
                              ->firstOrFail();
            
            $journal->update([
                'statut' => 'valide',
                'commentaire_tuteur' => $request->commentaire ?? $journal->commentaire_tuteur,
                'date_validation' => Carbon::now()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Journal validé avec succès'
                ]);
            }
            
            return redirect()->route('tuteur.journaux')
                             ->with('success', 'Journal validé avec succès');
                             
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la validation'
                ], 500);
            }
            
            return redirect()->route('tuteur.journaux')
                             ->with('error', 'Erreur lors de la validation');
        }
    }
    
    /**
     * Rejeter un journal
     */
    public function rejeter(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $journal = Journal::where('id', $id)
                              ->whereHas('user', function($query) use ($user) {
                                  $query->where('tuteur_id', $user->id);
                              })
                              ->firstOrFail();
            
            $request->validate([
                'commentaire' => 'required|string|min:3|max:1000'
            ]);
            
            $journal->update([
                'statut' => 'rejete',
                'commentaire_tuteur' => $request->commentaire,
                'date_rejet' => Carbon::now()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Journal rejeté'
                ]);
            }
            
            return redirect()->route('tuteur.journaux')
                             ->with('success', 'Journal rejeté');
                             
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du rejet'
                ], 500);
            }
            
            return redirect()->route('tuteur.journaux')
                             ->with('error', 'Erreur lors du rejet');
        }
    }
    
    /**
     * Voir un journal
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $journal = Journal::where('id', $id)
                          ->whereHas('user', function($query) use ($user) {
                              $query->where('tuteur_id', $user->id);
                          })
                          ->with('user')
                          ->firstOrFail();
        
        return view('tuteur.journaux-show', compact('journal'));
    }
}
