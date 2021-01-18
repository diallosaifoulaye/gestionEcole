<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");

$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if (isset($_POST) && $_POST != null) {
    $colname_rq_etablissement = "-1";
    if (isset($_SESSION['etab'])) {
        $colname_rq_etablissement = $lib->securite_xss($_SESSION['etab']);
    }
    $chapit=explode("_",$lib->securite_xss($_POST['pass']));
     $query = "SELECT *  FROM EMPLOIEDUTEMPS
                   WHERE IDPERIODE = ".$chapit[1]." AND IDCLASSROOM=".$chapit[2]." AND IDETABLISSEMENT = ".$colname_rq_etablissement." AND IDANNEE = ".$lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);

         $stmt = $dbh->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchObject();
        //var_dump($result);exit;
        if ($result>0) {
            $result1=1;
            echo json_encode($result1);
        } else {
            echo json_encode(0);
        }

}
