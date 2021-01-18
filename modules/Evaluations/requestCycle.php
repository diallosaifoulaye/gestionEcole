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

$etab = "-1";
if (isset($_SESSION['etab'])) {
    $etab = $lib->securite_xss($_SESSION['etab']);
}

$anneescolaire = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE']))
{
    $anneescolaire = 1;
    //$anneescolaire = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}

$query_rq_classe = $dbh->query("SELECT IDCLASSROOM, LIBELLE FROM CLASSROOM WHERE IDNIVEAU=".$idNiveau);
$classes = array();

while ($class = $query_rq_classe->fetchObject()) {
    $classes[] = $class;
}

$query_rq_periode = $dbh->query("SELECT IDPERIODE, NOM_PERIODE FROM PERIODE WHERE IDNIVEAU=".$idNiveau." AND IDANNEESSCOLAIRE =".$anneescolaire." AND IDETABLISSEMENT=".$etab);
$periodes = array();

while ($periode = $query_rq_periode->fetchObject()) {
    $periodes[] = $periode;
}

$data = array();

$data['classes'] = $classes;
$data['periodes'] = $periodes;

//echo json_encode($classes);
echo json_encode($data);

?>
