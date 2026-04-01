<?php
include_once "bd.inc.php";

/* ══════════════════════════════════
   PROFILS PRO
══════════════════════════════════ */

function getAllPros($ville = '', $animal_type = '') {
    $cnx    = connexionPDO();
    $sql    = "SELECT u.id, u.nom, u.prenom, u.bio, u.telephone,
                      p.nom_structure, p.adresse, p.ville, p.code_postal,
                      p.animaux_acceptes, p.capacite_max, p.photo
               FROM users u
               JOIN profils_pro p ON p.user_id = u.id
               WHERE u.role = 'pro' AND p.actif = 1";
    $params = [];

    if ($ville) {
        $sql .= " AND p.ville LIKE :ville";
        $params[':ville'] = "%$ville%";
    }
    if ($animal_type) {
        $sql .= " AND FIND_IN_SET(:animal_type, p.animaux_acceptes) > 0";
        $params[':animal_type'] = $animal_type;
    }
    $sql .= " ORDER BY u.created_at DESC";
    $req  = $cnx->prepare($sql);
    $req->execute($params);
    return $req->fetchAll();
}

function getRecentPros($limit = 6) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT u.id, u.nom, u.prenom, u.bio,
                p.nom_structure, p.ville, p.animaux_acceptes, p.photo
         FROM users u
         JOIN profils_pro p ON p.user_id = u.id
         WHERE u.role = 'pro' AND p.actif = 1
         ORDER BY u.created_at DESC LIMIT :lim"
    );
    $req->bindValue(':lim', $limit, PDO::PARAM_INT);
    $req->execute();
    return $req->fetchAll();
}

function getProById($userId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT u.*, p.nom_structure, p.adresse, p.ville, p.code_postal,
                p.animaux_acceptes, p.capacite_max, p.photo as profil_photo
         FROM users u
         JOIN profils_pro p ON p.user_id = u.id
         WHERE u.id = :id AND u.role = 'pro'"
    );
    $req->bindValue(':id', $userId, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch() ?: null;
}

function getProfilProByUserId($userId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("SELECT * FROM profils_pro WHERE user_id = :id");
    $req->bindValue(':id', $userId, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch() ?: null;
}

function createProfilPro($userId, $data) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo)
         VALUES (:user_id, :nom_structure, :adresse, :ville, :code_postal, :animaux_acceptes, :capacite_max, :photo)"
    );
    return $req->execute([
        ':user_id'          => $userId,
        ':nom_structure'    => $data['nom_structure'] ?? null,
        ':adresse'          => $data['adresse'],
        ':ville'            => $data['ville'],
        ':code_postal'      => $data['code_postal'] ?? null,
        ':animaux_acceptes' => $data['animaux_acceptes'],
        ':capacite_max'     => $data['capacite_max'] ?? 1,
        ':photo'            => $data['photo'] ?? null,
    ]);
}

function updateProfilPro($userId, $data) {
    $cnx = connexionPDO();
    // Si une nouvelle photo est fournie, on la met à jour aussi
    if (!empty($data['photo'])) {
        $req = $cnx->prepare(
            "UPDATE profils_pro
             SET nom_structure=:nom_structure, adresse=:adresse, ville=:ville,
                 code_postal=:code_postal, animaux_acceptes=:animaux_acceptes,
                 capacite_max=:capacite_max, photo=:photo
             WHERE user_id=:user_id"
        );
        return $req->execute([
            ':nom_structure'    => $data['nom_structure'] ?? null,
            ':adresse'          => $data['adresse'],
            ':ville'            => $data['ville'],
            ':code_postal'      => $data['code_postal'] ?? null,
            ':animaux_acceptes' => $data['animaux_acceptes'],
            ':capacite_max'     => $data['capacite_max'] ?? 1,
            ':photo'            => $data['photo'],
            ':user_id'          => $userId,
        ]);
    } else {
        // Pas de nouvelle photo, on ne touche pas à l'ancienne
        $req = $cnx->prepare(
            "UPDATE profils_pro
             SET nom_structure=:nom_structure, adresse=:adresse, ville=:ville,
                 code_postal=:code_postal, animaux_acceptes=:animaux_acceptes, capacite_max=:capacite_max
             WHERE user_id=:user_id"
        );
        return $req->execute([
            ':nom_structure'    => $data['nom_structure'] ?? null,
            ':adresse'          => $data['adresse'],
            ':ville'            => $data['ville'],
            ':code_postal'      => $data['code_postal'] ?? null,
            ':animaux_acceptes' => $data['animaux_acceptes'],
            ':capacite_max'     => $data['capacite_max'] ?? 1,
            ':user_id'          => $userId,
        ]);
    }
}

function uploadPhotoPro($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize      = 5 * 1024 * 1024;
    if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) return false;
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('pro_', true) . '.' . $ext;
    $dest     = __DIR__ . '/../images/uploads/' . $filename;
    if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
    return $filename;
}

function saveBase64Photo($dataURL) {
    // dataURL = "data:image/jpeg;base64,xxxx"
    if (!preg_match('/^data:image\/(jpeg|png|webp);base64,/', $dataURL, $m)) return false;
    $ext      = $m[1] === 'jpeg' ? 'jpg' : $m[1];
    $data     = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $dataURL));
    if (!$data) return false;
    $filename = uniqid('pro_', true) . '.' . $ext;
    $dest     = __DIR__ . '/../images/uploads/' . $filename;
    if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
    if (file_put_contents($dest, $data) === false) return false;
    return $filename;
}

/* ══════════════════════════════════
   TARIFS
══════════════════════════════════ */

function getTarifsByPro($proId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("SELECT * FROM tarifs WHERE pro_id = :pro_id");
    $req->bindValue(':pro_id', $proId, PDO::PARAM_INT);
    $req->execute();
    $map = [];
    foreach ($req->fetchAll() as $r) $map[$r['animal_type']] = $r['prix_heure'];
    return $map;
}

function saveTarifs($proId, array $tarifs) {
    $cnx = connexionPDO();
    foreach ($tarifs as $type => $prix) {
        if ($prix === '' || $prix === null) continue;
        $req = $cnx->prepare(
            "INSERT INTO tarifs (pro_id, animal_type, prix_heure)
             VALUES (:pro_id, :type, :prix)
             ON DUPLICATE KEY UPDATE prix_heure = :prix2"
        );
        $req->execute([':pro_id' => $proId, ':type' => $type, ':prix' => (float)$prix, ':prix2' => (float)$prix]);
    }
}

/* ══════════════════════════════════
   CRÉNEAUX
══════════════════════════════════ */

function getCreneauxByPro($proId, $seulementDispo = false) {
    $cnx = connexionPDO();
    $sql = "SELECT c.*,
                   p.capacite_max,
                   (SELECT COUNT(*)
                    FROM reservations r2
                    JOIN creneaux c2 ON r2.creneau_id = c2.id
                    WHERE c2.pro_id = c.pro_id
                      AND r2.statut = 'confirme'
                      AND c2.date_debut <= c.date_fin
                      AND c2.date_fin   >= c.date_debut
                   ) AS places_prises,
                   GREATEST(0, p.capacite_max - (
                    SELECT COUNT(*)
                    FROM reservations r2
                    JOIN creneaux c2 ON r2.creneau_id = c2.id
                    WHERE c2.pro_id = c.pro_id
                      AND r2.statut = 'confirme'
                      AND c2.date_debut <= c.date_fin
                      AND c2.date_fin   >= c.date_debut
                   )) AS places_libres
            FROM creneaux c
            JOIN profils_pro p ON p.user_id = c.pro_id
            WHERE c.pro_id = :pro_id";
    if ($seulementDispo) {
        $sql .= " AND c.statut = 'disponible'
                  AND (SELECT COUNT(*)
                       FROM reservations r2
                       JOIN creneaux c2 ON r2.creneau_id = c2.id
                       WHERE c2.pro_id = c.pro_id
                         AND r2.statut = 'confirme'
                         AND c2.date_debut <= c.date_fin
                         AND c2.date_fin   >= c.date_debut
                      ) < p.capacite_max";
    }
    $sql .= " ORDER BY c.date_debut ASC";
    $req = $cnx->prepare($sql);
    $req->bindValue(':pro_id', $proId, PDO::PARAM_INT);
    $req->execute();
    return $req->fetchAll();
}

function getCreneauById($id) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("SELECT * FROM creneaux WHERE id = :id");
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch() ?: null;
}

function addCreneau($proId, $dateDebut, $dateFin) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "INSERT INTO creneaux (pro_id, date_debut, date_fin) VALUES (:pro_id, :date_debut, :date_fin)"
    );
    $req->execute([':pro_id' => $proId, ':date_debut' => $dateDebut, ':date_fin' => $dateFin]);
    return (int)$cnx->lastInsertId(); // retourne l'ID inséré sur la même connexion
}

function deleteCreneau($id, $proId) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("DELETE FROM creneaux WHERE id=:id AND pro_id=:pro_id AND statut='disponible'");
    return $req->execute([':id' => $id, ':pro_id' => $proId]);
}

/**
 * Vérifie si le pro a déjà une réservation confirmée/en attente qui chevauche la plage demandée.
 */
function hasOverlappingReservation($proId, $dateDebut, $dateFin) {
    $cnx = connexionPDO();
    // Récupérer la capacité du pro
    $reqCap = $cnx->prepare("SELECT capacite_max FROM profils_pro WHERE user_id = :pro_id");
    $reqCap->execute([':pro_id' => $proId]);
    $capaciteMax = (int)($reqCap->fetchColumn() ?: 1);

    // Compter les réservations CONFIRMÉES qui chevauchent la période
    $req = $cnx->prepare(
        "SELECT COUNT(*) FROM creneaux c
         JOIN reservations r ON r.creneau_id = c.id
         WHERE c.pro_id = :pro_id
           AND r.statut = 'confirme'
           AND c.date_debut <= :date_fin
           AND c.date_fin   >= :date_debut"
    );
    $req->execute([':pro_id' => $proId, ':date_debut' => $dateDebut, ':date_fin' => $dateFin]);
    $nbConfirmes = (int)$req->fetchColumn();

    return $nbConfirmes >= $capaciteMax;
}

/**
 * Vérifie que chaque jour de la plage est couvert par au moins un créneau disponible du pro.
 */
function rangeFullyCovered($proId, $dateDebut, $dateFin) {
    $cnx = connexionPDO();
    $req = $cnx->prepare(
        "SELECT date_debut, date_fin FROM creneaux
         WHERE pro_id = :pro_id AND statut = 'disponible'
           AND date_debut <= :date_fin AND date_fin >= :date_debut
         ORDER BY date_debut ASC"
    );
    $req->execute([':pro_id' => $proId, ':date_debut' => $dateDebut . ' 00:00:00', ':date_fin' => $dateFin . ' 23:59:59']);
    $slots = $req->fetchAll();

    // Parcourir chaque jour de la plage demandée
    $cur = new DateTime($dateDebut);
    $end = new DateTime($dateFin);
    while ($cur <= $end) {
        $ds = $cur->format('Y-m-d');
        $covered = false;
        foreach ($slots as $s) {
            $sd = substr($s['date_debut'], 0, 10);
            $sf = substr($s['date_fin'],   0, 10);
            if ($ds >= $sd && $ds <= $sf) { $covered = true; break; }
        }
        if (!$covered) return false;
        $cur->modify('+1 day');
    }
    return true;
}

function updateStatutCreneau($id, $statut) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("UPDATE creneaux SET statut=:statut WHERE id=:id");
    return $req->execute([':statut' => $statut, ':id' => $id]);
}

/**
 * Découpe les créneaux disponibles du pro autour de la période réservée.
 *
 * Exemple : créneau pro = [1 jan → 31 déc], réservation = [10 jan → 17 jan]
 * Résultat : [1 jan → 9 jan] dispo  +  [10 jan → 17 jan] réservé  +  [18 jan → 31 déc] dispo
 *
 * @param int    $proId
 * @param string $resDebut  datetime ex: "2025-01-10 00:00:00"
 * @param string $resFin    datetime ex: "2025-01-17 23:59:59"
 */
function splitAndBlockCreneaux($proId, $resDebut, $resFin) {
    $cnx = connexionPDO();

    // Récupérer tous les créneaux disponibles du pro qui chevauchent la période
    $req = $cnx->prepare(
        "SELECT * FROM creneaux
         WHERE pro_id = :pro_id
           AND statut = 'disponible'
           AND date_debut <= :res_fin
           AND date_fin   >= :res_debut"
    );
    $req->execute([':pro_id' => $proId, ':res_debut' => $resDebut, ':res_fin' => $resFin]);
    $creneaux = $req->fetchAll();

    foreach ($creneaux as $c) {
        $cDebut = new DateTime($c['date_debut']);
        $cFin   = new DateTime($c['date_fin']);
        $rDebut = new DateTime($resDebut);
        $rFin   = new DateTime($resFin);

        // Supprimer le créneau original
        $del = $cnx->prepare("DELETE FROM creneaux WHERE id = :id");
        $del->execute([':id' => $c['id']]);

        // Partie AVANT la réservation (si le créneau commence avant)
        if ($cDebut < $rDebut) {
            $avant = clone $rDebut;
            $avant->modify('-1 second');
            $ins = $cnx->prepare(
                "INSERT INTO creneaux (pro_id, date_debut, date_fin, statut)
                 VALUES (:pro_id, :debut, :fin, 'disponible')"
            );
            $ins->execute([
                ':pro_id' => $proId,
                ':debut'  => $cDebut->format('Y-m-d H:i:s'),
                ':fin'    => $avant->format('Y-m-d H:i:s'),
            ]);
        }

        // Partie RÉSERVÉE (l'intersection)
        $intDebut = max($cDebut, $rDebut);
        $intFin   = min($cFin,   $rFin);
        $ins = $cnx->prepare(
            "INSERT INTO creneaux (pro_id, date_debut, date_fin, statut)
             VALUES (:pro_id, :debut, :fin, 'reserve')"
        );
        $ins->execute([
            ':pro_id' => $proId,
            ':debut'  => $intDebut->format('Y-m-d H:i:s'),
            ':fin'    => $intFin->format('Y-m-d H:i:s'),
        ]);

        // Partie APRÈS la réservation (si le créneau finit après)
        if ($cFin > $rFin) {
            $apres = clone $rFin;
            $apres->modify('+1 second');
            $ins = $cnx->prepare(
                "INSERT INTO creneaux (pro_id, date_debut, date_fin, statut)
                 VALUES (:pro_id, :debut, :fin, 'disponible')"
            );
            $ins->execute([
                ':pro_id' => $proId,
                ':debut'  => $apres->format('Y-m-d H:i:s'),
                ':fin'    => $cFin->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
?>
