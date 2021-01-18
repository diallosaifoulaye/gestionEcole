
<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("restriction.php");


require_once("../config/Connexion.php");
require_once ("../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

require_once('classe/IndividuManager.php');
$ind=new IndividuManager($dbh,'INDIVIDU');


$colname_rq_etablissement = "-1";
if (isset($_SESSION['etab'])) {
  $colname_rq_etablissement = $_SESSION['etab'];
}

$colname_rq_idindividu = "-1";
if (isset($_SESSION['id'])) {
  $colname_rq_idindividu = $_SESSION['id'];
}

$colname_rq_annee = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
  $colname_rq_annee = $_SESSION['ANNEESSCOLAIRE'];
}

$query_rq_periode = "SELECT `IDPERIODE`, `NOM_PERIODE`, `DEBUT_PERIODE`, `FIN_FPERIODE`, `IDANNEESSCOLAIRE`, `IDETABLISSEMENT`, `IDNIVEAU`
                                          FROM `PERIODE`
                                          WHERE IDNIVEAU=".$lib->securite_xss($_SESSION["IDNIVEAU"]);

        $stmtPeriode = $dbh->prepare($query_rq_periode);
        $stmtPeriode->execute();
        $rstPeriode = $stmtPeriode->fetchAll();

if(isset($_POST['idperiode']) && $_POST['idperiode']!='') {
    $idPeriode= $lib->securite_xss($_POST['idperiode']);
    $idClasse=$lib->securite_xss($_SESSION["IDCLASSROOM"]);
function calendar($jour, $date_deb, $date_fin, $idPeriode, $idClasse){
    require_once("../config/Connexion.php");
    require_once ("../config/Librairie.php");
    $connection =  new Connexion();
    $dbh = $connection->Connection();
    $lib =  new Librairie();

    $query = "SELECT DETAIL_TIMETABLE.JOUR_SEMAINE as jour,DETAIL_TIMETABLE.DATEDEBUT as debut,DETAIL_TIMETABLE.DATEFIN as fin,MATIERE.LIBELLE as libmatiere,SALL_DE_CLASSE.NOM_SALLE as libsalle,INDIVIDU.PRENOMS as prenom,INDIVIDU.NOM as nom
          FROM   DETAIL_TIMETABLE
          INNER JOIN MATIERE ON DETAIL_TIMETABLE.IDMATIERE = MATIERE.IDMATIERE
          INNER JOIN SALL_DE_CLASSE ON DETAIL_TIMETABLE.IDSALL_DE_CLASSE = SALL_DE_CLASSE.IDSALL_DE_CLASSE
          INNER JOIN EMPLOIEDUTEMPS ON DETAIL_TIMETABLE.IDEMPLOIEDUTEMPS = EMPLOIEDUTEMPS.IDEMPLOIEDUTEMPS
          INNER JOIN INDIVIDU ON DETAIL_TIMETABLE.IDINDIVIDU = INDIVIDU.IDINDIVIDU
          WHERE EMPLOIEDUTEMPS.IDPERIODE = ".$idPeriode." AND EMPLOIEDUTEMPS.IDANNEE = ".$lib->securite_xss($_SESSION["ANNEESSCOLAIRE"])." 
          AND EMPLOIEDUTEMPS.IDCLASSROOM = ".$idClasse ."
          AND DETAIL_TIMETABLE.JOUR_SEMAINE ='".$jour."'
          AND DETAIL_TIMETABLE.DATEDEBUT ='".$date_deb."'
          AND DETAIL_TIMETABLE.DATEFIN ='".$date_fin."'
          ORDER BY DETAIL_TIMETABLE.DATEDEBUT ASC";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $tabEmp = $stmt->fetchObject();
    return $tabEmp;
}
}
?>


            <?php include('header.php'); ?>
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#"> ELEVE/ETUDIANT </a></li>                    
                    <li class="active">Emploi du Temps </li>
                </ul>
                <!-- END BREADCRUMB -->                       
                
                <!-- PAGE CONTENT WRAPPER -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

    <!-- START WIDGETS -->
    <div class="row">

        <div class="panel panel-default">
                 <div class="panel-body">
                    <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){

                        if(isset($_GET['res']) && $_GET['res']==1)
                        {?>
                            <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo $_GET['msg']; ?></div>
                        <?php  }
                        if(isset($_GET['res']) && $_GET['res']!=1)
                        {?>
                            <div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo $_GET['msg']; ?></div>
                        <?php }
                        ?>

                    <?php } ?>
                    
                    <!-- START WIDGETS -->
                    <fieldset class="cadre"> <legend >CHOIX PERIODE</legend>
                    <div class="row">
                        <form id="form" name="form1" method="post" action="mesEmploisTemps.php" class="form-inline">
                                <div class="col-lg-2">
                                </div>
                                    <div class="col-lg-8">
                                    <label>Choisissez la période : </label>
                                    <select  id="idperiode" class="validate[required] form-control" required name="idperiode" style="width: 80%"  onchange="CButton();">
                                        <option value=""> --Selectionner--</option>
                                        <?php foreach ($rstPeriode as $row_rq_periode) { ?>
                                            <option
                                                    value="<?php echo $row_rq_periode['IDPERIODE'] ?>" <?php if($lib->securite_xss($_POST['idperiode'])== $row_rq_periode['IDPERIODE']) echo "selected=selected";?>><?php echo $row_rq_periode['NOM_PERIODE'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                </div>

                                <div class="col-lg-1">
                                    <button type="submit" id="valider" class="btn btn-primary pull-right" style="display: none;">Valider</button>

                                </div>
                            <div class="col-lg-1">
                            </div>

                        </form>
                    </fieldset>

                    
                <?php if(isset($_POST['idperiode']) && $_POST['idperiode']!='') { ?>
                
                    <div class="btn-group pull-right" style="padding-bottom: 15px;">
                
                        <a href="../ged/imprimer_EmploiTpsEleve.php?IDPERIODE=<?php echo base64_encode($lib->securite_xss($_POST['idperiode'])) ; ?>"><img src="../images/bt_imprimer.png" width="99" height="27" /></a>
                
                    </div>
                    <br/>
                     <table class="table table-bordered">
                         <thead>
                         <tr>
                             <th style="color: #2F4686;text-align: center;" width="17%">HEURES <br/>JOURS</th>
                             <th style="color: #2F4686;text-align: center;" width="17%">LUNDI</th>
                             <th style="color: #2F4686;text-align: center;" width="17%">MARDI</th>
                             <th style="color: #2F4686;text-align: center;" width="17%">MERCREDI</th>
                             <th style="color: #2F4686;text-align: center;" width="17%">JEUDI</th>
                             <th style="color: #2F4686;text-align: center;" width="16%">VENDREDI</th>
                             <th style="color: #2F4686;text-align: center;" width="16%">SAMEDI</th>
                         </tr>
                         </thead>
                         <tbody>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>08H30 - 09H30</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '08:30', '09:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '08:30', '09:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '08:30', '09:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '08:30', '09:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '08:30', '09:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '08:30', '09:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>09H30 - 10H30</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '09:30', '10:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '09:30', '10:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '09:30', '10:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '09:30', '10:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '09:30', '10:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '09:30', '10:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>11H00 - 12H00</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '11:00', '12:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '11:00', '12:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '11:00', '12:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '11:00', '12:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '11:00', '12:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '11:00', '12:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>12H00 - 13H00</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '12:00', '13:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '12:00', '13:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '12:00', '13:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '12:00', '13:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '12:00', '13:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '12:00', '13:00', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>13H30 - 14H30</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '13:30', '14:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '13:30', '14:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '13:30', '14:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '13:30', '14:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '13:30', '14:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '13:30', '14:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>14H30 - 15H30</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '14:30', '15:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '14:30', '15:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '14:30', '15:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '14:30', '15:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '14:30', '15:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '14:30', '15:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>

                         <tr>
                             <td style="color: #2F4686;text-align: left;"><strong>15H30 - 16H30</strong></td>


                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("LUN", '15:30', '16:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MAR", '15:30', '16:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("MER", '15:30', '16:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("JEU", '15:30', '16:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("VEN", '15:30', '16:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>

                             <td style='color: #56688A;'>
                                 <?php
                                 $result = calendar("SAM", '15:30', '16:30', $idPeriode, $idClasse);

                                 if ($result!=false) {


                                     print "<b>".$result->prenom." ".$result->nom."</b><br/><b>".$result->libmatiere."</b><br/>";

                                 } ?>
                             </td>





                         </tr>



                         </tbody>
                     </table>

                  <?php   }   ?>

                </div>
        </div>

                <!-- END PAGE CONTENT WRAPPER -->                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->
        
<script>
    function CButton() {
        var selectPeriode = $("#idperiode").find("option:selected").val();
        if(selectPeriode!='') {
            $('#valider').css("display", "block");
        }else{
            $('#valider').css("display", "none");

        }
    }
</script>
<?php include('footer.php'); ?>