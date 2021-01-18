

<?php
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

require_once("classe/CategorieEquipementManager.php");
require_once("classe/CategorieEquipement.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(14,$_SESSION['profil']));

$categ=new CategorieEquipementManager($dbh,'CATEGEQUIP');


if(isset($_POST) && $_POST !=null) {

               $res=$categ->insert($_POST);
               $urlredirect="";
			   if($res==1){
			      
                   $msg = 'insertion reussie';


                 
			   }
               else{
                   $msg = 'insertion echouée';
      


			   }
    
  header("Location: accueil.php?msg=".$msg."&res=".$res);
}

?>