<?php
if (!isset($_SESSION)){
    session_start();
}
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");



$connection =  new Connexion();
$dbh = $connection->Connection();
$lib=new Librairie();


$etab = $lib->securite_xss($_SESSION['etab']);
$libelle=$lib->securite_xss($_POST["LIBELLE"]);
$description= strip_tags( html_entity_decode ($lib->securite_xss($_POST["DESCRIPTION"])));
$matiere=$lib->securite_xss($_POST["FK_MATIERE"]);
$niveau=$lib->securite_xss($_POST["FK_NIVEAU"]);
$CYCLE =$lib->securite_xss($_POST["NIVEAU"]);
if(isset($_POST) && $_POST !=null)
{
    try{
        $sql = "INSERT INTO PHAR (LIBELLE, DESCRIPTION, FK_MATIERE, FK_NIVEAU,FK_CYCLE, IDETABLISSEMENT) VALUES ('".$libelle."','".$description."', $matiere, $niveau, $CYCLE, $etab)";

        $rest = $dbh->prepare($sql);
        $result = $rest->execute();

        if($result == true)
        {
            $res = 1;
            $msg = "Phar ajouté avec succès";
        }
        else
        {    $res = 0;
            $msg = "Echec d'ajout phar";
        }
    }

    catch (PDOException $e){
        echo $e;
    }


    header("Location: phar.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));


}

?>

