<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(35, $lib->securite_xss($_SESSION['profil'])));



if (isset($_POST) && $_POST != null) {
    require_once("classe/IndividuManager.php");
    require_once("classe/Individu.php");
    $ind = new IndividuManager($dbh, 'INDIVIDU');

    $res = $ind->modifier($lib->securite_xss_array($_POST), 'IDINDIVIDU', $lib->securite_xss($_POST['IDINDIVIDU']));
    if ($res == 1) {
        $msg = "Modification effectuée avec succés";
    } else {
        $msg = "Votre modification a échouée";
    }
    header("Location: accueil.php?msg=" . $msg . "&res=" . $res);
}else{
    require_once('../Parametrage/classe/ProfileManager.php');
    $profiles = new ProfileManager($dbh, 'profil');
    $profil = $profiles->getProfiles();

    $colname_rq_individu = "-1";
    if (isset($_SESSION['etab'])) {
    $colname_rq_individu = $lib->securite_xss($_SESSION['etab']);
    }
    $query_rq_individu = $dbh->query("SELECT `IDINDIVIDU`, `MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `TELDOM`, `COURRIEL`, `LOGIN`, `MP`, INDIVIDU.CODE, `BIOGRAPHIE`, `PHOTO_FACE`, INDIVIDU.IDETABLISSEMENT, INDIVIDU.IDTYPEINDIVIDU, `SEXE`, `IDTUTEUR`, `LIEN_PARENTE`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`, `IDSECTEUR`, `LIEU_TRAVAIL`, `PATHOLOGIE`, `ALLERGIE`, `MEDECIN_TRAITANT`, `FICHE_RECTO`, `FICHE_VERSO`, `DIPLOMES`, `DISCIPLINE`, `ANNEE`, `DATE_ARRIVE_CEMAD`, `FILIERE_ENSEIGNE`, `ID_NIVEAU`, `DUREE_ENSEIGNEMENT`, `ENGAGEMENT`, `RAISON_ENGAGEMENT`, TYPEINDIVIDU.LIBELLE AS TYPE, PAYS.LIBELLE as NATIO, NIVEAU.LIBELLE as CYCLE FROM `INDIVIDU` INNER JOIN TYPEINDIVIDU ON INDIVIDU.IDTYPEINDIVIDU = TYPEINDIVIDU.IDTYPEINDIVIDU LEFT JOIN  NIVEAU ON INDIVIDU.ID_NIVEAU = NIVEAU.IDNIVEAU LEFT JOIN PAYS ON INDIVIDU.NATIONNALITE = PAYS.ROWID WHERE IDINDIVIDU = " . $lib->securite_xss(base64_decode($_GET['idIndividu'])));

    foreach ($query_rq_individu->fetchAll() as $row_rq_individu) {

        $id = $row_rq_individu['IDINDIVIDU'];
        $idprofil = $row_rq_individu['IDTYPEINDIVIDU'];
        $MATRICULE = $row_rq_individu['MATRICULE'];
        $CODE = $row_rq_individu['CODE'];
        $NOMINDIVIDU = $row_rq_individu['NOM'];
        $PRENOMS = $row_rq_individu['PRENOMS'];
        $DATNAISSANCE = $row_rq_individu['DATNAISSANCE'];
        $ADRES = $row_rq_individu['ADRES'];
        $TELMOBILE = $row_rq_individu['TELMOBILE'];
        $TELDOM = $row_rq_individu['TELDOM'];
        $COURRIEL = $row_rq_individu['COURRIEL'];
        $BIOGRAPHIE = $row_rq_individu['BIOGRAPHIE'];
        $SIT_MATRIMONIAL = $row_rq_individu['SIT_MATRIMONIAL'];
        $NUMID = $row_rq_individu['NUMID'];
        $ANNEEBAC = $row_rq_individu['ANNEEBAC'];
        $NATIONNALITE = $row_rq_individu['NATIO'];
        $SEXE = $row_rq_individu['SEXE'];
        $PHOTO_FACE = $row_rq_individu['PHOTO_FACE'];
        $LIEU_TRAVAIL = $row_rq_individu['LIEU_TRAVAIL'];
        $PATHOLOGIE = $row_rq_individu['PATHOLOGIE'];
        $ALLERGIE = $row_rq_individu['ALLERGIE'];
        $MEDECIN_TRAITANT = $row_rq_individu['MEDECIN_TRAITANT'];
        $FICHE_RECTO = $row_rq_individu['FICHE_RECTO'];
        $FICHE_VERSO = $row_rq_individu['FICHE_VERSO'];
        $TYPE = $row_rq_individu['TYPE'];
        $DIPLOMES = $row_rq_individu['DIPLOMES'];
        $DISCIPLINE = $row_rq_individu['DISCIPLINE'];
        $ANNEE = $row_rq_individu['ANNEE'];
        $DATE_ARRIVE_CEMAD = $row_rq_individu['DATE_ARRIVE_CEMAD'];
        $FILIERE_ENSEIGNE = $row_rq_individu['FILIERE_ENSEIGNE'];
        $ID_NIVEAU = $row_rq_individu['CYCLE'];
        $DUREE_ENSEIGNEMENT = $row_rq_individu['DUREE_ENSEIGNEMENT'];
        $ENGAGEMENT = $row_rq_individu['ENGAGEMENT'];
        $RAISON_ENGAGEMENT = $row_rq_individu['RAISON_ENGAGEMENT'];


    }
//    echo "<pre>";var_dump($profil[0]->getIdProfil());exit;
}
?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Param&eacute;trage</a></li>
    <li>Détail individus</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">

           <fieldset class="cadre">

                    <legend>INFORMATIONS GENERALES</legend>

                    <div class="col-sm-12" align="center">
                        <img class="img-circle" width="200" alt="<?php echo $PHOTO_FACE; ?>"
                             src="../../imgtiers/<?php echo $PHOTO_FACE; ?>" style="padding-bottom: 20px;">
                    </div>
        <div class="row">
            <?php if ($idprofil==8){ ?>

            <div class="col-md-9">
                   <div class="row text-center m-t-10">
                       <div class="col-md-4 b-r"><strong>TYPE INDIVIDU</strong>
                           <p><?php echo $TYPE; ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>MATRICULE</strong>
                           <p><?php echo $MATRICULE; ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>PRENOMS</strong>
                           <p><?php echo $PRENOMS; ?></p>
                       </div>
                   </div>

                   <hr>
                   <div class="row text-center m-t-10">
                       <div class="col-md-4 b-r"><strong>NOM</strong>
                           <p><?php echo $NOMINDIVIDU; ?></p>
                       </div>

                       <div class="col-md-4 b-r"><strong>DATE DE NAISSANCE</strong>
                           <p><?php echo $DATNAISSANCE; ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>ADRESSE</strong>
                           <p><?php echo $ADRES; ?></p>
                       </div>
                   </div>
                       <hr>
                   <div class="row text-center m-t-10">

                       <div class="col-md-4 b-r"><strong>COURRIEL</strong>
                           <p><?php echo $COURRIEL; ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>TELEPHONE MOBILE</strong>
                           <p><?php echo $TELMOBILE; ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>SEXE</strong>
                           <p><?php if ($SEXE == 1) echo 'Masculin'; elseif ($SEXE == 0) echo'Feminin'; ?></p>
                       </div>
                   </div>
                   <hr>
                   <div class="row text-center m-t-10">

                       <div class="col-md-4 b-r"><strong>NATIONALITE</strong>
                           <p><?php echo $NATIONNALITE; ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>SITUATION MATRIMONIALE</strong>
                           <p><?php echo $SIT_MATRIMONIAL; ?></p>
                       </div>

                       <div class="col-md-4 b-r"><strong>NUMERO IDENTIFICATION</strong>
                           <p><?php echo $NUMID; ?></p>
                       </div>
                   </div>
                   <hr>

                   <div class="row text-center m-t-10">

                       <div class="col-md-4 b-r"><strong>PATHOLOGIE</strong>
                           <p><?php echo strip_tags( html_entity_decode ($PATHOLOGIE)); ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>ALLERGIE</strong>
                           <p><?php echo strip_tags( html_entity_decode ($ALLERGIE)); ?></p>
                       </div>
                       <div class="col-md-4 b-r"><strong>MEDECIN_TRAITANT</strong>
                           <p><?php echo strip_tags( html_entity_decode ($MEDECIN_TRAITANT)); ?></p>
                       </div>
                   </div>
                   <hr>
               </div>

            <div class="col-md-3">
            <div class="row text-center m-t-10">
                    <div class="col-md-12 b-r"><strong>FICHE D'INSCRIPTION RECTO</strong>
                        <?php if ($FICHE_RECTO!="") {?>
                        <p> <a href="<?php echo "../../imgtiers/".$FICHE_RECTO;?>" data-toggle="lightbox" data-gallery="multiimages"><img  width="150" alt="<?php echo $FICHE_RECTO; ?>" src="../../imgtiers/<?php echo $FICHE_RECTO;?>" style="padding-bottom: 20px;"></a></p>
                   <?php }else{
                            echo "<p> <strong> Pas de fiche </strong></p>";
                   }?>
                    </div>

            </div>
                <div class="row text-center m-t-10">
                    <div class="col-md-12 b-r"><strong>FICHE D'INSCRIPTION VERSO</strong>
                        <?php if ($FICHE_VERSO!="") {?>
                            <p> <a href="<?php echo "../../imgtiers/".$FICHE_VERSO;?>" data-toggle="lightbox" data-gallery="multiimages"><img  width="150" alt="<?php echo $FICHE_VERSO; ?>" src="../../imgtiers/<?php echo $FICHE_VERSO;?>" style="padding-bottom: 20px;"></a></p>
                        <?php }else{
                            echo "<p> <strong> Pas de fiche </strong></p>";
                        }?>
                    </div>

                    </div>
                </div>
            </div>
            <?php }else{?>
            <div class="col-md-12">
                <div class="row text-center m-t-10">
                    <div class="col-md-4 b-r"><strong>TYPE INDIVIDU</strong>
                        <p><?php echo $TYPE; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>MATRICULE</strong>
                        <p><?php echo $MATRICULE; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>PRENOMS</strong>
                        <p><?php echo $PRENOMS; ?></p>
                    </div>
                </div>

                <hr>
                <div class="row text-center m-t-10">
                    <div class="col-md-4 b-r"><strong>NOM</strong>
                        <p><?php echo $NOMINDIVIDU; ?></p>
                    </div>

                    <div class="col-md-4 b-r"><strong>DATE DE NAISSANCE</strong>
                        <p><?php echo $DATNAISSANCE; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>ADRESSE</strong>
                        <p><?php echo $ADRES; ?></p>
                    </div>
                </div>
                <hr>
                <div class="row text-center m-t-10">

                    <div class="col-md-4 b-r"><strong>COURRIEL</strong>
                        <p><?php echo $COURRIEL; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>TELEPHONE MOBILE</strong>
                        <p><?php echo $TELMOBILE; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>SEXE</strong>
                        <p><?php if ($SEXE == 1) echo 'Masculin'; elseif ($SEXE == 0) echo'Feminin'; ?></p>
                    </div>
                </div>
                <hr>
                <div class="row text-center m-t-10">

                    <div class="col-md-4 b-r"><strong>NATIONALITE</strong>
                        <p><?php echo $NATIONNALITE; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>SITUATION MATRIMONIALE</strong>
                        <p><?php echo $SIT_MATRIMONIAL; ?></p>
                    </div>

                    <div class="col-md-4 b-r"><strong>NUMERO IDENTIFICATION</strong>
                        <p><?php echo $NUMID; ?></p>
                    </div>
                </div>
                <hr><div class="row text-center m-t-10">

                    <div class="col-md-4 b-r"><strong>DIPLOMES</strong>
                        <p><?php echo str_replace("<p>","<li>", html_entity_decode ($DIPLOMES)); ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>DISCIPLINE</strong>
                        <p><?php echo str_replace("<p>","<li>", html_entity_decode ($DISCIPLINE)) ; ?></p>
                    </div>

                    <div class="col-md-4 b-r"><strong>ANNEE</strong>
                        <p><?php echo str_replace("<p>","<li>", html_entity_decode ($ANNEE)); ?></p>
                    </div>
                </div>
                <hr><div class="row text-center m-t-10">

                    <div class="col-md-4 b-r"><strong>DATE D'ARRIVEE AU CEMAD</strong>
                        <p><?php echo $DATE_ARRIVE_CEMAD; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>FILIERE(S) ENSEIGNEE(S)</strong>
                        <p><?php echo strip_tags( html_entity_decode ($FILIERE_ENSEIGNE)); ?></p>
                    </div>

                    <div class="col-md-4 b-r"><strong>NIVEAU OU CYCLE</strong>
                        <p><?php echo $ID_NIVEAU; ?></p>
                    </div>
                </div>
                <hr><div class="row text-center m-t-10">

                    <div class="col-md-4 b-r"><strong>DUREE DANS L'ENSEIGNEMENT</strong>
                        <p><?php echo $DUREE_ENSEIGNEMENT; ?></p>
                    </div>
                    <div class="col-md-4 b-r"><strong>CONFIRMATION ENGAGEMENT POUR CETTE ANNEE</strong>
                        <p><?php if($ENGAGEMENT==1) echo'OUI'; else echo'NON' ; ?></p>
                    </div>

                    <div class="col-md-4 b-r"><strong>LES RAISONS DE L'ENGAGEMENT</strong>
                        <p><?php echo str_replace("<p>","<li>", html_entity_decode ($RAISON_ENGAGEMENT)); ?></p>
                    </div>
                </div>
                <hr>
            <?php }?>
        </div>
            <div class="form-group">
                <div class="pull-right">
                    <a href="accueil.php">
                        <button type="button" class="btn btn-success">RETOUR</button>
                    </a>
                </div>

            </div>
     </fieldset>


    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->

<?php include('footer.php'); ?>

<script type="text/javascript">
    $(document).ready(function($) {
        // delegate calls to data-toggle="lightbox"
        $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
            event.preventDefault();
            return $(this).ekkoLightbox({
                onShown: function() {
                    if (window.console) {
                        return console.log('Checking our the events huh?');
                    }
                },
                onNavigate: function(direction, itemIndex) {
                    if (window.console) {
                        return console.log('Navigating ' + direction + '. Current item: ' + itemIndex);
                    }
                }
            });
        });
        //Programatically call
        $('#open-image').click(function(e) {
            e.preventDefault();
            $(this).ekkoLightbox();
        });
        $('#open-youtube').click(function(e) {
            e.preventDefault();
            $(this).ekkoLightbox();
        });
        // navigateTo
        $(document).delegate('*[data-gallery="navigateTo"]', 'click', function(event) {
            event.preventDefault();
            var lb;
            return $(this).ekkoLightbox({
                onShown: function() {
                    lb = this;
                    $(lb.modal_content).on('click', '.modal-footer a', function(e) {
                        e.preventDefault();
                        lb.navigateTo(2);
                    });
                }
            });
        });
    });
</script>
