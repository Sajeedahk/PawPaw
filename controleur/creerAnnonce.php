<?php
include_once "$racine/modele/bd.annonce.inc.php";

if (!isLoggedOn() || !isPro()) {
    header("Location: index.php?action=login");
    exit;
}

$error     = null;
$profilPro = getProfilProByUserId($_SESSION['user_id']);
$tarifs    = getTarifsByPro($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animauxRaw = $_POST['animaux_acceptes'] ?? [];
    $animaux    = implode(',', array_filter($animauxRaw));

    $data = [
        'nom_structure'    => trim($_POST['nom_structure'] ?? ''),
        'adresse'          => trim($_POST['adresse']       ?? ''),
        'ville'            => trim($_POST['ville']         ?? ''),
        'code_postal'      => trim($_POST['code_postal']   ?? ''),
        'animaux_acceptes' => $animaux ?: 'chien',
        'capacite_max'     => max(1, (int)($_POST['capacite_max'] ?? 1)),
    ];

    if (!$data['adresse'] || !$data['ville']) {
        $error = "L'adresse et la ville sont obligatoires.";
    } else {
        // Upload photo : base64 recadré depuis le crop JS
        $cropped = $_POST['pro_photo_cropped'] ?? '';
        if ($cropped && str_starts_with($cropped, 'data:image/')) {
            $photo = saveBase64Photo($cropped);
            if ($photo === false) {
                $error = "Impossible de sauvegarder la photo. Réessayez.";
            } else {
                $data['photo'] = $photo;
            }
        }

        if (!$error) {
            if ($profilPro) {
                updateProfilPro($_SESSION['user_id'], $data);
            } else {
                createProfilPro($_SESSION['user_id'], $data);
            }
            saveTarifs($_SESSION['user_id'], $_POST['tarifs'] ?? []);
            // Mettre à jour la photo en session + users.avatar si nouvelle photo
            if (!empty($data['photo'])) {
                $_SESSION['user_photo'] = $data['photo'];
                include_once "$racine/modele/bd.utilisateur.inc.php";
                updateUserAvatar($_SESSION['user_id'], $data['photo']);
            }
            $_SESSION['success'] = "Profil mis à jour avec succès !";
            header("Location: index.php?action=profil");
            exit;
        }
    }
}

$titre = "Mon profil professionnel — Paw Paw";

ob_start();
include "$racine/vue/annonces/create.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
