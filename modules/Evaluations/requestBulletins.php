<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("../../restriction.php");
require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();


if($_SESSION['profil']!=1)
    $lib->Restreindre($lib->Est_autoriser(20,$lib->securite_xss($_SESSION['profil'])));

$idClassroom = "-1";
if (isset($_POST['IDCLASSROOM']) && $_POST['IDCLASSROOM']!="") {
    $idClassroom = intval($lib->securite_xss(base64_decode($_POST['IDCLASSROOM'])));
}

$idPeriode = "-1";
if (isset($_POST['IDPERIODE']) && $_POST['IDPERIODE']!="") {
    $idPeriode = intval($lib->securite_xss(base64_decode($_POST['IDPERIODE'])));
}

$etab = "-1";
if (isset($_SESSION['etab'])) {
    $etab = $lib->securite_xss($_SESSION['etab']);
}

$anneescolaire = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE']))
{
    $anneescolaire = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}

$query_rq_classe = $dbh->query("SELECT ROWID FROM BULLETIN 
                                          WHERE IDCLASSROOM = ".$idClassroom." 
                                          AND IDANNEE =".$anneescolaire." 
                                          AND IDPERIODE =".$idPeriode." 
                                          AND IDETABLISSEMENT=".$etab);
$count = $query_rq_classe->rowCount();

$data = "";

if($count == 0) {
    $data = 0;
}elseif ($count > 0) {
    $data = -1;
}
echo json_encode($data); die();

?>
