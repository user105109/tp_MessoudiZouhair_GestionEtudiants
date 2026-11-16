<?php
require_once 'functions.php';

$test_data = [
    1 => ['id' => 1, 'nom' => 'benali', 'prenom' => 'sara', 'filiere' => 'GI', 'note' => 14.5, 'photo' => ''],
    2 => ['id' => 2, 'nom' => 'alami', 'prenom' => 'karim', 'filiere' => 'IDIA', 'note' => 9.0, 'photo' => ''],
    3 => ['id' => 3, 'nom' => 'tazi', 'prenom' => 'lina', 'filiere' => 'GC', 'note' => 16.0, 'photo' => '']
];

sauver_etudiants($test_data);
echo '<pre>' . h(file_get_contents(FICHIER)) . '</pre>';
