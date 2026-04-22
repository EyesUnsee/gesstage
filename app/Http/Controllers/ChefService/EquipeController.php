<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stage;
use App\Models\Evaluation;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EquipeController extends Controller
{
    /**
     * Afficher la liste des membres de l'équipe
     */
    public function index()
    {
        $serviceId = Auth::user()->service_id;
        
        // Récupérer les membres du service (tuteurs et stagiaires)
        $membres = User::whereIn('role', ['tuteur', 'candidat'])
            ->when($serviceId, function($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->with(['stage', 'tuteur'])
            ->get()
            ->map(function($membre) {
                // Compter les stagiaires pour les tuteurs
                if ($membre->role === 'tuteur') {
                    $membre->stagiaires_count = User::where('tuteur_id', $membre->id)
                        ->where('role', 'candidat')
                        ->count();
                } else {
                    $membre->stagiaires_count = 0;
                }
                
                // Compter les évaluations
                $membre->evaluations_count = Evaluation::where('candidat_id', $membre->id)
                    ->orWhere('evaluateur_id', $membre->id)
                    ->count();
                
                // Calculer le taux de présence
                $presences = Presence::where('user_id', $membre->id)
                    ->whereMonth('date', now()->month)
                    ->get();
                $total = $presences->count();
                $present = $presences->where('est_present', true)->count();
                $membre->presence_taux = $total > 0 ? round(($present / $total) * 100) : 0;
                
                return $membre;
            });
        
        // Statistiques
        $stats = [
            'total' => $membres->count(),
            'tuteurs' => $membres->where('role', 'tuteur')->count(),
            'stagiaires' => $membres->where('role', 'candidat')->count(),
            'actifs' => $membres->where('is_active', true)->count()
        ];
        
        // Liste des tuteurs pour le formulaire
        $tuteurs = User::where('role', 'tuteur')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->get();
        
        return view('chef-service.equipe', compact('membres', 'stats', 'tuteurs'));
    }
    
    /**
     * Ajouter un membre
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:tuteur,candidat',
            'tuteur_id' => 'nullable|exists:users,id',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'tuteur_id' => $request->role === 'candidat' ? $request->tuteur_id : null,
                'service_id' => Auth::user()->service_id,
                'password' => Hash::make($request->password),
                'is_active' => true
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Membre ajouté avec succès', 'user' => $user]);
            }
            
            return redirect()->route('chef-service.equipe')->with('success', 'Membre ajouté avec succès');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de l\'ajout');
        }
    }
    
    /**
     * Afficher les détails d'un membre
     */
    public function show($id)
    {
        $membre = User::with(['stage', 'tuteur', 'evaluations', 'presences'])
            ->findOrFail($id);
        
        // Vérifier que le membre appartient au service du chef
        if ($membre->service_id != Auth::user()->service_id && Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        return view('chef-service.equipe-show', compact('membre'));
    }
    
    /**
     * Modifier un membre
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:tuteur,candidat',
            'tuteur_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:8|confirmed'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role = $request->role;
            $user->tuteur_id = $request->role === 'candidat' ? $request->tuteur_id : null;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Membre modifié avec succès']);
            }
            
            return redirect()->route('chef-service.equipe')->with('success', 'Membre modifié avec succès');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la modification');
        }
    }
    
    /**
     * Supprimer un membre
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json(['success' => true, 'message' => 'Membre supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    /**
     * Récupérer les données d'un membre pour édition
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    
    /**
     * Mettre à jour le rôle d'un membre
     */
    public function updateRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:tuteur,candidat'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        try {
            $user = User::findOrFail($id);
            $user->role = $request->role;
            $user->save();
            
            return response()->json(['success' => true, 'message' => 'Rôle mis à jour avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
        }
    }
    
    /**
     * Obtenir les statistiques d'un membre
     */
    public function statistiques($id)
    {
        $user = User::findOrFail($id);
        
        $stats = [
            'stagiaires' => 0,
            'evaluations' => 0,
            'presence' => 0,
            'taches' => 0
        ];
        
        if ($user->role === 'tuteur') {
            $stats['stagiaires'] = User::where('tuteur_id', $id)->where('role', 'candidat')->count();
            $stats['evaluations'] = Evaluation::where('evaluateur_id', $id)->count();
        } else {
            $stats['evaluations'] = Evaluation::where('candidat_id', $id)->count();
            $stats['taches'] = DB::table('taches')->where('user_id', $id)->count();
        }
        
        $presences = Presence::where('user_id', $id)
            ->whereMonth('date', now()->month)
            ->get();
        $total = $presences->count();
        $present = $presences->where('est_present', true)->count();
        $stats['presence'] = $total > 0 ? round(($present / $total) * 100) : 0;
        
        return response()->json(['success' => true, 'stats' => $stats]);
    }
}
