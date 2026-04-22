<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Candidature;
use App\Models\Presence;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques Candidatures
        $candidaturesActives = Candidature::where('statut', 'en_attente')->count();
        $nouvellesCandidatures = Candidature::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        
        // Statistiques Stagiaires
        $stagiairesEnCours = User::where('role', 'candidat')
                                 ->whereHas('stage', function($q) {
                                     $q->where('statut', 'en_cours');
                                 })->count();
        $nouveauxStagiaires = User::where('role', 'candidat')
                                  ->where('created_at', '>=', Carbon::now()->subDays(30))
                                  ->count();
        
        // Tuteurs actifs
        $tuteursActifs = User::where('role', 'tuteur')->where('is_active', true)->count();
        
        // Présences
        $presences = Presence::whereDate('date', Carbon::today())->get();
        $presenceMoyenne = $presences->count() > 0 ? round(($presences->where('statut', 'present')->count() / $presences->count()) * 100) : 0;
        
        $retardsAujourdhui = $presences->where('statut', 'retard')->count();
        
        // Pointages du jour
        $pointages = $presences->map(function($p) {
            return (object)[
                'stagiaire_id' => $p->user_id,
                'stagiaire' => $p->user,
                'service' => $p->user->departement ?? 'Non défini',
                'statut' => $p->statut,
                'heure' => $p->heure_arrivee ? Carbon::parse($p->heure_arrivee)->format('H:i') : '--:--'
            ];
        });
        
        $services = User::where('role', 'candidat')->whereNotNull('departement')->distinct()->pluck('departement');
        
        // Graphiques
        $candidaturesParMois = [];
        for ($i = 1; $i <= 12; $i++) {
            $mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'][$i-1];
            $count = Candidature::whereMonth('created_at', $i)->whereYear('created_at', Carbon::now()->year)->count();
            $candidaturesParMois[$mois] = $count;
        }
        
        $totalCandidatures = Candidature::count();
        $acceptees = Candidature::where('statut', 'acceptee')->count();
        $enAttente = Candidature::where('statut', 'en_attente')->count();
        $refusees = Candidature::where('statut', 'refusee')->count();
        
        $pourcentageAcceptees = $totalCandidatures > 0 ? round(($acceptees / $totalCandidatures) * 100) : 0;
        $pourcentageEnAttente = $totalCandidatures > 0 ? round(($enAttente / $totalCandidatures) * 100) : 0;
        $pourcentageRefusees = $totalCandidatures > 0 ? round(($refusees / $totalCandidatures) * 100) : 0;
        
        $dernieresCandidatures = Candidature::with('candidat')->orderBy('created_at', 'desc')->limit(5)->get();
        
        return view('responsable.dashboard', compact(
            'candidaturesActives', 'nouvellesCandidatures',
            'stagiairesEnCours', 'nouveauxStagiaires', 'tuteursActifs',
            'presenceMoyenne', 'retardsAujourdhui', 'pointages', 'services',
            'candidaturesParMois', 'pourcentageAcceptees', 'pourcentageEnAttente',
            'pourcentageRefusees', 'dernieresCandidatures'
        ));
    }
}
