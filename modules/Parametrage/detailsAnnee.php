
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


require_once("classe/AnneeScolaireManager.php");
require_once("classe/AnneeScolaire.php");
$niv=new AnneeScolaireManager($dbh,'ANNEESSCOLAIRE');


$colname_rq_annee_etab = "-1";
if (isset($_GET['idAnnee'])) {
    $colname_rq_annee_etab = base64_decode($lib->securite_xss($_GET['idAnnee']));
}

$query_rq_annee_etab = $dbh->query("SELECT IDANNEESSCOLAIRE, LIBELLE_ANNEESSOCLAIRE, DATE_DEBUT, DATE_FIN, IDETABLISSEMENT,ETAT 
                                              FROM ANNEESSCOLAIRE WHERE IDANNEESSCOLAIRE = $colname_rq_annee_etab");
foreach($query_rq_annee_etab->fetchAll() as $row_rq_annee_etab){

    $id=$row_rq_annee_etab['IDANNEESSCOLAIRE'];
    $libelle=$row_rq_annee_etab['LIBELLE_ANNEESSOCLAIRE'];
    $debut=$row_rq_annee_etab['DATE_DEBUT'];
    $fin=$row_rq_annee_etab['DATE_FIN'];
    $etat=$row_rq_annee_etab['ETAT'];
}


if(isset($_POST) && $_POST !=null) {


    $res = $niv->modifier($lib->securite_xss_array($_POST),'IDANNEESSCOLAIRE',$lib->securite_xss($_POST['IDANNEESSCOLAIRE']));
    if ($res==1) {
        $msg="Modification effectuée avec succés";

    }
    else{
        $msg="Modification effectuée avec echec";
    }
    header("Location: anneesScolaires.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
}

$query_rq_individu_annee_scolaire = $dbh->query("SELECT COUNT(i.IDINSCRIPTION) as nbre, n.LIBELLE, s.LIBSERIE, i.IDSERIE, i.IDNIVEAU 
                                            FROM INSCRIPTION i
                                            INNER JOIN NIVEAU n ON i.IDNIVEAU = n.IDNIVEAU
                                            INNER JOIN SERIE s ON s.IDSERIE = i.IDSERIE 
                                            INNER JOIN INDIVIDU id ON  id.IDINDIVIDU = i.IDINDIVIDU 
                                            INNER JOIN ANNEESSCOLAIRE a ON a.IDANNEESSCOLAIRE = i.IDANNEESSCOLAIRE
                                            WHERE i.IDANNEESSCOLAIRE=".$colname_rq_annee_etab."
                                            AND a.ETAT = 0
                                            GROUP BY i.IDSERIE, i.IDNIVEAU");

?>


        <?php include('header.php'); ?>
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Param&eacute;trage</a></li>                    
                    <li>Ann&eacute;es scolaires</li>
                    <li>Details</li>
                </ul>
                <!-- END BREADCRUMB -->  
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    <!-- START WIDGETS -->                    
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
<!--                                        <div class="white-box">-->
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Detail</span></a></li>
                                                <li role="presentation" class=""><a href="#histoInscription" aria-controls="histoInscription" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Historique des inscriptions</span></a></li>
                                                <li role="presentation" class=""><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Messages</span></a></li>
                                                <li role="presentation" class=""><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-settings"></i></span> <span class="hidden-xs">Settings</span></a></li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content" style="margin-top: 30px;">
                                                <div role="tabpanel" class="tab-pane active" id="detail">
                                                    <div class="col-md-6">
                                                        <fieldset class="cadre"><legend>ANNEE SCOLAIRE : <?php  echo $libelle;  ?></legend>
                                                            <table class="table table-responsive table-striped">
                                                                <tr>
                                                                    <td>DATE DEBUT</td>
                                                                    <td style="text-align: center;"><?php  echo $lib->date_fr($debut);  ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>DATE FIN</td>
                                                                    <td style="text-align: center;"><?php  echo $lib->date_fr($fin);  ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>ETAT</td>
                                                                    <td style="text-align: center;">
                                                                        <?php
                                                                        if($etat == 0) {
                                                                            echo "<span class='badge badge-success'>EN COURS</span>";
                                                                        }else {
                                                                            echo "<span class='badge badge-danger'>TERMINÉE</span>";
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </fieldset>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="col-md-10">
                                                                    <a role="button" href="anneesScolaires.php" class="btn btn-primary">RETOUR</a>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" data-toggle="modal" data-target="#confirmer" class="btn btn-success" <?php if($etat != 0) echo disabled ;?>>CLOTURER</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="clearfix"></div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="histoInscription">
                                                    <div class="col-md-12">
                                                        <table id="customers2" class="table datatable">

                                                            <thead>
                                                            <tr>
                                                                <th>Cycle</th>
                                                                <th>Fili&egrave;re / Série </th>
                                                                <th>&Eacute;ffectif</th>
                                                                <th>D&eacute;tails</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            </thead>


                                                            <tbody>
                                                            <?php foreach ($query_rq_individu_annee_scolaire->fetchAll() as $individu){ ?>
                                                                <tr>
                                                                    <td ><?php echo $individu['LIBELLE']; ?></td>
                                                                    <td ><?php echo $individu['LIBSERIE']; ?></td>
                                                                    <td ><?php echo $individu['nbre']; ?></td>
                                                                    <td ><a href="detailHistoInscription.php?IDSERIE=<?php echo base64_encode($individu['IDSERIE']); ?>&amp;IDNIVEAU=<?php echo base64_encode($individu['IDNIVEAU']) ; ?>&amp;LIBSERIE=<?php echo base64_encode($individu['LIBSERIE']) ; ?>&amp;NIVEAU=<?php echo base64_encode($individu['LIBELLE']) ; ?>&amp;ANNEESSCOLAIRE=<?php echo $_GET['idAnnee'] ; ?>"><i class=" glyphicon glyphicon-list" data-toggle="tooltip" data-placement="bottom" title="DETAILS"></i></a></td>
                                                                    <td ><a href="../../ged/imprimer_individus.php?IDSERIE=<?php echo base64_encode($individu['IDSERIE']); ?>&amp;IDNIVEAU=<?php echo base64_encode($individu['IDNIVEAU']) ; ?>&amp;LIBSERIE=<?php echo base64_encode($individu['LIBSERIE']) ; ?>&amp;NIVEAU=<?php echo base64_encode($individu['LIBELLE']) ; ?>&amp;ANNEESSCOLAIRE=<?php echo $_GET['idAnnee'] ; ?>"><i class="glyphicon glyphicon-print" data-toggle="tooltip" data-placement="bottom" title="IMPRIMER"></i></a></td>
                                                                </tr>

                                                            <?php }  ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="messages">
                                                    <div class="col-md-6">
                                                        <h3>Come on you have a lot message</h3>
                                                        <h4>you can use it with the small code</h4> </div>
                                                    <div class="col-md-5 pull-right">
                                                        <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a.</p>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="settings">
                                                    <div class="col-md-6">
                                                        <h3>Just do Settings</h3>
                                                        <h4>you can use it with the small code</h4> </div>
                                                    <div class="col-md-5 pull-right">
                                                        <p>Vulputate eget, fringilla vel, aliquet nec, daf adfd vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a.</p>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
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

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="confirmer" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="panel-title text-center"> CONFIRMATION </h3> <!--IDNIVEAU LIBELLE IDETABLISSEMENT-->
                </div>
                <div class="panel-body">
                    <div class="row" id="message">
                        SOUHAITEZ VOUS CLÔTURER L'ANNÉE SCOLAIRE ?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ANNULER</button>
                    <button type="button" class="btn btn-primary" id="cloturer">VALIDER</button>
                </div>
            </div>

        </div>
    </div>
</div>
        <?php include('footer.php'); ?>

<script>
    $(function () {
        $('#myTab li:first-child a').tab('show')
    })

    $('#cloturer').on('click', function () {
        //$('#confirmer').modal('hide')
        $.ajax({
            method: "POST",
            url: "cloreAnnee.php",
            data: {
                IDANNEESCOLAIRE: btoa(<?php echo $id;?>)
            }

        }).done(function (data) {
            if(data == 1) {
                console.log("ok");
                location.reload()
            }else if(data == -1){
                console.log('echec cloture')
                $('#message').text('Echec cloture !!')
            }else if(data == -2) {
                console.log(data)
                console.log('echec new year')
            }
        })
    })




</script>
