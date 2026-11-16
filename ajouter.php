<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();
$erreurs = [];

// Vérification du mode Modification (?id=...) (Exercice 10)
$id_edit = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mode_edit = ($id_edit > 0 && isset($etudiants[$id_edit]));

// Valeurs par défaut du formulaire
$nom     = $mode_edit ? $etudiants[$id_edit]['nom'] : '';
$prenom  = $mode_edit ? $etudiants[$id_edit]['prenom'] : '';
$filiere = $mode_edit ? $etudiants[$id_edit]['filiere'] : '';
$note    = $mode_edit ? (string)$etudiants[$id_edit]['note'] : '';
$photo_actuelle = $mode_edit ? $etudiants[$id_edit]['photo'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id_post > 0 && isset($etudiants[$id_post])) {
        $mode_edit = true;
        $id_edit = $id_post;
        $photo_actuelle = $etudiants[$id_edit]['photo'];
    }

    // Récupération et nettoyage des champs
    $nom     = nettoyer($_POST['nom'] ?? '');
    $prenom  = nettoyer($_POST['prenom'] ?? '');
    $filiere = nettoyer($_POST['filiere'] ?? '');
    $note_raw = str_replace(',', '.', trim($_POST['note'] ?? ''));

    // 1. Validation des champs texte (Exercice 8)
    if ($nom === '') {
        $erreurs[] = 'Le nom est obligatoire.';
    }
    if ($prenom === '') {
        $erreurs[] = 'Le prénom est obligatoire.';
    }
    if ($nom !== '' && $prenom !== '') {
        foreach ($etudiants as $id_existant => $etu_existant) {
            if ($mode_edit && $id_existant === $id_edit) {
                continue; // Ignorer l'étudiant en cours de modification
            }
            if (strcasecmp($etu_existant['nom'], $nom) === 0 && strcasecmp($etu_existant['prenom'], $prenom) === 0) {
                $erreurs[] = 'Un étudiant avec ce nom et ce prénom existe déjà.';
                break;
            }
        }
    }
    if (!in_array($filiere, FILIERES)) {
        $erreurs[] = 'Veuillez choisir une filière valide.';
    }
    if ($note_raw === '' || !is_numeric($note_raw) || (float)$note_raw < 0 || (float)$note_raw > 20) {
        $erreurs[] = 'La note doit être un nombre compris entre 0 et 20.';
    } else {
        $note = (float)$note_raw;
    }

    // 2. Validation et traitement de la photo (Exercice 9)
    $nouvelle_photo = $photo_actuelle;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = 'Erreur lors du téléchargement de la photo.';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $extensions_ok = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $extensions_ok)) {
                $erreurs[] = 'Format de photo non autorisé (uniquement jpg, jpeg, png).';
            } elseif ($file['size'] > 1048576) { // 1 Mo = 1024 * 1024 octets
                $erreurs[] = 'La photo ne doit pas dépasser 1 Mo.';
            } elseif (empty($erreurs)) {
                $nom_fichier = uniqid('etu_') . '.' . $ext;
                $destination = __DIR__ . '/uploads/' . $nom_fichier;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // En mode modification, supprimer l'ancienne photo (Exercice 10)
                    if ($mode_edit && !empty($photo_actuelle)) {
                        $ancien_chemin = __DIR__ . '/uploads/' . $photo_actuelle;
                        if (file_exists($ancien_chemin)) {
                            unlink($ancien_chemin);
                        }
                    }
                    $nouvelle_photo = $nom_fichier;
                } else {
                    $erreurs[] = 'Impossible d\'enregistrer la photo sur le serveur.';
                }
            }
        }
    }

    // 3. Sauvegarde si aucune erreur
    if (empty($erreurs)) {
        if ($mode_edit) {
            $id = $id_edit;
            $_SESSION['flash'] = 'Étudiant modifié avec succès !';
        } else {
            $id = empty($etudiants) ? 1 : (max(array_keys($etudiants)) + 1);
            $_SESSION['flash'] = 'Étudiant ajouté avec succès !';
        }

        $etudiants[$id] = [
            'id'      => $id,
            'nom'     => $nom,
            'prenom'  => $prenom,
            'filiere' => $filiere,
            'note'    => $note,
            'photo'   => $nouvelle_photo
        ];

        sauver_etudiants($etudiants);
        header('Location: liste.php');
        exit;
    } else {
        // Conserver la saisie brute de la note en cas d'erreur
        $note = $note_raw;
    }
}

$titre = $mode_edit ? 'Modifier un étudiant' : 'Ajouter un étudiant';
include 'header.php';
?>

<div class="card">
    <h2><?= h($titre) ?></h2>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($erreurs as $err): ?>
                    <li><?= h($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="ajouter.php<?= $mode_edit ? '?id=' . $id_edit : '' ?>" enctype="multipart/form-data">
        <?php if ($mode_edit): ?>
            <input type="hidden" name="id" value="<?= $id_edit ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= h($nom) ?>" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= h($prenom) ?>" required>
        </div>

        <div class="form-group">
            <label for="filiere">Filière :</label>
            <select id="filiere" name="filiere" required>
                <option value="">-- Choisir une filière --</option>
                <?php foreach (FILIERES as $f): ?>
                    <option value="<?= h($f) ?>" <?= $filiere === $f ? 'selected' : '' ?>><?= h($f) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="note">Note (/20) :</label>
            <input type="number" step="0.01" min="0" max="20" id="note" name="note" value="<?= h((string)$note) ?>" required>
        </div>

        <div class="form-group">
            <label for="photo">Photo de profil (jpg, jpeg, png - max 1 Mo) :</label>
            <?php if ($mode_edit && !empty($photo_actuelle) && file_exists(__DIR__ . '/uploads/' . $photo_actuelle)): ?>
                <div style="margin-bottom: 8px;">
                    <img src="uploads/<?= h($photo_actuelle) ?>" alt="Photo actuelle" class="avatar">
                    <small>(Photo actuelle)</small>
                </div>
            <?php endif; ?>
            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png">
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn"><?= $mode_edit ? 'Enregistrer les modifications' : 'Ajouter' ?></button>
            <a href="liste.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
