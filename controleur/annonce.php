<?php
include_once "$racine/modele/bd.annonce.inc.php";
include_once "$racine/modele/bd.reservation.inc.php";

if (!$id) {
    header("Location: index.php?action=annonces");
    exit;
}

$pro = getProById($id);

if (!$pro) {
    http_response_code(404);
    ob_start();
    include "$racine/vue/404.php";
    $content = ob_get_clean();
    $titre   = "Page introuvable — Paw Paw";
    include "$racine/vue/layouts/main.php";
    exit;
}

$tarifs   = getTarifsByPro($id);
$creneaux = getCreneauxByPro($id, false); // tous les créneaux (dispo + réservés) pour le calendrier

$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error']   ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$titre = ($pro['nom_structure'] ?? ($pro['prenom'] . ' ' . $pro['nom'])) . ' — Paw Paw';

ob_start();
include "$racine/vue/annonces/show.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
