<?php

session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();


if($lib->securite_xss($_SESSION['profil'])!=1)
$lib->Restreindre($lib->Est_autoriser(48, $lib->securite_xss($_SESSION['profil'])));


require_once("classe/ClasseManager.php");
require_once("classe/Classe.php");
$niv=new ClasseManager($dbh,'CLASSROOM');

$colname_rq_classe_etab = "-1";
if (isset($_GET['idClasse'])) {
    $param=json_decode(base64_decode($_GET['idClasse']));
    $colname_rq_classe_etab=$param->id;


}

$res = $niv->supprimer('IDCLASSROOM',$colname_rq_classe_etab);

if ($res==1) {
    $msg="suppression reussie";
}
else{
    $msg="suppression echouee";
}
header("Location: classes.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
?>