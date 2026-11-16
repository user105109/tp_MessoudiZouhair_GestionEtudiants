<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titre) ? h($titre) : 'Gestion des étudiants' ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Gestion des Étudiants</h1>
    <?php if (connecte()): ?>
        <nav>
            <a href="liste.php">Liste des étudiants</a>
            <a href="ajouter.php">Ajouter un étudiant</a>
            <a href="logout.php">Déconnexion (<?= h($_SESSION['user']) ?>)</a>
        </nav>
    <?php endif; ?>
</header>
<main class="container">
