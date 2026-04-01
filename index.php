<?php
session_start();
require_once "getRacine.php";
require_once "$racine/modele/bd.inc.php";
require_once "$racine/modele/bd.utilisateur.inc.php";
require_once "$racine/modele/login.inc.php";
require_once "$racine/controleur/controleurPrincipal.php";

define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

$action = $_GET['action'] ?? 'defaut';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

$fichier = controleurPrincipal($action);
include "$racine/controleur/$fichier";
?>
