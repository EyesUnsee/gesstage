<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Models\Sanction;
use App\Models\Stage;
use App\Models\Bilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $serviceId = Auth::user()->service_id;
        
        // Services
        $services = Service::when($serviceId, function($query) use ($serviceId) {
                $query->where('id', $serviceId);
            })
            ->get();
        
        foreach ($services as $service) {
            $service->stagiaires_count = User::where('service_id', $service->id)
                ->where('role', 'candidat')
                ->count();
            
            $service->tuteurs_count = User::where('service_id', $service->id)
                ->where('role', 'tuteur')
                ->count();
            
            $service->sanctions_count = Sanction::where('service_id', $service->id)->count();
            $service->responsable_nom = $service->responsable ? ($service->responsable->first_name . ' ' . $service->responsable->last_name) : 'Non assigné';
            $service->tags = $service->tags ? explode(',', $service->tags) : [];
        }
        
        // Sanctions
        $sanctionsData = Sanction::with(['stagiaire', 'service'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($sanction) {
                return (object)[
                    'id' => $sanction->id,
                    'stagiaire_nom' => $sanction->stagiaire ? ($sanction->stagiaire->first_name . ' ' . $sanction->stagiaire->last_name) : 'Inconnu',
                    'service_nom' => $sanction->service ? $sanction->service->nom : 'N/A',
                    'type' => $sanction->type,
                    'motif' => $sanction->motif,
                    'gravite' => $sanction->gravite,
                    'created_at' => $sanction->created_at,
                    'statut' => $sanction->statut,
                    'duree' => $sanction->duree
                ];
            });
        
        // Bannis
        $bannis = Sanction::where('type', 'exclusion')
            ->where('statut', 'actif')
            ->with(['stagiaire', 'service'])
            ->get()
            ->map(function($banni) {
                return (object)[
                    'id' => $banni->id,
                    'stagiaire_nom' => $banni->stagiaire ? ($banni->stagiaire->first_name . ' ' . $banni->stagiaire->last_name) : 'Inconnu',
                    'service_nom' => $banni->service ? $banni->service->nom : 'N/A',
                    'motif' => $banni->motif,
                    'date_bannissement' => $banni->created_at,
                    'duree' => $banni->duree ?? 'Permanent'
                ];
            });
        
        // Stages finis
        $stagesFinis = Stage::where('statut', 'termine')
            ->with(['candidat'])
            ->orderBy('date_fin', 'desc')
            ->get()
            ->map(function($stage) {
                $stage->stagiaire_nom = $stage->candidat ? ($stage->candidat->first_name . ' ' . $stage->candidat->last_name) : 'Inconnu';
                $stage->service_nom = $stage->candidat && $stage->candidat->service ? $stage->candidat->service->nom : 'N/A';
                
                $bilan = Bilan::where('stagiaire_id', $stage->candidat_id)->first();
                $stage->note_finale = $bilan?->note ?? 'Non noté';
                $stage->bilan_statut = $bilan?->statut ?? 'en_attente';
                return $stage;
            });
        
        // Statistiques
        $sanctionsEnAttente = Sanction::where('statut', 'en_attente')->count();
        $sanctionsActives = Sanction::where('statut', 'actif')->count();
        $totalBannis = Sanction::where('type', 'exclusion')->where('statut', 'actif')->count();
        
        // Responsables disponibles
        $responsables = User::whereIn('role', ['responsable', 'chef-service'])->get();
        
        // Stagiaires
        $stagiaires = User::where('role', 'candidat')
            ->with('service')
            ->get()
            ->map(function($stagiaire) {
                $stagiaire->service_nom = $stagiaire->service ? $stagiaire->service->nom : 'Sans service';
                return $stagiaire;
            });
        
        return view('chef-service.services', compact(
            'services',
            'sanctionsData',
            'bannis',
            'stagesFinis',
            'sanctionsEnAttente',
            'sanctionsActives',
            'totalBannis',
            'responsables',
            'stagiaires'
        ));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:services',
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'statut' => 'nullable|in:actif,inactif,en-attente',
            'tags' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            $service = Service::create([
                'nom' => $request->nom,
                'responsable_id' => $request->responsable_id,
                'description' => $request->description,
                'statut' => $request->statut ?? 'actif',
                'tags' => $request->tags
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Service créé avec succès', 'service' => $service]);
            }
            
            return redirect()->route('chef-service.services')->with('success', 'Service créé avec succès');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la création du service');
        }
    }
    
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:services,nom,' . $id,
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'statut' => 'nullable|in:actif,inactif,en-attente',
            'tags' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        try {
            $service->update([
                'nom' => $request->nom,
                'responsable_id' => $request->responsable_id,
                'description' => $request->description,
                'statut' => $request->statut ?? 'actif',
                'tags' => $request->tags
            ]);
            
            return response()->json(['success' => true, 'message' => 'Service modifié avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            
            return response()->json(['success' => true, 'message' => 'Service supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }
    
    public function show($id)
    {
        $service = Service::with(['responsable', 'users'])->findOrFail($id);
        
        // Compter les stagiaires
        $service->stagiaires_count = User::where('service_id', $id)->where('role', 'candidat')->count();
        $service->tuteurs_count = User::where('service_id', $id)->where('role', 'tuteur')->count();
        $service->sanctions_count = Sanction::where('service_id', $id)->count();
        $service->responsable_nom = $service->responsable ? ($service->responsable->first_name . ' ' . $service->responsable->last_name) : 'Non assigné';
        
        // Rediriger vers la page des services avec un message
        return redirect()->route('chef-service.services')->with('info', 'Détails du service: ' . $service->nom);
    }
    
    public function storeSanction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stagiaire_id' => 'required|exists:users,id',
            'type' => 'required|in:avertissement,suspension,exclusion,retenue',
            'motif' => 'required|string|min:10',
            'gravite' => 'required|in:faible,moyenne,elevee',
            'duree' => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            $stagiaire = User::findOrFail($request->stagiaire_id);
            
            $sanction = Sanction::create([
                'stagiaire_id' => $request->stagiaire_id,
                'service_id' => $stagiaire->service_id,
                'type' => $request->type,
                'motif' => $request->motif,
                'gravite' => $request->gravite,
                'duree' => $request->duree,
                'statut' => 'actif',
                'cree_par' => Auth::id()
            ]);
            
            // Si c'est une exclusion, désactiver le compte
            if ($request->type === 'exclusion') {
                $stagiaire->update(['is_active' => false, 'status' => 'banni']);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Sanction appliquée avec succès',
                    'sanction' => $sanction
                ]);
            }
            
            return redirect()->route('chef-service.services')->with('success', 'Sanction appliquée avec succès');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de l\'application de la sanction');
        }
    }
    
    public function deleteSanction($id)
    {
        try {
            $sanction = Sanction::findOrFail($id);
            
            // Si c'était une exclusion, réactiver le compte
            if ($sanction->type === 'exclusion' && $sanction->stagiaire) {
                $sanction->stagiaire->update(['is_active' => true, 'status' => 'actif']);
            }
            
            $sanction->delete();
            
            return response()->json(['success' => true, 'message' => 'Sanction supprimée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    public function appealBanni($id)
    {
        try {
            $sanction = Sanction::findOrFail($id);
            $sanction->update(['statut' => 'en_appel']);
            
            if ($sanction->stagiaire) {
                $sanction->stagiaire->update(['is_active' => true, 'status' => 'actif']);
            }
            
            return response()->json(['success' => true, 'message' => 'Appel enregistré, le compte a été réactivé']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'appel'], 500);
        }
    }
    
    public function validateBilan($id)
    {
        try {
            $bilan = Bilan::where('stagiaire_id', $id)->first();
            
            if ($bilan) {
                $bilan->update([
                    'statut' => 'valide',
                    'valide_par' => Auth::id(),
                    'date_validation' => now()
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Bilan validé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la validation'], 500);
        }
    }
    
    public function archiveStage($id)
    {
        try {
            $stage = Stage::findOrFail($id);
            $stage->update(['archive' => true]);
            
            return response()->json(['success' => true, 'message' => 'Stage archivé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'archivage'], 500);
        }
    }
    
    public function exportData($type)
    {
        // Logique d'export
        return response()->json(['success' => true, 'message' => "Export des $type en cours"]);
    }
}
