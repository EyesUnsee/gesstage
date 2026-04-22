<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDossierValide
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est un candidat avec dossier non validé
        if ($user && $user->role === 'candidat' && !$user->dossier_valide) {
            // Rediriger vers le dashboard de dépôt de dossier
            return redirect()->route('candidat.new.dashboard');
        }
        
        return $next($request);
    }
}
