<?php
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

require_once("classe/RecrutManager.php");
require_once("classe/Recrut.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

$recrut=new RecrutManager($dbh,'RECRUTE_PROF');

if(isset($_POST) && $_POST !=null)
{

               $stmt = $dbh->prepare("INSERT INTO RECRUTE_PROF(TARIF_HORAIRE, VOLUME_HORAIRE, IDETABLISSEMENT, IDINDIVIDU, IDANNEESSCOLAIRE, TYPES, FK_FORFAIT) VALUES (?, ?, ?, ?, ?, ?, ?)");
               $res = $stmt->execute(array($lib->securite_xss($_POST['TARIF_HORAIRE']), $lib->securite_xss($_POST['VOLUME_HORAIRE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDINDIVIDU']), $lib->securite_xss($_POST['IDANNEESSCOLAIRE']), $lib->securite_xss($_POST['TYPES']), $lib->securite_xss($_POST['FK_FORFAIT'])));
               $urlredirect="";
			   if($res==1)
			   {
                   $id_recrutProf = $dbh->lastInsertId();
                   $matiere = count($lib->securite_xss_array($_POST['IDMATIERE']));
                //   var_dump($matiere);exit;
                   $tabmatiere = $lib->securite_xss_array($_POST['IDMATIERE']);
                   for($j=0; $j< $matiere;$j++)
                   {

                       $stmt1 = $dbh->prepare("INSERT INTO MATIERE_ENSEIGNE(ID_INDIVIDU, ID_MATIERE, IDETABLISSEMENT, IDANNESCOLAIRE) VALUES (?, ?, ?, ?)");
                       $res2 = $stmt1->execute(array($lib->securite_xss($_POST['IDINDIVIDU']), $tabmatiere[$j], $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDANNEESSCOLAIRE'])));


                   }
                   if($res2==1){
                       $class = count($lib->securite_xss_array($_POST['IDCLASSROOM']));
                       $tabclasse = $lib->securite_xss_array($_POST['IDCLASSROOM']);
                       for($i=0; $i< $class;$i++)
                       {
                           $stmt = $dbh->prepare("INSERT INTO CLASSE_ENSEIGNE(IDCLASSROM, IDRECRUTE_PROF, IDANNESCOLAIRE, IDETABLISSEMENT)VALUES (?, ?, ?, ?)");
                           $res1 = $stmt->execute(array($tabclasse[$i], $id_recrutProf, $lib->securite_xss($_POST['IDANNEESSCOLAIRE']), $lib->securite_xss($_POST['IDETABLISSEMENT'])));
                       }
                   }
                   if($res1==1)
                   {
                       $msg = 'Ajout effectué avec succés';
                   }
                   else
                   {
                       $msg = 'Ajout effectué avec echec';
                   }

               }
               header("Location: listeProfesseurRecrutes.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
}

?>