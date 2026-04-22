<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tache;
use App\Models\SemaineValidee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Gérer la navigation entre semaines
        $offset = $request->get('offset', 0);
        $semaineChoisie = $request->get('semaine');
        $anneeChoisie = $request->get('annee');
        
        $aujourdhui = Carbon::now();
        
        if ($semaineChoisie && $anneeChoisie) {
            $dateDebutSemaine = Carbon::now()->setISODate($anneeChoisie, $semaineChoisie)->startOfWeek();
            $dateFinSemaine = Carbon::now()->setISODate($anneeChoisie, $semaineChoisie)->endOfWeek();
            $semaineActuelle = $semaineChoisie;
            $anneeActuelle = $anneeChoisie;
        } else {
            // Appliquer l'offset pour naviguer entre semaines
            if ($offset != 0) {
                $dateDebutSemaine = Carbon::now()->startOfWeek()->addWeeks($offset);
                $dateFinSemaine = Carbon::now()->endOfWeek()->addWeeks($offset);
            } else {
                $dateDebutSemaine = Carbon::now()->startOfWeek();
                $dateFinSemaine = Carbon::now()->endOfWeek();
            }
            $semaineActuelle = $dateDebutSemaine->weekOfYear;
            $anneeActuelle = $dateDebutSemaine->year;
        }
        
        // Récupérer les tâches de la semaine
        $taches = Tache::where('user_id', $user->id)
            ->whereBetween('date', [$dateDebutSemaine->format('Y-m-d'), $dateFinSemaine->format('Y-m-d')])
            ->get();
        
        $totalTachesSemaine = $taches->count();
        $tachesTermineesSemaine = $taches->where('terminee', true)->count();
        $tachesEnCoursSemaine = $totalTachesSemaine - $tachesTermineesSemaine;
        $progressionSemaine = $totalTachesSemaine > 0 ? round(($tachesTermineesSemaine / $totalTachesSemaine) * 100) : 0;
        
        // Vérifier si la semaine est validée
        $semaineValidee = SemaineValidee::where('user_id', $user->id)
            ->where('semaine', $semaineActuelle)
            ->where('annee', $anneeActuelle)
            ->exists();
        
        // Préparer les jours de la semaine
        $joursSemaine = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        for ($i = 0; $i < 7; $i++) {
            $dateJour = $dateDebutSemaine->copy()->addDays($i);
            $tachesJour = $taches->filter(function($tache) use ($dateJour) {
                return $tache->date == $dateJour->format('Y-m-d');
            });
            
            $joursSemaine[] = [
                'nom' => $jours[$i],
                'date' => $dateJour,
                'taches' => $tachesJour
            ];
        }
        
        // Récupérer les semaines du mois
        $semainesDuMois = $this->getSemainesDuMois($dateDebutSemaine);
        $semainesDisponibles = $this->getSemainesDisponibles($user);
        $currentMonthName = Carbon::now()->translatedFormat('F Y');
        
        // Statistiques globales
        $stats = [
            'total' => Tache::where('user_id', $user->id)->count(),
            'terminees' => Tache::where('user_id', $user->id)->where('terminee', true)->count(),
            'en_cours' => Tache::where('user_id', $user->id)->where('terminee', false)->count(),
        ];
        
        return view('candidat.journal', compact(
            'taches',
            'totalTachesSemaine',
            'tachesTermineesSemaine',
            'tachesEnCoursSemaine',
            'progressionSemaine',
            'semaineActuelle',
            'anneeActuelle',
            'dateDebutSemaine',
            'dateFinSemaine',
            'joursSemaine',
            'semaineValidee',
            'semainesDuMois',
            'semainesDisponibles',
            'currentMonthName',
            'stats'
        ));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'date' => 'required|date',
            'priorite' => 'nullable|in:low,medium,high',
            'description' => 'nullable|string'
        ]);
        
        $tache = Tache::create([
            'user_id' => Auth::id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'date' => $request->date,
            'priorite' => $request->priorite ?? 'medium',
            'terminee' => false
        ]);
        
        return response()->json(['success' => true, 'message' => 'Tâche ajoutée', 'tache' => $tache]);
    }
    
    public function update(Request $request, $id)
    {
        $tache = Tache::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'priorite' => 'nullable|in:low,medium,high',
            'description' => 'nullable|string'
        ]);
        
        $tache->update([
            'titre' => $request->titre,
            'priorite' => $request->priorite ?? $tache->priorite,
            'description' => $request->description
        ]);
        
        return response()->json(['success' => true, 'message' => 'Tâche modifiée']);
    }
    
    public function toggle($id)
    {
        $tache = Tache::where('user_id', Auth::id())->findOrFail($id);
        $tache->terminee = !$tache->terminee;
        $tache->save();
        
        return response()->json(['success' => true, 'message' => 'Statut mis à jour']);
    }
    
    public function destroy($id)
    {
        $tache = Tache::where('user_id', Auth::id())->findOrFail($id);
        $tache->delete();
        
        return response()->json(['success' => true, 'message' => 'Tâche supprimée']);
    }
    
    public function validerSemaine(Request $request)
    {
        $request->validate([
            'semaine' => 'required|integer',
            'annee' => 'required|integer'
        ]);
        
        $user = Auth::user();
        
        // Vérifier si toutes les tâches de la semaine sont terminées
        $dateDebut = Carbon::now()->setISODate($request->annee, $request->semaine)->startOfWeek();
        $dateFin = Carbon::now()->setISODate($request->annee, $request->semaine)->endOfWeek();
        
        $tachesEnCours = Tache::where('user_id', $user->id)
            ->whereBetween('date', [$dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d')])
            ->where('terminee', false)
            ->exists();
        
        if ($tachesEnCours) {
            return response()->json(['success' => false, 'message' => 'Toutes les tâches doivent être terminées pour valider la semaine']);
        }
        
        // Créer la validation de semaine
        SemaineValidee::updateOrCreate(
            [
                'user_id' => $user->id,
                'semaine' => $request->semaine,
                'annee' => $request->annee
            ],
            ['validee_le' => Carbon::now()]
        );
        
        return response()->json(['success' => true, 'message' => 'Semaine validée']);
    }
    
    private function getSemainesDuMois($dateReference)
    {
        $semaines = [];
        $date = $dateReference->copy()->startOfMonth()->startOfWeek();
        $finMois = $dateReference->copy()->endOfMonth();
        
        while ($date <= $finMois) {
            $semaineNum = $date->weekOfYear;
            $annee = $date->year;
            
            $tachesSemaine = Tache::where('user_id', Auth::id())
                ->whereBetween('date', [$date->format('Y-m-d'), $date->copy()->endOfWeek()->format('Y-m-d')])
                ->get();
            
            $terminees = $tachesSemaine->where('terminee', true)->count();
            $totalTaches = $tachesSemaine->count();
            
            $semaines[] = [
                'numero' => $semaineNum,
                'annee' => $annee,
                'debut' => $date->copy(),
                'fin' => $date->copy()->endOfWeek(),
                'terminees' => $terminees,
                'totalTaches' => $totalTaches,
                'validee' => SemaineValidee::where('user_id', Auth::id())
                    ->where('semaine', $semaineNum)
                    ->where('annee', $annee)
                    ->exists()
            ];
            
            $date->addWeek();
        }
        
        return $semaines;
    }
    
    private function getSemainesDisponibles($user)
    {
        $semaines = [];
        
        // Récupérer la première et dernière tâche
        $premiereTache = Tache::where('user_id', $user->id)->orderBy('date', 'asc')->first();
        $derniereTache = Tache::where('user_id', $user->id)->orderBy('date', 'desc')->first();
        
        if (!$premiereTache || !$derniereTache) {
            $aujourdhui = Carbon::now();
            $semaines[] = [
                'semaine' => $aujourdhui->weekOfYear,
                'annee' => $aujourdhui->year,
                'libelle' => 'Semaine ' . $aujourdhui->weekOfYear . ' (' . $aujourdhui->startOfWeek()->format('d/m') . ' - ' . $aujourdhui->endOfWeek()->format('d/m') . ')',
                'active' => true
            ];
            return $semaines;
        }
        
        $dateDebut = Carbon::parse($premiereTache->date)->startOfWeek();
        $dateFin = Carbon::parse($derniereTache->date)->endOfWeek();
        $aujourdhui = Carbon::now();
        
        while ($dateDebut <= $dateFin) {
            $semaineNum = $dateDebut->weekOfYear;
            $annee = $dateDebut->year;
            
            $semaines[] = [
                'semaine' => $semaineNum,
                'annee' => $annee,
                'libelle' => 'Semaine ' . $semaineNum . ' (' . $dateDebut->format('d/m') . ' - ' . $dateDebut->copy()->endOfWeek()->format('d/m') . ')',
                'active' => ($semaineNum == $aujourdhui->weekOfYear && $annee == $aujourdhui->year)
            ];
            
            $dateDebut->addWeek();
        }
        
        return $semaines;
    }
}
