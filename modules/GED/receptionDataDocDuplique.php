
<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");


$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1)
    $lib->Restreindre($lib->Est_autoriser(19,$lib->securite_xss($_SESSION['profil'])));


if(isset($_POST) && $_POST !=null)
{

    $libelle = $lib->securite_xss($_POST['LIBELLE']);
    $motif = $lib->securite_xss($_POST['MOTIF']);
    $typeDoc = $lib->securite_xss($_POST['IDTYPEDOCADMIN']);
    $date = $lib->securite_xss($_POST['date_foo']);

    $idindividu = $lib->securite_xss($_POST['IDINDIVIDU']);
    $idTypeindividu = $lib->securite_xss($_POST['IDTYPEINDIVIDU']);



    $msg = "Document Pret";
    $res = "1";


    header("Location: documentTermineDuplique.php?msg=".base64_encode($msg)."&res=".base64_encode($res)."&libelle=".base64_encode($libelle)."&motif=".base64_encode($motif)."&typeDoc=".base64_encode($typeDoc)."&idindividu=".base64_encode($idindividu)."&idTypeindividu=".base64_encode($idTypeindividu));


}

?>