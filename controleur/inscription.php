<?php
include_once "$racine/modele/bd.utilisateur.inc.php";

if (isLoggedOn()) {
    header("Location: index.php?action=defaut");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom']       ?? '');
    $prenom    = trim($_POST['prenom']    ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $pwdConfirm= $_POST['password_confirm'] ?? '';
    $role      = $_POST['role']           ?? 'particulier';
    $telephone = trim($_POST['telephone'] ?? '');

    if (!in_array($role, ['particulier', 'pro'])) $role = 'particulier';

    if (!$nom || !$prenom || !$email || !$password) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif (strlen($password) < 12) {
        $error = "Le mot de passe doit contenir au moins 12 caractères.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Le mot de passe doit contenir au moins une lettre majuscule.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un chiffre.";
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $error = "Le mot de passe doit contenir au moins un caractère spécial (!@#$...).";
    } elseif ($password !== $pwdConfirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $ret = addUser($nom, $prenom, $email, $password, $role, $telephone);
        if ($ret === false) {
            $error = "Cet email est déjà utilisé.";
        } else {
            login($email, $password);
            // Si pro → on l'envoie compléter son profil
            header($role === 'pro'
                ? "Location: index.php?action=creerAnnonce"
                : "Location: index.php?action=defaut"
            );
            exit;
        }
    }
}

$titre = "Inscription — Paw Paw";

ob_start();
include "$racine/vue/auth/register.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
