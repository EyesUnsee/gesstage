<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidature;
use App\Models\User;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CandidatureController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidature::with('candidat');
        
        // Filtres
        if ($request->statut && $request->statut != 'all') {
            $query->where('statut', $request->statut);
        }
        
        if ($request->type && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->entreprise) {
            $query->where('entreprise', 'like', '%' . $request->entreprise . '%');
        }
        
        $candidatures = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistiques
        $totalCandidatures = Candidature::count();
        $candidaturesEnAttente = Candidature::where('statut', 'en_attente')->count();
        $candidaturesAcceptees = Candidature::where('statut', 'acceptee')->count();
        $candidaturesRefusees = Candidature::where('statut', 'refusee')->count();
        $candidaturesEnCours = Candidature::where('statut', 'en_cours')->count();
        
        $nouvellesCandidatures = Candidature::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $nouvellesAcceptees = Candidature::where('statut', 'acceptee')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Variation des refusées (mois précédent vs mois actuel)
        $refuseesMoisActuel = Candidature::where('statut', 'refusee')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $refuseesMoisPrecedent = Candidature::where('statut', 'refusee')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();
        $variationRefusees = $refuseesMoisActuel - $refuseesMoisPrecedent;
        $variationRefusees = ($variationRefusees >= 0 ? '+' : '') . $variationRefusees;
        
        return view('responsable.candidatures', compact(
            'candidatures',
            'totalCandidatures',
            'candidaturesEnAttente',
            'candidaturesAcceptees',
            'candidaturesRefusees',
            'candidaturesEnCours',
            'nouvellesCandidatures',
            'nouvellesAcceptees',
            'variationRefusees'
        ));
    }
    
    public function show($id)
    {
        $candidature = Candidature::with('candidat')->findOrFail($id);
        return view('responsable.candidatures-show', compact('candidature'));
    }
    
    public function accepter($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->update([
            'statut' => 'acceptee',
            'date_reponse' => Carbon::now()
        ]);
        
        return redirect()->route('responsable.candidatures.index')
                         ->with('success', 'Candidature acceptée avec succès');
    }
    
    public function refuser($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidature->update([
            'statut' => 'refusee',
            'date_reponse' => Carbon::now()
        ]);
        
        return redirect()->route('responsable.candidatures.index')
                         ->with('success', 'Candidature refusée');
    }
    
    /**
     * Générer un token d'accès pour le candidat après validation
     */
    public function genererToken($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidat = $candidature->candidat;
        
        // Vérifier que la candidature est acceptée
        if ($candidature->statut !== 'acceptee') {
            return redirect()->route('responsable.candidatures.show', $id)
                ->with('error', 'La candidature doit être acceptée avant de générer un token.');
        }
        
        // Générer un token unique de 12 caractères
        $token = strtoupper(substr(md5(uniqid() . $candidat->id . time() . rand(1000, 9999)), 0, 12));
        
        // Sauvegarder le token et valider le dossier
        $candidat->update([
            'token_acces' => $token,
            'dossier_valide' => true,
            'date_validation_dossier' => Carbon::now()
        ]);
        
        // Rediriger avec le token à afficher
        return redirect()->route('responsable.candidatures.show', $id)
            ->with('token_generated', $token)
            ->with('success', '✅ Token généré avec succès !');
    }
    
    public function create()
    {
        $candidats = User::where('role', 'candidat')->orWhere('role', 'user')->get();
        return view('responsable.candidatures-create', compact('candidats'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidat_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'entreprise' => 'nullable|string|max:255',
            'type' => 'required|string|in:developpement,marketing,rh,data,design',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'lettre_motivation' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
        ]);
        
        // Gestion des fichiers
        $cvPath = null;
        $lettrePath = null;
        
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }
        
        if ($request->hasFile('lettre_motivation')) {
            $lettrePath = $request->file('lettre_motivation')->store('lettres', 'public');
        }
        
        $candidature = Candidature::create([
            'candidat_id' => $validated['candidat_id'],
            'titre' => $validated['titre'],
            'entreprise' => $validated['entreprise'],
            'type' => $validated['type'],
            'date_debut' => $validated['date_debut'] ?? null,
            'date_fin' => $validated['date_fin'] ?? null,
            'description' => $validated['description'] ?? null,
            'cv_path' => $cvPath,
            'lettre_motivation_path' => $lettrePath,
            'statut' => 'en_attente'
        ]);
        
        return redirect()->route('responsable.candidatures.index')
                         ->with('success', 'Candidature créée avec succès');
    }
    
    public function edit($id)
    {
        $candidature = Candidature::findOrFail($id);
        $candidats = User::where('role', 'candidat')->orWhere('role', 'user')->get();
        return view('responsable.candidatures-edit', compact('candidature', 'candidats'));
    }
    
    public function update(Request $request, $id)
    {
        $candidature = Candidature::findOrFail($id);
        
        $validated = $request->validate([
            'candidat_id' => 'required|exists:users,id',
            'titre' => 'required|string|max:255',
            'entreprise' => 'nullable|string|max:255',
            'type' => 'required|string',
            'statut' => 'required|string|in:en_attente,en_cours,acceptee,refusee',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'description' => 'nullable|string'
        ]);
        
        $candidature->update($validated);
        
        return redirect()->route('responsable.candidatures.index')
                         ->with('success', 'Candidature mise à jour avec succès');
    }
    
    public function destroy($id)
    {
        $candidature = Candidature::findOrFail($id);
        
        // Supprimer les fichiers associés
        if ($candidature->cv_path) {
            Storage::disk('public')->delete($candidature->cv_path);
        }
        if ($candidature->lettre_motivation_path) {
            Storage::disk('public')->delete($candidature->lettre_motivation_path);
        }
        
        $candidature->delete();
        
        return redirect()->route('responsable.candidatures.index')
                         ->with('success', 'Candidature supprimée avec succès');
    }
    
    /**
     * Synchroniser les candidatures existantes
     */
    public function syncExistingCandidatures()
    {
        $candidats = User::where('role', 'candidat')->get();
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $details = [];
        
        foreach ($candidats as $candidat) {
            $hasCV = Document::where('user_id', $candidat->id)->where('type', 'cv')->exists();
            $hasLettre = Document::where('user_id', $candidat->id)->where('type', 'lettre_motivation')->exists();
            
            if (!$hasCV || !$hasLettre) {
                $skipped++;
                $details[] = [
                    'candidat' => $candidat->first_name . ' ' . $candidat->last_name,
                    'status' => 'skip',
                    'reason' => 'Manque CV ou lettre'
                ];
                continue;
            }
            
            $existingCandidature = Candidature::where('candidat_id', $candidat->id)->first();
            $titre = 'Candidature - ' . ($candidat->formation ?? $candidat->first_name ?? 'Stage');
            
            if (!$existingCandidature) {
                Candidature::create([
                    'candidat_id' => $candidat->id,
                    'titre' => $titre,
                    'entreprise' => null,
                    'type' => 'developpement',
                    'statut' => 'en_attente',
                    'description' => "Candidature synchronisée le " . now()->format('d/m/Y à H:i'),
                    'created_at' => $candidat->created_at ?? now()
                ]);
                $created++;
                $details[] = [
                    'candidat' => $candidat->first_name . ' ' . $candidat->last_name,
                    'status' => 'created',
                    'reason' => 'Candidature créée'
                ];
            } else {
                if ($existingCandidature->statut === 'en_attente') {
                    $existingCandidature->update([
                        'titre' => $titre,
                        'updated_at' => now()
                    ]);
                    $updated++;
                    $details[] = [
                        'candidat' => $candidat->first_name . ' ' . $candidat->last_name,
                        'status' => 'updated',
                        'reason' => 'Candidature mise à jour'
                    ];
                } else {
                    $details[] = [
                        'candidat' => $candidat->first_name . ' ' . $candidat->last_name,
                        'status' => 'exists',
                        'reason' => 'Candidature déjà traitée'
                    ];
                }
            }
        }
        
        return view('responsable.candidatures-sync-result', compact('created', 'updated', 'skipped', 'details', 'candidats'));
    }
    
    /**
     * Vérifier l'état des candidatures
     */
    public function checkStatus()
    {
        $totalCandidats = User::where('role', 'candidat')->count();
        $totalCandidatures = Candidature::count();
        $candidatsAvecCV = Document::where('type', 'cv')->distinct('user_id')->count('user_id');
        $candidatsAvecLettre = Document::where('type', 'lettre_motivation')->distinct('user_id')->count('user_id');
        
        $candidatsQualifies = DB::table('documents as d1')
            ->join('documents as d2', function($join) {
                $join->on('d1.user_id', '=', 'd2.user_id')
                     ->where('d1.type', '=', 'cv')
                     ->where('d2.type', '=', 'lettre_motivation');
            })
            ->distinct('d1.user_id')
            ->count('d1.user_id');
        
        $candidatsSansCandidature = DB::table('documents as d1')
            ->join('documents as d2', function($join) {
                $join->on('d1.user_id', '=', 'd2.user_id')
                     ->where('d1.type', '=', 'cv')
                     ->where('d2.type', '=', 'lettre_motivation');
            })
            ->leftJoin('candidatures', 'd1.user_id', '=', 'candidatures.candidat_id')
            ->whereNull('candidatures.id')
            ->distinct('d1.user_id')
            ->count('d1.user_id');
        
        $candidatsManquants = DB::table('documents as d1')
            ->join('documents as d2', function($join) {
                $join->on('d1.user_id', '=', 'd2.user_id')
                     ->where('d1.type', '=', 'cv')
                     ->where('d2.type', '=', 'lettre_motivation');
            })
            ->join('users', 'd1.user_id', '=', 'users.id')
            ->leftJoin('candidatures', 'd1.user_id', '=', 'candidatures.candidat_id')
            ->whereNull('candidatures.id')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->distinct()
            ->get();
        
        return view('responsable.candidatures-check-status', compact(
            'totalCandidats', 'totalCandidatures', 'candidatsAvecCV', 'candidatsAvecLettre',
            'candidatsQualifies', 'candidatsSansCandidature', 'candidatsManquants'
        ));
    }
    
    /**
     * Créer une candidature pour un utilisateur spécifique
     */
    public function createForUser($id)
    {
        $candidat = User::findOrFail($id);
        
        $existingCandidature = Candidature::where('candidat_id', $candidat->id)->first();
        
        if ($existingCandidature) {
            return response()->json([
                'success' => false,
                'message' => 'Une candidature existe déjà pour cet utilisateur'
            ]);
        }
        
        $candidature = Candidature::create([
            'candidat_id' => $candidat->id,
            'titre' => 'Candidature - ' . ($candidat->formation ?? $candidat->first_name ?? 'Stage'),
            'entreprise' => null,
            'type' => 'developpement',
            'statut' => 'en_attente',
            'description' => "Candidature créée manuellement le " . now()->format('d/m/Y à H:i')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Candidature créée avec succès',
            'candidature' => $candidature
        ]);
    }
}
