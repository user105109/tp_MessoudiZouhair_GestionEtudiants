<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="etudiants.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Nom', 'Prénom', 'Filière', 'Note', 'Photo'], ';');

foreach ($etudiants as $etu) {
    fputcsv($output, [
        $etu['id'],
        strtoupper($etu['nom']),
        ucfirst(strtolower($etu['prenom'])),
        $etu['filiere'],
        $etu['note'],
        $etu['photo']
    ], ';');
}

fclose($output);
exit;
