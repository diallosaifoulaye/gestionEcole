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
   $INDIV=substr(strstr($lib->securite_xss($_POST['IDINDIVIDU']),"-"),1) ;
        $query = "SELECT MA.IDMATIERE, MA.LIBELLE FROM MATIERE MA 
                  INNER JOIN MATIERE_ENSEIGNE on MATIERE_ENSEIGNE.ID_MATIERE= MA.IDMATIERE 
                  INNER JOIN RECRUTE_PROF ON RECRUTE_PROF.IDINDIVIDU=MATIERE_ENSEIGNE.ID_INDIVIDU 
                  WHERE RECRUTE_PROF.IDINDIVIDU = ".$INDIV." AND MA.IDETABLISSEMENT = " .$colname_rq_etablissement;
        $stmt = $dbh->prepare($query);


        $stmt->execute();
        $result = $stmt->fetchAll();
         print_r(json_encode($result));

}
