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

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(20,$lib->securite_xss($_SESSION['profil'])));
 

$individu=$lib->securite_xss(base64_decode($_POST['individu']));
$champ_note="note".$_POST['individu'];

$note=$lib->securite_xss($_POST["$champ_note"]);

$controle=$lib->securite_xss(base64_decode($_POST['IDCONTROLE']));

$ETABLISSEMNT=$lib->securite_xss($_SESSION['etab']);

$class=$lib->securite_xss(base64_decode($_POST['IDCLASSROOM']));

// verifier sur la l'eleve est deja note
$colname_rq_note_etudiant = "-1";
if (isset($_POST['IDCONTROLE'])) {
  $colname_rq_note_etudiant = $lib->securite_xss(base64_decode($_POST['IDCONTROLE']));
}
$colname_individu_rq_note_etudiant = $lib->securite_xss(base64_decode($_POST['individu']));


$query_rq_note_etudiant = $dbh->query("SELECT * FROM NOTE WHERE IDCONTROLE = ".$colname_rq_note_etudiant." AND NOTE.IDINDIVIDU=".$colname_individu_rq_note_etudiant);

$row_rq_note_etudiant = $query_rq_note_etudiant->fetchAll();
$totalRows_rq_note_etudiant = $query_rq_note_etudiant->rowCount();
// insertion
if($totalRows_rq_note_etudiant==0)
{
    $sql = "INSERT INTO NOTE (IDNOTE, NOTE, IDCONTROLE, IDINDIVIDU, IDETABLISSEMENT) VALUES ('', $note,$controle, $individu, $ETABLISSEMNT)";
    $rest = $dbh->prepare($sql);
    $result = $rest->execute();

    if($result == true)
    {
        $res = 1;
        $msg = "Note ajoutée avec succès";
    }
    else
    {    $res = 0;
        $msg = "Echec d'ajout de la note";
    }
}
// mis a jour de la note
else
{
	$updateSQL = $dbh->query("UPDATE NOTE SET   NOTE= $note WHERE IDCONTROLE=$controle AND IDINDIVIDU= $individu");

	if($updateSQL->execute() == true){
        $msg = "Note mis à jour avec succès";
        $res = 1;
    }
    else{
        $msg = "Echec mis à jour de la note";
        $res = 0;
    }

}
if(isset($_POST['validControle']) && base64_decode($_POST['validControle']) == 1){
    $insertGoTo = "detailControle.php?IDCLASSROOM=".$lib->securite_xss($_POST['IDCLASSROOM']) ."&IDCONTROLE=".$lib->securite_xss($_POST['IDCONTROLE']);
}else {
    $insertGoTo = "noterControle.php?IDCLASSROOM=".$lib->securite_xss($_POST['IDCLASSROOM']) ."&IDCONTROLE=".$lib->securite_xss($_POST['IDCONTROLE'])."&msg=".$lib->securite_xss($msg)."&res=".$res;
}
 echo '<script type="text/javascript" language="javascript">';
echo 'window.location.replace("'.$insertGoTo.'");'; 
echo'</script>';
?>