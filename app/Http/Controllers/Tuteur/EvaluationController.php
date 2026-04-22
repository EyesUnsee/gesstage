<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation;
use App\Models\User;

class EvaluationController extends Controller
{
    /**
     * Afficher la liste des évaluations
     */
    public function index()
    {
        $user = Auth::user();
        
        $evaluations = Evaluation::where('evaluateur_id', $user->id)
                                 ->orderBy('created_at', 'desc')
                                 ->with('candidat')
                                 ->get();
        
        $stagiaires = User::where('role', 'candidat')
                          ->where('tuteur_id', $user->id)
                          ->get();
        
        $totalEvaluations = $evaluations->count();
        $evaluationsRealisees = $evaluations->where('statut', 'publie')->count();
        $evaluationsEnAttente = $evaluations->where('statut', 'en_attente')->count();
        $evaluationsEnRetard = $evaluations->where('statut', 'en_retard')->count();
        
        return view('tuteur.evaluations', compact(
            'evaluations', 
            'stagiaires',
            'totalEvaluations',
            'evaluationsRealisees',
            'evaluationsEnAttente',
            'evaluationsEnRetard'
        ));
    }
    
    /**
     * Afficher le formulaire de création d'évaluation
     */
    public function create()
    {
        $user = Auth::user();
        
        // Récupérer les stagiaires du tuteur
        $stagiaires = User::where('role', 'candidat')
                          ->where('tuteur_id', $user->id)
                          ->get();
        
        // Types d'évaluation possibles
        $types = [
            'mi-parcours' => 'Évaluation mi-parcours',
            'finale' => 'Évaluation finale',
            'projet' => 'Évaluation de projet',
        ];
        
        // Critères d'évaluation par défaut
        $criteria = [
            ['nom' => 'Compétences techniques', 'note' => null],
            ['nom' => 'Intégration dans l\'équipe', 'note' => null],
            ['nom' => 'Autonomie', 'note' => null],
            ['nom' => 'Qualité du travail', 'note' => null],
            ['nom' => 'Respect des délais', 'note' => null],
        ];
        
        return view('tuteur.evaluations-create', compact('stagiaires', 'types', 'criteria'));
    }
    
    /**
     * Enregistrer une nouvelle évaluation
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'candidat_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'criteria' => 'nullable|array',
            'commentaire' => 'nullable|string|max:1000',
            'date_limite' => 'nullable|date',
        ]);
        
        // Calculer la note moyenne
        $noteMoyenne = null;
        if ($request->criteria) {
            $notes = array_column($request->criteria, 'note');
            $notesValides = array_filter($notes);
            if (count($notesValides) > 0) {
                $noteMoyenne = array_sum($notesValides) / count($notesValides);
            }
        }
        
        Evaluation::create([
            'candidat_id' => $request->candidat_id,
            'evaluateur_id' => $user->id,
            'titre' => $request->titre,
            'type' => $request->type,
            'criteria' => json_encode($request->criteria),
            'commentaire' => $request->commentaire,
            'note' => $noteMoyenne,
            'date_limite' => $request->date_limite,
            'statut' => 'en_attente',
        ]);
        
        return redirect()->route('tuteur.evaluations')
                         ->with('success', 'Évaluation créée avec succès');
    }
    
    /**
     * Afficher les détails d'une évaluation
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $evaluation = Evaluation::where('id', $id)
                                ->where('evaluateur_id', $user->id)
                                ->with('candidat')
                                ->firstOrFail();
        
        return view('tuteur.evaluations-show', compact('evaluation'));
    }
    
    /**
     * Afficher le formulaire d'édition d'une évaluation
     */
public function edit($id)
{
    $user = Auth::user();
    
    $evaluation = Evaluation::where('id', $id)
                            ->where('evaluateur_id', $user->id)
                            ->with('candidat')
                            ->firstOrFail();
    
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'stagiaire' => $evaluation->candidat->first_name . ' ' . $evaluation->candidat->last_name,
            'criteria' => json_decode($evaluation->criteria, true) ?? [],
            'commentaire' => $evaluation->commentaire,
            'note' => $evaluation->note,
        ]);
    }
    
    // Utiliser edit.blade.php au lieu de evaluations-edit.blade.php
    return view('tuteur.evaluations-edit', compact('evaluation'));
}
    
    /**
     * Mettre à jour une évaluation
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $evaluation = Evaluation::where('id', $id)
                                ->where('evaluateur_id', $user->id)
                                ->firstOrFail();
        
        $request->validate([
            'criteria' => 'nullable|array',
            'commentaire' => 'nullable|string|max:1000',
        ]);
        
        // Calculer la note moyenne
        $noteMoyenne = null;
        if ($request->criteria) {
            $notes = array_column($request->criteria, 'note');
            $notesValides = array_filter($notes);
            if (count($notesValides) > 0) {
                $noteMoyenne = array_sum($notesValides) / count($notesValides);
            }
        }
        
        $evaluation->update([
            'criteria' => json_encode($request->criteria),
            'commentaire' => $request->commentaire,
            'note' => $noteMoyenne,
            'statut' => 'publie',
            'date_evaluation' => now(),
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Évaluation enregistrée avec succès'
            ]);
        }
        
        return redirect()->route('tuteur.evaluations')
                         ->with('success', 'Évaluation enregistrée avec succès');
    }
    
    /**
     * Supprimer une évaluation
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $evaluation = Evaluation::where('id', $id)
                                ->where('evaluateur_id', $user->id)
                                ->firstOrFail();
        
        $evaluation->delete();
        
        return redirect()->route('tuteur.evaluations')
                         ->with('success', 'Évaluation supprimée avec succès');
    }
}
