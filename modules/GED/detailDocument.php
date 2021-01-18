
<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();


if($_SESSION['profil']!=1)
    $lib->Restreindre($lib->Est_autoriser(11,$lib->securite_xss($_SESSION['profil'])));

if (isset($_SESSION['etab']) && isset($_SESSION['ANNEESSCOLAIRE'])) {
    $etab = $lib->securite_xss($_SESSION['etab']);
    $anne = $lib->securite_xss($_SESSION["ANNEESSCOLAIRE"]);
}

$id_docadmin = "-1";
if (isset($_GET['IDINDIVIDU'])) {
    $indiv = $lib->securite_xss(base64_decode($_GET['IDINDIVIDU']));

}



$query_rq_individu = $dbh->query("SELECT INDIVIDU.IDINDIVIDU, MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, PHOTO_FACE, CLASSROOM.LIBELLE as classe, NIVEAU.LIBELLE as niveau
                                            FROM INDIVIDU 
                                            INNER JOIN INSCRIPTION ON INSCRIPTION.IDINDIVIDU = INDIVIDU.IDINDIVIDU 
                                            INNER JOIN NIVEAU ON INSCRIPTION.IDNIVEAU = NIVEAU.IDNIVEAU 
                                            INNER JOIN AFFECTATION_ELEVE_CLASSE ON AFFECTATION_ELEVE_CLASSE.IDINDIVIDU = INDIVIDU.IDINDIVIDU
                                            INNER JOIN CLASSROOM ON AFFECTATION_ELEVE_CLASSE.IDCLASSROOM = CLASSROOM.IDCLASSROOM
                                            WHERE INDIVIDU.IDINDIVIDU = ".$indiv);





$row_rq_individu = $query_rq_individu->fetchObject();



$query_rq_individu1 = $dbh->query("SELECT DOCADMIN.IDDOCADMIN, DOCADMIN.LIBELLE, DOCADMIN.NOM, TYPEDOCADMIN.LIBELLE as typeDoc
                                            FROM `DOCADMIN` 
                                            INNER JOIN TYPEDOCADMIN ON TYPEDOCADMIN.IDTYPEDOCADMIN = DOCADMIN.IDTYPEDOCADMIN

                                            WHERE DOCADMIN.IDINDIVIDU =".$indiv."  AND DOCADMIN.IDANNEESSCOLAIRE=".$anne." AND  DOCADMIN.IDETABLISSEMENT=".$etab);
//$row_rq_individu1 = $query_rq_individu1->fetchAll();

 include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">GED</a></li>
    <li>Liste Documents</li>
    <li>Details</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">
        <div class=" panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4 col-xs-12 ">

                        <div class="panel" style="padding: 20px;">
                            <div class="user-btm-box">
                                <!-- .row    -->
                                <div class="col-sm-12" align="center">
                                    <img class="img-circle" width="200" alt="<?php echo $row_rq_individu->PHOTO_FACE; ?>"
                                         src="../../imgtiers/<?php echo $row_rq_individu->PHOTO_FACE; ?>" style="padding-bottom: 20px;">
                                </div>

                                <div class="row text-center m-t-10">
                                    <div class="col-md-6 b-r"><strong>PRENOM</strong>
                                        <p><?php echo $row_rq_individu->PRENOMS; ?></p>
                                    </div>
                                    <div class="col-md-6"><strong>NOM</strong>
                                        <p><?php echo  $row_rq_individu->NOM; ?></p>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <hr>
                                <!-- .row -->
                                <div class="row text-center m-t-10">
                                    <div class="col-md-6 b-r"><strong>ADRESSE</strong>
                                        <p><?php echo  $row_rq_individu->ADRES; ?></p>
                                    </div>
                                    <div class="col-md-6"><strong>TELEPHONE MOBILE</strong>
                                        <p><?php echo $row_rq_individu->TELMOBILE; ?></p>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <hr>
                                <div class="row text-center m-t-10">
                                    <div class="col-md-6 b-r"><strong>CYCLE</strong>
                                        <p><?php echo $row_rq_individu->niveau; ?></p>
                                    </div>
                                    <div class="col-md-6"><strong>CLASSE</strong>
                                        <p><?php echo $row_rq_individu->classe; ?></p>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <hr>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 col-xs-12">
                        <div class="panel" style="padding: 20px;">

                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="true">
                                        <span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Pièces jointes</span>
                                    </a>
                                </li>

                               <!-- <li role="presentation" class="">
                                    <a href="#scolarite" aria-controls="scolarite" role="tab" data-toggle="tab" aria-expanded="false">
                                        <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Scolarité</span>
                                    </a>
                                </li>

                                <li role="presentation" class="">
                                    <a href="#frais" aria-controls="frais" role="tab" data-toggle="tab" aria-expanded="false">
                                        <span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Frais supplémentaire</span>
                                    </a>
                                </li>

                                <li role="presentation" class="">
                                    <a href="#periode" aria-controls="frais" role="tab" data-toggle="tab" aria-expanded="false">
                                        <span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Bulletion par période</span>
                                    </a>
                                </li>-->

                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content" style="margin-top: 30px;">

                                <div role="tabpanel" class="tab-pane active" id="profile">
                                   <!-- <fieldset ><legend>Liste Fichier</legend>-->
                                        <table id="customers2" class="table datatable">
                                            <thead>
                                            <tr>
                                                <th>LIBELLE</th>
                                                <th>TYPE DE FICHIER</th>
                                                <th>TELECHARGER</th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?
                                            foreach ($query_rq_individu1->fetchAll() as $document) {
                                                /*$even=new Evenement();*/
                                                ?>
                                                <tr>
                                                    <td><?php echo $document['LIBELLE']; ?></td>
                                                    <td><?php echo $document['typeDoc']; ?></td>
                                                    <td>
                                                        <a href="../../document/<?php echo $document['NOM']; ?>" target="_blank" title="Télécharger">
                                                            <i class=" glyphicon glyphicon-download " style="font-size: 21px"></i>

                                                        </a>

                                                    </td>

                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table><!--
                                 -->   </fieldset>
                                   <!-- <fieldset class="cadre"><legend>INFORMATIONS COMPLEMENTAIRES</legend>
                                        <table class="table table-responsive table-striped">
                                            <tr>
                                                <td><strong>PATHOLOGIE</strong></td>
                                                <td style="text-align: center;"><?php /* echo strip_tags( html_entity_decode ($PATHOLOGIE));  */?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>ALLERGIE</strong></td>
                                                <td style="text-align: center;"><?php /* echo strip_tags( html_entity_decode ($ALLERGIE));  */?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>MEDECIN_TRAITANT</strong></td>
                                                <td style="text-align: center;">  <?php /* echo strip_tags( html_entity_decode ($MEDECIN_TRAITANT)); */?> </td>
                                            </tr>

                                        </table>
                                    </fieldset>

-->                                </div>
<!--
                                <div role="tabpanel" class="tab-pane" id="scolarite">

                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>

                                            <th>Num&eacute;ro</th>
                                            <th>Mois</th>
                                            <th>Date facture</th>
                                            <th>Montant</th>
                                            <th>Montant vers&eacute;</th>
                                            <th>Montant restant</th>
                                            <th>Payer</th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php /*foreach ($rs_fact as $row_fact) { */?>
                                            <tr>

                                                <td><?php /*echo $row_fact['NUMFACTURE']; */?></td>
                                                <td><?php /*echo $lib->affiche_mois($row_fact['MOIS']); */?></td>
                                                <td><?php /*echo $lib->date_franc($row_fact['DATEREGLMT']); */?></td>
                                                <td><?php /*echo $lib->nombre_form($row_fact['MONTANT']); */?></td>
                                                <td><?php /*echo $lib->nombre_form($row_fact['MT_VERSE']); */?></td>
                                                <td><?php /*echo $lib->nombre_form($row_fact['MT_RELIQUAT']); */?></td>

                                                <td><?php /*if ($row_fact['ETAT'] == 0) {
                                                        echo "<span class='badge badge-danger'>NON PAYER</span>";

                                                    } */?>
                                                    <?php /*if ($row_fact['ETAT'] == 1) {
                                                        echo "<span class='badge badge-success'>PAYER</span>";
                                                    } */?>
                                                </td>
                                            </tr>

                                        <?php /*} */?>

                                        </tbody>
                                    </table>


                                </div>-->

<!--                                <div role="tabpanel" class="tab-pane" id="frais">-->

<!--                                    <fieldset class="cadre"><legend>FRAIS</legend>
-->                                       <!-- <table class="table table-responsive table-striped">
                                            <?php /*if($FRAIS_DOSSIER>0){*/?>
                                                <tr>
                                                    <td><strong>FRAIS DE DOSSIER</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $FRAIS_DOSSIER;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($FRAIS_EXAMEN>0){*/?>
                                                <tr>
                                                    <td><strong>FRAIS D'EXAMEN</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $FRAIS_EXAMEN;  */?></td>
                                                </tr>
                                            <?php /* } */?>

                                            <?php /*if($UNIFORME>0){*/?>
                                                <tr>
                                                    <td><strong>UNIFORME</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $UNIFORME;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($VACCINATION>0){*/?>
                                                <tr>
                                                    <td><strong>VACCINATION</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $VACCINATION;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($ASSURANCE>0){*/?>
                                                <tr>
                                                    <td><strong>ASSURANCE</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $ASSURANCE;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($FRAIS_SOUTENANCE>0){*/?>
                                                <tr>
                                                    <td><strong>FRAIS DE SOUTENANCE</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $FRAIS_SOUTENANCE;  */?></td>
                                                </tr>
                                            <?php /* } */?>

                                            <?php /*if($MONTANT_TRANSPORT>0){*/?>
                                                <tr>
                                                    <td><strong>MONTANT TRANSPORT</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $MONTANT_TRANSPORT;  */?></td>
                                                </tr>
                                            <?php /* } */?>

                                            <?php /*if($MONTANT_DOSSIER>0){*/?>
                                                <tr>
                                                    <td><strong>MONTANT DOSSIER</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $MONTANT_DOSSIER;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($MONTANT_UNIFORME>0){*/?>
                                                <tr>
                                                    <td><strong>MONTANT UNIFORME</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $MONTANT_UNIFORME;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($MONTANT_VACCINATION>0){*/?>
                                                <tr>
                                                    <td><strong>MONTANT VACCINATION</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $MONTANT_VACCINATION;  */?></td>
                                                </tr>
                                            <?php /* } */?>

                                            <?php /*if($MONTANT_ASSURANCE>0){*/?>
                                                <tr>
                                                    <td><strong>MONTANT ASSURANCE</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $MONTANT_ASSURANCE;  */?></td>
                                                </tr>
                                            <?php /* } */?>
                                            <?php /*if($MONTANT_SOUTENANCE>0){*/?>
                                                <tr>
                                                    <td><strong>MONTANT SOUTENANCE</strong></td>
                                                    <td style="text-align: center;"><?php /* echo $MONTANT_SOUTENANCE;  */?></td>
                                                </tr>
                                            <?php /* } */?>

                                        </table>
                                    </fieldset>
                                </div>


                                <div role="tabpanel" class="tab-pane" id="periode">

                                    <table class="table">

                                        <thead>
                                        <tr>
                                            <th>Périodes</th>
                                            <th style="text-align: center !important;">Voir bulletion</th>
                                        </tr>
                                        </thead>


                                        <tbody>
                                        <?php /*foreach ($rs_bulletin as $row_bulletin) { */?>

                                            <tr>

                                                <td>
                                                    <?php /*echo $row_bulletin['NOM_PERIODE']; */?>
                                                </td>

                                                <td style="text-align: center !important;">
                                                    <a target="_blank" href="../../ged/imprimer_bulletin_individu.php?idIndividu=<?php /*echo base64_encode($id_individu); */?>&idclassroom=<?php /*echo base64_encode($row_bulletin['IDCLASSROOM']);*/?>&periode=<?php /*echo base64_encode($row_bulletin['IDPERIODE']); */?>">
                                                        <i class="glyphicon glyphicon-print"></i>
                                                    </a>
                                                </td>

                                            </tr>

                                        <?php /*} */?>

                                        </tbody>
                                    </table>-->


<!--                                </div>-->


                            </div>
                            <!--                                        </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT WRAPPER -->
    </div>
    <!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->


<?php include('footer.php'); ?>

<script>
    $(function () {
        $('#myTab li:first-child a').tab('show')
    })



</script>

