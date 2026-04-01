<?php
include_once "$racine/modele/bd.utilisateur.inc.php";
include_once "$racine/modele/bd.annonce.inc.php";
include_once "$racine/modele/bd.reservation.inc.php";

if (!isLoggedOn()) {
    header("Location: index.php?action=login");
    exit;
}

$user    = getUserById($_SESSION['user_id']);
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error']   ?? null;
unset($_SESSION['success'], $_SESSION['error']);

if (isPro()) {
    $profilPro    = getProfilProByUserId($_SESSION['user_id']);
    $tarifs       = getTarifsByPro($_SESSION['user_id']);
    $creneaux     = getCreneauxByPro($_SESSION['user_id']);
    $reservations = getReservationsByPro($_SESSION['user_id']);
    $titre        = "Mon espace pro — Paw Paw";
} else {
    $reservations = getReservationsByClient($_SESSION['user_id']);
    $titre        = "Mon profil — Paw Paw";
}

ob_start();
include "$racine/vue/auth/profile.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
