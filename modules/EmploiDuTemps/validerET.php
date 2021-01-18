<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");

$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

//var_dump($_SESSION);exit;
//IDPERIODE 	IDETABLISSEMENT 	IDCLASSROOM 
// DATEDEBUT 	DATEFIN 	JOUR_SEMAINE 	IDEMPLOIEDUTEMPS	IDMATIERE 	IDSALL_DE_CLASSE 	IDETABLISSEMENT 	IDINDIVIDU 	REPETITION -->
$tabET = explode(",", $lib->securite_xss($_POST['tabET']));

$stmt1 = $dbh->prepare("INSERT INTO EMPLOIEDUTEMPS(IDPERIODE, IDETABLISSEMENT, IDCLASSROOM, IDANNEE) VALUES (?, ?, ?, ?)");
$res1 = $stmt1->execute(array($lib->securite_xss($_GET['IDPERIODE']), $lib->securite_xss($_GET['IDETABLISSEMENT']), $lib->securite_xss($_GET['IDCLASSROOM']), $lib->securite_xss($_SESSION["ANNEESSCOLAIRE"])));
$idET = $dbh->lastInsertId();
foreach ($tabET as $oneET) {
    $oneET = explode("-", $oneET);

    $stmt = $dbh->prepare("INSERT INTO DETAIL_TIMETABLE(DATEDEBUT,     DATEFIN, JOUR_SEMAINE, IDEMPLOIEDUTEMPS, IDMATIERE, 	IDSALL_DE_CLASSE, IDETABLISSEMENT, IDINDIVIDU, REPETITION) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $res = $stmt->execute(array($oneET[1], $oneET[2], $oneET[3], $idET, $oneET[4], $oneET[5], $lib->securite_xss($_GET['IDETABLISSEMENT']), $oneET[6], 0));
}
echo json_encode(1);