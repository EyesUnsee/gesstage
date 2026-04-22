<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\Bilan;
use App\Models\Stage;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BilanController extends Controller
{
    /**
     * Afficher la liste des bilans
     */
    public function index()
    {
        $serviceId = Auth::user()->service_id;
        
        // Bilans en attente
        $bilansEnAttente = Bilan::where('statut', 'en_attente')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('stagiaire', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->with(['stagiaire', 'tuteur'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($bilan) {
                $bilan->stagiaire_nom = $bilan->stagiaire ? ($bilan->stagiaire->first_name . ' ' . $bilan->stagiaire->last_name) : 'Inconnu';
                $bilan->tuteur_nom = $bilan->tuteur ? ($bilan->tuteur->first_name . ' ' . $bilan->tuteur->last_name) : 'Non assigné';
                $bilan->service_nom = $bilan->stagiaire && $bilan->stagiaire->service ? $bilan->stagiaire->service->nom : 'N/A';
                $bilan->service_id = $bilan->stagiaire && $bilan->stagiaire->service ? $bilan->stagiaire->service->id : null;
                
                // Récupérer les dates du stage
                $stage = Stage::where('candidat_id', $bilan->stagiaire_id)->first();
                if ($stage) {
                    $bilan->date_debut = $stage->date_debut;
                    $bilan->date_fin = $stage->date_fin;
                }
                
                return $bilan;
            });
        
        // Bilans validés
        $bilansValides = Bilan::where('statut', 'valide')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('stagiaire', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Stages terminés
        $stagesTermines = Stage::where('statut', 'termine')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->with(['candidat', 'candidat.service'])
            ->orderBy('date_fin', 'desc')
            ->get()
            ->map(function($stage) {
                $stage->stagiaire_nom = $stage->candidat ? ($stage->candidat->first_name . ' ' . $stage->candidat->last_name) : 'Inconnu';
                $stage->service_nom = $stage->candidat && $stage->candidat->service ? $stage->candidat->service->nom : 'N/A';
                $stage->service_id = $stage->candidat && $stage->candidat->service ? $stage->candidat->service->id : null;
                
                // Récupérer le bilan associé
                $bilan = Bilan::where('stagiaire_id', $stage->candidat_id)->first();
                $stage->note = $bilan ? $bilan->note : null;
                $stage->bilan_id = $bilan ? $bilan->id : null;
                
                return $stage;
            });
        
        // Statistiques
        $stats = [
            'en_attente' => $bilansEnAttente->count(),
            'valides' => $bilansValides,
            'termines' => $stagesTermines->count(),
            'note_moyenne' => Bilan::where('statut', 'valide')
                ->when($serviceId, function($query) use ($serviceId) {
                    $query->whereHas('stagiaire', function($q) use ($serviceId) {
                        $q->where('service_id', $serviceId);
                    });
                })
                ->avg('note') ?? 0
        ];
        
        // Services pour les filtres
        $services = Service::when($serviceId, function($query) use ($serviceId) {
                $query->where('id', $serviceId);
            })
            ->get();
        
        // Stagiaires pour le formulaire - Version corrigée sans whereDoesntHave('bilan')
        $stagiaires = User::where('role', 'candidat')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->whereDoesntHave('bilans')
            ->get();
        
        return view('chef-service.bilans', compact(
            'bilansEnAttente',
            'bilansValides',
            'stagesTermines',
            'stats',
            'services',
            'stagiaires'
        ));
    }
    
    /**
     * Afficher un bilan spécifique
     */
    public function show($id)
    {
        $bilan = Bilan::with(['stagiaire', 'tuteur', 'stagiaire.service'])
            ->findOrFail($id);
        
        // Vérifier l'accès
        $serviceId = Auth::user()->service_id;
        if ($serviceId && $bilan->stagiaire && $bilan->stagiaire->service_id != $serviceId) {
            abort(403);
        }
        
        return view('chef-service.bilans-show', compact('bilan'));
    }
    
/**
 * Créer un nouveau bilan
 */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'stagiaire_id' => 'required|exists:users,id',
        'contenu' => 'required|string|min:10',
        'note' => 'nullable|numeric|min:0|max:20'
    ]);
    
    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }
    
    try {
        $stagiaire = User::findOrFail($request->stagiaire_id);
        
        $bilan = Bilan::create([
            'stagiaire_id' => $request->stagiaire_id,
            'tuteur_id' => $stagiaire->tuteur_id,
            'service_id' => $stagiaire->service_id,
            'contenu' => $request->contenu,
            'note' => $request->note,
            'statut' => 'en_attente'
            // Supprimer date_soumission si la colonne n'existe pas
        ]);
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Bilan créé avec succès', 'bilan' => $bilan]);
        }
        
        return redirect()->route('chef-service.bilans')->with('success', 'Bilan créé avec succès');
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
        return redirect()->back()->with('error', 'Erreur lors de la création');
    }
}
    /**
     * Valider un bilan
     */
    public function valider($id)
    {
        try {
            $bilan = Bilan::findOrFail($id);
            $bilan->update([
                'statut' => 'valide',
                'valide_par' => Auth::id(),
                'date_validation' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Bilan validé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la validation'], 500);
        }
    }
    
    /**
     * Rejeter un bilan
     */
    public function rejeter($id)
    {
        try {
            $bilan = Bilan::findOrFail($id);
            $bilan->update([
                'statut' => 'rejete',
                'valide_par' => Auth::id(),
                'date_validation' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Bilan rejeté']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors du rejet'], 500);
        }
    }
    
    /**
     * Modifier un bilan
     */
    public function update(Request $request, $id)
    {
        $bilan = Bilan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'contenu' => 'required|string|min:10',
            'note' => 'nullable|numeric|min:0|max:20'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator);
        }
        
        try {
            $bilan->update([
                'contenu' => $request->contenu,
                'note' => $request->note
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Bilan modifié avec succès']);
            }
            
            return redirect()->route('chef-service.bilans')->with('success', 'Bilan modifié avec succès');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la modification');
        }
    }
    
    /**
     * Supprimer un bilan
     */
    public function destroy($id)
    {
        try {
            $bilan = Bilan::findOrFail($id);
            $bilan->delete();
            
            return response()->json(['success' => true, 'message' => 'Bilan supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    /**
     * Exporter les bilans en Excel
     */
    public function exportExcel()
    {
        $serviceId = Auth::user()->service_id;
        
        $bilans = Bilan::where('statut', 'valide')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('stagiaire', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->with(['stagiaire', 'tuteur'])
            ->get();
        
        // Logique d'export Excel à implémenter
        return response()->json(['success' => true, 'message' => 'Export en cours de développement']);
    }
    
    /**
     * Récupérer les données d'un bilan pour édition
     */
    public function edit($id)
    {
        $bilan = Bilan::findOrFail($id);
        return response()->json($bilan);
    }
}
