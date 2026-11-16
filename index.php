<?php
require_once 'functions.php';

if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
$login_val = $_COOKIE['dernier_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $mdp   = $_POST['mdp'] ?? '';
    $login_val = $login;

    if (isset(UTILISATEURS[$login]) && UTILISATEURS[$login] === $mdp) {
        $_SESSION['user'] = $login;
        setcookie('dernier_user', $login, time() + (7 * 24 * 3600), '/');
        header('Location: liste.php');
        exit;
    } else {
        $erreur = 'Identifiants incorrects. Veuillez réessayer.';
    }
}

$titre = 'Connexion';
include 'header.php';
?>

<div class="card">
    <h2>Connexion</h2>

    <?php if ($erreur !== ''): ?>
        <div class="alert alert-error"><?= h($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <div class="form-group">
            <label for="login">Identifiant :</label>
            <input type="text" id="login" name="login" value="<?= h($login_val) ?>" required>
        </div>

        <div class="form-group">
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>
        </div>

        <button type="submit" class="btn">Se connecter</button>
    </form>
</div>

<?php include 'footer.php'; ?>
