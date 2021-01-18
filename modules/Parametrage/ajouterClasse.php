<?php
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

require_once("classe/ClasseManager.php");
require_once("classe/Classe.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib=new Librairie();

if($lib->securite_xss($_SESSION['profil'])!=1)
$lib->Restreindre($lib->Est_autoriser(48,$lib->securite_xss($_SESSION['profil'])));

$classe = new ClasseManager($dbh,'CLASSROOM');


if(isset($_POST) && $_POST !=null)
{
               $res=$classe->insert($lib->securite_xss_array($_POST));
               $urlredirect="";
			   if($res==1)
			   {
                   $msg = 'Ajout effectué avec succés';
			   }
               else{
                   $msg = 'Ajout effectué avec echec';
			   }
               header("Location: classes.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
}

?>