
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
$lib->Restreindre($lib->Est_autoriser(22,$lib->securite_xss($_SESSION['profil'])));
$idIDINDIVIDU= base64_decode($lib->securite_xss($_GET['IDINDIVIDU']));
$nomProf = str_replace("-","  ",base64_decode($lib->securite_xss($_GET['NOM'])));
$_SESSION["IDINDIVIDU"]=$idIDINDIVIDU;

function calendar($jour, $date_deb, $date_fin, $idIDINDIVIDU, $periode)
{
    require_once("../../config/Connexion.php");
    require_once ("../../config/Librairie.php");
    $connection =  new Connexion();
    $dbh = $connection->Connection();
    $lib =  new Librairie();

    try
    {
        $query = "SELECT DETAIL_TIMETABLE.JOUR_SEMAINE as jour,DETAIL_TIMETABLE.DATEDEBUT as debut, 
              DETAIL_TIMETABLE.DATEFIN as fin,MATIERE.LIBELLE as libmatiere,SALL_DE_CLASSE.NOM_SALLE as libsalle,CLASSROOM.LIBELLE as libclass
              FROM   DETAIL_TIMETABLE
              INNER JOIN MATIERE ON DETAIL_TIMETABLE.IDMATIERE = MATIERE.IDMATIERE
              INNER JOIN SALL_DE_CLASSE ON DETAIL_TIMETABLE.IDSALL_DE_CLASSE = SALL_DE_CLASSE.IDSALL_DE_CLASSE
              INNER JOIN EMPLOIEDUTEMPS ON DETAIL_TIMETABLE.IDEMPLOIEDUTEMPS = EMPLOIEDUTEMPS.IDEMPLOIEDUTEMPS
              INNER JOIN CLASSROOM ON EMPLOIEDUTEMPS.IDCLASSROOM = CLASSROOM.IDCLASSROOM
              WHERE EMPLOIEDUTEMPS.IDANNEE =".$lib->securite_xss($_SESSION["ANNEESSCOLAIRE"])." 
              AND DETAIL_TIMETABLE.IDINDIVIDU =".$idIDINDIVIDU."
              AND DETAIL_TIMETABLE.JOUR_SEMAINE ='".$jour."'
              AND DETAIL_TIMETABLE.DATEDEBUT ='".$date_deb."'
              AND DETAIL_TIMETABLE.DATEFIN ='".$date_fin."'
              AND EMPLOIEDUTEMPS.IDPERIODE = ".$periode."
              ORDER BY DETAIL_TIMETABLE.DATEDEBUT ASC  ";


        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $tabEmp = $stmt->fetchObject();
        return $tabEmp;
    }
    catch (PDOException $e)
    {
        return -2;
    }
}



?>
<?php include('header.php'); ?>
<ul class="breadcrumb">
    <li><a href="#"> Emploi du Temps </a></li>
    <li> Emploi du temps professeur</li>
</ul>
<div class="page-content-wrap">
    <!-- START WIDGETS -->

    <div class="row">

        <div class="panel panel-default">
            <div class="panel-heading">
                &nbsp;&nbsp;&nbsp;
                <div class="btn-group pull-right">

                    <a href="../../ged/imprimer_EmploiTpsProf.php?IDINDIVIDU=<?= base64_encode($idIDINDIVIDU) ; ?>&NOM=<?= $lib->securite_xss($_GET['NOM']); ?>" target="_blank">
                        <img src="../../images/bt_imprimer.png" width="99" height="27" />
                    </a>

                </div>

            </div>
            <div>
                <p><h4 style="color:#E05D1F;margin-left: 15px;">Emploi du temps du professeur  : <?= $nomProf; ?></h4></p>
            </div>
            <div class="panel-body">
                <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){
                    if(isset($_GET['res']) && $_GET['res']==1)  {?>
                        <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']) ; ?>
                        </div>
                    <?php  }
                    if(isset($_GET['res']) && $_GET['res']!=1)  {?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?>
                        </div>
                    <?php } ?>
                <?php } ?>


                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="color: #2F4686;text-align: center;" width="10%">HEURES <br/>JOURS </th>
                        <th style="color: #2F4686;text-align: center;" width="13%">08H30 - 09H30</th>
                        <th style="color: #2F4686;text-align: center;" width="13%">09H30 - 10H30</th>
                        <th style="color: #2F4686;text-align: center;" width="13%">11H00 - 12H00</th>
                        <th style="color: #2F4686;text-align: center;" width="13%">12H00 - 13H00</th>
                        <th style="color: #2F4686;text-align: center;" width="13%">13H30 - 14H30</th>
                        <th style="color: #2F4686;text-align: center;" width="13%">14H30 - 15H30</th>
                        <th style="color: #2F4686;text-align: center;" width="13%">15H30 - 16H30</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                    <td style="color: #2F4686;text-align: left;"><strong>LUNDI</strong></td>


                            <td style='color: #56688A;'>
                                <?php
                                $result = calendar("LUN", '08:30', '09:30', $idIDINDIVIDU);

                                if ($result!=false) {


                                    print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                                  } ?>
                            </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("LUN", '09:30', '10:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("LUN", '11:00', '12:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("LUN", '12:00', '13:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("LUN", '13:30', '14:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("LUN", '14:30', '15:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("LUN", '15:30', '16:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>




                    </tr>

                    <tr>
                    <td style="color: #2F4686;text-align: left;"><strong>MARDI</strong></td>


                            <td style='color: #56688A;'>
                                <?php
                                $result = calendar("MAR", '08:30', '09:30', $idIDINDIVIDU);

                                if ($result!=false) {


                                    print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                                  } ?>
                            </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MAR", '09:30', '10:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MAR", '11:00', '12:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MAR", '12:00', '13:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MAR", '13:30', '14:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MAR", '14:30', '15:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MAR", '15:30', '16:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>




                    </tr>

                    <tr>
                    <td style="color: #2F4686;text-align: left;"><strong>MERCREDI</strong></td>


                            <td style='color: #56688A;'>
                                <?php
                                $result = calendar("MER", '08:30', '09:30', $idIDINDIVIDU);

                                if ($result!=false) {


                                    print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                                  } ?>
                            </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MER", '09:30', '10:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MER", '11:00', '12:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MER", '12:00', '13:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MER", '13:30', '14:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MER", '14:30', '15:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("MER", '15:30', '16:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>




                    </tr>



 <tr>
                    <td style="color: #2F4686;text-align: left;"><strong>JEUDI</strong></td>


                            <td style='color: #56688A;'>
                                <?php
                                $result = calendar("JEU", '08:30', '09:30', $idIDINDIVIDU);

                                if ($result!=false) {


                                    print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                                  } ?>
                            </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("JEU", '09:30', '10:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("JEU", '11:00', '12:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("JEU", '12:00', '13:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("JEU", '13:30', '14:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("JEU", '14:30', '15:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("JEU", '15:30', '16:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>




                    </tr>

                    <tr>
                        <td style="color: #2F4686;text-align: left;"><strong>VENDREDI</strong></td>


                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '08:30', '09:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '09:30', '10:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '11:00', '12:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '12:00', '13:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '13:30', '14:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '14:30', '15:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("VEN", '15:30', '16:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>




                    </tr>

 <tr>
                    <td style="color: #2F4686;text-align: left;"><strong>SAMDREDI</strong></td>


                            <td style='color: #56688A;'>
                                <?php
                                $result = calendar("SAM", '08:30', '09:30', $idIDINDIVIDU);

                                if ($result!=false) {


                                    print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                                  } ?>
                            </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("SAM", '09:30', '10:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("SAM", '11:00', '12:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("SAM", '12:00', '13:00', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("SAM", '13:30', '14:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("SAM", '14:30', '15:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>

                        <td style='color: #56688A;'>
                            <?php
                            $result = calendar("SAM", '15:30', '16:30', $idIDINDIVIDU);

                            if ($result!=false) {


                                print "<b>".$result->libclass."</b><br/><b>salle</b> : ".$result->libsalle."<br/>";

                            } ?>
                        </td>




                    </tr>



                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<?php include('footer.php'); ?>