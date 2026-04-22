<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Document;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NewDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user->role !== 'candidat' || $user->dossier_valide) {
                return redirect()->route('candidat.dashboard');
            }
            return $next($request);
        });
    }
    
    public function index()
    {
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->get();
        $candidature = Candidature::where('candidat_id', $user->id)->first();
        
        return view('candidat.new-dashboard', compact('user', 'documents', 'candidature'));
    }
    
    /**
     * Vérifier le token d'accès
     */
    public function verifierToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:12'
        ]);
        
        $user = Auth::user();
        $token = strtoupper($request->token);
        
        // Vérifier si le token correspond
        if ($user->token_acces === $token) {
            // Token valide - on ne valide pas encore le dossier
            // Le responsable doit encore valider le dossier
            return redirect()->route('candidat.new.dashboard')
                ->with('success', '✅ Token validé ! Votre dossier est en attente de validation par l\'administrateur.');
        } else {
            return redirect()->route('candidat.new.dashboard')
                ->with('token_invalid', '❌ Token invalide. Veuillez vérifier le code fourni par votre responsable.');
        }
    }
    
    /**
     * Crée ou met à jour la candidature
     */
    private function createOrUpdateCandidature($user)
    {
        // Vérifier si le candidat a un CV et une lettre de motivation
        $hasCV = Document::where('user_id', $user->id)
            ->where('type', 'cv')
            ->exists();
        
        $hasLettre = Document::where('user_id', $user->id)
            ->where('type', 'lettre_motivation')
            ->exists();
        
        // Vérifier si le profil est suffisamment rempli
        $hasProfile = !empty($user->phone) && !empty($user->address);
        
        // Ne créer une candidature que si les documents essentiels sont présents
        if ($hasCV && $hasLettre) {
            // Chercher une candidature existante
            $candidature = Candidature::where('candidat_id', $user->id)->first();
            
            // Déterminer le titre de la candidature
            $titre = 'Candidature - ' . ($user->formation ?? $user->first_name ?? 'Stage');
            
            if (!$candidature) {
                // Créer une nouvelle candidature
                Candidature::create([
                    'candidat_id' => $user->id,
                    'titre' => $titre,
                    'entreprise' => null,
                    'type' => 'developpement',
                    'statut' => 'en_attente',
                    'date_debut' => null,
                    'date_fin' => null,
                    'description' => "Candidature générée automatiquement le " . now()->format('d/m/Y à H:i'),
                    'created_at' => now()
                ]);
                
                Log::info('Candidature créée pour le candidat ID: ' . $user->id);
            } else {
                // Mettre à jour la candidature existante si nécessaire
                if ($candidature->statut === 'en_attente') {
                    $candidature->update([
                        'titre' => $titre,
                        'updated_at' => now()
                    ]);
                    Log::info('Candidature mise à jour pour le candidat ID: ' . $user->id);
                }
            }
        } else {
            Log::info('Candidature non créée - Documents manquants pour le candidat ID: ' . $user->id .
                       ' (CV: ' . ($hasCV ? 'oui' : 'non') .
                       ', Lettre: ' . ($hasLettre ? 'oui' : 'non') . ')');
        }
    }
    
    public function storeDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'lettre_motivation' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'diplome' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        
        // Sauvegarder le CV
        if ($request->hasFile('cv')) {
            // Supprimer l'ancien CV s'il existe
            $oldCv = Document::where('user_id', $user->id)->where('type', 'cv')->first();
            if ($oldCv) {
                Storage::disk('public')->delete($oldCv->fichier_path);
                $oldCv->delete();
            }
            
            $cvPath = $request->file('cv')->store('documents/cv', 'public');
            Document::create([
                'user_id' => $user->id,
                'titre' => 'CV - ' . ($user->first_name ?? $user->name),
                'type' => 'cv',
                'fichier_path' => $cvPath,
                'fichier_nom' => $request->file('cv')->getClientOriginalName(),
                'taille' => $request->file('cv')->getSize() / 1024,
                'statut' => 'en_attente'
            ]);
        }
        
        // Sauvegarder la lettre de motivation
        if ($request->hasFile('lettre_motivation')) {
            // Supprimer l'ancienne lettre si elle existe
            $oldLm = Document::where('user_id', $user->id)->where('type', 'lettre_motivation')->first();
            if ($oldLm) {
                Storage::disk('public')->delete($oldLm->fichier_path);
                $oldLm->delete();
            }
            
            $lmPath = $request->file('lettre_motivation')->store('documents/lettres', 'public');
            Document::create([
                'user_id' => $user->id,
                'titre' => 'Lettre de motivation - ' . ($user->first_name ?? $user->name),
                'type' => 'lettre_motivation',
                'fichier_path' => $lmPath,
                'fichier_nom' => $request->file('lettre_motivation')->getClientOriginalName(),
                'taille' => $request->file('lettre_motivation')->getSize() / 1024,
                'statut' => 'en_attente'
            ]);
        }
        
        // Sauvegarder le diplôme
        if ($request->hasFile('diplome')) {
            $diplomePath = $request->file('diplome')->store('documents/diplomes', 'public');
            Document::create([
                'user_id' => $user->id,
                'titre' => 'Diplôme - ' . ($user->first_name ?? $user->name),
                'type' => 'diplome',
                'fichier_path' => $diplomePath,
                'fichier_nom' => $request->file('diplome')->getClientOriginalName(),
                'taille' => $request->file('diplome')->getSize() / 1024,
                'statut' => 'en_attente'
            ]);
        }
        
        // CRÉER OU METTRE À JOUR LA CANDIDATURE
        $this->createOrUpdateCandidature($user);
        
        return redirect()->route('candidat.new.dashboard')
            ->with('success', 'Documents envoyés avec succès. Votre candidature a été créée et est en attente de validation.');
    }
    
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'formation' => 'nullable|string|max:255',
            'universite' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $user->update($request->only(['phone', 'address', 'birth_date', 'formation', 'universite']));
        
        // CRÉER OU METTRE À JOUR LA CANDIDATURE
        $this->createOrUpdateCandidature($user);
        
        return redirect()->route('candidat.new.dashboard')
            ->with('success', 'Profil mis à jour avec succès');
    }
}
