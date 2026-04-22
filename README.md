
# 🎓 GesStage - Tutoriel d'utilisation

## Installation rapide

Decompresse le fichier .zip

cd gesstage
php artisan serve

# Configurer votre base de données dans .env

## 👤 Création de comptes (par le responsable)

1. Se connecter avec le compte **responsable**
2. Aller dans `Gestion > Utilisateurs`
3. Cliquer sur `Nouvel utilisateur`
4. Choisir le rôle : `Candidat` / `Encadreur` / `Chef de service`
5. Remplir email, nom, mot de passe → `Créer`

---

## 📄 Candidat - Déposer un dossier

1. Se connecter avec son compte candidat
2. Menu `Mon dossier de stage`
3. Uploader :
   - CV (PDF)
   - Lettre de motivation (PDF)
4. Cliquer sur `Déposer`
5. Statut : `En attente de validation`

---

## ✅ Responsable - Gérer les dossiers

1. Menu `Dossiers reçus`
2. Cliquer sur `Voir` un dossier
3. Boutons :
   - `Valider` → génère un **token de validation** automatique
   - `Refuser` (avec motif)
4. Après validation : `Attribuer un encadreur`
5. Menu `Créer compte` pour tout rôle

---

## 👨‍🏫 Encadreur - Suivi et évaluation

1. Menu `Mes stages attribués`
2. Cliquer sur un stage
3. Onglet `Ajouter un rapport de suivi`
4. Onglet `Évaluation finale` :
   - Note /20
   - Appréciation
   - Valider l'évaluation

---

## 🧾 Chef de service - Validation par service

1. Menu `Stages de mon service`
2. Filtrer par service
3. Voir les dossiers validés par responsable
4. Cliquer sur `Valider le stage` (validation finale)
5. Le stage est officiellement clos

---

## 🔑 Tokens de validation

- Généré automatiquement après validation par le responsable
- Envoyé par email au candidat
- Format : `STG-XXXX-YYYY`
- Utilisé pour justifier l'acceptation officielle



.
