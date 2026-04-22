<?php

namespace App\Http\Controllers\Tuteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Journal;
use App\Models\Evaluation;

class StagiaireController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stagiaires = User::where('role', 'candidat')
                          ->where('tuteur_id', $user->id)
                          ->get();
        
        return view('tuteur.stagiaires', compact('stagiaires'));
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $stagiaire = User::where('role', 'candidat')
                         ->where('id', $id)
                         ->where('tuteur_id', $user->id)
                         ->firstOrFail();
        
        $journaux = Journal::where('user_id', $stagiaire->id)
                           ->orderBy('created_at', 'desc')
                           ->get();
        
        $evaluations = Evaluation::where('candidat_id', $stagiaire->id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();
        
        return view('tuteur.stagiaire-show', compact('stagiaire', 'journaux', 'evaluations'));
    }
}
