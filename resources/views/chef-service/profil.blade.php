@extends('layouts.chef-service')

@section('title', 'Mon Profil - Chef de service')
@section('page-title', 'Mon profil')
@section('active-profil', 'active')

@section('content')
@php
    $user = auth()->user();
    $statistiques = [
        'stagiaires_encadres' => $stagiairesEncadres ?? 0,
        'tuteurs' => $tuteursCount ?? 0,
        'projets_en_cours' => $projetsEnCours ?? 0,
        'satisfaction' => $satisfaction ?? 95
    ];
@endphp

<!-- En-tête du profil -->
<div class="profile-header-card">
    <div class="profile-avatar-large">
        @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 30px; object-fit: cover;">
        @else
            <i class="fas fa-user-tie"></i>
        @endif
    </div>
    <div class="profile-header-info">
        <h1>{{ $user->first_name ?? 'Chef' }} <span>{{ $user->last_name ?? '' }}</span></h1>
        <div class="profile-header-title">
            <i class="fas fa-crown"></i> {{ $user->poste ?? 'Chef de service' }} - {{ $serviceName ?? 'Service' }}
        </div>
        <div class="profile-header-meta">
            <div class="meta-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $user->email }}</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-phone"></i>
                <span>{{ $user->phone ?? 'Non renseigné' }}</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $user->bureau ?? 'Bureau non spécifié' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Grille d'informations -->
<div class="profile-grid">
    <!-- Colonne gauche : Informations détaillées -->
    <div>
        <div class="info-card">
            <div class="card-header">
                <h2>
                    <i class="fas fa-user-circle"></i>
                    Informations personnelles
                </h2>
                <button class="edit-btn" onclick="openEditModal()">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>
            
            <div class="info-row">
                <span class="info-label">Nom complet</span>
                <span class="info-value">{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de naissance</span>
                <span class="info-value">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d F Y') : 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone</span>
                <span class="info-value">{{ $user->phone ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email personnel</span>
                <span class="info-value">{{ $user->email_personnel ?? $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adresse</span>
                <span class="info-value">{{ $user->address ?? 'Non renseignée' }}</span>
            </div>
        </div>

        <div class="info-card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h2>
                    <i class="fas fa-briefcase"></i>
                    Informations professionnelles
                </h2>
            </div>
            
            <div class="info-row">
                <span class="info-label">Département</span>
                <span class="info-value">{{ $user->departement ?? $serviceName ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Poste</span>
                <span class="info-value">{{ $user->poste ?? 'Chef de service' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de prise de fonction</span>
                <span class="info-value">{{ $user->date_prise_fonction ? \Carbon\Carbon::parse($user->date_prise_fonction)->format('d F Y') : 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Équipe</span>
                <span class="info-value">{{ $equipeSize ?? 0 }} membres</span>
            </div>
            <div class="info-row">
                <span class="info-label">Spécialités</span>
                <span class="info-value">
                    <div class="competences-list">
                        @forelse($competences ?? [] as $competence)
                            <span class="competence-tag">{{ $competence }}</span>
                        @empty
                            <span class="competence-tag">Gestion de projet</span>
                            <span class="competence-tag">Encadrement</span>
                            <span class="competence-tag">Développement</span>
                            <span class="competence-tag">Sécurité</span>
                        @endforelse
                    </div>
                </span>
            </div>
        </div>
    </div>

    <!-- Colonne droite : Statistiques et activité -->
    <div>
        <div class="info-card">
            <div class="card-header">
                <h2>
                    <i class="fas fa-chart-simple"></i>
                    Statistiques
                </h2>
            </div>
            
            <div class="stats-mini-grid">
                <div class="stat-mini-card">
                    <div class="number">{{ $statistiques['stagiaires_encadres'] }}</div>
                    <div class="label">Stagiaires encadrés</div>
                </div>
                <div class="stat-mini-card">
                    <div class="number">{{ $statistiques['tuteurs'] }}</div>
                    <div class="label">Tuteurs</div>
                </div>
                <div class="stat-mini-card">
                    <div class="number">{{ $statistiques['projets_en_cours'] }}</div>
                    <div class="label">Projets en cours</div>
                </div>
                <div class="stat-mini-card">
                    <div class="number">{{ $statistiques['satisfaction'] }}%</div>
                    <div class="label">Satisfaction</div>
                </div>
            </div>

            <div class="info-row">
                <span class="info-label">Validations en attente</span>
                <span class="info-value" style="color: var(--rouge); font-weight: 700;">{{ $validationsEnAttente ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Bilans à valider</span>
                <span class="info-value" style="color: var(--orange, #f59e0b); font-weight: 700;">{{ $bilansEnAttente ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ancienneté</span>
                <span class="info-value">{{ $anciennete ?? '2 ans' }}</span>
            </div>
        </div>

        <div class="info-card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Dernières activités
                </h2>
                <span class="view-all" onclick="window.location.href='{{ route('chef-service.activites') }}'">
                    Voir tout <i class="fas fa-arrow-right"></i>
                </span>
            </div>

            <ul class="recent-activity">
                @forelse($activitesRecentes ?? [] as $activite)
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas {{ $activite->icone ?? 'fa-check-circle' }}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">{{ $activite->titre }}</div>
                        <div class="activity-time">
                            <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($activite->created_at)->diffForHumans() }}
                        </div>
                    </div>
                </li>
                @empty
                <li class="activity-item">
                    <div class="activity-content">
                        <div class="activity-text">Aucune activité récente</div>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Modal de modification du profil -->
<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-user-edit"></i> Modifier mon profil</h2>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="profileForm" method="POST" action="{{ route('chef-service.profil.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="form-group">
                    <label>Bureau</label>
                    <input type="text" name="bureau" class="form-control" value="{{ old('bureau', $user->bureau) }}">
                </div>
                <div class="form-group">
                    <label>Avatar</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF. Max 2MB</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="btn-submit">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de changement de mot de passe -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-key"></i> Changer mon mot de passe</h2>
            <button class="modal-close" onclick="closePasswordModal()">&times;</button>
        </div>
        <form id="passwordForm" method="POST" action="{{ route('chef-service.profil.password') }}">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Confirmation du mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closePasswordModal()">Annuler</button>
                <button type="submit" class="btn-submit">Changer le mot de passe</button>
            </div>
        </form>
    </div>
</div>

<style>
    .profile-header-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        position: relative;
        overflow: hidden;
    }

    .profile-header-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 50%;
    }

    .profile-avatar-large {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 3.5rem;
        border: 4px solid var(--blanc);
        box-shadow: 0 15px 30px -10px var(--bleu);
        flex-shrink: 0;
        overflow: hidden;
    }

    .profile-header-info {
        flex: 1;
    }

    .profile-header-info h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .profile-header-info h1 span {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .profile-header-title {
        color: var(--gris-fonce);
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .profile-header-title i {
        color: var(--vert);
        margin-right: 0.5rem;
    }

    .profile-header-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        color: var(--gris-fonce);
    }

    .meta-item i {
        width: 30px;
        height: 30px;
        background: var(--gris-clair);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bleu);
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 1.5rem;
    }

    .info-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.8rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gris-clair);
    }

    .card-header h2 {
        color: var(--noir);
        font-size: 1.3rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header h2 i {
        color: var(--bleu);
    }

    .edit-btn {
        background: var(--gris-clair);
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        color: var(--gris-fonce);
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .edit-btn:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -10px var(--bleu);
    }

    .view-all {
        color: var(--bleu);
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s ease;
    }

    .view-all:hover {
        color: var(--vert);
        gap: 0.5rem;
    }

    .info-row {
        display: flex;
        margin-bottom: 1.2rem;
        padding: 0.5rem 0;
        border-bottom: 1px dashed var(--gris-clair);
    }

    .info-label {
        width: 140px;
        color: var(--gris);
        font-weight: 500;
    }

    .info-value {
        flex: 1;
        color: var(--noir);
        font-weight: 600;
    }

    .competences-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-top: 0.5rem;
    }

    .competence-tag {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        padding: 0.5rem 1.2rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        box-shadow: 0 5px 15px -5px var(--bleu);
    }

    .stats-mini-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-mini-card {
        background: var(--gris-clair);
        border-radius: 18px;
        padding: 1.2rem;
        text-align: center;
        border: 2px solid var(--blanc);
        transition: all 0.3s ease;
    }

    .stat-mini-card:hover {
        transform: translateY(-3px);
        border-color: var(--bleu);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }

    .stat-mini-card .number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1.2;
    }

    .stat-mini-card .label {
        color: var(--gris);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .recent-activity {
        list-style: none;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 2px solid var(--gris-clair);
        transition: all 0.2s ease;
    }

    .activity-item:hover {
        background: var(--gris-clair);
        padding: 1rem;
        border-radius: 14px;
        transform: translateX(5px);
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        color: var(--noir);
        font-weight: 600;
        margin-bottom: 0.3rem;
        font-size: 0.95rem;
    }

    .activity-time {
        color: var(--gris);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 28px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
        box-shadow: var(--shadow-lg);
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.2rem 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
        background: linear-gradient(135deg, var(--gris-clair), white);
        border-radius: 28px 28px 0 0;
    }

    .modal-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .modal-close {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1.2rem;
    }

    .modal-close:hover {
        background: var(--rouge);
        color: white;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .form-group {
        margin-bottom: 1.2rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gris-fonce);
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 14px;
        outline: none;
        transition: all 0.2s;
        font-size: 0.9rem;
        font-family: inherit;
    }

    .form-control:focus {
        border-color: var(--bleu);
        background: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-cancel, .btn-submit {
        padding: 0.7rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .btn-cancel {
        background: var(--gris-clair);
        color: var(--gris-fonce);
    }

    .btn-cancel:hover {
        background: #e2e8f0;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .form-text {
        font-size: 0.7rem;
        color: var(--gris);
        margin-top: 0.3rem;
        display: block;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header-card, .info-card {
        animation: fadeInUp 0.6s ease backwards;
    }
    .profile-header-card { animation-delay: 0.1s; }
    .info-card:nth-child(1) { animation-delay: 0.2s; }
    .info-card:nth-child(2) { animation-delay: 0.3s; }

    @media (max-width: 1200px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-header-card {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        .profile-header-meta {
            justify-content: center;
        }
        .info-row {
            flex-direction: column;
            gap: 0.3rem;
        }
        .info-label {
            width: auto;
        }
        .stats-mini-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .profile-header-info h1 {
            font-size: 1.8rem;
        }
        .profile-header-meta {
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }
        .modal-footer {
            flex-direction: column;
        }
        .modal-footer button {
            width: 100%;
        }
    }
</style>

<script>
    function openEditModal() {
        document.getElementById('editProfileModal').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('editProfileModal').classList.remove('active');
    }

    function openPasswordModal() {
        document.getElementById('passwordModal').classList.add('active');
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').classList.remove('active');
    }

    // Fermer les modals en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        const editModal = document.getElementById('editProfileModal');
        const passwordModal = document.getElementById('passwordModal');
        
        if (event.target === editModal) {
            closeEditModal();
        }
        if (event.target === passwordModal) {
            closePasswordModal();
        }
    });

    // Afficher les notifications de session
    @if(session('success'))
        showNotification('Succès', '{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('Erreur', '{{ session('error') }}', 'error');
    @endif

    @if($errors->any())
        showNotification('Erreur', 'Veuillez vérifier les champs du formulaire', 'error');
    @endif
</script>
@endsection
