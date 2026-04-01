<?php
include_once "$racine/modele/bd.annonce.inc.php";
include_once "$racine/modele/bd.reservation.inc.php";

if (!isLoggedOn() || !isParticulier()) {
    header("Location: index.php?action=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?action=annonces");
    exit;
}

$mode = $_POST['mode'] ?? 'creneau';

/* ══════════════════════════════════════════════════════════
   MODE PLAGE DE DATES : le client choisit ses dates
══════════════════════════════════════════════════════════ */
if ($mode === 'range') {
    $proId      = (int)($_POST['pro_id'] ?? 0);
    $dateDebut  = trim($_POST['date_debut'] ?? '');
    $dateFin    = trim($_POST['date_fin']   ?? '');
    $animalNom  = trim($_POST['animal_nom']  ?? '');
    $animalType = trim($_POST['animal_type'] ?? '');
    $message    = trim($_POST['message']     ?? '');

    if (!$proId || !$dateDebut || !$dateFin || !$animalNom || !$animalType) {
        $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
        header("Location: index.php?action=annonce&id=$proId");
        exit;
    }

    $dateDebut = date('Y-m-d', strtotime($dateDebut));
    $dateFin   = date('Y-m-d', strtotime($dateFin));

    if ($dateFin < $dateDebut) {
        $_SESSION['error'] = "La date de fin doit être après la date de début.";
        header("Location: index.php?action=annonce&id=$proId");
        exit;
    }

    $pro = getProById($proId);
    if (!$pro) {
        $_SESSION['error'] = "Professionnel introuvable.";
        header("Location: index.php?action=annonces");
        exit;
    }

    $animauxAcceptes = array_map('trim', explode(',', $pro['animaux_acceptes']));
    if (!in_array($animalType, $animauxAcceptes)) {
        $_SESSION['error'] = "Ce professionnel n'accepte pas ce type d'animal.";
        header("Location: index.php?action=annonce&id=$proId");
        exit;
    }

    // Vérifier chevauchement avec réservations confirmées
    if (hasOverlappingReservation($proId, $dateDebut . ' 00:00:00', $dateFin . ' 23:59:59')) {
        $_SESSION['error'] = "Le professionnel a déjà une réservation sur cette période.";
        header("Location: index.php?action=annonce&id=$proId");
        exit;
    }

    // Vérifier que toute la plage est couverte par des créneaux disponibles
    if (!rangeFullyCovered($proId, $dateDebut, $dateFin)) {
        $_SESSION['error'] = "Certains jours de cette période ne sont pas disponibles du professionnel.";
        header("Location: index.php?action=annonce&id=$proId");
        exit;
    }

    // ── Vérification capacité pour le mode range ─────────────────────
    // On crée d'abord un créneau temporaire pour compter (ou on vérifie via les créneaux existants)
    $capaciteMax = max(1, (int)($pro['capacite_max'] ?? 1));
    $creneauxCovered = getCreneauxByPro($proId, false); // tous les créneaux
    // Trouver les créneaux qui couvrent cette plage
    foreach ($creneauxCovered as $cv) {
        if ($cv['date_debut'] <= $dateDebut . ' 00:00:00' && $cv['date_fin'] >= $dateFin . ' 23:59:59') {
            $placesLibres = (int)($cv['places_libres'] ?? $capaciteMax);
            if ($placesLibres <= 0) {
                $_SESSION['error'] = "Ce créneau est complet — toutes les " . $capaciteMax . " places sont prises.";
                header("Location: index.php?action=annonce&id=$proId");
                exit;
            }
        }
    }

    // Créer le créneau et la réservation
    $animalPhoto = uploadAnimalPhoto('animal_photo');
    $creneauId = addCreneau($proId, $dateDebut . ' 00:00:00', $dateFin . ' 23:59:59');
    addReservation($creneauId, $_SESSION['user_id'], $animalNom, $animalType, $message, $animalPhoto);

    // Marquer comme réservé uniquement si complet
    $nbApresRange = getActiveReservationCount($creneauId);
    if ($nbApresRange >= $capaciteMax) {
        updateStatutCreneau($creneauId, 'reserve');
    }

    $_SESSION['success'] = "Votre demande a été envoyée au gardien ! Il la confirmera bientôt.";
    header("Location: index.php?action=annonce&id=$proId");
    exit;
}

/* ══════════════════════════════════════════════════════════
   MODE CRÉNEAU EXISTANT (legacy)
══════════════════════════════════════════════════════════ */
if (!$id) {
    header("Location: index.php?action=annonces");
    exit;
}

$creneau = getCreneauById($id);

if (!$creneau) {
    $_SESSION['error'] = "Ce créneau est introuvable.";
    header("Location: index.php?action=annonces");
    exit;
}

// ── Vérification de la capacité (basée sur les réservations confirmées uniquement) ──
$pro         = getProById($creneau['pro_id']);
$capaciteMax = $pro ? max(1, (int)$pro['capacite_max']) : 1;
$nbConfirmes = getActiveReservationCount($id);

if ($nbConfirmes >= $capaciteMax) {
    $_SESSION['error'] = "Ce créneau est complet — toutes les places sont prises.";
    header("Location: index.php?action=annonce&id=" . $creneau['pro_id']);
    exit;
}

if ($creneau['pro_id'] == $_SESSION['user_id']) {
    $_SESSION['error'] = "Vous ne pouvez pas réserver votre propre créneau.";
    header("Location: index.php?action=annonce&id=" . $creneau['pro_id']);
    exit;
}

if (alreadyReserved($id, $_SESSION['user_id'])) {
    $_SESSION['error'] = "Vous avez déjà une demande pour ce créneau.";
    header("Location: index.php?action=annonce&id=" . $creneau['pro_id']);
    exit;
}

$animalNom  = trim($_POST['animal_nom']  ?? '');
$animalType = trim($_POST['animal_type'] ?? '');
$message    = trim($_POST['message']     ?? '');

if (!$animalNom || !$animalType) {
    $_SESSION['error'] = "Veuillez indiquer le nom et le type de votre animal.";
    header("Location: index.php?action=annonce&id=" . $creneau['pro_id']);
    exit;
}

if ($pro) {
    $animauxAcceptes = array_map('trim', explode(',', $pro['animaux_acceptes']));
    if (!in_array($animalType, $animauxAcceptes)) {
        $_SESSION['error'] = "Ce professionnel n'accepte pas ce type d'animal.";
        header("Location: index.php?action=annonce&id=" . $creneau['pro_id']);
        exit;
    }
}

addReservation($id, $_SESSION['user_id'], $animalNom, $animalType, $message, uploadAnimalPhoto('animal_photo'));

// Marquer le créneau comme "réservé" uniquement quand il est complet
$nbApres = getActiveReservationCount($id);
if ($nbApres >= $capaciteMax) {
    updateStatutCreneau($id, 'reserve');
}

$_SESSION['success'] = "Votre demande a été envoyée au gardien !";
header("Location: index.php?action=annonce&id=" . $creneau['pro_id']);
exit;
