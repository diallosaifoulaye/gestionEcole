<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");

require_once("classe/TypeDocManager.php");
require_once("classe/TypeDoc.php");

$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(17, $_SESSION['profil']));

$type = new TypeDocManager($dbh, 'TYPEDOCADMIN');


if (isset($_POST) && $_POST != null) {

    $res = $type->insert($_POST);
    $urlredirect = "";
    if ($res == 1) {
        $msg = 'Ajout effectué avec succès';
        $urlredirect = "accueil.php?msg=$msg&res=$res";
    } else {
        $msg = 'Votre ajout a échoué';
        $urlredirect = "accueil.php?msg=$msg&res=$res";
    }
    header("Location: $urlredirect");
    // echo "<meta http-equiv='refresh' content='0;URL=accueil.php?msg=$msg&res=$res'>";
}