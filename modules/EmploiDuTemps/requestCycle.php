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

$query_rq_classe = $dbh->query("SELECT IDCLASSROOM, LIBELLE FROM CLASSROOM WHERE IDNIVEAU=".$idNiveau);
$classes = array();

while ($class = $query_rq_classe->fetchObject()) {
    $classes[] = $class;
}

$query_rq_matiere = $dbh->query("SELECT IDMATIERE, LIBELLE FROM MATIERE WHERE IDNIVEAU=".$idNiveau);
$matieres = array();

while ($matiere = $query_rq_matiere->fetchObject()) {
    $matieres[] = $matiere;
}

$data = array();

$data['classes'] = $classes;
$data['matieres'] = $matieres;

//echo json_encode($classes);
echo json_encode($data);

?>
