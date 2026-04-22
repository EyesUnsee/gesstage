<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;

class DocumentController extends Controller
{
    /**
     * Afficher la liste des documents
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'candidat') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
        }

        $documents = Document::where('user_id', $user->id)
                             ->orderBy('created_at', 'desc')
                             ->get();

        $stats = [
            'total' => $documents->count(),
            'conventions' => $documents->where('type', 'convention')->count(),
            'rapports' => $documents->where('type', 'rapport')->count(),
            'attestations' => $documents->where('type', 'attestation')->count(),
            'autres' => $documents->where('type', 'autre')->count(),
            'taille_totale' => $documents->sum('taille'),
        ];

        $types = [
            'convention' => 'Convention de stage',
            'rapport' => 'Rapport de stage',
            'attestation' => 'Attestation de stage',
            'autre' => 'Autre document',
        ];

        return view('candidat.documents', compact('documents', 'stats', 'types'));
    }

    /**
     * Afficher le formulaire de création/upload
     */
    public function create()
    {
        $types = [
            'convention' => 'Convention de stage',
            'rapport' => 'Rapport de stage',
            'attestation' => 'Attestation de stage',
            'autre' => 'Autre document',
        ];

        return view('candidat.documents-upload', compact('types'));
    }

    /**
     * Enregistrer un nouveau document
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|in:convention,rapport,attestation,autre',
            'fichier' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('documents/' . $user->id, $filename, 'public');

            Document::create([
                'user_id' => $user->id,
                'titre' => $request->titre,
                'type' => $request->type,
                'fichier_path' => $path,
                'fichier_nom' => $file->getClientOriginalName(),
                'taille' => round($file->getSize() / 1024, 2),
                'description' => $request->description,
                'statut' => 'en_attente',
            ]);
        }

        return redirect()->route('candidat.documents.index')
                         ->with('success', 'Document téléchargé avec succès');
    }

    /**
     * Afficher les détails d'un document
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $document = Document::where('id', $id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        $types = [
            'convention' => 'Convention de stage',
            'rapport' => 'Rapport de stage',
            'attestation' => 'Attestation de stage',
            'autre' => 'Autre document',
        ];

        return view('candidat.documents-show', compact('document', 'types'));
    }

    /**
     * Télécharger un document
     */
    public function download($id)
    {
        $user = Auth::user();
        
        $document = Document::where('id', $id)
                            ->where('user_id', $user->id)
                            ->first();

        if (!$document) {
            return redirect()->route('candidat.documents.index')
                             ->with('error', 'Document non trouvé');
        }

        if (Storage::disk('public')->exists($document->fichier_path)) {
            return Storage::disk('public')->download($document->fichier_path, $document->fichier_nom ?? $document->titre);
        }

        return redirect()->route('candidat.documents.index')
                         ->with('error', 'Fichier non trouvé');
    }

    /**
     * Supprimer un document (utilise POST au lieu de DELETE)
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Chercher le document
            $document = Document::where('id', $id)
                                ->where('user_id', $user->id)
                                ->first();
            
            // Vérifier si le document existe
            if (!$document) {
                return redirect()->route('candidat.documents.index')
                                 ->with('error', 'Document non trouvé');
            }

            // Supprimer le fichier physique s'il existe
            if ($document->fichier_path && Storage::disk('public')->exists($document->fichier_path)) {
                Storage::disk('public')->delete($document->fichier_path);
            }

            // Supprimer le document de la base de données
            $document->delete();

            return redirect()->route('candidat.documents.index')
                             ->with('success', 'Document supprimé avec succès');
                             
        } catch (\Exception $e) {
            \Log::error('Erreur suppression document: ' . $e->getMessage());
            
            return redirect()->route('candidat.documents.index')
                             ->with('error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
