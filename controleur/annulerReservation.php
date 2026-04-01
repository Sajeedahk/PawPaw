<?php
include_once "$racine/modele/bd.annonce.inc.php";
include_once "$racine/modele/bd.reservation.inc.php";

if (!isLoggedOn() || !isParticulier()) {
    header("Location: index.php?action=login");
    exit;
}

if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?action=profil");
    exit;
}

$reservation = getReservationById($id);

if (!$reservation || $reservation['client_id'] != $_SESSION['user_id']) {
    header("Location: index.php?action=profil");
    exit;
}

updateStatutReservation($id, 'annule');
updateStatutCreneau($reservation['creneau_id'], 'disponible');

$_SESSION['success'] = "Réservation annulée.";
header("Location: index.php?action=profil");
exit;
?>
