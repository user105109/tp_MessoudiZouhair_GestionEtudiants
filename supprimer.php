<?php
require_once 'functions.php';
exiger_connexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$etudiants = lire_etudiants();

if ($id > 0 && isset($etudiants[$id])) {
    // Supprimer la photo si elle existe
    $photo = $etudiants[$id]['photo'];
    if (!empty($photo)) {
        $chemin_photo = __DIR__ . '/uploads/' . $photo;
        if (file_exists($chemin_photo)) {
            unlink($chemin_photo);
        }
    }

    // Retirer l'étudiant et sauvegarder
    unset($etudiants[$id]);
    sauver_etudiants($etudiants);

    $_SESSION['flash'] = 'Étudiant supprimé avec succès.';
}

header('Location: liste.php');
exit;
