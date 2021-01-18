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




    $query = "SELECT *  FROM DETAIL_TIMETABLE d INNER JOIN EMPLOIEDUTEMPS e ON e.IDEMPLOIEDUTEMPS = d.IDEMPLOIEDUTEMPS
                   WHERE d.DATEDEBUT = '".$chapit[3]."'
                   AND d.DATEFIN = '".$chapit[4]."'
                   AND d.JOUR_SEMAINE =  '".$chapit[2]."'
                   AND d.IDMATIERE = ".$chapit[1]." 
                   AND d.IDETABLISSEMENT = " .$colname_rq_etablissement." 
                   AND d.IDINDIVIDU = ".$chapit[0]."
                   AND e.IDPERIODE=".$chapit[5];

    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $result = $stmt->rowCount();

    if ($result>0) {
        $result1=1;
        echo json_encode($result1);
    } else {
        echo json_encode(0);
    }

}
