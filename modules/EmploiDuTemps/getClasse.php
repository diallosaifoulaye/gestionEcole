<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");

$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();
//var_dump($_POST);exit;
if (isset($_POST) && $_POST != null) {
    $colname_rq_etablissement = "-1";
    if (isset($_SESSION['etab'])) {
        $colname_rq_etablissement = $lib->securite_xss($_SESSION['etab']);
    }
    $query = "SELECT IDCLASSROOM, LIBELLE FROM CLASSROOM
                    WHERE IDNIVEAU = ".$lib->securite_xss($_POST['NIVEAU'])." AND IDETABLISSEMENT = " .$colname_rq_etablissement;
    $stmt = $dbh->prepare($query);
    // var_dump($stmt);exit;
    $stmt->execute();
    $result = $stmt->fetchAll();
    print_r(json_encode($result));

}
