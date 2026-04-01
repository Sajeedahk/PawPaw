<?php
function controleurPrincipal($action) {
    $lesActions = [
        'defaut'               => 'accueil.php',
        'annonces'             => 'annonces.php',
        'annonce'              => 'annonce.php',
        'creerAnnonce'         => 'creerAnnonce.php',
        'modifierAnnonce'      => 'modifierAnnonce.php',
        'supprimerAnnonce'     => 'supprimerAnnonce.php',
        'reserver'             => 'reserver.php',
        'confirmerReservation' => 'confirmerReservation.php',
        'refuserReservation'   => 'refuserReservation.php',
        'annulerReservation'   => 'annulerReservation.php',
        'updateAvatar'         => 'updateAvatar.php',
        'login'                => 'login.php',
        'inscription'          => 'inscription.php',
        'deconnexion'          => 'deconnexion.php',
        'profil'               => 'profil.php',
    ];
    return $lesActions[$action] ?? $lesActions['defaut'];
}
?>
