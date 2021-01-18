<?php

session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(17,$_SESSION['profil']));


require_once("classe/TypeDocManager.php");
require_once("classe/TypeDoc.php");
$type=new TypeDocManager($dbh,'TYPEDOCADMIN');

$colname_rq_annee_etab = "-1";
if (isset($_GET['IDTYPEDOC'])) {
    $colname_rq_annee_etab = $lib->securite_xss(base64_decode($_GET['IDTYPEDOC']));
}

$res = $type->supprimer('IDTYPEDOCADMIN',$colname_rq_annee_etab);

if ($res==1) {
    $msg="suppression reussie";

}
else{
    $msg="suppression echouee";
}
echo "<meta http-equiv='refresh' content='0;URL=accueil.php?msg=$msg&res=$res'>";
?>