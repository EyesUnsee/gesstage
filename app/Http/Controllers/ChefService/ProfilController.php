<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activite;
use App\Models\Stage;
use App\Models\Bilan;
use App\Models\Validation;
use App\Models\Service;
use App\Models\Presence;
use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ProfilController extends Controller
{
    /**
     * Afficher le profil du chef de service
     */
    public function index()
    {
        $user = Auth::user();
        $serviceId = $user->service_id;
        $serviceName = $serviceId ? Service::find($serviceId)?->nom : null;
        
        // ========== STATISTIQUES GLOBALES ==========
        
        // Stagiaires encadrés par le service
        $stagiairesEncadres = Stage::where('statut', 'en_cours')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Nombre de tuteurs dans le service
        $tuteursCount = User::where('role', 'tuteur')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->count();
        
        // Projets en cours
        $projetsEnCours = Stage::where('statut', 'en_cours')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('candidat', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Validations en attente
        $validationsEnAttente = Validation::where('statut', 'en_attente')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Bilans à valider
        $bilansEnAttente = Bilan::where('statut', 'en_attente')
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('stagiaire', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        // Taille de l'équipe
        $equipeSize = User::whereIn('role', ['tuteur', 'candidat', 'stagiaire'])
            ->when($serviceId, function($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->count();
        
        // Ancienneté
        $anciennete = $user->created_at ? Carbon::parse($user->created_at)->diffForHumans() : 'Nouveau';
        
        // Taux de présence du service
        $presencesMois = Presence::whereMonth('date', Carbon::now()->month)
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->get();
        
        $totalPresences = $presencesMois->count();
        $presencesPresent = $presencesMois->where('est_present', true)->count();
        $tauxPresenceService = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
        
        // Tâches complétées du service
        $tachesTotales = Tache::when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        $tachesCompletees = Tache::where('terminee', true)
            ->when($serviceId, function($query) use ($serviceId) {
                $query->whereHas('user', function($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            })
            ->count();
        
        $tauxCompletion = $tachesTotales > 0 ? round(($tachesCompletees / $tachesTotales) * 100) : 0;
        
        // ========== COMPÉTENCES ==========
        $competences = [];
        if ($user->expertises) {
            $competences = is_array($user->expertises) ? $user->expertises : explode(',', $user->expertises);
        }
        
        if (empty($competences)) {
            $competences = [
                'Gestion de projet',
                'Encadrement d\'équipe',
                'Développement stratégique',
                'Gestion des ressources humaines',
                'Évaluation de performance'
            ];
        }
        
        // ========== ACTIVITÉS RÉCENTES ==========
        $activitesRecentes = Activite::where(function($query) use ($user, $serviceId) {
                $query->where('user_id', $user->id);
                if ($serviceId) {
                    $query->orWhereHas('user', function($q) use ($serviceId) {
                        $q->where('service_id', $serviceId);
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Si aucune activité, créer des activités de démonstration
        if ($activitesRecentes->isEmpty()) {
            $activitesRecentes = collect([
                (object)['id' => 1, 'titre' => 'Bienvenue sur votre profil', 'icone' => 'fa-user-check', 'created_at' => Carbon::now(), 'user_nom' => 'Système'],
                (object)['id' => 2, 'titre' => 'Configurez votre profil', 'icone' => 'fa-cog', 'created_at' => Carbon::now()->subHours(2), 'user_nom' => 'Système'],
            ]);
        }
        
        // ========== SATISFACTION (moyenne des évaluations) - VERSION CORRIGÉE ==========
        $satisfaction = 85; // valeur par défaut
        
        // Vérifier si la table evaluations existe
        if (Schema::hasTable('evaluations')) {
            // Requête corrigée sans whereHas sur DB
            $satisfactionMoyenne = DB::table('evaluations')
                ->when($serviceId, function($query) use ($serviceId) {
                    // Si vous avez une relation avec stagiaire, utilisez une sous-requête
                    $query->whereExists(function($subquery) use ($serviceId) {
                        $subquery->select(DB::raw(1))
                            ->from('users')
                            ->whereColumn('users.id', 'evaluations.stagiaire_id')
                            ->where('users.service_id', $serviceId);
                    });
                })
                ->avg('note');
            
            $satisfaction = $satisfactionMoyenne ? round($satisfactionMoyenne * 20) : 85;
        }
        
        // ========== ACTIVITÉS PAR MOIS (pour graphique) ==========
        $activitesParMois = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $count = Activite::whereMonth('created_at', $mois->month)
                ->whereYear('created_at', $mois->year)
                ->where(function($query) use ($user, $serviceId) {
                    $query->where('user_id', $user->id);
                    if ($serviceId) {
                        $query->orWhereHas('user', function($q) use ($serviceId) {
                            $q->where('service_id', $serviceId);
                        });
                    }
                })
                ->count();
            $activitesParMois[] = [
                'mois' => $mois->format('M'),
                'count' => $count
            ];
        }
        
        return view('chef-service.profil', compact(
            'user',
            'serviceName',
            'stagiairesEncadres',
            'tuteursCount',
            'projetsEnCours',
            'validationsEnAttente',
            'bilansEnAttente',
            'equipeSize',
            'anciennete',
            'competences',
            'activitesRecentes',
            'satisfaction',
            'tauxPresenceService',
            'tauxCompletion',
            'activitesParMois'
        ));
    }
    
    /**
     * Mettre à jour le profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bureau' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date|before:today',
            'bio' => 'nullable|string|max:1000',
            'linkedin' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }
        
        // Mise à jour des informations
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->bureau = $request->bureau ?? $user->bureau;
        $user->address = $request->address ?? $user->address;
        $user->bio = $request->bio ?? $user->bio;
        $user->linkedin = $request->linkedin ?? $user->linkedin;
        
        if ($request->birth_date) {
            $user->birth_date = Carbon::parse($request->birth_date);
        }
        
        // Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }
        
        $user->save();
        
        // Enregistrer l'activité
        $this->logActivity('Profil mis à jour', 'Votre profil a été modifié', 'fa-user-edit');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'user' => $user
            ]);
        }
        
        return redirect()->route('chef-service.profil')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }
    
    /**
     * Mettre à jour l'avatar uniquement
     */
    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format d\'image invalide',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        
        $this->logActivity('Avatar modifié', 'Votre photo de profil a été mise à jour', 'fa-camera');
        
        return response()->json([
            'success' => true,
            'message' => 'Avatar mis à jour avec succès',
            'avatar_url' => asset('storage/' . $path)
        ]);
    }
    
    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect.'
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        $this->logActivity('Mot de passe modifié', 'Votre mot de passe a été changé', 'fa-key');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Votre mot de passe a été modifié avec succès.'
            ]);
        }
        
        return redirect()->route('chef-service.profil')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
    
    /**
     * Mettre à jour les compétences
     */
    public function updateCompetences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'competences' => 'required|array|min:1',
            'competences.*' => 'string|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        $user->expertises = implode(',', $request->competences);
        $user->save();
        
        $this->logActivity('Compétences mises à jour', 'Votre liste de compétences a été modifiée', 'fa-trophy');
        
        return response()->json([
            'success' => true,
            'message' => 'Compétences mises à jour avec succès',
            'competences' => $request->competences
        ]);
    }
    
    /**
     * Mettre à jour les informations professionnelles
     */
    public function updateProfessional(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poste' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'date_prise_fonction' => 'nullable|date',
            'max_stagiaires' => 'nullable|integer|min:1|max:50'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        $user->poste = $request->poste ?? $user->poste;
        $user->departement = $request->departement ?? $user->departement;
        $user->max_stagiaires = $request->max_stagiaires ?? $user->max_stagiaires;
        
        if ($request->date_prise_fonction) {
            $user->date_prise_fonction = Carbon::parse($request->date_prise_fonction);
        }
        
        $user->save();
        
        $this->logActivity('Informations professionnelles mises à jour', 'Vos informations professionnelles ont été modifiées', 'fa-briefcase');
        
        return response()->json([
            'success' => true,
            'message' => 'Informations professionnelles mises à jour avec succès'
        ]);
    }
    
    /**
     * Supprimer l'avatar
     */
    public function deleteAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
            
            $this->logActivity('Avatar supprimé', 'Votre photo de profil a été supprimée', 'fa-trash');
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar supprimé avec succès'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Aucun avatar à supprimer'
        ], 404);
    }
    
    /**
     * Enregistrer une activité
     */
    private function logActivity($titre, $description, $icone = 'fa-info-circle')
    {
        try {
            Activite::create([
                'titre' => $titre,
                'description' => $description,
                'icone' => $icone,
                'type' => 'profil',
                'user_id' => Auth::id(),
                'user_nom' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'created_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            // Ignorer les erreurs d'activité
        }
    }
}
