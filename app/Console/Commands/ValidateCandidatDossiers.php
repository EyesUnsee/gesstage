<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Document;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ValidateCandidatDossiers extends Command
{
    protected $signature = 'candidats:validate-dossiers';
    protected $description = 'Valide automatiquement les dossiers des candidats complets';

    public function handle()
    {
        $candidats = User::where('role', 'candidat')
            ->where('dossier_valide', false)
            ->get();

        foreach ($candidats as $candidat) {
            $hasCV = Document::where('user_id', $candidat->id)->where('type', 'cv')->exists();
            $hasLM = Document::where('user_id', $candidat->id)->where('type', 'lettre_motivation')->exists();
            $hasProfile = $candidat->phone && $candidat->address;
            
            if ($hasCV && $hasLM && $hasProfile) {
                $candidat->update([
                    'dossier_valide' => true,
                    'date_validation_dossier' => Carbon::now(),
                    'status' => 'actif'
                ]);
                $this->info("Dossier validé pour: {$candidat->email}");
            }
        }
        
        $this->info('Vérification terminée.');
    }
}
