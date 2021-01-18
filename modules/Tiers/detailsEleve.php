
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



$id_individu = "-1";
if (isset($_GET['idIndividu'])) {
    $param=json_decode(base64_decode($_GET['idIndividu']));
    $id_individu =$param->id;
}
$anne = $lib->securite_xss($_SESSION["ANNEESSCOLAIRE"]);

$query_rq_individu = $dbh->query("SELECT INDIVIDU.IDINDIVIDU, MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, `PHOTO_FACE`, INDIVIDU.IDETABLISSEMENT, `IDTYPEINDIVIDU`, `SEXE`, `IDTUTEUR`, `LIEN_PARENTE`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`,
                                            IDSECTEUR, LIEU_TRAVAIL, PATHOLOGIE, `ALLERGIE`, `MEDECIN_TRAITANT`, `FICHE_RECTO`, `FICHE_VERSO`, `DIPLOMES`, `DISCIPLINE`, `ANNEE`, `DATE_ARRIVE_CEMAD`, `FILIERE_ENSEIGNE`, `ID_NIVEAU`, `DUREE_ENSEIGNEMENT`, `ENGAGEMENT`, `RAISON_ENGAGEMENT`, NIVEAU.LIBELLE as CYCLE, CLASSROOM.LIBELLE AS CLASSE, INSCRIPTION.IDINSCRIPTION as IDINSCRIPTION,
                                             `FRAIS_DOSSIER`, `FRAIS_EXAMEN`, `UNIFORME`, `VACCINATION`, `ASSURANCE`, `FRAIS_SOUTENANCE`, `TRANSPORT`, `MONTANT_TRANSPORT`, `MONTANT_DOSSIER`, `MONTANT_EXAMEN`, `MONTANT_UNIFORME`, `MONTANT_VACCINATION`, `MONTANT_ASSURANCE`, `MONTANT_SOUTENANCE`
                                            FROM `INDIVIDU` 
                                            INNER JOIN INSCRIPTION ON INSCRIPTION.IDINDIVIDU=INDIVIDU.IDINDIVIDU 
                                            INNER JOIN  NIVEAU ON INSCRIPTION.IDNIVEAU = NIVEAU.IDNIVEAU 
                                            INNER JOIN AFFECTATION_ELEVE_CLASSE ON AFFECTATION_ELEVE_CLASSE.IDINDIVIDU=INDIVIDU.IDINDIVIDU
                                            INNER JOIN CLASSROOM ON AFFECTATION_ELEVE_CLASSE.IDCLASSROOM = CLASSROOM.IDCLASSROOM
                                            WHERE INDIVIDU.IDINDIVIDU =".$id_individu." AND INSCRIPTION.IDANNEESSCOLAIRE=".$lib->affichage_xss($_SESSION['ANNEESSCOLAIRE']) );
                                            $row_rq_individu = $query_rq_individu->fetchObject();

                                            $id = $row_rq_individu->IDINDIVIDU;
                                            $FRAIS_DOSSIER = $row_rq_individu->FRAIS_DOSSIER;
                                            $FRAIS_EXAMEN = $row_rq_individu->FRAIS_EXAMEN;
                                            $UNIFORME = $row_rq_individu->UNIFORME;
                                            $NOMINDIVIDU = $row_rq_individu->NOM;
                                            $PRENOMS = $row_rq_individu->PRENOMS;
                                            $DATNAISSANCE = $row_rq_individu->DATNAISSANCE;
                                            $ADRES = $row_rq_individu->ADRES;
                                            $TELMOBILE = $row_rq_individu->TELMOBILE;
                                            $TELDOM = $row_rq_individu->TELDOM;
                                            $VACCINATION = $row_rq_individu->VACCINATION;
                                            $ASSURANCE = $row_rq_individu->ASSURANCE;
                                            $FRAIS_SOUTENANCE = $row_rq_individu->FRAIS_SOUTENANCE;
                                            $MONTANT_TRANSPORT = $row_rq_individu->MONTANT_TRANSPORT;
                                            $MONTANT_DOSSIER = $row_rq_individu->MONTANT_DOSSIER;
                                            $MONTANT_UNIFORME = $row_rq_individu->MONTANT_UNIFORME;
                                            $MONTANT_VACCINATION = $row_rq_individu->MONTANT_VACCINATION;
                                            $PHOTO_FACE = $row_rq_individu->PHOTO_FACE;
                                            $LIEU_TRAVAIL = $row_rq_individu->LIEU_TRAVAIL;
                                            $PATHOLOGIE = $row_rq_individu->PATHOLOGIE;
                                            $ALLERGIE = $row_rq_individu->ALLERGIE;
                                            $MEDECIN_TRAITANT = $row_rq_individu->MEDECIN_TRAITANT;
                                            $MONTANT_ASSURANCE = $row_rq_individu->MONTANT_ASSURANCE;
                                            $MONTANT_SOUTENANCE = $row_rq_individu->MONTANT_SOUTENANCE;
                                            $TYPE = $row_rq_individu->TYPE;
                                            $DIPLOMES = $row_rq_individu->DIPLOMES;
                                            $DISCIPLINE = $row_rq_individu->DISCIPLINE;
                                            $ANNEE = $row_rq_individu->ANNEE;
                                            $DATE_ARRIVE_CEMAD = $row_rq_individu->DATE_ARRIVE_CEMAD;
                                            $FILIERE_ENSEIGNE = $row_rq_individu->FILIERE_ENSEIGNE;
                                            $ID_NIVEAU = $row_rq_individu->CYCLE;
                                            $CLASS = $row_rq_individu->CLASSE;
                                            $IDINSCRIPTION = $row_rq_individu->IDINSCRIPTION;
                                            $IDTUTEUR = $row_rq_individu->IDTUTEUR;


if($IDTUTEUR!='' && $IDTUTEUR!='NULL')
{
$query_rq_tuteur = $dbh->query("SELECT INDIVIDU.IDINDIVIDU, `MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `TELDOM`, `COURRIEL`, `LOGIN`, `MP`, `CODE`, `BIOGRAPHIE`, `PHOTO_FACE`, INDIVIDU.IDETABLISSEMENT, 
                                          `IDTYPEINDIVIDU`, `SEXE`, `IDTUTEUR`, `LIEN_PARENTE`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`, `IDSECTEUR`, `LIEU_TRAVAIL`, `PATHOLOGIE`, `ALLERGIE`, `MEDECIN_TRAITANT`, `FICHE_RECTO`, 
                                          `FICHE_VERSO`, `DIPLOMES`, `DISCIPLINE`, `ANNEE`, `DATE_ARRIVE_CEMAD`, `FILIERE_ENSEIGNE`, `ID_NIVEAU`, `DUREE_ENSEIGNEMENT`, `ENGAGEMENT`, `RAISON_ENGAGEMENT`
                                           FROM `INDIVIDU` where INDIVIDU.IDINDIVIDU=".$IDTUTEUR);
                                           $row_rq_tuteur = $query_rq_tuteur->fetchObject();
}


$query_rq_liste_fact = $dbh->query("SELECT IDFACTURE, NUMFACTURE, MOIS, MONTANT, DATEREGLMT, IDINSCRIPTION, IDETABLISSEMENT, MT_VERSE, MT_RELIQUAT, ETAT, IDANNEESCOLAIRE, USER_MODIFICATION, DATE_MODIFICATION 
                                              FROM FACTURE 
                                              WHERE IDINSCRIPTION =".$IDINSCRIPTION);

$rs_fact = $query_rq_liste_fact->fetchAll();


$query_bulletin = $dbh->query("SELECT DISTINCT b.IDPERIODE, b.IDCLASSROOM, p.NOM_PERIODE 
                                         FROM BULLETIN b 
                                         INNER JOIN PERIODE p ON p.IDPERIODE = b.IDPERIODE 
                                         WHERE b.IDINDIVIDU = ".$id_individu." 
                                         AND b.IDANNEE = ".$anne);

$rs_bulletin = $query_bulletin->fetchAll(PDO::FETCH_ASSOC);


?>


        <?php include('header.php'); ?>
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Tier</a></li>
                    <li>Eléve</li>
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
                                                    <img class="img-circle" width="200" alt="<?php echo $PHOTO_FACE; ?>"
                                                         src="../../imgtiers/<?php echo $PHOTO_FACE; ?>" style="padding-bottom: 20px;">
                                                </div>

                                                <div class="row text-center m-t-10">
                                                    <div class="col-md-6 b-r"><strong>PRENOM</strong>
                                                        <p><?php echo $PRENOMS; ?></p>
                                                    </div>
                                                    <div class="col-md-6"><strong>NOM</strong>
                                                        <p><?php echo $NOMINDIVIDU; ?></p>
                                                    </div>
                                                </div>
                                                <!-- /.row -->
                                                <hr>
                                                <!-- .row -->
                                                <div class="row text-center m-t-10">
                                                    <div class="col-md-6 b-r"><strong>ADRESSE</strong>
                                                        <p><?php echo $ADRES; ?></p>
                                                    </div>
                                                    <div class="col-md-6"><strong>TELEPHONE MOBILE</strong>
                                                        <p><?php echo $TELMOBILE; ?></p>
                                                    </div>
                                                </div>
                                                <!-- /.row -->
                                                <hr>
                                                 <div class="row text-center m-t-10">
                                                    <div class="col-md-6 b-r"><strong>CYCLE</strong>
                                                        <p><?php echo $ID_NIVEAU; ?></p>
                                                    </div>
                                                    <div class="col-md-6"><strong>CLASSE</strong>
                                                        <p><?php echo $CLASS; ?></p>
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
                                                        <span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Profile</span>
                                                    </a>
                                                </li>

                                                <li role="presentation" class="">
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
                                                </li>

                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content" style="margin-top: 30px;">

                                                <div role="tabpanel" class="tab-pane active" id="profile">
                                                    <fieldset class="cadre"><legend>INFORMATIONS TUTEUR</legend>
                                                        <table class="table table-responsive table-striped">
                                                            <tr>
                                                                <td><strong>NOM</strong></td>
                                                                <td style="text-align: center;"><?php  echo $row_rq_tuteur->PRENOMS;  ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>PRENOM</strong></td>
                                                                <td style="text-align: center;"><?php  echo $row_rq_tuteur->NOM;  ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>ADRESSE</strong> </td>
                                                                <td>  <?php  echo $row_rq_tuteur->ADRES; ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>TELEPHONE</strong></td>
                                                                <td style="text-align: center;">  <?php  echo $row_rq_tuteur->TELMOBILE; ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>LIEU DE TRAVAIL</strong></td>
                                                                <td style="text-align: center;">  <?php  echo $row_rq_tuteur->LIEU_TRAVAIL; ?> </td>
                                                            </tr>
                                                        </table>
                                                    </fieldset>
                                                    <fieldset class="cadre"><legend>INFORMATIONS COMPLEMENTAIRES</legend>
                                                        <table class="table table-responsive table-striped">
                                                            <tr>
                                                                <td><strong>PATHOLOGIE</strong></td>
                                                                <td style="text-align: center;"><?php  echo strip_tags( html_entity_decode ($PATHOLOGIE));  ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>ALLERGIE</strong></td>
                                                                <td style="text-align: center;"><?php  echo strip_tags( html_entity_decode ($ALLERGIE));  ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>MEDECIN_TRAITANT</strong></td>
                                                                <td style="text-align: center;">  <?php  echo strip_tags( html_entity_decode ($MEDECIN_TRAITANT)); ?> </td>
                                                            </tr>

                                                        </table>
                                                    </fieldset>

                                                </div>

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
                                                        <?php foreach ($rs_fact as $row_fact) { ?>
                                                            <tr>

                                                                <td><?php echo $row_fact['NUMFACTURE']; ?></td>
                                                                <td><?php echo $lib->affiche_mois($row_fact['MOIS']); ?></td>
                                                                <td><?php echo $lib->date_franc($row_fact['DATEREGLMT']); ?></td>
                                                                <td><?php echo $lib->nombre_form($row_fact['MONTANT']); ?></td>
                                                                <td><?php echo $lib->nombre_form($row_fact['MT_VERSE']); ?></td>
                                                                <td><?php echo $lib->nombre_form($row_fact['MT_RELIQUAT']); ?></td>

                                                                <td><?php if ($row_fact['ETAT'] == 0) {
                                                                        echo "<span class='badge badge-danger'>NON PAYER</span>";
                                                                        
                                                                   } ?>
                                                                    <?php if ($row_fact['ETAT'] == 1) {
                                                                        echo "<span class='badge badge-success'>PAYER</span>";
                                                                    } ?>
                                                                </td>
                                                            </tr>

                                                        <?php } ?>

                                                        </tbody>
                                                    </table>


                                                </div>

                                                <div role="tabpanel" class="tab-pane" id="frais">

                                                    <fieldset class="cadre"><legend>FRAIS</legend>
                                                        <table class="table table-responsive table-striped">
                                                            <?php if($FRAIS_DOSSIER>0){?>
                                                                <tr>
                                                                    <td><strong>FRAIS DE DOSSIER</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $FRAIS_DOSSIER;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                            <?php if($FRAIS_EXAMEN>0){?>
                                                                <tr>
                                                                    <td><strong>FRAIS D'EXAMEN</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $FRAIS_EXAMEN;  ?></td>
                                                                </tr>
                                                            <?php  } ?>

                                                            <?php if($UNIFORME>0){?>
                                                                <tr>
                                                                    <td><strong>UNIFORME</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $UNIFORME;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                             <?php if($VACCINATION>0){?>
                                                                <tr>
                                                                    <td><strong>VACCINATION</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $VACCINATION;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                            <?php if($ASSURANCE>0){?>
                                                                <tr>
                                                                    <td><strong>ASSURANCE</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $ASSURANCE;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                            <?php if($FRAIS_SOUTENANCE>0){?>
                                                                <tr>
                                                                    <td><strong>FRAIS DE SOUTENANCE</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $FRAIS_SOUTENANCE;  ?></td>
                                                                </tr>
                                                            <?php  } ?>

                                                            <?php if($MONTANT_TRANSPORT>0){?>
                                                                <tr>
                                                                    <td><strong>MONTANT TRANSPORT</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $MONTANT_TRANSPORT;  ?></td>
                                                                </tr>
                                                            <?php  } ?>

                                                            <?php if($MONTANT_DOSSIER>0){?>
                                                                <tr>
                                                                    <td><strong>MONTANT DOSSIER</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $MONTANT_DOSSIER;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                            <?php if($MONTANT_UNIFORME>0){?>
                                                                <tr>
                                                                    <td><strong>MONTANT UNIFORME</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $MONTANT_UNIFORME;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                            <?php if($MONTANT_VACCINATION>0){?>
                                                                <tr>
                                                                    <td><strong>MONTANT VACCINATION</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $MONTANT_VACCINATION;  ?></td>
                                                                </tr>
                                                            <?php  } ?>

                                                            <?php if($MONTANT_ASSURANCE>0){?>
                                                                <tr>
                                                                    <td><strong>MONTANT ASSURANCE</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $MONTANT_ASSURANCE;  ?></td>
                                                                </tr>
                                                            <?php  } ?>
                                                            <?php if($MONTANT_SOUTENANCE>0){?>
                                                                <tr>
                                                                    <td><strong>MONTANT SOUTENANCE</strong></td>
                                                                    <td style="text-align: center;"><?php  echo $MONTANT_SOUTENANCE;  ?></td>
                                                                </tr>
                                                            <?php  } ?>

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
                                                        <?php foreach ($rs_bulletin as $row_bulletin) { ?>

                                                            <tr>

                                                                <td>
                                                                    <?php echo $row_bulletin['NOM_PERIODE']; ?>
                                                                </td>

                                                                <td style="text-align: center !important;">
                                                                    <a target="_blank" href="../../ged/imprimer_bulletin_individu.php?idIndividu=<?php echo base64_encode($id_individu); ?>&idclassroom=<?php echo base64_encode($row_bulletin['IDCLASSROOM']);?>&periode=<?php echo base64_encode($row_bulletin['IDPERIODE']); ?>">
                                                                        <i class="glyphicon glyphicon-print"></i>
                                                                    </a>
                                                                </td>

                                                            </tr>

                                                        <?php } ?>

                                                        </tbody>
                                                    </table>


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


        <?php include('footer.php'); ?>

<script>
    $(function () {
        $('#myTab li:first-child a').tab('show')
    })



</script>

}