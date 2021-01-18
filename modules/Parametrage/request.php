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

$idNiveau = "-1";
if (isset($_POST['IDNIVEAU']) && $_POST['IDNIVEAU']!="") {
    $idNiveau = intval($lib->securite_xss(base64_decode($_POST['IDNIVEAU'])));
}

$query_rq_cycle = $dbh->query("SELECT IDSERIE, LIBSERIE FROM SERIE WHERE IDNIVEAU=".$idNiveau);
$series = array();

while ($serie = $query_rq_cycle->fetchObject()) {
    $series[] = $serie;
}

echo json_encode($series);

?>
