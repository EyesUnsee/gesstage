<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DossierValideMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur est un candidat
        if ($user->role !== 'candidat') {
            abort(403, 'Accès non autorisé.');
        }

        // Vérifier si le dossier est validé
        if (!$user->dossier_valide) {
            // Rediriger vers la page de complétion du dossier
            return redirect()->route('candidat.new.dashboard')
                ->with('warning', 'Veuillez compléter votre dossier avant d\'accéder au tableau de bord.');
        }

        return $next($request);
    }
}
