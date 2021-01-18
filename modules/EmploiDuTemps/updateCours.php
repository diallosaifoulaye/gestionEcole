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

if(isset($_POST) && $_POST !=null) {
    $res = $cours->modifier($_POST,'IDDISPENSER_COURS',$_GET['IDDISPENSER_COURS']);
    $msg = $res == 1 ? "Modification cours reussie" : "Modification cours échouée";
    echo "<meta http-equiv='refresh' content='0;URL=MAJCours.php?msg=$msg&res=$res'>";
}