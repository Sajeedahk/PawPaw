<?php
include_once "bd.inc.php";

function getUserByEmail($email) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("SELECT * FROM users WHERE email = :email");
    $req->bindValue(':email', $email, PDO::PARAM_STR);
    $req->execute();
    return $req->fetch() ?: null;
}

function getUserById($id) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("SELECT * FROM users WHERE id = :id");
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch() ?: null;
}

function updateUserAvatar($userId, $filename) {
    $cnx = connexionPDO();
    $req = $cnx->prepare("UPDATE users SET avatar=:avatar WHERE id=:id");
    return $req->execute([':avatar' => $filename, ':id' => $userId]);
}

function addUser($nom, $prenom, $email, $motDePasse, $role = 'particulier', $telephone = null) {
    $cnx   = connexionPDO();
    $check = $cnx->prepare("SELECT id FROM users WHERE email = :email");
    $check->bindValue(':email', $email, PDO::PARAM_STR);
    $check->execute();
    if ($check->fetch()) return false;

    $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
    $req  = $cnx->prepare(
        "INSERT INTO users (nom, prenom, email, password, role, telephone)
         VALUES (:nom, :prenom, :email, :password, :role, :telephone)"
    );
    return $req->execute([
        ':nom'       => $nom,
        ':prenom'    => $prenom,
        ':email'     => $email,
        ':password'  => $hash,
        ':role'      => $role,
        ':telephone' => $telephone,
    ]);
}
?>
