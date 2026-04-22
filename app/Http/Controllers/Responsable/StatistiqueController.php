<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stage;
use App\Models\Candidature;
use App\Models\Evaluation;
use App\Models\Presence;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StatistiqueController extends Controller
{
    /**
     * Afficher la page des statistiques
     */
    public function index(Request $request)
    {
        // Récupérer la période sélectionnée
        $periode = $request->get('periode', 'month');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        // Définir les dates selon la période
        if ($dateDebut && $dateFin) {
            $periode = 'custom';
        } else {
            switch ($periode) {
                case 'week':
                    $dateDebut = Carbon::now()->startOfWeek();
                    $dateFin = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $dateDebut = Carbon::now()->startOfMonth();
                    $dateFin = Carbon::now()->endOfMonth();
                    break;
                case 'quarter':
                    $dateDebut = Carbon::now()->startOfQuarter();
                    $dateFin = Carbon::now()->endOfQuarter();
                    break;
                case 'year':
                    $dateDebut = Carbon::now()->startOfYear();
                    $dateFin = Carbon::now()->endOfYear();
                    break;
                default:
                    $dateDebut = Carbon::now()->startOfMonth();
                    $dateFin = Carbon::now()->endOfMonth();
            }
        }
        
        // Statistiques globales
        $stagesEnCours = Stage::where('statut', 'en_cours')->count();
        $stagesTermines = Stage::where('statut', 'termine')->count();
        $totalCandidatures = Candidature::count();
        
        // Variations
        $stagesEnCoursMoisDernier = Stage::where('statut', 'en_cours')
            ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])
            ->count();
        $variationStagesEnCours = $stagesEnCours - $stagesEnCoursMoisDernier;
        
        $stagesTerminesAnneeDerniere = Stage::where('statut', 'termine')
            ->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()])
            ->count();
        $variationStagesTermines = $stagesTermines - $stagesTerminesAnneeDerniere;
        
        $candidaturesMoisDernier = Candidature::whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()])->count();
        $candidaturesMoisActuel = Candidature::whereBetween('created_at', [$dateDebut, $dateFin])->count();
        $variationCandidatures = $candidaturesMoisActuel - $candidaturesMoisDernier;
        
        // Taux de satisfaction
        $evaluations = Evaluation::whereNotNull('note')->get();
        $tauxSatisfaction = $evaluations->count() > 0 ? round($evaluations->avg('note') * 20) : 85;
        
        // Taux de présence
        $totalPresences = Presence::count();
        $presencesPresent = Presence::where('statut', 'present')->count();
        $tauxPresence = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
        
        // Taux de complétion des documents
        $totalDocuments = Document::count();
        $documentsValides = Document::where('statut', 'valide')->count();
        $tauxCompletion = $totalDocuments > 0 ? round(($documentsValides / $totalDocuments) * 100) : 0;
        
        // Stages par mois
        $stagesParMois = [];
        $moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $maxValeur = 0;
        
        for ($i = 1; $i <= 12; $i++) {
            $count = Stage::whereMonth('created_at', $i)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->count();
            $stagesParMois[$moisLabels[$i-1]] = ['courant' => $count];
            if ($count > $maxValeur) $maxValeur = $count;
        }
        $stagesParMoisMax = $maxValeur > 0 ? $maxValeur : 1;
        
        // Répartition par département
        $stagiairesParDepartement = User::where('role', 'candidat')
            ->whereNotNull('departement')
            ->select('departement', DB::raw('count(*) as total'))
            ->groupBy('departement')
            ->get();
        
        $totalStagiaires = User::where('role', 'candidat')->count();
        $repartitionDepartements = [];
        $couleurs = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#84cc16'];
        
        foreach ($stagiairesParDepartement as $index => $dep) {
            $pourcentage = $totalStagiaires > 0 ? round(($dep->total / $totalStagiaires) * 100) : 0;
            $repartitionDepartements[] = [
                'nom' => $dep->departement,
                'couleur' => $couleurs[$index % count($couleurs)],
                'couleur_secondaire' => $couleurs[$index % count($couleurs)],
                'pourcentage' => $pourcentage,
                'total' => $dep->total
            ];
        }
        
        $pieChartGradient = '';
        $cumul = 0;
        foreach ($repartitionDepartements as $dep) {
            $pieChartGradient .= $dep['couleur'] . ' ' . $cumul . '% ' . ($cumul + $dep['pourcentage']) . '%, ';
            $cumul += $dep['pourcentage'];
        }
        $pieChartGradient = rtrim($pieChartGradient, ', ');
        
        // Évolution des inscriptions
        $evolutionLabels = [];
        $evolutionStagiaires = [];
        $evolutionTuteurs = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $evolutionLabels[] = $mois->format('M');
            
            $evolutionStagiaires[] = User::where('role', 'candidat')
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->count();
            
            $evolutionTuteurs[] = User::where('role', 'tuteur')
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->count();
        }
        
        // Performance par département
        $performanceDepartements = [];
        foreach ($stagiairesParDepartement as $index => $dep) {
            $stagiairesIds = User::where('role', 'candidat')
                ->where('departement', $dep->departement)
                ->pluck('id');
            
            $evaluationsCount = Evaluation::whereIn('candidat_id', $stagiairesIds)->count();
            $evaluationsNote = Evaluation::whereIn('candidat_id', $stagiairesIds)->avg('note') ?? 0;
            $performance = $evaluationsCount > 0 ? round(($evaluationsNote / 5) * 100) : 70;
            
            $performanceDepartements[] = [
                'nom' => $dep->departement,
                'couleur' => $couleurs[$index % count($couleurs)],
                'couleur_secondaire' => $couleurs[$index % count($couleurs)],
                'performance' => $performance,
                'stagiaires' => $dep->total,
                'evaluations' => $evaluationsCount
            ];
        }
        
        usort($performanceDepartements, function($a, $b) {
            return $b['performance'] <=> $a['performance'];
        });
        
        // Top 5 des tuteurs
        $topTuteurs = User::where('role', 'tuteur')
            ->withCount('stagiaires')
            ->withCount('evaluationsDonnees')
            ->orderBy('stagiaires_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($tuteur) {
                $evaluations = $tuteur->evaluationsDonnees;
                $satisfaction = $evaluations->count() > 0 ? round($evaluations->avg('note') * 20) : 85;
                $performance = $tuteur->stagiaires_count > 0 ? round(($tuteur->stagiaires_count / 3) * 100) : 0;
                
                return [
                    'id' => $tuteur->id,
                    'nom' => $tuteur->first_name . ' ' . $tuteur->last_name,
                    'departement' => $tuteur->departement ?? 'Non défini',
                    'stagiaires' => $tuteur->stagiaires_count,
                    'evaluations' => $tuteur->evaluations_donnees_count,
                    'satisfaction' => $satisfaction,
                    'performance' => $performance
                ];
            });
        
        return view('responsable.statistiques', compact(
            'periode', 'dateDebut', 'dateFin',
            'stagesEnCours', 'stagesTermines', 'totalCandidatures',
            'variationStagesEnCours', 'variationStagesTermines', 'variationCandidatures',
            'tauxSatisfaction', 'tauxPresence', 'tauxCompletion',
            'stagesParMois', 'stagesParMoisMax',
            'repartitionDepartements', 'pieChartGradient',
            'evolutionLabels', 'evolutionStagiaires', 'evolutionTuteurs',
            'performanceDepartements',
            'topTuteurs'
        ));
    }
    
    /**
     * Exporter le rapport en PDF
     */
    public function exportPDF(Request $request)
    {
        // Récupérer les mêmes données que pour l'affichage
        $periode = $request->get('periode', 'month');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        
        if ($dateDebut && $dateFin) {
            $periode = 'custom';
        } else {
            switch ($periode) {
                case 'week':
                    $dateDebut = Carbon::now()->startOfWeek();
                    $dateFin = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $dateDebut = Carbon::now()->startOfMonth();
                    $dateFin = Carbon::now()->endOfMonth();
                    break;
                case 'quarter':
                    $dateDebut = Carbon::now()->startOfQuarter();
                    $dateFin = Carbon::now()->endOfQuarter();
                    break;
                case 'year':
                    $dateDebut = Carbon::now()->startOfYear();
                    $dateFin = Carbon::now()->endOfYear();
                    break;
                default:
                    $dateDebut = Carbon::now()->startOfMonth();
                    $dateFin = Carbon::now()->endOfMonth();
            }
        }
        
        // Statistiques globales
        $stagesEnCours = Stage::where('statut', 'en_cours')->count();
        $stagesTermines = Stage::where('statut', 'termine')->count();
        $totalCandidatures = Candidature::count();
        
        $evaluations = Evaluation::whereNotNull('note')->get();
        $tauxSatisfaction = $evaluations->count() > 0 ? round($evaluations->avg('note') * 20) : 85;
        
        $totalPresences = Presence::count();
        $presencesPresent = Presence::where('statut', 'present')->count();
        $tauxPresence = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
        
        $totalDocuments = Document::count();
        $documentsValides = Document::where('statut', 'valide')->count();
        $tauxCompletion = $totalDocuments > 0 ? round(($documentsValides / $totalDocuments) * 100) : 0;
        
        // Stages par mois
        $stagesParMois = [];
        $moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        for ($i = 1; $i <= 12; $i++) {
            $stagesParMois[$moisLabels[$i-1]] = Stage::whereMonth('created_at', $i)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();
        }
        
        // Top tuteurs
        $topTuteurs = User::where('role', 'tuteur')
            ->withCount('stagiaires')
            ->orderBy('stagiaires_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($tuteur) {
                return [
                    'nom' => $tuteur->first_name . ' ' . $tuteur->last_name,
                    'departement' => $tuteur->departement ?? 'Non défini',
                    'stagiaires' => $tuteur->stagiaires_count
                ];
            });
        
        $data = [
            'date' => Carbon::now()->format('d/m/Y H:i'),
            'periode' => $periode,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'stagesEnCours' => $stagesEnCours,
            'stagesTermines' => $stagesTermines,
            'totalCandidatures' => $totalCandidatures,
            'tauxSatisfaction' => $tauxSatisfaction,
            'tauxPresence' => $tauxPresence,
            'tauxCompletion' => $tauxCompletion,
            'stagesParMois' => $stagesParMois,
            'topTuteurs' => $topTuteurs
        ];
        
        $pdf = Pdf::loadView('responsable.statistiques-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('rapport-statistiques-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
