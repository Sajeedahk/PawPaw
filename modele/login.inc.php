<?php
include_once "bd.utilisateur.inc.php";

function login($email, $motDePasse) {
    $user = getUserByEmail($email);
    if ($user && password_verify($motDePasse, $user['password'])) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_nom']   = $user['prenom'] . ' ' . $user['nom'];
        $_SESSION['user_role']  = $user['role'];
        // Avatar : colonne users.avatar pour tout le monde
        $_SESSION['user_photo'] = !empty($user['avatar']) ? $user['avatar'] : null;
        return true;
    }
    return false;
}

function logout() {
    unset($_SESSION['user_id'], $_SESSION['user_nom'], $_SESSION['user_role']);
}

function isLoggedOn() {
    return isset($_SESSION['user_id']);
}

function isPro() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pro';
}

function isParticulier() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'particulier';
}

function getUserIdLoggedOn() {
    return $_SESSION['user_id'] ?? null;
}

function getUserNomLoggedOn() {
    return $_SESSION['user_nom'] ?? '';
}
?>
