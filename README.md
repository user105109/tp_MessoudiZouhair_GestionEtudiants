# Gestion des Étudiants (TP PHP)

Une petite application web développée en PHP pur pour gérer une liste d'étudiants sans base de données (les informations sont sauvegardées dans un simple fichier texte).

## Ce que fait l'application

* **Connexion sécurisée :** Accès protégé par identifiant et mot de passe, avec mémorisation du dernier utilisateur via un cookie.
* **Liste & Statistiques :** Affichage des étudiants avec calcul automatique de la moyenne et de la meilleure note.
* **Recherche et Tri :** Filtrage instantané en JavaScript ou tri par nom, filière et note.
* **Ajout & Modification :** Formulaire complet avec validation des données et envoi de photo de profil (JPG/PNG, max 1 Mo).
* **Suppression :** Suppression d'un étudiant et de sa photo avec confirmation.
* **Export CSV :** Téléchargement de la liste complète au format Excel/CSV.

## Comment lancer le projet

1. Installez **XAMPP** (ou Laragon / MAMP) et démarrez le serveur **Apache**.
2. Placez le dossier `gestion_etudiants` dans `htdocs` (ex: `C:\xampp\htdocs\gestion_etudiants`).
3. Vérifiez que les dossiers `data/` et `uploads/` existent bien à la racine du projet.
4. Ouvrez votre navigateur et allez sur : `http://localhost/gestion_etudiants/`

## Identifiants de test

* **Login :** `admin` | **Mot de passe :** `1234`
* **Login :** `prof` | **Mot de passe :** `pass2026`

## Structure des fichiers

* `index.php` : Page de connexion
* `liste.php` : Tableau des étudiants, recherche, tri et statistiques
* `ajouter.php` : Formulaire d'ajout et de modification
* `supprimer.php` : Script de suppression
* `export.php` : Exportation de la liste en fichier CSV
* `logout.php` : Déconnexion
* `functions.php` : Fonctions utiles (lecture/écriture du fichier, sécurité)
* `header.php` & `footer.php` : En-tête et pied de page communs
* `style.css` & `script.js` : Design et interactions dynamiques
* `data/etudiants.txt` : Fichier de sauvegarde des données
