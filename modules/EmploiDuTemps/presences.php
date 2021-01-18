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
$lib->Restreindre($lib->Est_autoriser(23,$_SESSION['profil']));

$id = $_POST['IDDISPENSER_COURS'];
$deleteSQL = $dbh->query("DELETE FROM PRESENCE_ELEVES WHERE IDDISPENSER_COURS=".$id);
$etablissement = $_SESSION['etab'];
$dipensercours = $_POST['IDDISPENSER_COURS'];


        //for($i=0;$i< count($_SESSION['eleve_presente']) ;$i++)
        foreach($_POST['individu'] as $row)
		{
            $retard = $_POST['retard'.$row];
		    $presences = $_POST['presences'.$row];


			$individu = $row;


                try
                {
                    $sql = "INSERT INTO PRESENCE_ELEVES (ETAT_PRESENCE, RETARD, IDETABLISSEMENT, IDDISPENSER_COURS, IDINDIVIDU) 
                             VALUES (:ETAT_PRESENCE, :RETARD, :IDETABLISSEMENT, :IDDISPENSER_COURS, :IDINDIVIDU)";

                    if($presences == 'on' && $retard==NULL)//presences = abscence
                    {
                        $present =1;
                        $retards = 0;
                        $query_SQL = $dbh->prepare($sql);
                        $query_SQL->bindParam(":ETAT_PRESENCE", $present);
                        $query_SQL->bindParam(":RETARD", $retards);
                        $query_SQL->bindParam(":IDETABLISSEMENT", $etablissement);
                        $query_SQL->bindParam(":IDDISPENSER_COURS", $dipensercours);
                        $query_SQL->bindParam(":IDINDIVIDU", $individu);


                    }
                    elseif($retard == 'on' && $presences == NULL){
                        $present =0;
                        $retards =1;
                        $query_SQL = $dbh->prepare($sql);
                        $query_SQL->bindParam(":ETAT_PRESENCE", $present);
                        $query_SQL->bindParam(":RETARD", $retards);
                        $query_SQL->bindParam(":IDETABLISSEMENT", $etablissement);
                        $query_SQL->bindParam(":IDDISPENSER_COURS", $dipensercours);
                        $query_SQL->bindParam(":IDINDIVIDU", $individu);
                    }
                    elseif($presences == NULL && $retard==NULL){
                        $present =0;
                        $retards =0;
                        $query_SQL = $dbh->prepare($sql);
                        $query_SQL->bindParam(":ETAT_PRESENCE", $present);
                        $query_SQL->bindParam(":RETARD", $retards);
                        $query_SQL->bindParam(":IDETABLISSEMENT", $etablissement);
                        $query_SQL->bindParam(":IDDISPENSER_COURS", $dipensercours);
                        $query_SQL->bindParam(":IDINDIVIDU", $individu);

                    }
                    else{
                        $present =0;
                        $retards =0;
                        $query_SQL = $dbh->prepare($sql);
                        $query_SQL->bindParam(":ETAT_PRESENCE", $present);
                        $query_SQL->bindParam(":RETARD", $retards);
                        $query_SQL->bindParam(":IDETABLISSEMENT", $etablissement);
                        $query_SQL->bindParam(":IDDISPENSER_COURS", $dipensercours);
                        $query_SQL->bindParam(":IDINDIVIDU", $individu);

                        var_dump(2);
                    }
                    $query_SQL->execute();


                }catch (PDOException $exception){
                    echo $exception;
                }

        }











    $deleteGoTo = "presenceCours.php?IDDISPENSER_COURS=".$_POST['IDDISPENSER_COURS'];
      unset($_SESSION['eleve_presente']);
      header(sprintf("Location: %s", $deleteGoTo));
  ?>