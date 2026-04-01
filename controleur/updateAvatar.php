<?php
include_once "$racine/modele/bd.annonce.inc.php";
include_once "$racine/modele/bd.utilisateur.inc.php";

if (!isLoggedOn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?action=profil");
    exit;
}

$dataURL = $_POST['avatar_cropped'] ?? '';

if ($dataURL && str_starts_with($dataURL, 'data:image/')) {
    $filename = saveBase64Photo($dataURL);
    if ($filename) {
        updateUserAvatar($_SESSION['user_id'], $filename);
        $_SESSION['user_photo'] = $filename;
        $_SESSION['success']    = "Photo de profil mise à jour !";
    }
}

header("Location: index.php?action=profil");
exit;
?>
