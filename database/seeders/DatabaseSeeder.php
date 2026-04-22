<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Candidat;
use App\Models\Tache;
use App\Models\Competence;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Créer un utilisateur candidat
        $user = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => Hash::make('password'),
            'role' => 'candidat',
            'is_active' => true
        ]);

        // Créer le profil candidat
        $candidat = Candidat::create([
            'user_id' => $user->id,
            'telephone' => '+221 77 123 45 67',
            'adresse' => 'Dakar, Sénégal',
            'date_naissance' => '1995-05-15',
            'lieu_naissance' => 'Dakar',
            'nationalite' => 'Sénégalaise',
            'niveau_etude' => 'Master 2',
            'experience' => 2,
            'competences' => ['PHP', 'Laravel', 'JavaScript', 'Vue.js'],
            'status' => 'stagiaire'
        ]);

        // Créer des compétences
        $competences = [
            ['nom' => 'Développement PHP', 'valeur' => 75],
            ['nom' => 'Laravel', 'valeur' => 60],
            ['nom' => 'JavaScript', 'valeur' => 45],
            ['nom' => 'Base de données', 'valeur' => 80],
            ['nom' => 'Git', 'valeur' => 70],
            ['nom' => 'Travail d\'équipe', 'valeur' => 90],
        ];

        foreach ($competences as $comp) {
            Competence::create([
                'candidat_id' => $candidat->id,
                'nom' => $comp['nom'],
                'valeur' => $comp['valeur']
            ]);
        }

        // Créer des tâches
        $taches = [
            ['titre' => 'Compléter le profil', 'terminee' => true],
            ['titre' => 'Soumettre le CV', 'terminee' => true],
            ['titre' => 'Préparer la présentation', 'terminee' => false],
            ['titre' => 'Remplir le journal de bord', 'terminee' => false],
            ['titre' => 'Prendre rendez-vous avec le tuteur', 'terminee' => false],
        ];

        foreach ($taches as $tache) {
            Tache::create([
                'candidat_id' => $candidat->id,
                'titre' => $tache['titre'],
                'terminee' => $tache['terminee']
            ]);
        }

        // Créer d'autres utilisateurs pour les tests
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Système',
            'email' => 'admin@gesstage.com',
            'password' => Hash::make('password'),
            'role' => 'responsable',
            'is_active' => true
        ]);
    }
}
