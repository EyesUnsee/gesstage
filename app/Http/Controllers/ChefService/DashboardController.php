<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stage;
use App\Models\Presence;
use App\Models\Service;
use App\Models\Validation;
use App\Models\Bilan;
use App\Models\Activite;
use App\Models\Tache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $serviceId = $user->service_id ?? null;
        $serviceName = $serviceId ? Service::find($serviceId)?->nom : null;
        
        // Statistiques des stagiaires actifs
        $stagiairesActifs = Stage::where('statut', 'en_cours')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Évolution des stagiaires
        $stagiairesMoisDernier = Stage::whereBetween('date_debut', [
                Carbon::now()->subMonth()->startOfMonth(), 
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        $evolutionStagiaires = $stagiairesMoisDernier > 0 
            ? round((($stagiairesActifs - $stagiairesMoisDernier) / $stagiairesMoisDernier) * 100) 
            : 0;
        
        // Validations en attente
        $validationsEnAttente = Validation::where('statut', 'en_attente')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Bilans en attente
        $bilansEnAttente = Bilan::where('statut', 'en_attente')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('stagiaire', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Taux de présence
        $presences = Presence::where('date', '>=', Carbon::now()->startOfMonth())
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->get();
        
        $totalPresences = $presences->count();
        $presencesPresent = $presences->where('est_present', true)->count();
        $tauxPresence = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
        
        // Évolution des validations
        $validationsHier = Validation::where('statut', 'en_attente')
            ->whereDate('created_at', Carbon::yesterday())
            ->count();
        $evolutionValidations = $validationsHier - $validationsEnAttente;
        
        // Activités récentes
        $activitesRecentes = Activite::when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Validations urgentes
        $validationsUrgentes = Validation::where('statut', 'en_attente')
            ->where(function($query) {
                $query->where('priorite', 'urgente')
                    ->orWhere('echeance', '<=', Carbon::now()->addDays(2));
            })
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->orderBy('echeance', 'asc')
            ->limit(4)
            ->get();
        
        // Services
        $services = collect();
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $stagiairesCount = User::where('service_id', $serviceId)
                    ->where('role', 'candidat')
                    ->whereHas('stage', function($q) {
                        $q->where('statut', 'en_cours');
                    })
                    ->count();
                $service->stagiaires_count = $stagiairesCount;
                $services = collect([$service]);
            }
        } else {
            $services = Service::all();
            foreach ($services as $service) {
                $service->stagiaires_count = User::where('service_id', $service->id)
                    ->where('role', 'candidat')
                    ->whereHas('stage', function($q) {
                        $q->where('statut', 'en_cours');
                    })
                    ->count();
            }
        }
        
        // Indicateurs de performance
        $indicateursPerformance = $this->calculerIndicateursPerformance($serviceId);
        
        // Notifications
        $pendingValidationsCount = $validationsEnAttente;
        $notificationsCount = Activite::where('user_id', $user->id)
            ->where('lu', false)
            ->count();
        $unreadMessagesCount = 0;
        
        return view('chef-service.dashboard', compact(
            'stagiairesActifs',
            'validationsEnAttente',
            'bilansEnAttente',
            'tauxPresence',
            'evolutionStagiaires',
            'evolutionValidations',
            'activitesRecentes',
            'validationsUrgentes',
            'services',
            'indicateursPerformance',
            'serviceName',
            'pendingValidationsCount',
            'notificationsCount',
            'unreadMessagesCount'
        ));
    }
    
    private function calculerIndicateursPerformance($serviceId)
    {
        $indicateurs = [];
        
        // Taux d'achèvement des tâches - Version corrigée sans whereHas sur DB
        $totalTaches = 0;
        $tachesTerminees = 0;
        
        // Utiliser le modèle Tache s'il existe, sinon DB
        if (class_exists(Tache::class)) {
            $query = Tache::query();
            if ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            }
            $totalTaches = $query->count();
            
            $query2 = Tache::where('terminee', true);
            if ($serviceId) {
                $query2->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            }
            $tachesTerminees = $query2->count();
        } else {
            // Fallback si la table taches n'existe pas
            $totalTaches = 0;
            $tachesTerminees = 0;
        }
        
        $indicateurs[] = [
            'nom' => 'Achèvement des tâches',
            'valeur' => $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : 0
        ];
        
        // Taux de validation des bilans
        $totalBilans = Bilan::count();
        $bilansValides = Bilan::where('statut', 'valide')->count();
        
        $indicateurs[] = [
            'nom' => 'Bilans validés',
            'valeur' => $totalBilans > 0 ? round(($bilansValides / $totalBilans) * 100) : 0
        ];
        
        // Satisfaction moyenne (si la table evaluations existe)
        $satisfactionMoyenne = 75; // valeur par défaut
        if (Schema::hasTable('evaluations')) {
            $satisfactionMoyenne = DB::table('evaluations')->avg('note') ?? 75;
            $satisfactionMoyenne = round($satisfactionMoyenne * 20);
        }
        
        $indicateurs[] = [
            'nom' => 'Satisfaction',
            'valeur' => $satisfactionMoyenne
        ];
        
        $indicateurs[] = [
            'nom' => 'Présence cible',
            'valeur' => 95
        ];
        
        return $indicateurs;
    }
    
    public function activites(Request $request)
    {
        $serviceId = Auth::user()->service_id ?? null;
        
        $activites = Activite::when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        if ($request->ajax()) {
            return response()->json($activites);
        }
        
        return view('chef-service.activites', compact('activites'));
    }
}
