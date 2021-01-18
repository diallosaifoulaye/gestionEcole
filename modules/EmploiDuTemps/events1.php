<?php
function getDonnees1(){
 if(!session_status()){
  session_start();
 }


$idIndividu=" ";
if(isset($_SESSION["IDINDIVIDU"]) && $_SESSION["IDINDIVIDU"]!=" " )
{
	$idIndividu="  and DETAIL_TIMETABLE.IDINDIVIDU = ".$_SESSION["IDINDIVIDU"];
}


//$idIndividu= $_SESSION["IDINDIVIDU"];


$idEtablissement = $_SESSION['etab'];


 // connection to the database
 try {
  $bdd = new PDO("mysql:host=localhost;dbname=sunuecole", 'seeyni-faay', 'passer123');
 } catch(Exception $e) {
  exit('Unable to connect to database.');
 }
 
 $requete= $bdd->query("select DETAIL_TIMETABLE.DATEDEBUT, DETAIL_TIMETABLE.DATEFIN, MATIERE.LIBELLE, SALL_DE_CLASSE.NOM_SALLE, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS from DETAIL_TIMETABLE, MATIERE, SALL_DE_CLASSE, INDIVIDU where  DETAIL_TIMETABLE.IDMATIERE = MATIERE.IDMATIERE and DETAIL_TIMETABLE.IDSALL_DE_CLASSE = SALL_DE_CLASSE.IDSALL_DE_CLASSE and DETAIL_TIMETABLE.IDINDIVIDU = INDIVIDU.IDINDIVIDU  ".$idIndividu." and DETAIL_TIMETABLE.IDETABLISSEMENT = ".$idEtablissement);
 
 
 
 $json = array();

foreach ( $requete->fetchAll(PDO::FETCH_ASSOC) as $row) {
    
	 array_push($json, array(
	                          "title" => $row['LIBELLE']."  ".$row['MATRICULE']."  ".$row['PRENOMS']."  ".$row['NOM']."  ".$row['NOM_SALLE'],
                              "start" => $row['DATEDEBUT'],
                              "end" => $row['DATEFIN'],
							  'NOM_SALLE'=>$row['NOM_SALLE'],
							  'LIBELLE'=>$row['LIBELLE'],
							  'PROFESSEUR'=>$row['MATRICULE']."  ".$row['PRENOMS']."  ".$row['NOM'])); 
}
 
//unset($_SESSION['IDINDIVIDU']);



return json_encode($json);
}
?>