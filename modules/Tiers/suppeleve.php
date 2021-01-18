x<?php

session_start();
//require_once("restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();
require_once("classe/IndividuManager.php");
require_once("classe/Individu.php");
$ind=new IndividuManager($dbh,'INDIVIDU');

$colname_rq_ind = "-1";


if (isset($_GET['idIndividu'])) {
    $param=json_decode(base64_decode($_GET['idIndividu']));
    $colname_rq_ind=$param->id;
}

$res = $ind->supprimer('IDINDIVIDU',$colname_rq_ind);

if ($res==1) {
    $msg="suppression reussie";

}
else{
    $msg="suppression echouee";
}
echo "<meta http-equiv='refresh' content='0;URL=listeEtudiant.php?msg=$msg&res=$res'>";
?>