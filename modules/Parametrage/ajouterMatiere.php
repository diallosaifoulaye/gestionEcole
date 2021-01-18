<?php
if (!isset($_SESSION)){
    session_start();
}
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

require_once("classe/MatiereManager.php");
require_once("classe/Matiere.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib=new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(5,$lib->securite_xss($_SESSION['profil'])));

$mat=new MatiereManager($dbh,'MATIERE');

if(isset($_POST) && $_POST !=null) {
    $_POST['IDETABLISSEMENT']= base64_decode($lib->securite_xss($_POST['IDETABLISSEMENT']));
    $values_matiere=$_POST;
    $checking_matiere=['LIBELLE','BASE_NOTES','IDNIVEAU','IDETABLISSEMENT'];
    foreach ($values_matiere as $key => $value) {
        if(!in_array($key,$checking_matiere)){
            unset($values_matiere[$key]);
        }
    }

    $res=$mat->insert($lib->securite_xss_array($values_matiere));
    $urlredirect="";

   if($res==1){
       $values_coef=$_POST;
       $checking_coef=['LIBELLE','BASE_NOTES'];
       foreach ($values_coef as $key => $value) {
           if(in_array($key,$checking_coef)){
               unset($values_coef[$key]);
           }
       }

       $query_last_insert = $dbh->query("SELECT MAX(IDMATIERE) as IDMATIERE FROM MATIERE ");
       $rs_mat_etab = $query_last_insert->fetchObject();

       for($i=0; $i < sizeof($values_coef['coef_']);$i++){
           if($values_coef['coef_'][$i] != ""){
               $stmt1 = $dbh->prepare("INSERT INTO COEFFICIENT(COEFFICIENT, IDSERIE, IDMATIERE, IDNIVEAU, IDETABLISSEMENT) VALUES (?, ?, ?, ?, ?)");
               $res1 = $stmt1->execute(array($values_coef['coef_'][$i],$values_coef['serie_'][$i],$rs_mat_etab->IDMATIERE,$values_coef['IDNIVEAU'],$values_coef['IDETABLISSEMENT']));
           }
       }


       $msg = 'Ajout effectué avec succès';


       //$urlredirect="profile.php?msg=$msg&res=$res";
   }
   else{
       $msg = 'Ajout effectué avec echec';
      // $urlredirect="profile.php?msg=$msg&res=$res";


   }
    //header("Location:$urlredirect");
  header("Location: modules.php?msg=".$msg."&res=".$res);
}

?>