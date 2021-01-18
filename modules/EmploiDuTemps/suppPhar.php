<?php

session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();




$id = "-1";
if (isset($_GET['idPhar'])) {
    $id=json_decode(base64_decode($_GET['idPhar']));



}
try{

    $query = "DELETE FROM PHAR WHERE ROWID =". $id ;
    $stmt = $dbh->prepare($query);
    $resultat= $stmt->execute();
//var_dump($resultat);die;
    if ($resultat==true) {
        $res = 1;
        $msg="suppression reussie";
    }
    else{
        $res = 0;
        $msg="suppression echouee";
    }
}
catch (PDOException $e){
    echo $e;
}


header("Location: phar.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
?>