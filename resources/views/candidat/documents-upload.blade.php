@extends('layouts.candidat')

@section('title', 'Uploader un document - Candidat')

@section('content')
<div class="upload-container">
    <div class="welcome-section">
        <h1 class="welcome-title">Uploader un <span>document</span> 📤</h1>
        <p class="welcome-subtitle">Ajoutez un nouveau document à votre espace personnel</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="upload-card">
        <form action="{{ route('candidat.documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <div class="form-group">
                <label for="titre">Titre du document *</label>
                <input type="text" 
                       id="titre" 
                       name="titre" 
                       class="form-control" 
                       value="{{ old('titre') }}"
                       placeholder="Ex: Convention de stage - Entreprise ABC"
                       required>
            </div>
            
            <div class="form-group">
                <label for="type">Type de document *</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="fichier">Fichier *</label>
                <div class="file-upload-area" id="fileUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Cliquez ou glissez-déposez votre fichier ici</p>
                    <p class="file-info-text">Formats acceptés: PDF, DOC, DOCX, JPG, PNG (max 10MB)</p>
                    <input type="file" 
                           id="fichier" 
                           name="fichier" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           style="display: none;"
                           required>
                    <button type="button" class="btn-upload" onclick="document.getElementById('fichier').click()">
                        <i class="fas fa-folder-open"></i> Parcourir
                    </button>
                </div>
                <div id="fileInfo" class="file-info-display" style="display: none;">
                    <i class="fas fa-file"></i>
                    <span id="fileName"></span>
                    <span id="fileSize"></span>
                    <button type="button" onclick="clearFile()" class="btn-clear">✕</button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description (optionnel)</label>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          rows="4"
                          placeholder="Ajoutez une description pour ce document...">{{ old('description') }}</textarea>
            </div>
            
            <div class="form-actions">
                <a href="{{ route('candidat.documents.index') }}" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    <i class="fas fa-upload"></i> Uploader le document
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .upload-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .welcome-section {
        margin-bottom: 2rem;
        background: var(--blanc);
        padding: 2rem;
        border-radius: 24px;
        box-shadow: var(--shadow);
        border: 2px solid var(--gris-clair);
    }
    
    .welcome-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }
    
    .welcome-title span {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .welcome-subtitle {
        color: var(--gris);
        font-size: 1rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--rouge);
        color: var(--rouge);
    }
    
    .upload-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--gris-fonce);
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--bleu);
    }
    
    .file-upload-area {
        border: 2px dashed var(--gris-clair);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .file-upload-area:hover {
        border-color: var(--bleu);
        background: var(--gris-clair);
    }
    
    .file-upload-area i {
        font-size: 3rem;
        color: var(--bleu);
        margin-bottom: 1rem;
    }
    
    .file-info-text {
        font-size: 0.85rem;
        color: var(--gris);
    }
    
    .btn-upload {
        margin-top: 1rem;
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .file-info-display {
        margin-top: 1rem;
        padding: 0.8rem;
        background: var(--gris-clair);
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-clear {
        margin-left: auto;
        background: none;
        border: none;
        color: var(--rouge);
        cursor: pointer;
        font-size: 1.2rem;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .btn-cancel {
        padding: 0.8rem 1.5rem;
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-cancel:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .btn-submit {
        padding: 0.8rem 2rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .upload-card {
            padding: 1.5rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-cancel, .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    const fileInput = document.getElementById('fichier');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    
    fileUploadArea.addEventListener('click', () => fileInput.click());
    
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--bleu)';
        fileUploadArea.style.background = 'var(--gris-clair)';
    });
    
    fileUploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--gris-clair)';
        fileUploadArea.style.background = 'transparent';
    });
    
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--gris-clair)';
        fileUploadArea.style.background = 'transparent';
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            updateFileInfo(e.dataTransfer.files[0]);
        }
    });
    
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            updateFileInfo(fileInput.files[0]);
        }
    });
    
    function updateFileInfo(file) {
        fileName.textContent = file.name;
        const sizeKo = (file.size / 1024).toFixed(2);
        fileSize.textContent = `(${sizeKo} Ko)`;
        
        fileUploadArea.style.display = 'none';
        fileInfo.style.display = 'flex';
        submitBtn.disabled = false;
    }
    
    function clearFile() {
        fileInput.value = '';
        fileUploadArea.style.display = 'block';
        fileInfo.style.display = 'none';
        submitBtn.disabled = true;
    }
    
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const titre = document.getElementById('titre').value.trim();
        const type = document.getElementById('type').value;
        const fichier = fileInput.files[0];
        
        if (!titre) {
            e.preventDefault();
            alert('Veuillez entrer un titre');
            return false;
        }
        
        if (!type) {
            e.preventDefault();
            alert('Veuillez sélectionner un type');
            return false;
        }
        
        if (!fichier) {
            e.preventDefault();
            alert('Veuillez sélectionner un fichier');
            return false;
        }
        
        if (fichier.size > 10 * 1024 * 1024) {
            e.preventDefault();
            alert('Le fichier ne doit pas dépasser 10MB');
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upload en cours...';
    });
</script>
@endsection
