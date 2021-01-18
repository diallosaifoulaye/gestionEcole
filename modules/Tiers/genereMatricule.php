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
    if($lib->securite_xss($_POST['IDTYPEINDIVIDU']==8)) {
        $query = "SELECT MATRICULE FROM INDIVIDU WHERE IDTYPEINDIVIDU = 8 ORDER BY IDINDIVIDU DESC";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchObject();
       // var_dump($result->MATRICULE);exit;
        if ($result>0) {
            $leMatricule = sprintf("%04d",$result->MATRICULE + 1);
            echo json_encode($leMatricule);
        } else {
            $leMatricule='0001';
            echo json_encode($leMatricule);
        }
    }elseif($lib->securite_xss($_POST['IDTYPEINDIVIDU']==7)){

        $query = "SELECT MATRICULE FROM INDIVIDU WHERE IDTYPEINDIVIDU = 7 ORDER BY IDINDIVIDU DESC" ;
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchObject();
       // var_dump(substr($result->MATRICULE,6,4));exit;
        if ($result>0) {
            $leMatricule = $_SESSION['PREFIXE'].'P'.sprintf("%04d",substr($result->MATRICULE,6,4) + 1);
            echo json_encode($leMatricule);
        } else {
            $leMatricule=$_SESSION['PREFIXE'].'P0001';
            echo json_encode($leMatricule);
        }
    }

}
