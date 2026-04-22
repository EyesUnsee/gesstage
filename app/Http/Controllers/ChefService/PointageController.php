<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PointageController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $serviceId = Auth::user()->service_id;
        
        // Définir la plage de dates selon la période
        $dateDebut = $this->getDateDebut($period);
        $dateFin = $this->getDateFin($period);
        
        // Récupérer les pointages
        $pointages = Presence::whereBetween('date', [$dateDebut, $dateFin])
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->with(['user', 'user.service'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($presence) {
                return (object)[
                    'id' => $presence->id,
                    'stagiaire_nom' => $presence->user?->full_name ?? 'Inconnu',
                    'service_id' => $presence->user?->service_id,
                    'service_nom' => $presence->user?->service?->nom ?? 'N/A',
                    'date' => $presence->date,
                    'heure_arrivee' => $presence->heure_arrivee,
                    'heure_depart' => $presence->heure_depart,
                    'statut' => $presence->statut ?? ($presence->est_present ? 'present' : 'absent')
                ];
            });
        
        // Statistiques
        $stats = $this->getStats($serviceId, $dateDebut, $dateFin);
        
        // Services pour les filtres
        $services = Service::when($serviceId, fn($q) => $q->where('id', $serviceId))->get();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'pointages' => $pointages,
                'stats' => $stats
            ]);
        }
        
        return view('chef-service.pointages', compact('pointages', 'stats', 'services', 'period'));
    }
    
    public function justifier(Request $request, $id)
    {
        $request->validate([
            'motif' => 'required|string|min:5'
        ]);
        
        try {
            $presence = Presence::findOrFail($id);
            $presence->update([
                'statut' => 'justifie',
                'justification' => $request->motif,
                'est_justifie' => true,
                'motif_absence' => $request->motif
            ]);
            
            return response()->json(['success' => true, 'message' => 'Absence justifiée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
    
    private function getDateDebut($period)
    {
        return match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth()
        };
    }
    
    private function getDateFin($period)
    {
        return match($period) {
            'week' => Carbon::now()->endOfWeek(),
            'month' => Carbon::now()->endOfMonth(),
            'year' => Carbon::now()->endOfYear(),
            default => Carbon::now()->endOfMonth()
        };
    }
    
    private function getStats($serviceId, $dateDebut, $dateFin)
    {
        $pointages = Presence::whereBetween('date', [$dateDebut, $dateFin])
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', fn($q) => $q->where('service_id', $serviceId));
            })
            ->get();
        
        $total = $pointages->count();
        $presents = $pointages->where('statut', 'present')->count();
        $absents = $pointages->where('statut', 'absent')->count();
        $retards = $pointages->where('statut', 'retard')->count();
        $justifies = $pointages->where('statut', 'justifie')->count();
        
        // Si les statuts ne sont pas définis, utiliser est_present
        if ($total > 0 && $presents === 0 && $absents === 0) {
            $presents = $pointages->where('est_present', true)->count();
            $absents = $total - $presents;
        }
        
        return [
            'total_presents' => $presents,
            'total_absents' => $absents,
            'total_retards' => $retards,
            'total_justifies' => $justifies,
            'taux_presence' => $total > 0 ? round(($presents / $total) * 100) : 0
        ];
    }
}
