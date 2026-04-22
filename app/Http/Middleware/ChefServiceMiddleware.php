<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChefServiceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérification simple sans appel à la base de données
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        if (auth()->user()->role !== 'chef-service') {
            abort(403, 'Accès non autorisé - Vous n\'avez pas les droits de Chef de service');
        }
        
        return $next($request);
    }
}
