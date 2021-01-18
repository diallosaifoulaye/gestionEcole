
<?php

session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(22,$_SESSION['profil']));

require_once("classe/EmploiTempsManager.php");
require_once("classe/EmploiTemps.php");
$temps=new EmploiTempsManager($dbh,'EMPLOIEDUTEMPS');

//var_dump(base64_decode($lib->securite_xss($_GET['idET'])));exit;
$colname_rq_periode_etab = "-1";
if (isset($_GET['idET'])) {
    $colname_rq_periode_etab = base64_decode($lib->securite_xss($_GET['idET']));
}

$res = $temps->supprimer('IDEMPLOIEDUTEMPS',$colname_rq_periode_etab);


if ($res==1) {
    $res1 = $dbh->exec("delete from DETAIL_TIMETABLE where IDEMPLOIEDUTEMPS=".$colname_rq_periode_etab);
    if ($res1){
        $msg="suppression reussie";
    } else {
        $msg="suppression echouee";
    }
}
else{
    $msg="suppression echouee";
}
header ("Location: accueil.php?msg=".$msg."&res=".$res);
?>