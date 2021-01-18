
<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("../../restriction.php");


require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

$updateSQL = "UPDATE CONTROLE SET VALIDER=1 WHERE IDCONTROLE=".$lib->securite_xss(base64_decode($_GET['IDCONTROLE']));
$rest = $dbh->prepare($updateSQL);
$result = $rest->execute();
if($result == true)
{
    $res = 1;
    $msg = "Note validée avec succès";
}
else
{    $res = 0;
    $msg = "Echec de validation de la note";
}
//$updateGoTo = "gestionControle.php";
$updateGoTo = "gestionControle.php?&msg=".$lib->securite_xss($msg)."&res=".$res;

 header(sprintf("Location: %s", $updateGoTo));
?>