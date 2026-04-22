<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Candidat\DashboardController as CandidatDashboardController;
use App\Http\Controllers\Candidat\NewDashboardController as CandidatNewDashboardController;
use App\Http\Controllers\Candidat\ProfilController as CandidatProfilController;
use App\Http\Controllers\Candidat\CandidatureController as CandidatCandidatureController;
use App\Http\Controllers\Candidat\DocumentController as CandidatDocumentController;
use App\Http\Controllers\Candidat\JournalController as CandidatJournalController;
use App\Http\Controllers\Candidat\EvaluationController as CandidatEvaluationController;
use App\Http\Controllers\Candidat\PointageController as CandidatPointageController;
use App\Http\Controllers\Tuteur\DashboardController as TuteurDashboardController;
use App\Http\Controllers\Tuteur\ProfilController as TuteurProfilController;
use App\Http\Controllers\Tuteur\StagiaireController as TuteurStagiaireController;
use App\Http\Controllers\Tuteur\EvaluationController as TuteurEvaluationController;
use App\Http\Controllers\Tuteur\JournalController as TuteurJournalController;
use App\Http\Controllers\Responsable\DashboardController as ResponsableDashboardController;
use App\Http\Controllers\Responsable\ProfilController as ResponsableProfilController;
use App\Http\Controllers\Responsable\CandidatureController as ResponsableCandidatureController;
use App\Http\Controllers\Responsable\StagiaireController as ResponsableStagiaireController;
use App\Http\Controllers\Responsable\TuteurController as ResponsableTuteurController;
use App\Http\Controllers\Responsable\ResponsableController as ResponsableResponsableController;
use App\Http\Controllers\Responsable\StatistiqueController as ResponsableStatistiqueController;
use App\Http\Controllers\Responsable\PointageController as ResponsablePointageController;
use App\Http\Controllers\Responsable\ActiviteController as ResponsableActiviteController;
use App\Http\Controllers\Responsable\ValidationController as ResponsableValidationController;

// Chef de service Controllers
use App\Http\Controllers\ChefService\DashboardController as ChefServiceDashboardController;
use App\Http\Controllers\ChefService\ProfilController as ChefServiceProfilController;
use App\Http\Controllers\ChefService\EquipeController as ChefServiceEquipeController;
use App\Http\Controllers\ChefService\RapportController as ChefServiceRapportController;
use App\Http\Controllers\ChefService\PointageController as ChefServicePointageController;
use App\Http\Controllers\ChefService\StatistiqueController as ChefServiceStatistiqueController;
use App\Http\Controllers\ChefService\DocumentController as ChefServiceDocumentController;
use App\Http\Controllers\ChefService\IndicateurController as ChefServiceIndicateurController;
use App\Http\Controllers\ChefService\ServiceController as ChefServiceServiceController;
use App\Http\Controllers\ChefService\BilanController as ChefServiceBilanController;
use App\Http\Controllers\ChefService\ValidationController as ChefServiceValidationController;
use App\Http\Controllers\ChefService\MessageController as ChefServiceMessageController;
use App\Http\Controllers\ChefService\NotificationController as ChefServiceNotificationController;
use App\Http\Controllers\ChefService\RechercheController as ChefServiceRechercheController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Routes d'authentification
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

// Routes protégées
Route::middleware(['auth'])->group(function () {
    
    // ==================== ROUTES CANDIDAT ====================
    Route::prefix('candidat')->name('candidat.')->middleware(['auth', 'candidat'])->group(function () {
        
        // Routes pour le nouveau candidat (dépôt de dossier) - accessible sans dossier validé
        Route::get('/new-dashboard', [CandidatNewDashboardController::class, 'index'])->name('new.dashboard');
        Route::post('/new-dashboard/documents', [CandidatNewDashboardController::class, 'storeDocuments'])->name('new.documents');
        Route::post('/new-dashboard/profile', [CandidatNewDashboardController::class, 'updateProfile'])->name('new.profile');
        
        // Route pour vérifier le token d'accès
        Route::post('/verifier-token', [CandidatNewDashboardController::class, 'verifierToken'])->name('verifier-token');
        
        // Routes pour le dashboard classique (nécessite dossier validé)
        Route::middleware(['dossier.valide'])->group(function () {
            Route::get('/dashboard', [CandidatDashboardController::class, 'index'])->name('dashboard');
            
            // Tâches - Routes pour AJAX
            Route::post('/taches', [CandidatDashboardController::class, 'storeTask'])->name('taches.store');
            Route::patch('/taches/{id}/toggle', [CandidatDashboardController::class, 'toggleTask'])->name('taches.toggle');
            Route::get('/taches/{id}', [CandidatDashboardController::class, 'getTask'])->name('taches.show');
            Route::put('/taches/{id}', [CandidatDashboardController::class, 'updateTask'])->name('taches.update');
            Route::delete('/taches/{id}', [CandidatDashboardController::class, 'deleteTask'])->name('taches.delete');
            
            // Profil
            Route::get('/profil', [CandidatProfilController::class, 'index'])->name('profil');
            Route::put('/profil', [CandidatProfilController::class, 'update'])->name('profil.update');
            Route::post('/profil/avatar', [CandidatProfilController::class, 'updateAvatar'])->name('profil.avatar');
            Route::put('/profil/password', [CandidatProfilController::class, 'updatePassword'])->name('profil.password');
            
            // Pointage
            Route::get('/pointage', [CandidatPointageController::class, 'index'])->name('pointage');
            Route::post('/pointage/arrival', [CandidatPointageController::class, 'arrival'])->name('pointage.arrival');
            Route::post('/pointage/departure', [CandidatPointageController::class, 'departure'])->name('pointage.departure');
            Route::post('/pointage/absence', [CandidatPointageController::class, 'justifiedAbsence'])->name('pointage.absence');
            
            // Candidatures
            Route::get('/candidatures', [CandidatCandidatureController::class, 'index'])->name('candidatures.index');
            Route::get('/candidatures/create', [CandidatCandidatureController::class, 'create'])->name('candidatures.create');
            Route::post('/candidatures', [CandidatCandidatureController::class, 'store'])->name('candidatures.store');
            Route::get('/candidatures/{id}', [CandidatCandidatureController::class, 'show'])->name('candidatures.show');
            Route::get('/candidatures/{id}/edit', [CandidatCandidatureController::class, 'edit'])->name('candidatures.edit');
            Route::put('/candidatures/{id}', [CandidatCandidatureController::class, 'update'])->name('candidatures.update');
            Route::delete('/candidatures/{id}', [CandidatCandidatureController::class, 'destroy'])->name('candidatures.destroy');
            
            // Documents
            Route::get('/documents', [CandidatDocumentController::class, 'index'])->name('documents.index');
            Route::get('/documents/upload', [CandidatDocumentController::class, 'create'])->name('documents.upload');
            Route::post('/documents', [CandidatDocumentController::class, 'store'])->name('documents.store');
            Route::get('/documents/{id}', [CandidatDocumentController::class, 'show'])->name('documents.show');
            Route::get('/documents/{id}/download', [CandidatDocumentController::class, 'download'])->name('documents.download');
            Route::delete('/documents/{id}', [CandidatDocumentController::class, 'destroy'])->name('documents.destroy');
            
            // Journal de bord
            Route::get('/journal', [CandidatJournalController::class, 'index'])->name('journal');
            Route::post('/journal', [CandidatJournalController::class, 'store'])->name('journal.store');
            Route::put('/journal/{id}', [CandidatJournalController::class, 'updateTask'])->name('journal.update');
            Route::patch('/journal/{id}/toggle', [CandidatJournalController::class, 'toggle'])->name('journal.toggle');
            Route::get('/journal/{id}', [CandidatJournalController::class, 'getTask'])->name('journal.show');
            Route::delete('/journal/{id}', [CandidatJournalController::class, 'destroy'])->name('journal.destroy');
            Route::post('/journal/valider', [CandidatJournalController::class, 'validerSemaine'])->name('journal.valider');
            
            // Évaluations
            Route::get('/evaluations', [CandidatEvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{id}', [CandidatEvaluationController::class, 'show'])->name('evaluations.show');
        });
    });
    
    // ==================== ROUTES TUTEUR ====================
    Route::prefix('tuteur')->name('tuteur.')->middleware(['auth', 'tuteur'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [TuteurDashboardController::class, 'index'])->name('dashboard');
        
        // Profil
        Route::get('/profil', [TuteurProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [TuteurProfilController::class, 'update'])->name('profil.update');
        Route::post('/profil/avatar', [TuteurProfilController::class, 'updateAvatar'])->name('profil.avatar');
        Route::put('/profil/password', [TuteurProfilController::class, 'updatePassword'])->name('profil.password');
        
        // Stagiaires
        Route::get('/stagiaires', [TuteurStagiaireController::class, 'index'])->name('stagiaires');
        Route::get('/stagiaire/{id}', [TuteurStagiaireController::class, 'show'])->name('stagiaire.show');
        
        // Évaluations
        Route::get('/evaluations', [TuteurEvaluationController::class, 'index'])->name('evaluations');
        Route::get('/evaluations/create/{stagiaire_id}', [TuteurEvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('/evaluations', [TuteurEvaluationController::class, 'store'])->name('evaluations.store');
        Route::get('/evaluations/{id}', [TuteurEvaluationController::class, 'show'])->name('evaluations.show');
        Route::get('/evaluations/{id}/edit', [TuteurEvaluationController::class, 'edit'])->name('evaluations.edit');
        Route::put('/evaluations/{id}', [TuteurEvaluationController::class, 'update'])->name('evaluations.update');
        Route::delete('/evaluations/{id}', [TuteurEvaluationController::class, 'destroy'])->name('evaluations.destroy');
        
        // Journaux de bord
        Route::get('/journaux', [TuteurJournalController::class, 'index'])->name('journaux');
        Route::get('/journaux/{id}', [TuteurJournalController::class, 'show'])->name('journaux.show');
        Route::post('/journaux/{id}/valider', [TuteurJournalController::class, 'valider'])->name('journaux.valider');
        Route::post('/journaux/{id}/rejeter', [TuteurJournalController::class, 'rejeter'])->name('journaux.rejeter');
    });
    
    // ==================== ROUTES RESPONSABLE ====================
    Route::prefix('responsable')->name('responsable.')->middleware(['responsable'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [ResponsableDashboardController::class, 'index'])->name('dashboard');
        
        // Activités
        Route::get('/activites', [ResponsableActiviteController::class, 'index'])->name('activites.index');
        
        // Validations
        Route::get('/validations', [ResponsableValidationController::class, 'index'])->name('validations.index');
        Route::post('/validations/{id}/approuver', [ResponsableValidationController::class, 'approuver'])->name('validations.approuver');
        Route::post('/validations/{id}/refuser', [ResponsableValidationController::class, 'refuser'])->name('validations.refuser');
        
        // Profil
        Route::get('/profil', [ResponsableProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [ResponsableProfilController::class, 'update'])->name('profil.update');
        Route::post('/profil/avatar', [ResponsableProfilController::class, 'updateAvatar'])->name('profil.avatar');
        Route::put('/profil/password', [ResponsableProfilController::class, 'updatePassword'])->name('profil.password');
        
        // ========== ROUTES CANDIDATURES ==========
        Route::get('/candidatures', [ResponsableCandidatureController::class, 'index'])->name('candidatures.index');
        Route::get('/candidatures/create', [ResponsableCandidatureController::class, 'create'])->name('candidatures.create');
        Route::post('/candidatures', [ResponsableCandidatureController::class, 'store'])->name('candidatures.store');
        Route::get('/candidatures/{id}', [ResponsableCandidatureController::class, 'show'])->name('candidatures.show');
        Route::get('/candidatures/{id}/edit', [ResponsableCandidatureController::class, 'edit'])->name('candidatures.edit');
        Route::put('/candidatures/{id}', [ResponsableCandidatureController::class, 'update'])->name('candidatures.update');
        Route::delete('/candidatures/{id}', [ResponsableCandidatureController::class, 'destroy'])->name('candidatures.destroy');
        
        // Route pour générer le token d'accès
        Route::post('/candidatures/{id}/generer-token', [ResponsableCandidatureController::class, 'genererToken'])->name('candidatures.generer-token');
        
        // Routes pour accepter/refuser
        Route::post('/candidatures/{id}/accepter', [ResponsableCandidatureController::class, 'accepter'])->name('candidatures.accepter');
        Route::post('/candidatures/{id}/refuser', [ResponsableCandidatureController::class, 'refuser'])->name('candidatures.refuser');
        
        // Routes de synchronisation
        Route::get('/candidatures/sync', [ResponsableCandidatureController::class, 'syncExistingCandidatures'])->name('candidatures.sync');
        Route::get('/candidatures/check-status', [ResponsableCandidatureController::class, 'checkStatus'])->name('candidatures.check-status');
        Route::post('/candidatures/create-for-user/{id}', [ResponsableCandidatureController::class, 'createForUser'])->name('candidatures.create-for-user');
        
        // Stagiaires
        Route::get('/stagiaires', [ResponsableStagiaireController::class, 'index'])->name('stagiaires');
        Route::get('/stagiaires/create', [ResponsableStagiaireController::class, 'create'])->name('stagiaires.create');
        Route::post('/stagiaires', [ResponsableStagiaireController::class, 'store'])->name('stagiaires.store');
        Route::get('/stagiaire/{id}', [ResponsableStagiaireController::class, 'show'])->name('stagiaire.show');
        Route::get('/stagiaire/{id}/edit', [ResponsableStagiaireController::class, 'edit'])->name('stagiaire.edit');
        Route::put('/stagiaire/{id}', [ResponsableStagiaireController::class, 'update'])->name('stagiaire.update');
        Route::delete('/stagiaire/{id}', [ResponsableStagiaireController::class, 'destroy'])->name('stagiaire.destroy');
        
        // Tuteurs
        Route::get('/tuteurs', [ResponsableTuteurController::class, 'index'])->name('tuteurs');
        Route::get('/tuteurs/create', [ResponsableTuteurController::class, 'create'])->name('tuteurs.create');
        Route::post('/tuteurs', [ResponsableTuteurController::class, 'store'])->name('tuteurs.store');
        Route::get('/tuteurs/{id}', [ResponsableTuteurController::class, 'show'])->name('tuteurs.show');
        Route::get('/tuteurs/{id}/edit', [ResponsableTuteurController::class, 'edit'])->name('tuteurs.edit');
        Route::put('/tuteurs/{id}', [ResponsableTuteurController::class, 'update'])->name('tuteurs.update');
        Route::delete('/tuteurs/{id}', [ResponsableTuteurController::class, 'destroy'])->name('tuteurs.destroy');
        Route::post('/tuteurs/assign', [ResponsableTuteurController::class, 'assignStagiaire'])->name('tuteurs.assign');
        Route::post('/tuteurs/{tuteurId}/desassign/{stagiaireId}', [ResponsableTuteurController::class, 'desassignStagiaire'])->name('tuteurs.desassign');
        Route::get('/tuteurs/{id}/statistiques', [ResponsableTuteurController::class, 'statistiques'])->name('tuteurs.statistiques');
        
        // Responsables
        Route::get('/responsables', [ResponsableResponsableController::class, 'index'])->name('responsables.index');
        Route::get('/responsables/create', [ResponsableResponsableController::class, 'create'])->name('responsables.create');
        Route::post('/responsables', [ResponsableResponsableController::class, 'store'])->name('responsables.store');
        Route::get('/responsables/{id}', [ResponsableResponsableController::class, 'show'])->name('responsables.show');
        Route::get('/responsables/{id}/edit', [ResponsableResponsableController::class, 'edit'])->name('responsables.edit');
        Route::put('/responsables/{id}', [ResponsableResponsableController::class, 'update'])->name('responsables.update');
        Route::delete('/responsables/{id}', [ResponsableResponsableController::class, 'destroy'])->name('responsables.destroy');
        
        // Statistiques
        Route::get('/statistiques', [ResponsableStatistiqueController::class, 'index'])->name('statistiques');
        Route::get('/statistiques/export/pdf', [ResponsableStatistiqueController::class, 'exportPDF'])->name('statistiques.export.pdf');
        Route::get('/statistiques/export/excel', [ResponsableStatistiqueController::class, 'exportExcel'])->name('statistiques.export.excel');
        
        // Pointages
        Route::post('/pointages/{id}', [ResponsablePointageController::class, 'update'])->name('pointages.update');
    });
    
    // ==================== ROUTES CHEF DE SERVICE ====================
    Route::prefix('chef-service')->name('chef-service.')->middleware(['chef.service'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [ChefServiceDashboardController::class, 'index'])->name('dashboard');
        Route::get('/activites', [ChefServiceDashboardController::class, 'activites'])->name('activites');
        Route::get('/recherche', [ChefServiceRechercheController::class, 'index'])->name('recherche');
        Route::post('/recherche', [ChefServiceRechercheController::class, 'search'])->name('recherche.search');
        
        // Profil
        Route::get('/profil', [ChefServiceProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [ChefServiceProfilController::class, 'update'])->name('profil.update');
        Route::post('/profil/avatar', [ChefServiceProfilController::class, 'updateAvatar'])->name('profil.avatar');
        Route::put('/profil/password', [ChefServiceProfilController::class, 'updatePassword'])->name('profil.password');
        Route::delete('/profil/avatar', [ChefServiceProfilController::class, 'deleteAvatar'])->name('profil.avatar.delete');
        Route::post('/profil/competences', [ChefServiceProfilController::class, 'updateCompetences'])->name('profil.competences');
        Route::post('/profil/professional', [ChefServiceProfilController::class, 'updateProfessional'])->name('profil.professional');
        Route::get('/profil/export', [ChefServiceProfilController::class, 'exportData'])->name('profil.export');
        Route::get('/profil/notifications', [ChefServiceProfilController::class, 'getNotifications'])->name('profil.notifications');
        
        // Équipe
        Route::get('/equipe', [ChefServiceEquipeController::class, 'index'])->name('equipe');
        Route::post('/equipe', [ChefServiceEquipeController::class, 'store'])->name('equipe.store');
        Route::get('/equipe/{id}', [ChefServiceEquipeController::class, 'show'])->name('equipe.show');
        Route::get('/equipe/{id}/edit', [ChefServiceEquipeController::class, 'edit'])->name('equipe.edit');
        Route::put('/equipe/{id}', [ChefServiceEquipeController::class, 'update'])->name('equipe.update');
        Route::delete('/equipe/{id}', [ChefServiceEquipeController::class, 'destroy'])->name('equipe.destroy');
        Route::get('/equipe/{id}/statistiques', [ChefServiceEquipeController::class, 'statistiques'])->name('equipe.statistiques');
        Route::post('/equipe/{id}/role', [ChefServiceEquipeController::class, 'updateRole'])->name('equipe.role');
        
        // Indicateurs
        Route::get('/indicateurs', [ChefServiceIndicateurController::class, 'index'])->name('indicateurs');
        Route::get('/indicateurs/export', [ChefServiceIndicateurController::class, 'export'])->name('indicateurs.export');
        Route::get('/indicateurs/performance', [ChefServiceIndicateurController::class, 'performance'])->name('indicateurs.performance');
        
        // Services
        Route::get('/services', [ChefServiceServiceController::class, 'index'])->name('services');
        Route::post('/services', [ChefServiceServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{id}', [ChefServiceServiceController::class, 'show'])->name('services.show');
        Route::get('/services/{id}/edit', [ChefServiceServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{id}', [ChefServiceServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{id}', [ChefServiceServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/services/{id}/statistiques', [ChefServiceServiceController::class, 'statistiques'])->name('services.statistiques');
        
        // Sanctions
        Route::post('/sanctions', [ChefServiceServiceController::class, 'storeSanction'])->name('sanctions.store');
        Route::delete('/sanctions/{id}', [ChefServiceServiceController::class, 'deleteSanction'])->name('sanctions.delete');
        Route::post('/bannis/{id}/appeal', [ChefServiceServiceController::class, 'appealBanni'])->name('bannis.appeal');
        Route::get('/bannis/{id}', [ChefServiceServiceController::class, 'viewBanni'])->name('bannis.show');
        
        // Stages
        Route::post('/stages/{id}/validate-bilan', [ChefServiceServiceController::class, 'validateBilan'])->name('stages.validate-bilan');
        Route::post('/stages/{id}/archive', [ChefServiceServiceController::class, 'archiveStage'])->name('stages.archive');
        Route::get('/stages/{id}', [ChefServiceServiceController::class, 'viewStage'])->name('stages.show');
        
        // Bilans
        Route::get('/bilans', [ChefServiceBilanController::class, 'index'])->name('bilans');
        Route::get('/bilans/{id}', [ChefServiceBilanController::class, 'show'])->name('bilans.show');
        Route::post('/bilans', [ChefServiceBilanController::class, 'store'])->name('bilans.store');
        Route::get('/bilans/{id}/edit', [ChefServiceBilanController::class, 'edit'])->name('bilans.edit');
        Route::put('/bilans/{id}', [ChefServiceBilanController::class, 'update'])->name('bilans.update');
        Route::delete('/bilans/{id}', [ChefServiceBilanController::class, 'destroy'])->name('bilans.destroy');
        Route::post('/bilans/{id}/valider', [ChefServiceBilanController::class, 'valider'])->name('bilans.valider');
        Route::post('/bilans/{id}/rejeter', [ChefServiceBilanController::class, 'rejeter'])->name('bilans.rejeter');
        Route::get('/bilans/export/excel', [ChefServiceBilanController::class, 'exportExcel'])->name('bilans.export.excel');
        
        // Validations
        Route::get('/validations', [ChefServiceValidationController::class, 'index'])->name('validations');
        Route::get('/validations/{id}', [ChefServiceValidationController::class, 'show'])->name('validations.show');
        Route::post('/validations/{id}/approuver', [ChefServiceValidationController::class, 'approuver'])->name('validations.approuver');
        Route::post('/validations/{id}/refuser', [ChefServiceValidationController::class, 'refuser'])->name('validations.refuser');
        Route::post('/validations/{id}/commenter', [ChefServiceValidationController::class, 'commenter'])->name('validations.commenter');
        Route::get('/validations/export/pdf', [ChefServiceValidationController::class, 'exportPDF'])->name('validations.export.pdf');
        
        // Rapports
        Route::get('/rapports', [ChefServiceRapportController::class, 'index'])->name('rapports');
        Route::get('/rapports/generate', [ChefServiceRapportController::class, 'generate'])->name('rapports.generate');
        Route::post('/rapports/generate', [ChefServiceRapportController::class, 'store'])->name('rapports.store');
        Route::get('/rapports/download/{id}', [ChefServiceRapportController::class, 'download'])->name('rapports.download');
        Route::delete('/rapports/{id}', [ChefServiceRapportController::class, 'destroy'])->name('rapports.destroy');
        Route::get('/rapports/schedule', [ChefServiceRapportController::class, 'schedule'])->name('rapports.schedule');
        Route::post('/rapports/schedule', [ChefServiceRapportController::class, 'storeSchedule'])->name('rapports.storeSchedule');
        
        // Pointages
        Route::get('/pointages', [ChefServicePointageController::class, 'index'])->name('pointages');
        Route::get('/pointages/export', [ChefServicePointageController::class, 'export'])->name('pointages.export');
        Route::post('/pointages/{id}', [ChefServicePointageController::class, 'update'])->name('pointages.update');
        Route::get('/pointages/statistiques', [ChefServicePointageController::class, 'statistiques'])->name('pointages.statistiques');
        Route::get('/pointages/absences', [ChefServicePointageController::class, 'absences'])->name('pointages.absences');
        Route::post('/pointages/absences/{id}/justifier', [ChefServicePointageController::class, 'justifierAbsence'])->name('pointages.absences.justifier');
        
        // Statistiques
        Route::get('/statistiques', [ChefServiceStatistiqueController::class, 'index'])->name('statistiques');
        Route::get('/statistiques/export/pdf', [ChefServiceStatistiqueController::class, 'exportPDF'])->name('statistiques.export.pdf');
        Route::get('/statistiques/export/excel', [ChefServiceStatistiqueController::class, 'exportExcel'])->name('statistiques.export.excel');
        Route::get('/statistiques/globales', [ChefServiceStatistiqueController::class, 'globales'])->name('statistiques.globales');
        Route::get('/statistiques/par-service', [ChefServiceStatistiqueController::class, 'parService'])->name('statistiques.par-service');
        
        // Documents
        Route::get('/documents', [ChefServiceDocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{id}', [ChefServiceDocumentController::class, 'show'])->name('documents.show');
        Route::get('/documents/{id}/download', [ChefServiceDocumentController::class, 'download'])->name('documents.download');
        Route::patch('/documents/{id}/statut', [ChefServiceDocumentController::class, 'updateStatut'])->name('documents.statut');
        Route::post('/documents/{id}/valider', [ChefServiceDocumentController::class, 'valider'])->name('documents.valider');
        Route::post('/documents/{id}/rejeter', [ChefServiceDocumentController::class, 'rejeter'])->name('documents.rejeter');
        Route::get('/documents/en-attente', [ChefServiceDocumentController::class, 'enAttente'])->name('documents.en-attente');
        
        // Messages
        Route::get('/messages', [ChefServiceMessageController::class, 'index'])->name('messages');
        Route::get('/messages/{id}', [ChefServiceMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages', [ChefServiceMessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/{id}/reply', [ChefServiceMessageController::class, 'reply'])->name('messages.reply');
        Route::put('/messages/{id}/read', [ChefServiceMessageController::class, 'markAsRead'])->name('messages.read');
        Route::delete('/messages/{id}', [ChefServiceMessageController::class, 'destroy'])->name('messages.destroy');
        
        // Notifications
        Route::get('/notifications', [ChefServiceNotificationController::class, 'index'])->name('notifications');
        Route::put('/notifications/{id}/read', [ChefServiceNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::put('/notifications/read-all', [ChefServiceNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notifications/{id}', [ChefServiceNotificationController::class, 'destroy'])->name('notifications.destroy');
        
        // Export
        Route::get('/export/{type}', [ChefServiceServiceController::class, 'exportData'])->name('export');
    });
});
