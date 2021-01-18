<?php
session_start();
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

require_once("classe/EquipementManager.php");
require_once("classe/Equipement.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(15, $lib->securite_xss($_SESSION['profil'])));

$equip = new EquipementManager($dbh,'EQUIPEMENT');

if(isset($_POST) && $_POST !=null)
{

    $_POST['QTE_RESTANTE'] = $lib->securite_xss($_POST['QTE']);
    /*$_POST['reference'] = $lib->securite_xss($_POST['reference']);
    $_POST['modele'] = $lib->securite_xss($_POST['modele']);
    $_POST['marque'] = $lib->securite_xss($_POST['modele']);
    $_POST['acquisition'] = $lib->securite_xss($_POST['acquisition']);*/


    $res = $equip->insert($lib->securite_xss_array($_POST));
    $urlredirect="";
    if($res==1)
    {
        $msg = 'insertion reussie';
    }
    else{
        $msg = 'insertion echouée';
    }
    header("Location: inventaireEquipement.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
}
?>