<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Presence;
use App\Models\Stage;
use Carbon\Carbon;

class PointageController extends Controller
{
    /**
     * Afficher la page de pointage
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un candidat
        if ($user->role !== 'candidat') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }
        
        // Récupérer le stage actif du candidat
        $stage = Stage::where('candidat_id', $user->id)
                      ->where('statut', 'en_cours')
                      ->first();
        
        if (!$stage) {
            return view('candidat.pointage', [
                'error' => 'Aucun stage actif trouvé. Veuillez contacter votre responsable.',
                'stage' => null,
                'presenceAujourdhui' => null,
                'historique' => collect([]),
                'stats' => null
            ]);
        }
        
        // Récupérer la présence du jour - UTILISER user_id au lieu de candidat_id
        $aujourdhui = Carbon::today();
        $presenceAujourdhui = Presence::where('user_id', $user->id)  // Changé ici
                                       ->where('stage_id', $stage->id)
                                       ->whereDate('date', $aujourdhui)
                                       ->first();
        
        // Récupérer l'historique des présences (30 derniers jours) - UTILISER user_id
        $historique = Presence::where('user_id', $user->id)  // Changé ici
                              ->where('stage_id', $stage->id)
                              ->where('date', '>=', Carbon::now()->subDays(30))
                              ->orderBy('date', 'desc')
                              ->get();
        
        // Calculer les statistiques
        $stats = $this->calculateStats($user, $stage);
        
        return view('candidat.pointage', compact('stage', 'presenceAujourdhui', 'historique', 'stats'));
    }
    
    /**
     * Enregistrer l'arrivée
     */
    public function arrival(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un candidat
        if ($user->role !== 'candidat') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        // Récupérer le stage actif
        $stage = Stage::where('candidat_id', $user->id)
                      ->where('statut', 'en_cours')
                      ->first();
        
        if (!$stage) {
            return response()->json(['error' => 'Aucun stage actif trouvé'], 400);
        }
        
        $aujourdhui = Carbon::today();
        
        // Vérifier si une présence existe déjà pour aujourd'hui - UTILISER user_id
        $presence = Presence::where('user_id', $user->id)  // Changé ici
                            ->where('stage_id', $stage->id)
                            ->whereDate('date', $aujourdhui)
                            ->first();
        
        if ($presence && $presence->heure_arrivee) {
            return response()->json(['error' => 'Vous avez déjà pointé votre arrivée aujourd\'hui'], 400);
        }
        
        $heureArrivee = Carbon::now();
        
        if (!$presence) {
            // Créer une nouvelle présence - UTILISER user_id
            $presence = Presence::create([
                'user_id' => $user->id,  // Changé ici
                'stage_id' => $stage->id,
                'date' => $aujourdhui,
                'heure_arrivee' => $heureArrivee,
                'est_present' => true,
            ]);
        } else {
            // Mettre à jour la présence existante
            $presence->update([
                'heure_arrivee' => $heureArrivee,
                'est_present' => true,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Arrivée enregistrée avec succès',
            'heure_arrivee' => $heureArrivee->format('H:i:s'),
            'presence_id' => $presence->id
        ]);
    }
    
    /**
     * Enregistrer le départ
     */
    public function departure(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un candidat
        if ($user->role !== 'candidat') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        // Récupérer le stage actif
        $stage = Stage::where('candidat_id', $user->id)
                      ->where('statut', 'en_cours')
                      ->first();
        
        if (!$stage) {
            return response()->json(['error' => 'Aucun stage actif trouvé'], 400);
        }
        
        $aujourdhui = Carbon::today();
        
        // Récupérer la présence du jour - UTILISER user_id
        $presence = Presence::where('user_id', $user->id)  // Changé ici
                            ->where('stage_id', $stage->id)
                            ->whereDate('date', $aujourdhui)
                            ->first();
        
        if (!$presence || !$presence->heure_arrivee) {
            return response()->json(['error' => 'Vous devez d\'abord pointer votre arrivée'], 400);
        }
        
        if ($presence->heure_depart) {
            return response()->json(['error' => 'Vous avez déjà pointé votre départ aujourd\'hui'], 400);
        }
        
        $heureDepart = Carbon::now();
        
        // Calculer les heures travaillées
        $arrivee = Carbon::parse($presence->heure_arrivee);
        $heuresTravaillees = $arrivee->diffInHours($heureDepart);
        
        $presence->update([
            'heure_depart' => $heureDepart,
            'heures_travaillees' => $heuresTravaillees,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Départ enregistré avec succès',
            'heure_depart' => $heureDepart->format('H:i:s'),
            'heures_travaillees' => $heuresTravaillees,
            'presence_id' => $presence->id
        ]);
    }
    
    /**
     * Enregistrer une absence justifiée
     */
    public function justifiedAbsence(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'motif' => 'required|string|min:3|max:500',
        ]);
        
        // Vérifier que l'utilisateur est un candidat
        if ($user->role !== 'candidat') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        // Récupérer le stage actif
        $stage = Stage::where('candidat_id', $user->id)
                      ->where('statut', 'en_cours')
                      ->first();
        
        if (!$stage) {
            return response()->json(['error' => 'Aucun stage actif trouvé'], 400);
        }
        
        $aujourdhui = Carbon::today();
        
        // Vérifier si une présence existe déjà pour aujourd'hui - UTILISER user_id
        $presence = Presence::where('user_id', $user->id)  // Changé ici
                            ->where('stage_id', $stage->id)
                            ->whereDate('date', $aujourdhui)
                            ->first();
        
        if ($presence && $presence->est_present) {
            return response()->json(['error' => 'Vous avez déjà pointé votre présence aujourd\'hui'], 400);
        }
        
        if (!$presence) {
            $presence = Presence::create([
                'user_id' => $user->id,  // Changé ici
                'stage_id' => $stage->id,
                'date' => $aujourdhui,
                'est_present' => false,
                'est_justifie' => true,
                'motif_absence' => $request->motif,
            ]);
        } else {
            $presence->update([
                'est_present' => false,
                'est_justifie' => true,
                'motif_absence' => $request->motif,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Absence justifiée enregistrée',
            'presence_id' => $presence->id
        ]);
    }
    
    /**
     * Calculer les statistiques
     */
    private function calculateStats($user, $stage)
    {
        // Dernier mois
        $dateDebut = Carbon::now()->subDays(30);
        
        $presences = Presence::where('user_id', $user->id)  // Changé ici
                             ->where('stage_id', $stage->id)
                             ->where('date', '>=', $dateDebut)
                             ->get();
        
        $totalJours = $presences->count();
        $joursPresent = $presences->where('est_present', true)->count();
        $joursAbsent = $presences->where('est_present', false)->count();
        $joursJustifies = $presences->where('est_justifie', true)->count();
        
        $totalHeures = $presences->sum('heures_travaillees');
        $moyenneHeures = $totalJours > 0 ? round($totalHeures / $totalJours, 1) : 0;
        
        // Calculer le taux de présence
        $tauxPresence = $totalJours > 0 ? round(($joursPresent / $totalJours) * 100) : 0;
        
        // Heures restantes à faire (objectif: 35h/semaine)
        $heuresObjectif = 35 * 4; // 35h/semaine * 4 semaines
        $heuresRestantes = max(0, $heuresObjectif - $totalHeures);
        
        return [
            'total_jours' => $totalJours,
            'jours_present' => $joursPresent,
            'jours_absent' => $joursAbsent,
            'jours_justifies' => $joursJustifies,
            'taux_presence' => $tauxPresence,
            'total_heures' => $totalHeures,
            'moyenne_heures' => $moyenneHeures,
            'heures_restantes' => $heuresRestantes,
        ];
    }
}
