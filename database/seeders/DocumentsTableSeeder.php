<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Candidat;

class DocumentsTableSeeder extends Seeder
{
    public function run()
    {
        $candidat = Candidat::first();
        
        if ($candidat) {
            Document::create([
                'candidat_id' => $candidat->id,
                'titre' => 'Curriculum Vitae',
                'type' => 'cv',
                'fichier_path' => 'documents/cv-test.pdf',
                'fichier_nom' => 'CV_Test.pdf',
                'fichier_taille' => '1024',
                'mime_type' => 'application/pdf',
                'description' => 'Mon CV à jour',
                'statut' => 'valide'
            ]);

            Document::create([
                'candidat_id' => $candidat->id,
                'titre' => 'Lettre de motivation',
                'type' => 'lettre_motivation',
                'fichier_path' => 'documents/lettre-test.pdf',
                'fichier_nom' => 'Lettre_Motivation.pdf',
                'fichier_taille' => '512',
                'mime_type' => 'application/pdf',
                'description' => 'Lettre pour le stage',
                'statut' => 'en_attente'
            ]);
        }
    }
}
