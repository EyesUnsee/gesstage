<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\Validation;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ValidationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // S'assurer que le chef a un service
        if (!$user->service_id) {
            $service = Service::first();
            if ($service) {
                $user->update(['service_id' => $service->id]);
            } else {
                $service = Service::create([
                    'nom' => 'Service Général',
                    'code' => 'SERV-GEN'
                ]);
                $user->update(['service_id' => $service->id]);
            }
            $user->refresh();
        }
        
        // Récupérer les validations pour ce service
        $validations = Validation::where('service_id', $user->service_id)
            ->where('statut', 'en_attente')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Ajouter les accessoires
        foreach ($validations as $validation) {
            $validation->icone = $this->getIcone($validation->type);
            $validation->stagiaire_nom = $validation->user?->first_name . ' ' . $validation->user?->last_name;
            $validation->service_nom = $user->service?->nom ?? 'Mon Service';
            $validation->jours_attente = $validation->created_at ? $validation->created_at->diffInDays(now()) : 0;
            $validation->message_date = $this->getMessageDate($validation);
            $validation->urgent = false; // Valeur par défaut
        }
        
        // Statistiques
        $stats = [
            'total' => Validation::where('service_id', $user->service_id)->where('statut', 'en_attente')->count(),
            'urgent' => 0,
            'en_attente' => Validation::where('service_id', $user->service_id)->where('statut', 'en_attente')->count(),
            'traitees' => Validation::where('service_id', $user->service_id)
                ->whereIn('statut', ['approuve', 'rejete'])
                ->whereMonth('updated_at', Carbon::now()->month)
                ->count()
        ];
        
        return view('chef-service.validations', compact('validations', 'stats'));
    }
    
    private function getIcone($type)
    {
        return match($type) {
            'bilan' => 'fa-file-alt',
            'inscription' => 'fa-user-plus',
            'convention' => 'fa-file-signature',
            default => 'fa-file',
        };
    }
    
    private function getMessageDate($validation)
    {
        $jours = $validation->created_at ? $validation->created_at->diffInDays(now()) : 0;
        
        if ($jours == 0) {
            return '📅 Reçu aujourd\'hui';
        } elseif ($jours == 1) {
            return '📅 Reçu hier';
        } elseif ($jours <= 3) {
            return "📅 Reçu il y a {$jours} jours";
        } else {
            return "⏳ En attente depuis {$jours} jours";
        }
    }
    
    public function approuver($id)
    {
        $validation = Validation::findOrFail($id);
        $validation->update([
            'statut' => 'approuve',
            'valide_par' => auth()->id(),
            'date_reponse' => Carbon::now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Demande approuvée avec succès']);
    }
    
    public function refuser(Request $request, $id)
    {
        $validation = Validation::findOrFail($id);
        $validation->update([
            'statut' => 'rejete',
            'valide_par' => auth()->id(),
            'date_reponse' => Carbon::now(),
            'motif_rejet' => $request->reason
        ]);
        
        return response()->json(['success' => true, 'message' => 'Demande refusée']);
    }
    
    public function show($id)
    {
        $validation = Validation::with('user')->findOrFail($id);
        return view('chef-service.validations-show', compact('validation'));
    }
}
