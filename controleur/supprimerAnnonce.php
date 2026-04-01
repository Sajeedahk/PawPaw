<?php
include_once "$racine/modele/bd.annonce.inc.php";

if (!isLoggedOn() || !isPro()) {
    header("Location: index.php?action=login");
    exit;
}

if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    deleteCreneau($id, $_SESSION['user_id']);
    $_SESSION['success'] = "Créneau supprimé.";
}

header("Location: index.php?action=profil");
exit;
?>
