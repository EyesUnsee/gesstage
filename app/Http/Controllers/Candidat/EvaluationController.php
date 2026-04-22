<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation;

class EvaluationController extends Controller
{
    /**
     * Afficher la liste des évaluations
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'candidat') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }

        // Récupérer les évaluations du candidat
        $evaluations = Evaluation::where('candidat_id', $user->id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        // Calculer les statistiques
        $moyenneGenerale = Evaluation::where('candidat_id', $user->id)
                                     ->avg('note') ?? 0;
        $evaluationsRecues = $evaluations->count();
        $enAttente = Evaluation::where('candidat_id', $user->id)
                               ->where('statut', 'en_attente')
                               ->count();

        return view('candidat.evaluations', compact(
            'evaluations',
            'moyenneGenerale',
            'evaluationsRecues',
            'enAttente'
        ));
    }

    /**
     * Afficher les détails d'une évaluation
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $evaluation = Evaluation::where('id', $id)
                                ->where('candidat_id', $user->id)
                                ->firstOrFail();

        return view('candidat.evaluations-show', compact('evaluation'));
    }
}
