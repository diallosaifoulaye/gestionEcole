<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($lib->securite_xss($_SESSION['profil']) != 1)
    $lib->Restreindre($lib->Est_autoriser(35, $lib->securite_xss($_SESSION['profil'])));



if (isset($_POST) && $_POST != null) {
    require_once("classe/IndividuManager.php");
    require_once("classe/Individu.php");
    $ind = new IndividuManager($dbh, 'INDIVIDU');

    $dossier = "../../imgtiers/";
    $fichier = basename($_FILES['PHOTO_FACE']['name']);
    $fichier1 = basename($_FILES['FICHE_RECTO']['name']);
    $fichier2 = basename($_FILES['FICHE_VERSO']['name']);

    if (!empty($fichier1) && !empty($fichier2)) {

        $res = -1;
        $taille_maxi = 1000000;
        $taille = filesize($_FILES['PHOTO_FACE']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['PHOTO_FACE']['name'], '.');
        $extension1 = strrchr($_FILES['FICHE_RECTO']['name'], '.');
        $extension2 = strrchr($_FILES['FICHE_VERSO']['name'], '.');

        if (!in_array($extension1, $extensions) || !in_array($extension2, $extensions)) //Si l'extension n'est pas dans le tableau
        {
            $msg = "Vous devez uploader un fichier de type png, gif, jpg, jpeg";
        }
        if ($taille > $taille_maxi) {
            $msg = "Le fichier est trop gros";
        }

        if (!isset($msg)) //S'il n'y a pas d'erreur, on upload
        {

            if (move_uploaded_file($_FILES['PHOTO_FACE']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
            {
                $_POST['PHOTO_FACE'] = $_FILES['PHOTO_FACE']['name'];
            }
            if (move_uploaded_file($_FILES['FICHE_RECTO']['tmp_name'], $dossier . $fichier1) && move_uploaded_file($_FILES['FICHE_VERSO']['tmp_name'], $dossier . $fichier2)) {
                $_POST['FICHE_RECTO'] = $_FILES['FICHE_RECTO']['name'];
                $_POST['FICHE_VERSO'] = $_FILES['FICHE_VERSO']['name'];

            }
        }
    }

    $res = $ind->modifier($lib->securite_xss_array($_POST), 'IDINDIVIDU',$lib->securite_xss($_POST['IDINDIVIDU']) );
    if ($res == 1) {
        $msg = "Modification effectuée avec succés";
    } else {
        $msg = "Votre modification a échouée";
    }
    header("Location: listeEtudiant.php?msg=" . $msg . "&res=" . $res);
}else{
    require_once('../Parametrage/classe/ProfileManager.php');
    $profiles = new ProfileManager($dbh, 'profil');
    $profil = $profiles->getProfiles();

    $colname_rq_individu = "-1";
    if (isset($_SESSION['etab'])) {
    $colname_rq_individu =$lib->securite_xss($_SESSION['etab']);
    }
    $id_individu = "-1";
    if (isset($_GET['idIndividu'])) {
        $param=json_decode(base64_decode($_GET['idIndividu']));
        $id_individu =$param->id;
    }
    $query_rq_individu = $dbh->query("SELECT * FROM INDIVIDU WHERE IDINDIVIDU =".$id_individu);
    /* IDINDIVIDU MATRICULE NOM PRENOMS DATNAISSANCE ADRES TELMOBILE TELDOM COURRIEL LOGIN MP CODE BIOGRAPHIE PHOTO_FACE IDETABLISSEMENT IDTYPEINDIVIDU SEXE IDPERE IDMERE IDTUTEUR ANNEEBAC NATIONNALITE SIT_MATRIMONIAL NUMID  */
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
        $NATIONNALITE = $row_rq_individu['NATIONNALITE'];
        $SEXE = $row_rq_individu['SEXE'];
        $PHOTO_FACE = $row_rq_individu['PHOTO_FACE'];

        $pathologie = $row_rq_individu['PATHOLOGIE'];
        $allergie = $row_rq_individu['ALLERGIE'];
        $medecin_traitant = $row_rq_individu['MEDECIN_TRAITANT'];
        $FICHE_RECTO = $row_rq_individu['FICHE_RECTO'];
        $FICHE_VERSO = $row_rq_individu['FICHE_VERSO'];
        $lieuNaissance = $row_rq_individu['LIEU_NAISSANCE'];
        $pere = $row_rq_individu['PERE'];
        $mere = $row_rq_individu['MERE'];


    }
//    echo "<pre>";var_dump($profil[0]->getIdProfil());exit;
}
?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Param&eacute;trage</a></li>
    <li>Liste individus</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">

        <form action="modifeleve.php" method="POST" enctype="multipart/form-data" id="form">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label>Type individu:</label>
                        <div>
                            <select name="IDTYPEINDIVIDU" class="form-control" data-live-search="true">
                                <?php
                                foreach ($profil as $profile) {

                                    ?>
                                    <option
                                        value="<?php echo $profile->getIdProfil(); ?>" <?php if ($idprofil == $profile->getIdProfil()) echo "selected"; ?>><?php echo $profile->getProfil(); ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>


                <fieldset class="cadre">

                    <legend>INFORMATIONS GENERALES</legend>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>MATRICULE</label>

                            <div>
                                <input type="text" name="MATRICULE" id="MATRICULE" required class="form-control"
                                       value="<?php echo $MATRICULE; ?>" readonly/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>CODE BARRE</label>

                            <div>
                                <input type="text" name="CODE" id="CODE" required class="form-control"
                                       value="<?php echo $CODE; ?>" readonly/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>PRENOMS</label>

                            <div>
                                <input type="text" name="PRENOMS" id="PRENOMS" required class="form-control"
                                       value="<?php echo $PRENOMS; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>NOM</label>

                            <div>
                                <input type="text" name="NOM" id="NOM" required class="form-control"
                                       value="<?php echo $NOMINDIVIDU; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>DATE DE NAISSANCE</label>

                            <div>
                                <input type="text" name="DATNAISSANCE" id="date_foo" required class="form-control"
                                       value="<?php echo $DATNAISSANCE; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>LIEU DE NAISSANCE</label>

                            <div>
                                <input type="text" name="LIEU_NAISSANCE" id="LIEU_NAISSANCE" required class="form-control"
                                       value="<?php echo $lieuNaissance ; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>PERE</label>

                            <div>
                                <input type="text" name="PERE" id="PERE" required class="form-control"
                                       value="<?php echo $pere; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>MERE</label>

                            <div>
                                <input type="text" name="MERE" id="MERE" required class="form-control"
                                       value="<?php echo $mere; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>ADRESSE</label>

                            <div>
                                <input type="text" name="ADRES" id="ADRES" required class="form-control"
                                       value="<?php echo $ADRES; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>TELEPHONE MOBILE</label>

                            <div>
                                <input type="tel" name="TELMOBILE" id="TELMOBILE" required class="form-control"
                                       value="<?php echo $TELMOBILE; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>SEXE</label>

                            <div>
                                <input type="radio" name="SEXE" value="1" <?php if ($SEXE == 1) echo "checked"; ?>/>&nbsp;Masculin
                                &nbsp;&nbsp;&nbsp;
                                <input name="SEXE" type="radio" value="0" <?php if ($SEXE == 0) echo "checked"; ?> />&nbsp;Feminin
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>TELEPHONE DOMICILE</label>

                            <div>
                                <input type="tel" name="TELDOM" id="TELDOM" class="form-control"
                                       value="<?php echo $TELDOM; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>COURRIEL</label>

                            <div>
                                <input type="email" onchange="validateMail();" name="COURRIEL" id="COURRIEL" required class="form-control"
                                       value="<?php echo $COURRIEL; ?>"/>
                            </div>
                        </div>
                    </div>


                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>BIOGRAPHIE</label>

                            <div>
                                <textarea name="BIOGRAPHIE" id="mytextarea"
                                          class="form-control"><?php echo $BIOGRAPHIE; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>ANNEE BAC</label>

                            <div>
                                <input type="number" name="ANNEEBAC" id="ANNEEBAC"  class="form-control"
                                       value="<?php echo $ANNEEBAC; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>NATIONALITE</label>

                            <div>
                                <select name="NATIONNALITE" id="NATIONNALITE" data-live-search="true">
                                    <option value="22">SENEGAL</option>

                                    <?php
                                    $requete = $dbh->query("select ROWID, LIBELLE from PAYS");
                                    foreach ($requete->fetchAll() as $row_rq_pays) {
                                        ?>
                                        <option
                                            value="<?php echo $row_rq_pays['ROWID'] ?>" <?php if ($NATIONNALITE == $row_rq_pays['ROWID']) echo "selected"; ?>><?php echo $row_rq_pays['LIBELLE'] ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>SITUATION MATRIMONIALE</label>

                            <div>
                                <select name="SIT_MATRIMONIAL" id="SIT_MATRIMONIAL">
                                    <option
                                        value="Celibataire sans enfant" <?php if ($SIT_MATRIMONIAL == "Celibataire sans enfant") echo "selected"; ?>>
                                        Celibataire sans enfant
                                    </option>
                                    <option
                                        value="celibataire avec enfant" <?php if ($SIT_MATRIMONIAL == "celibataire avec enfant") echo "selected"; ?>>
                                        celibataire avec enfant
                                    </option>
                                    <option
                                        value="Marié(e) sans enfant" <?php if ($SIT_MATRIMONIAL == "Marié(e) sans enfant") echo "selected"; ?>>
                                        Marié(e) sans enfant
                                    </option>
                                    <option
                                        value="Marié(e) avec enfant" <?php if ($SIT_MATRIMONIAL == "Marié(e) avec enfant") echo "selected"; ?>>
                                        Marié(e) avec enfant
                                    </option>
                                    <option
                                        value="Veuf(ve) sans enfant" <?php if ($SIT_MATRIMONIAL == "Veuf(ve) sans enfant") echo "selected"; ?>>
                                        Veuf(ve) sans enfant
                                    </option>
                                    <option
                                        value="Veuf(ve) avec enfant" <?php if ($SIT_MATRIMONIAL == "Veuf(ve) avec enfant") echo "selected"; ?>>
                                        Veuf(ve) avec enfant
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>NUMERO IDENTIFICATION</label>

                            <div>
                                <input type="text" name="NUMID" id="NUMID"  class="form-control"
                                       value="<?php echo $NUMID; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label>PHOTO</label>
                                <div>
                                    <input type="file" name="PHOTO_FACE" id="PHOTO_FACE" class="form-control" value=""/>
                                    <img src="../../imgtiers/<?php echo $PHOTO_FACE; ?>" alt="" width="41px" height="41px"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <div class="form-group">
                                <label >FICHE INSCRIPTION RECTO</label>
                                <div>
                                    <input type="file" name="FICHE_RECTO" id="FICHE_RECTO"  class="form-control"/>
                                    <img src="../../imgtiers/<?php echo $FICHE_RECTO; ?>" alt="" width="41px" height="41px"/>

                                </div>
                            </div>
                        </div> <div class="col-xs-4">
                            <div class="form-group">
                                <label >FICHE INSCRIPTION VERSO</label>
                                <div>
                                    <input type="file" name="FICHE_VERSO" id="FICHE_VERSO"  class="form-control"/>
                                    <img src="../../imgtiers/<?php echo $FICHE_VERSO; ?>" alt="" width="41px" height="41px"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label >PATHOLOGIE</label>
                                <div>
                                    <textarea  name="PATHOLOGIE" id="mytextarea1"  class="form-control"><?php echo $pathologie; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label >ALLERGIE</label>
                                <div>
                                    <textarea  name="ALLERGIE" id="mytextarea2"  class="form-control"><?php echo $allergie; ?></textarea>
                                </div>
                            </div>
                        </div> <div class="col-xs-4">
                            <div class="form-group">
                                <label >MEDECIN TRAITANT</label>
                                <div>
                                    <textarea  name="MEDECIN_TRAITANT" id="mytextarea3"  class="form-control"><?php echo $medecin_traitant; ?></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                </fieldset>



            </div>

            <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $_SESSION['etab']; ?>"/>
            <input type="hidden" name="IDINDIVIDU" value="<?php echo $id; ?>"/>

            <button type="submit" id="validerAj" class="btn btn-primary center-block">Modifier</button>
            <div style="height: 50px;"></div>
        </form>

        <script>
//        var test = false;
        function validateMail()
        {
            var mailteste = $('#COURRIEL').val();
            var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
            if(reg.test(mailteste)){
                $('#validerAj').removeClass("disabled");
//                test = true;
            }else{
                $('#validerAj').addClass("disabled");
//                test = false;
                alert("Veillez saisir un courriel valide")
            }
        }


    </script>

        <!-- END WIDGETS -->


    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->


<?php include('footer.php'); ?>