

<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

require_once("classe/TypeControleManager.php");
require_once("classe/TypeControle.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(19,$lib->securite_xss($_SESSION['profil'])));

$type = new TypeControleManager($dbh,'TYP_CONTROL');

if(isset($_POST) && $_POST !=null)
{
    $_POST['IDETABLISSEMENT'] = $lib->securite_xss(base64_decode($_POST['IDETABLISSEMENT']));
    $res = $type->insert($lib->securite_xss_array($_POST));
    $urlredirect="";
    if($res==1)
    {
        $msg = 'insertion reussie';
    }
    else{
        $msg = 'insertion echouee';
    }
    header("Location: accueil.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
}

?>