<?php

session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(23,$_SESSION['profil']));

require_once("classe/DispenseCoursManager.php");
require_once("classe/DispenseCours.php");
$cours=new DispenseCoursManager($dbh,'DISPENSER_COURS');

$colname_rq_periode_etab = "-1";
if (isset($_GET['IDDISPENSER_COURS'])) {
    $colname_rq_periode_etab = $_GET['IDDISPENSER_COURS'];
}

$res = $cours->supprimer('IDDISPENSER_COURS',$colname_rq_periode_etab);

if ($res==1) {
    $msg="suppression reussie";

}
else{
    $msg="suppression echouee";
}
header ("Location: MAJCours.php?msg=".$msg."&res=".$res);
?>