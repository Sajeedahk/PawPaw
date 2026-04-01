<?php
require_once 'modele/bd.inc.php';
require_once 'modele/bd.utilisateur.inc.php';
require_once 'modele/bd.annonce.inc.php';

echo "=== Tests unitaires - Paw Paw ===\n\n";

// ── Test 2 : getProById() retourne un pro existant ───────────
echo "Test getProById() :";
$pro = getProById(3);
if (is_array($pro) && isset($pro['nom'])) {
    echo " Reussi! (Pro trouve : " . $pro['prenom'] . " " . $pro['nom'] . ")\n";
} else {
    echo " Echec!\n";
}