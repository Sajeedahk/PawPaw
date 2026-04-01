<?php
include_once "bd.inc.php";

/**
 * Compte les réservations actives (en_attente + confirme) pour un créneau.
 */
function getActiveReservationCount($creneauId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT COUNT(*) FROM reservations
         WHERE creneau_id = :creneau_id
           AND statut = 'confirme'"
    );
    $req->execute([':creneau_id' => $creneauId]);
    return (int)$req->fetchColumn();
}

function addReservation($creneauId, $clientId, $animalNom, $animalType, $message = null, $animalPhoto = null) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "INSERT INTO reservations (creneau_id, client_id, animal_nom, animal_type, animal_photo, message)
         VALUES (:creneau_id, :client_id, :animal_nom, :animal_type, :animal_photo, :message)"
    );
    return $req->execute([
        ':creneau_id'   => $creneauId,
        ':client_id'    => $clientId,
        ':animal_nom'   => $animalNom,
        ':animal_type'  => $animalType,
        ':animal_photo' => $animalPhoto,
        ':message'      => $message,
    ]);
}

/**
 * Gere l'upload de la photo de l'animal, retourne le nom du fichier ou null.
 */
function uploadAnimalPhoto($fileKey = 'animal_photo') {
    if (empty($_FILES[$fileKey]['tmp_name'])) return null;

    $file     = $_FILES[$fileKey];
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowed))   return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;   // max 5 Mo

    $ext = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        default      => 'jpg',
    };
    $filename = 'animal_' . uniqid('', true) . '.' . $ext;
    $dest     = __DIR__ . '/../images/uploads/' . $filename;

    return move_uploaded_file($file['tmp_name'], $dest) ? $filename : null;
}

function getReservationsByPro($proId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT r.*, c.date_debut, c.date_fin, c.pro_id,
                u.nom, u.prenom, u.email, u.telephone
         FROM reservations r
         JOIN creneaux c ON r.creneau_id = c.id
         JOIN users u    ON r.client_id  = u.id
         WHERE c.pro_id = :pro_id
         ORDER BY r.created_at DESC"
    );
    $req->bindValue(':pro_id', $proId, PDO::PARAM_INT);
    $req->execute();
    return $req->fetchAll();
}

function getReservationsByClient($clientId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT r.*, c.date_debut, c.date_fin, c.pro_id,
                u.nom as pro_nom, u.prenom as pro_prenom,
                p.nom_structure, p.ville
         FROM reservations r
         JOIN creneaux c      ON r.creneau_id = c.id
         JOIN users u         ON c.pro_id     = u.id
         LEFT JOIN profils_pro p ON p.user_id = u.id
         WHERE r.client_id = :client_id
         ORDER BY r.created_at DESC"
    );
    $req->bindValue(':client_id', $clientId, PDO::PARAM_INT);
    $req->execute();
    return $req->fetchAll();
}

function getReservationById($id) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT r.*, c.pro_id, c.date_debut, c.date_fin
         FROM reservations r
         JOIN creneaux c ON r.creneau_id = c.id
         WHERE r.id = :id"
    );
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch() ?: null;
}

function updateStatutReservation($id, $statut) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("UPDATE reservations SET statut=:statut WHERE id=:id");
    return $req->execute([':statut' => $statut, ':id' => $id]);
}

function alreadyReserved($creneauId, $clientId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT COUNT(*) FROM reservations
         WHERE creneau_id=:creneau_id AND client_id=:client_id
         AND statut NOT IN ('refuse','annule')"
    );
    $req->execute([':creneau_id' => $creneauId, ':client_id' => $clientId]);
    return (bool)$req->fetchColumn();
}
?>
