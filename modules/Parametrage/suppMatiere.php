<?php

session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();


if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(5,$lib->securite_xss($_SESSION['profil'])));


require_once("classe/MatiereManager.php");
require_once("classe/Matiere.php");
$mat=new MatiereManager($dbh,'MATIERE');

$colname_rq_mat_etab = "-1";
if (isset($_GET['idMatiere'])) {
    $colname_rq_mat_etab = $lib->securite_xss(base64_decode($_GET['idMatiere']));
}
//var_dump($dbh->MATIERE->IDMATIERE);die;

$res = $mat->supprimer('IDMATIERE',$colname_rq_mat_etab);
if ($res==1) {
    $dbh->exec("DELETE FROM COEFFICIENT WHERE IDMATIERE=".$colname_rq_mat_etab);
    $msg="suppression reussie";

}
else{
    $msg="suppression echouee";
}
header("Location: modules.php?msg=".$msg."&res=".$res);
?>