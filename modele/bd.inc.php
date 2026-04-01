<?php
function connexionPDO() {
    $login   = "root";
    $mdp     = "";
    $bd      = "pawpaw";
    $serveur = "localhost";
    try {
        $conn = new PDO(
            "mysql:host=$serveur;dbname=$bd;charset=utf8mb4",
            $login, $mdp,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        die("Erreur de connexion PDO : " . $e->getMessage());
    }
}
?>
