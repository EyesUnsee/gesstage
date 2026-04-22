<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tache;
use App\Models\Presence;
use App\Models\Document;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer le tuteur si existant
        $tuteur = null;
        if ($user->tuteur_id) {
            $tuteur = User::find($user->tuteur_id);
        }
        
        // Calculer la présence
        $presenceValue = $this->calculatePresence($user);
        $presenceVariation = $this->calculatePresenceVariation($user);
        
        // Compter les documents
        $documentsCount = Document::where('user_id', $user->id)->count();
        $documentsNouveaux = Document::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        
        // Récupérer les tâches
        $taches = Tache::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $tachesCount = Tache::where('user_id', $user->id)->count();
        $tachesTerminees = Tache::where('user_id', $user->id)
            ->where('terminee', true)
            ->count();
        
        // Calculer la progression
        $progression = $tachesCount > 0 ? round(($tachesTerminees / $tachesCount) * 100) : 0;
        $progressionVariation = $this->calculateProgressionVariation($user);
        
        // Objectif de stage
        $objectifMessage = $this->getObjectifMessage($progression);
        $joursRestants = $this->calculateJoursRestants();
        
        // Modules
        $modulesTotal = 10;
        $modulesCompletes = round(($progression / 100) * $modulesTotal);
        
        // Compétences
        $competences = $this->getCompetences($user);
        
        return view('candidat.dashboard', compact(
            'user', 'tuteur',
            'presenceValue', 'presenceVariation',
            'documentsCount', 'documentsNouveaux',
            'taches', 'tachesCount', 'tachesTerminees',
            'progression', 'progressionVariation',
            'objectifMessage', 'joursRestants',
            'modulesTotal', 'modulesCompletes',
            'competences'
        ));
    }
    
    /**
     * Ajouter une nouvelle tâche
     */
    public function storeTask(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priorite' => 'nullable|in:low,medium,high',
            'categorie' => 'nullable|string|max:50',
            'echeance' => 'nullable|date',
        ]);

        $user = Auth::user();

        $tache = Tache::create([
            'user_id' => $user->id,
            'titre' => $request->titre,
            'description' => $request->description,
            'priorite' => $request->priorite ?? 'medium',
            'categorie' => $request->categorie ?? 'tache',
            'echeance' => $request->echeance,
            'terminee' => false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tâche ajoutée avec succès',
                'tache' => $tache
            ]);
        }

        return redirect()->route('candidat.dashboard')
                         ->with('success', 'Tâche ajoutée avec succès');
    }
    
    /**
     * Marquer une tâche comme terminée
     */
    public function toggleTask($id)
    {
        $user = Auth::user();
        
        $tache = Tache::where('id', $id)
                      ->where('user_id', $user->id)
                      ->firstOrFail();
        
        $tache->update([
            'terminee' => !$tache->terminee,
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $tache->terminee ? 'Tâche terminée !' : 'Tâche remise en cours',
                'terminee' => $tache->terminee
            ]);
        }

        return redirect()->route('candidat.dashboard')
                         ->with('success', $tache->terminee ? 'Tâche terminée !' : 'Tâche remise en cours');
    }
    
    /**
     * Récupérer une tâche spécifique
     */
    public function getTask($id)
    {
        $user = Auth::user();
        
        $tache = Tache::where('id', $id)
                      ->where('user_id', $user->id)
                      ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'tache' => $tache
        ]);
    }
    
    /**
     * Mettre à jour une tâche
     */
    public function updateTask(Request $request, $id)
    {
        $user = Auth::user();
        
        $tache = Tache::where('id', $id)
                      ->where('user_id', $user->id)
                      ->firstOrFail();
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priorite' => 'nullable|in:low,medium,high',
            'categorie' => 'nullable|string|max:50',
            'echeance' => 'nullable|date',
        ]);
        
        $tache->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'priorite' => $request->priorite ?? 'medium',
            'categorie' => $request->categorie ?? 'tache',
            'echeance' => $request->echeance,
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tâche modifiée avec succès',
                'tache' => $tache
            ]);
        }
        
        return redirect()->route('candidat.dashboard')
                         ->with('success', 'Tâche modifiée avec succès');
    }
    
    /**
     * Supprimer une tâche
     */
    public function deleteTask($id)
    {
        try {
            $user = Auth::user();
            
            $tache = Tache::where('id', $id)
                          ->where('user_id', $user->id)
                          ->first();
            
            if (!$tache) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tâche non trouvée'
                    ], 404);
                }
                return redirect()->route('candidat.dashboard')
                                 ->with('error', 'Tâche non trouvée');
            }
            
            $tache->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tâche supprimée avec succès'
                ]);
            }

            return redirect()->route('candidat.dashboard')
                             ->with('success', 'Tâche supprimée avec succès');
                             
        } catch (\Exception $e) {
            \Log::error('Erreur suppression tâche: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression'
                ], 500);
            }
            
            return redirect()->route('candidat.dashboard')
                             ->with('error', 'Une erreur est survenue lors de la suppression');
        }
    }
    
    /**
     * Calculer le taux de présence
     */
    private function calculatePresence($user)
    {
        $totalJours = Presence::where('user_id', $user->id)->count();
        $joursPresent = Presence::where('user_id', $user->id)
                                ->where('statut', 'present')
                                ->count();
        
        return $totalJours > 0 ? round(($joursPresent / $totalJours) * 100) : 0;
    }
    
    /**
     * Calculer la variation de présence
     */
    private function calculatePresenceVariation($user)
    {
        $semaineDerniere = Presence::where('user_id', $user->id)
                                   ->whereBetween('date', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()])
                                   ->where('statut', 'present')
                                   ->count();
        
        $semaineActuelle = Presence::where('user_id', $user->id)
                                   ->whereBetween('date', [Carbon::now()->subWeek(), Carbon::now()])
                                   ->where('statut', 'present')
                                   ->count();
        
        if ($semaineDerniere == 0) return 0;
        
        return round((($semaineActuelle - $semaineDerniere) / $semaineDerniere) * 100);
    }
    
    /**
     * Calculer la variation de progression
     */
    private function calculateProgressionVariation($user)
    {
        $semaineDerniere = Tache::where('user_id', $user->id)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->where('terminee', true)
            ->count();
        
        $totalAvant = Tache::where('user_id', $user->id)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->count();
        
        $progressionAvant = $totalAvant > 0 ? round(($semaineDerniere / $totalAvant) * 100) : 0;
        
        $tachesActuelles = Tache::where('user_id', $user->id)->count();
        $termineesActuelles = Tache::where('user_id', $user->id)->where('terminee', true)->count();
        $progressionActuelle = $tachesActuelles > 0 ? round(($termineesActuelles / $tachesActuelles) * 100) : 0;
        
        return max(0, $progressionActuelle - $progressionAvant);
    }
    
    /**
     * Obtenir le message d'objectif
     */
    private function getObjectifMessage($progression)
    {
        if ($progression >= 100) {
            return "Félicitations ! Vous avez terminé toutes vos tâches !";
        } elseif ($progression >= 75) {
            return "Excellent travail ! Continuez comme ça !";
        } elseif ($progression >= 50) {
            return "Bonne progression, restez motivé !";
        } elseif ($progression >= 25) {
            return "Bon début, continuez vos efforts !";
        } else {
            return "Commencez à ajouter des tâches pour avancer !";
        }
    }
    
    /**
     * Calculer les jours restants
     */
    private function calculateJoursRestants()
    {
        $finStage = Carbon::now()->addMonths(3);
        return Carbon::now()->diffInDays($finStage);
    }
    
    /**
     * Obtenir les compétences
     */
    private function getCompetences($user)
    {
        return collect([
            (object)['nom' => 'Développement Web', 'valeur' => 75],
            (object)['nom' => 'Gestion de projet', 'valeur' => 60],
            (object)['nom' => 'Communication', 'valeur' => 85],
            (object)['nom' => 'Résolution de problèmes', 'valeur' => 70],
        ]);
    }
}
