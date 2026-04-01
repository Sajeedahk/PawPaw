<?php
include_once "$racine/modele/bd.annonce.inc.php";

if (!isLoggedOn() || !isPro()) {
    header("Location: index.php?action=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debut = $_POST['date_debut'] ?? '';
    $fin   = $_POST['date_fin']   ?? '';

    if ($debut && $fin && $debut < $fin) {
        addCreneau($_SESSION['user_id'], $debut, $fin);
        $_SESSION['success'] = "Créneau ajouté !";
    } else {
        $_SESSION['error'] = "Dates invalides.";
    }
}

header("Location: index.php?action=profil");
exit;
?>
