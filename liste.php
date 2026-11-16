<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// 1. Récupération et nettoyage des paramètres GET (Exercice 7)
$q = trim($_GET['q'] ?? '');
$tri = $_GET['tri'] ?? 'ajout';
$tris_valides = ['ajout', 'nom', 'filiere', 'note'];
if (!in_array($tri, $tris_valides)) {
    $tri = 'ajout';
}

// 2. Filtrage par recherche (nom ou prénom)
if ($q !== '') {
    $etudiants = array_filter($etudiants, function ($etu) use ($q) {
        return stripos($etu['nom'], $q) !== false || stripos($etu['prenom'], $q) !== false;
    });
}

// 3. Tri des résultats
if ($tri === 'nom') {
    usort($etudiants, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
} elseif ($tri === 'filiere') {
    usort($etudiants, fn($a, $b) => strcasecmp($a['filiere'], $b['filiere']));
} elseif ($tri === 'note') {
    usort($etudiants, fn($a, $b) => $b['note'] <=> $a['note']); // Décroissant
}

// 4. Calcul des statistiques
$nb_etudiants = count($etudiants);
if ($nb_etudiants > 0) {
    $notes = array_column($etudiants, 'note');
    $moyenne = number_format(array_sum($notes) / $nb_etudiants, 2, ',', ' ');
    $meilleure_note = number_format(max($notes), 2, ',', ' ');
} else {
    $moyenne = '0,00';
    $meilleure_note = '0,00';
}

// 5. Récupération du message flash (Exercice 6)
$flash = $_SESSION['flash'] ?? null;
if ($flash !== null) {
    unset($_SESSION['flash']);
}

$titre = 'Liste des étudiants';
include 'header.php';
?>

<h2>Liste des étudiants</h2>

<?php if ($flash): ?>
    <div class="alert alert-success"><?= h($flash) ?></div>
<?php endif; ?>

<!-- Formulaire de recherche et tri (GET) -->
<form method="GET" action="liste.php" class="filter-bar">
    <input type="text" id="search-input" name="q" placeholder="Rechercher par nom ou prénom..." value="<?= h($q) ?>">

    <select name="tri">
        <option value="ajout" <?= $tri === 'ajout' ? 'selected' : '' ?>>Ordre d'ajout</option>
        <option value="nom" <?= $tri === 'nom' ? 'selected' : '' ?>>Trier par Nom (A-Z)</option>
        <option value="filiere" <?= $tri === 'filiere' ? 'selected' : '' ?>>Trier par Filière</option>
        <option value="note" <?= $tri === 'note' ? 'selected' : '' ?>>Trier par Note (décroissant)</option>
    </select>

    <button type="submit" class="btn">Filtrer</button>
    <?php if ($q !== '' || $tri !== 'ajout'): ?>
        <a href="liste.php" class="btn btn-secondary">Réinitialiser</a>
    <?php endif; ?>
    <a href="export.php" class="btn btn-secondary">Exporter CSV</a>
</form>

<!-- Statistiques -->
<div class="stats-bar">
    <span><strong>Étudiants affichés :</strong> <?= $nb_etudiants ?></span>
    <span><strong>Moyenne :</strong> <?= $moyenne ?> / 20</span>
    <span><strong>Meilleure note :</strong> <?= $meilleure_note ?> / 20</span>
</div>

<!-- Tableau des étudiants -->
<table>
    <thead>
    <tr>
        <th>Photo</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Filière</th>
        <th>Note</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody id="table-etudiants">
    <?php if (empty($etudiants)): ?>
        <tr>
            <td colspan="6" style="text-align: center;">Aucun étudiant</td>
        </tr>
    <?php else: ?>
        <?php foreach ($etudiants as $etu): ?>
            <?php $classe_note = ($etu['note'] >= 10) ? 'note-verte' : 'note-rouge'; ?>
            <tr class="ligne-etudiant">
                <td>
                    <?php if (!empty($etu['photo']) && file_exists(__DIR__ . '/uploads/' . $etu['photo'])): ?>
                        <img src="uploads/<?= h($etu['photo']) ?>" alt="Photo" class="avatar">
                    <?php else: ?>
                        <div class="avatar-placeholder"><?= h(strtoupper(substr($etu['prenom'], 0, 1))) ?></div>
                    <?php endif; ?>
                </td>
                <td class="col-nom"><?= h(strtoupper($etu['nom'])) ?></td>
                <td class="col-prenom"><?= h(ucfirst(strtolower($etu['prenom']))) ?></td>
                <td><?= h($etu['filiere']) ?></td>
                <td class="<?= $classe_note ?>"><?= number_format($etu['note'], 2, ',', ' ') ?></td>
                <td class="actions">
                    <a href="ajouter.php?id=<?= (int)$etu['id'] ?>" class="btn-edit">Modifier</a>
                    <a href="supprimer.php?id=<?= (int)$etu['id'] ?>" class="btn-delete">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
