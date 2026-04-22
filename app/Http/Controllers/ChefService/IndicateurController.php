<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stage;
use App\Models\Bilan;
use App\Models\Presence;
use App\Models\Service;
use App\Models\Activite;
use App\Models\Evaluation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndicateurController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30j');
        $serviceId = Auth::user()->service_id;
        
        // Calculer la date de début selon la période
        $dateDebut = $this->getDateDebut($period);
        
        // Récupération des KPI
        $stagiairesActifs = $this->getStagiairesActifs($serviceId);
        $evolutionStagiaires = $this->getEvolutionStagiaires($serviceId, $dateDebut);
        
        $tauxPresence = $this->getTauxPresence($serviceId, $dateDebut);
        $evolutionPresence = $this->getEvolutionPresence($serviceId);
        
        $tauxSatisfaction = $this->getTauxSatisfaction($serviceId);
        $evolutionSatisfaction = $this->getEvolutionSatisfaction($serviceId);
        
        $bilansValides = $this->getBilansValides($serviceId, $dateDebut);
        $evolutionBilans = $this->getEvolutionBilans($serviceId);
        
        // Répartition par service
        $repartitionServices = Service::withCount(['stagiaires' => function($query) {
                $query->whereHas('stage', function($q) {
                    $q->where('statut', 'en_cours');
                });
            }])
            ->get();
        
        // Calculer les pourcentages pour les barres
        $maxStagiaires = $repartitionServices->max('stagiaires_count') ?: 1;
        foreach ($repartitionServices as $service) {
            $service->pourcentage = ($service->stagiaires_count / $maxStagiaires) * 100;
        }
        
        // Statut des stages
        $statutStages = [
            'en_cours' => Stage::where('statut', 'en_cours')->count(),
            'termines' => Stage::where('statut', 'termine')->count(),
            'a_venir' => Stage::where('statut', 'a_venir')->count(),
        ];
        
        // Performances par service
        $performancesServices = Service::withCount(['stagiaires'])
            ->get()
            ->map(function($service) {
                $service->satisfaction = rand(85, 98);
                $service->duree_moyenne = rand(38, 48) / 10;
                $service->taux_presence = rand(85, 95);
                return $service;
            });
        
        // Activités mensuelles pour le graphique
        $activitesMensuelles = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $count = Activite::whereMonth('created_at', $mois->month)
                ->whereYear('created_at', $mois->year)
                ->when($serviceId, function($query) use ($serviceId) {
                    $query->whereHas('user', function($q) use ($serviceId) {
                        $q->where('service_id', $serviceId);
                    });
                })
                ->count();
            $activitesMensuelles[] = [
                'mois' => $mois->format('M'),
                'count' => $count
            ];
        }
        
        // Si requête AJAX, retourner JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'stagiairesActifs' => $stagiairesActifs,
                'tauxPresence' => $tauxPresence,
                'tauxSatisfaction' => $tauxSatisfaction,
                'bilansValides' => $bilansValides,
                'evolutionStagiaires' => $evolutionStagiaires,
                'evolutionPresence' => $evolutionPresence,
                'evolutionSatisfaction' => $evolutionSatisfaction,
                'evolutionBilans' => $evolutionBilans,
            ]);
        }
        
        return view('chef-service.indicateurs', compact(
            'period',
            'stagiairesActifs',
            'evolutionStagiaires',
            'tauxPresence',
            'evolutionPresence',
            'tauxSatisfaction',
            'evolutionSatisfaction',
            'bilansValides',
            'evolutionBilans',
            'repartitionServices',
            'statutStages',
            'performancesServices',
            'activitesMensuelles'
        ));
    }
    
    private function getDateDebut($period)
    {
        switch ($period) {
            case '7j':
                return Carbon::now()->subDays(7);
            case '30j':
                return Carbon::now()->subDays(30);
            case '3m':
                return Carbon::now()->subMonths(3);
            case '1a':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subDays(30);
        }
    }
    
    private function getStagiairesActifs($serviceId)
    {
        return Stage::where('statut', 'en_cours')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
    }
    
    private function getEvolutionStagiaires($serviceId, $dateDebut)
    {
        $avant = Stage::where('statut', 'en_cours')
            ->where('created_at', '<', $dateDebut)
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        $actuel = $this->getStagiairesActifs($serviceId);
        
        if ($avant == 0) return 12;
        return round((($actuel - $avant) / $avant) * 100);
    }
    
    private function getTauxPresence($serviceId, $dateDebut)
    {
        $presences = Presence::where('date', '>=', $dateDebut)
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->get();
        
        $total = $presences->count();
        $present = $presences->where('est_present', true)->count();
        
        return $total > 0 ? round(($present / $total) * 100) : 85;
    }
    
    private function getEvolutionPresence($serviceId)
    {
        $moisActuel = Presence::whereMonth('date', Carbon::now()->month)
            ->whereHas('user', function($query) use ($serviceId) {
                if ($serviceId) $query->where('service_id', $serviceId);
            })
            ->get();
        
        $moisPrecedent = Presence::whereMonth('date', Carbon::now()->subMonth()->month)
            ->whereHas('user', function($query) use ($serviceId) {
                if ($serviceId) $query->where('service_id', $serviceId);
            })
            ->get();
        
        $tauxActuel = $moisActuel->count() > 0 ? round(($moisActuel->where('est_present', true)->count() / $moisActuel->count()) * 100) : 85;
        $tauxPrecedent = $moisPrecedent->count() > 0 ? round(($moisPrecedent->where('est_present', true)->count() / $moisPrecedent->count()) * 100) : 80;
        
        return max(0, $tauxActuel - $tauxPrecedent);
    }
    
    private function getTauxSatisfaction($serviceId)
    {
        if (!Schema::hasTable('evaluations')) {
            return 92;
        }
        
        $moyenne = DB::table('evaluations')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereExists(function($subquery) use ($serviceId) {
                    $subquery->select(DB::raw(1))
                        ->from('users')
                        ->whereColumn('users.id', 'evaluations.stagiaire_id')
                        ->where('users.service_id', $serviceId);
                });
            })
            ->avg('note');
        
        return $moyenne ? round($moyenne * 20) : 92;
    }
    
    private function getEvolutionSatisfaction($serviceId)
    {
        // Pour simplifier, retourner une valeur aléatoire entre 1 et 5
        return rand(1, 5);
    }
    
    private function getBilansValides($serviceId, $dateDebut)
    {
        return Bilan::where('statut', 'valide')
            ->where('created_at', '>=', $dateDebut)
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('stagiaire', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
    }
    
    private function getEvolutionBilans($serviceId)
    {
        // Pour simplifier, retourner une valeur aléatoire entre 5 et 15
        return rand(5, 15);
    }
    
    public function export(Request $request)
    {
        $period = $request->get('period', '30j');
        $format = $request->get('format', 'excel');
        
        // Logique d'export à implémenter
        return response()->json([
            'success' => true,
            'message' => 'Export en cours de développement'
        ]);
    }
}
