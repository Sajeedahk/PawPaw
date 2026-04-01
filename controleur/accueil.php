<?php
include_once "$racine/modele/bd.annonce.inc.php";

$pros  = getRecentPros(6);
$titre = "Paw Paw — Trouvez un gardien professionnel pour votre animal";

ob_start();
include "$racine/vue/home/index.php";
$content = ob_get_clean();

include "$racine/vue/layouts/main.php";
?>
