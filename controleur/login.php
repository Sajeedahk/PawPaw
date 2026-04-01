<?php
if (isLoggedOn()) {
    header("Location: index.php?action=defaut");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password']   ?? '';

    if (!$email || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $ok = login($email, $password);
        if ($ok) {
            header("Location: index.php?action=defaut");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}

$titre = "Connexion — Paw Paw";

ob_start();
include "$racine/vue/auth/login.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
