<?php
include_once "$racine/modele/bd.reservation.inc.php";
include_once "$racine/modele/bd.annonce.inc.php";

if (!isLoggedOn() || !isPro()) {
    header("Location: index.php?action=login");
    exit;
}

if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?action=profil");
    exit;
}

$reservation = getReservationById($id);

if (!$reservation || $reservation['pro_id'] != $_SESSION['user_id']) {
    header("Location: index.php?action=profil");
    exit;
}

include_once "$racine/modele/bd.annonce.inc.php";

updateStatutReservation($id, 'confirme');

// Vérifier si la capacité du créneau est atteinte → bloquer seulement à ce moment
$pro         = getProById($reservation['pro_id']);
$capaciteMax = $pro ? max(1, (int)$pro['capacite_max']) : 1;
$nbConfirmes = getActiveReservationCount($reservation['creneau_id']);

if ($nbConfirmes >= $capaciteMax) {
    updateStatutCreneau($reservation['creneau_id'], 'reserve');
}

$_SESSION['success'] = "Réservation confirmée !";
header("Location: index.php?action=profil");
exit;
?>
