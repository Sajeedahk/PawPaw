<?php
include_once "$racine/modele/bd.annonce.inc.php";

$ville       = trim($_GET['ville'] ?? '');
$animal_type = trim($_GET['type']  ?? '');

$pros  = getAllPros($ville, $animal_type);
$titre = "Nos gardiens professionnels — Paw Paw";

ob_start();
include "$racine/vue/annonces/index.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
