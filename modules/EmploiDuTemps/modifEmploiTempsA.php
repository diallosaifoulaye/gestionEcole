<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once("../../restriction.php");


require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(22, $lib->securite_xss($_SESSION['profil'])));
$msg = null;
$msg1 = null;
$res = null;

if (isset($_POST) && count($_POST) > 0) {

    if (isset($_GET['idDTT'])) {

        $hd = $lib->securite_xss($_POST['HEUREDEBUT']) . ":00";
        $hf = $lib->securite_xss($_POST['HEUREFIN']) . ":00";
        $jour = substr($lib->securite_xss($_POST['JOUR']), 0, 3);
        try {
            $query = "UPDATE DETAIL_TIMETABLE SET DATEDEBUT = ?, DATEFIN = ?, JOUR_SEMAINE = ?, IDMATIERE = ?, IDSALL_DE_CLASSE = ?, IDINDIVIDU = ? WHERE IDDETAIL_TIMETABLE = " .$lib->securite_xss($_GET['idDTT']);
            $stmt = $dbh->prepare($query);
            if ($stmt->execute(array($hd, $hf, $jour, $lib->securite_xss($_POST['IDMATIERE']), $lib->securite_xss($_POST['IDSALLE']), $lib->securite_xss($_POST['IDINDIVIDU'])))) {
                $msg = "Votre modification a été effectuée avec succes";
                $res = 1;
            } else {
                $msg = "Votre modification a échoué";
                $res = -1;
            }
        } catch (PDOException $e) {
            $msg = "Erreur requete ! Veillez en informer l'administrateur";
        }
    }
    elseif (isset($_GET['idEMP'])) {

        if($_POST['IDMATIERE']!='' && $_POST['IDSALLE']!='' && $_POST['IDINDIVIDU']!='' && $_POST['JOUR']!='' && $_POST['HEUREDEBUT']!='' && $_POST['HEUREFIN']!=''){
            //var_dump($_POST);exit;
            $hd = $lib->securite_xss($_POST['HEUREDEBUT']) . ":00";
            $hf = $lib->securite_xss($_POST['HEUREFIN']) . ":00";
            $jour = substr($lib->securite_xss($_POST['JOUR']), 0, 3);

            $idemp = $lib->securite_xss($_POST['idemp']);

            try
            {
                $query = "INSERT INTO DETAIL_TIMETABLE ( DATEDEBUT, DATEFIN, JOUR_SEMAINE, IDEMPLOIEDUTEMPS, IDMATIERE, IDSALL_DE_CLASSE, IDETABLISSEMENT, IDINDIVIDU) VALUES (?,?,?,?,?,?,?,?)";
                $stmt = $dbh->prepare($query);
                if ($stmt->execute(array($hd, $hf, $jour, base64_decode($lib->securite_xss($_GET['idEMP'])), $lib->securite_xss($_POST['IDMATIERE']), $lib->securite_xss($_POST['IDSALLE']), $lib->securite_xss($_SESSION['etab']), $lib->securite_xss($_POST['IDINDIVIDU'])))) {
                    $msg = "Votre nouveau cours a été ajouté avec succes";
                    $res = 1;
                } else {
                    $msg = "L'ajout de votre nouveau cours a échoué";
                    $res = -1;
                }
            } catch (PDOException $e) {
                $msg = "L'ajout de votre nouveau cours a échoué";

            }
        }else{
            $msg1 = "Veuillez renseigner les champs!";

        }

    }
    unset($_POST);
}

$idClasse = (isset($_GET['idClasse'])) ? base64_decode($lib->securite_xss($_GET['idClasse'])) : $_SESSION["idClasse"];
$idPeriode = (isset($_GET['idPeriode'])) ? base64_decode($lib->securite_xss($_GET['idPeriode'])) : $_SESSION["idPeriode"];
$nomClasse = (isset($_GET['NOM'])) ? str_replace("-", " ", base64_decode($lib->securite_xss($_GET['NOM']))) : $_SESSION["NOM"];
$_SESSION["idClasse"] = $idClasse;
$_SESSION["idPeriode"] = $idPeriode;
$_SESSION["NOM"] = $nomClasse;

$query = "SELECT DETAIL_TIMETABLE.IDDETAIL_TIMETABLE as idDTT,DETAIL_TIMETABLE.JOUR_SEMAINE as jour,DETAIL_TIMETABLE.DATEDEBUT as debut,DETAIL_TIMETABLE.DATEFIN as fin,MATIERE.IDMATIERE as idmatiere,MATIERE.LIBELLE as libmatiere,SALL_DE_CLASSE.IDSALL_DE_CLASSE as idsalle,SALL_DE_CLASSE.NOM_SALLE as libsalle,INDIVIDU.IDINDIVIDU as idprof,INDIVIDU.PRENOMS as prenom,INDIVIDU.NOM as nom
              FROM   DETAIL_TIMETABLE
              INNER JOIN MATIERE ON DETAIL_TIMETABLE.IDMATIERE = MATIERE.IDMATIERE
              INNER JOIN SALL_DE_CLASSE ON DETAIL_TIMETABLE.IDSALL_DE_CLASSE = SALL_DE_CLASSE.IDSALL_DE_CLASSE
              INNER JOIN EMPLOIEDUTEMPS ON DETAIL_TIMETABLE.IDEMPLOIEDUTEMPS = EMPLOIEDUTEMPS.IDEMPLOIEDUTEMPS
              INNER JOIN INDIVIDU ON DETAIL_TIMETABLE.IDINDIVIDU = INDIVIDU.IDINDIVIDU
              WHERE EMPLOIEDUTEMPS.IDPERIODE = " . $idPeriode . " AND EMPLOIEDUTEMPS.IDANNEE = " . $lib->securite_xss($_SESSION["ANNEESSCOLAIRE"]) . "  AND EMPLOIEDUTEMPS.IDCLASSROOM = " . $idClasse . "
              ORDER BY DETAIL_TIMETABLE.DATEDEBUT ASC ";
$stmt = $dbh->prepare($query);
$stmt->execute();
$tabEmp = $stmt->fetchAll(PDO::FETCH_OBJ);

if (isset($_SESSION['etab'])) {
    $colname_rq_etablissement = $lib->securite_xss($_SESSION['etab']);
}

$query_rq_matiere = $dbh->query("SELECT `IDMATIERE`, `LIBELLE`, `IDETABLISSEMENT` FROM `MATIERE` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);

$query_rq_matiere2 = $dbh->query("SELECT `IDMATIERE`, `LIBELLE`, `IDETABLISSEMENT` FROM `MATIERE` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);

$query_rq_salle = $dbh->query("SELECT `IDSALL_DE_CLASSE`, `NOM_SALLE`, `IDTYPE_SALLE`, `IDETABLISSEMENT`, `NBR_PLACES` FROM `SALL_DE_CLASSE` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);

$query_rq_salle2 = $dbh->query("SELECT `IDSALL_DE_CLASSE`, `NOM_SALLE`, `IDTYPE_SALLE`, `IDETABLISSEMENT`, `NBR_PLACES` FROM `SALL_DE_CLASSE` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);

$query_rq_prof = $dbh->query("SELECT `IDINDIVIDU`, `MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `TELDOM`, `COURRIEL`, `LOGIN`, `MP`, `CODE`, `BIOGRAPHIE`, `PHOTO_FACE`, `IDETABLISSEMENT`, `IDTYPEINDIVIDU`, `SEXE`, `IDTUTEUR`, `LIEN_PARENTE`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`, `IDSECTEUR`, `LIEU_TRAVAIL`, `PATHOLOGIE`, `ALLERGIE`, `MEDECIN_TRAITANT`, `FICHE_RECTO`, `FICHE_VERSO` FROM `INDIVIDU` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement . " AND IDTYPEINDIVIDU=7");

$query_rq_prof2 = $dbh->query("SELECT `IDINDIVIDU`, `MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `TELDOM`, `COURRIEL`, `LOGIN`, `MP`, `CODE`, `BIOGRAPHIE`, `PHOTO_FACE`, `IDETABLISSEMENT`, `IDTYPEINDIVIDU`, `SEXE`, `IDTUTEUR`, `LIEN_PARENTE`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`, `IDSECTEUR`, `LIEU_TRAVAIL`, `PATHOLOGIE`, `ALLERGIE`, `MEDECIN_TRAITANT`, `FICHE_RECTO`, `FICHE_VERSO` FROM `INDIVIDU` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement . " AND IDTYPEINDIVIDU=7");

?>
<?php include('header.php'); ?>
<ul class="breadcrumb">
    <li><a href="#"> Emploi du Temps </a></li>
    <li> Modification emploi du temps</li>
</ul>
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">
        <div class="panel panel-default">
            <div class="col-lg-12">
                <div class="col-lg-10">
                    <p>
                        <h4 style="color:#E05D1F;text-underline: dash;">Modification de l'emploi du temps de la classe : <b><?= $nomClasse; ?></b></h4>
                    </p>
                </div>
                <div class="col-lg-2" style="padding-top: 8px;">
                    <button class="btn btn-success" onmouseover="$('.alert').addClass('hidden');" data-toggle="modal" href="#add-emp">
                        Ajouter nouveau cours
                    </button>
                </div>

            </div>
            <div class="panel-body">
                <?php if (isset($msg) && isset($res) && !is_null($msg) && !is_null($res)) {
                    if ($res == 1) {  ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $msg; ?>
                        </div>
                    <?php } else {  ?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $msg; ?>
                        </div>
                    <?php }?>
                <?php } ?>
                <?php if(!is_null($msg1)){  ?>
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $msg1; ?>
                    </div>
                <?php } ?>
                <table class="table table-bordered">
                    <thead>
                    <tr>
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
                        <td>
                            <table class="table">
                                <tbody>
                                <?php foreach ($tabEmp as $oneEmp) { ?>
                                    <?php if ($oneEmp->jour == "LUN") { ?>
                                        <tr style='background-color: #f1f5f9;'>
                                            <td class="LUNDI">
                                                <i class="<?= substr($oneEmp->debut, 0, 5); ?>"></i>
                                                <i class="<?= substr($oneEmp->fin, 0, 5); ?>"></i>
                                                <a data-toggle="modal"
                                                   onclick="setAction('<?= $oneEmp->idDTT; ?>','<?= $oneEmp->idmatiere; ?>','<?= $oneEmp->idprof; ?>','<?= $oneEmp->idsalle; ?>','LUNDI','<?= $oneEmp->debut; ?>','<?= $oneEmp->fin; ?>');"
                                                   href="#modif-emp">
                                                    <b><?= substr($oneEmp->debut, 0, 5) . " à " . substr($oneEmp->fin, 0, 5); ?></b><br/><b><?= $oneEmp->libmatiere; ?></b><br/>
                                                    <b>salle</b> : <?= $oneEmp->libsalle; ?><br/><b>Prof</b>
                                                    : <?= $oneEmp->prenom . " " . $oneEmp->nom; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table">
                                <tbody>
                                <?php foreach ($tabEmp as $oneEmp) { ?>
                                    <?php if ($oneEmp->jour == "MAR") { ?>
                                        <tr style='background-color: #f1f5f9;'>
                                            <td class="MARDI">
                                                <i class="<?= substr($oneEmp->debut, 0, 5); ?>"></i>
                                                <i class="<?= substr($oneEmp->fin, 0, 5); ?>"></i>
                                                <a data-toggle="modal"
                                                   onclick="setAction('<?= $oneEmp->idDTT; ?>','<?= $oneEmp->idmatiere; ?>','<?= $oneEmp->idprof; ?>','<?= $oneEmp->idsalle; ?>','MARDI','<?= $oneEmp->debut; ?>','<?= $oneEmp->fin; ?>');"
                                                   href="#modif-emp">
                                                    <b><?= substr($oneEmp->debut, 0, 5) . " à " . substr($oneEmp->fin, 0, 5); ?></b><br/><b><?= $oneEmp->libmatiere; ?></b><br/>
                                                    <b>salle</b> : <?= $oneEmp->libsalle; ?><br/><b>Prof</b>
                                                    : <?= $oneEmp->prenom . " " . $oneEmp->nom; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table">
                                <tbody>
                                <?php foreach ($tabEmp as $oneEmp) { ?>
                                    <?php if ($oneEmp->jour == "MER") { ?>
                                        <tr style='background-color: #f1f5f9;'>
                                            <td class="MERCREDI">
                                                <i class="<?= substr($oneEmp->debut, 0, 5); ?>"></i>
                                                <i class="<?= substr($oneEmp->fin, 0, 5); ?>"></i>
                                                <a data-toggle="modal"
                                                   onclick="setAction('<?= $oneEmp->idDTT; ?>','<?= $oneEmp->idmatiere; ?>','<?= $oneEmp->idprof; ?>','<?= $oneEmp->idsalle; ?>','MERCREDI','<?= $oneEmp->debut; ?>','<?= $oneEmp->fin; ?>');"
                                                   href="#modif-emp">
                                                    <b><?= substr($oneEmp->debut, 0, 5) . " à " . substr($oneEmp->fin, 0, 5); ?></b><br/><b><?= $oneEmp->libmatiere; ?></b><br/>
                                                    <b>salle</b> : <?= $oneEmp->libsalle; ?><br/><b>Prof</b>
                                                    : <?= $oneEmp->prenom . " " . $oneEmp->nom; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table">
                                <tbody>
                                <?php foreach ($tabEmp as $oneEmp) { ?>
                                    <?php if ($oneEmp->jour == "JEU") { ?>
                                        <tr style='background-color: #f1f5f9;'>
                                            <td class="JEUDI">
                                                <i class="<?= substr($oneEmp->debut, 0, 5); ?>"></i>
                                                <i class="<?= substr($oneEmp->fin, 0, 5); ?>"></i>
                                                <a data-toggle="modal"
                                                   onclick="setAction('<?= $oneEmp->idDTT; ?>','<?= $oneEmp->idmatiere; ?>','<?= $oneEmp->idprof; ?>','<?= $oneEmp->idsalle; ?>','JEUDI','<?= $oneEmp->debut; ?>','<?= $oneEmp->fin; ?>');"
                                                   href="#modif-emp">
                                                    <b><?= substr($oneEmp->debut, 0, 5) . " à " . substr($oneEmp->fin, 0, 5); ?></b><br/><b><?= $oneEmp->libmatiere; ?></b><br/>
                                                    <b>salle</b> : <?= $oneEmp->libsalle; ?><br/><b>Prof</b>
                                                    : <?= $oneEmp->prenom . " " . $oneEmp->nom; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table">
                                <tbody>
                                <?php foreach ($tabEmp as $oneEmp) { ?>
                                    <?php if ($oneEmp->jour == "VEN") { ?>
                                        <tr style='background-color: #f1f5f9;'>
                                            <td class="VENDREDI">
                                                <i class="<?= substr($oneEmp->debut, 0, 5); ?>"></i>
                                                <i class="<?= substr($oneEmp->fin, 0, 5); ?>"></i>
                                                <a data-toggle="modal"
                                                   onclick="setAction('<?= $oneEmp->idDTT; ?>','<?= $oneEmp->idmatiere; ?>','<?= $oneEmp->idprof; ?>','<?= $oneEmp->idsalle; ?>','VENDREDI','<?= $oneEmp->debut; ?>','<?= $oneEmp->fin; ?>');"
                                                   href="#modif-emp">
                                                    <b><?= substr($oneEmp->debut, 0, 5) . " à " . substr($oneEmp->fin, 0, 5); ?></b><br/><b><?= $oneEmp->libmatiere; ?></b><br/>
                                                    <b>salle</b> : <?= $oneEmp->libsalle; ?><br/><b>Prof</b>
                                                    : <?= $oneEmp->prenom . " " . $oneEmp->nom; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table">
                                <tbody>
                                <?php foreach ($tabEmp as $oneEmp) { ?>
                                    <?php if ($oneEmp->jour == "SAM") { ?>
                                        <tr style='background-color: #f1f5f9;'>
                                            <td class="SAMEDI">
                                                <i class="<?= substr($oneEmp->debut, 0, 5); ?>"></i>
                                                <i class="<?= substr($oneEmp->fin, 0, 5); ?>"></i>
                                                <a data-toggle="modal"
                                                   onclick="setAction('<?= $oneEmp->idDTT; ?>','<?= $oneEmp->idmatiere; ?>','<?= $oneEmp->idprof; ?>','<?= $oneEmp->idsalle; ?>','SAMEDI','<?= $oneEmp->debut; ?>','<?= $oneEmp->fin; ?>');"
                                                   href="#modif-emp">
                                                    <b><?= substr($oneEmp->debut, 0, 5) . " à " . substr($oneEmp->fin, 0, 5); ?></b><br/><b><?= $oneEmp->libmatiere; ?></b><br/>
                                                    <b>salle</b> : <?= $oneEmp->libsalle; ?><br/><b>Prof</b>
                                                    : <?= $oneEmp->prenom . " " . $oneEmp->nom; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <form class="form-modal" onsubmit="return validCours('JOUR','HEUREDEBUT','HEUREFIN');" id="form-modif-emp" method="POST">
                <div class="modal fade" id="modif-emp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #1F85C7;">
                                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">
                                    <i style="color: white;" class="bold glyphicon glyphicon-remove"></i>
                                </button>
                                <h4 class="modal-title" style="color: white;">
                                    <i style="margin-right: 5px;" class="fa fa-edit"></i>
                                    Modification emploi du temps
                                </h4>
                            </div>
                            <div class="modal-body">
                                <div class="panel-body">
                                    <fieldset>
                                        <!--	IDPERIODE 	IDETABLISSEMENT 	IDCLASSROOM
                                         DATEDEBUT 	DATEFIN 	JOUR_SEMAINE 	IDEMPLOIEDUTEMPS	IDMATIERE 	IDSALL_DE_CLASSE 	IDETABLISSEMENT 	IDINDIVIDU 	REPETITION -->

                                        <div class="col-lg-6">
                                            <label>Matiere:</label>
                                            <select id="js_matier" name="IDMATIERE" required class="validate[required] form-control">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($query_rq_matiere->fetchAll() as $row_rq_matiere) { ?>
                                                    <option
                                                            value="<?= $row_rq_matiere['IDMATIERE']; ?>"><?= $row_rq_matiere['LIBELLE']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Professeur:</label>
                                            <select id="js_prof" name="IDINDIVIDU" required class="validate[required] form-control">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($query_rq_prof->fetchAll() as $row_rq_prof) { ?>
                                                    <option
                                                            value="<?= $row_rq_prof['IDINDIVIDU']; ?>"><?= $row_rq_prof['PRENOMS'] . "  " . $row_rq_prof['NOM']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Salle:</label>
                                            <select id="js_salle" name="IDSALLE" required class="validate[required] form-control">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($query_rq_salle->fetchAll() as $row_rq_salle) { ?>
                                                    <option
                                                            value="<?= $row_rq_salle['IDSALL_DE_CLASSE']; ?>"><?= $row_rq_salle['NOM_SALLE']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Jour du Cours:</label>
                                            <select name="JOUR" id="JOUR" required class="validate[required] form-control">
                                                <option value="">--Selectionner--</option>
                                                <option  value="LUNDI">LUNDI</option>
                                                <option  value="MARDI">MARDI</option>
                                                <option  value="MERCREDI">MERCREDI</option>
                                                <option  value="JEUDI">JEUDI</option>
                                                <option  value="VENDREDI">VENDREDI</option>
                                                <option value="SAMEDI">SAMEDI</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Heure de debut du Cours:</label>
                                            <input type="time" class="form-control" required name="HEUREDEBUT" id="HEUREDEBUT"/>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>heure de fin du Cours:</label>
                                            <input type="time" class="form-control" required name="HEUREFIN" id="HEUREFIN"/>
                                        </div>
                                    </fieldset>
                                    <br/>
                                    <div class="alert alert-danger hidden">
                                        Modification impossible ! Collision entre les heures de cours.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top-color: #2b4f89;">
                                <button type="submit" class="btn btn-sm btn-success">Valider</button>
                                <button class="btn btn-sm btn-default text-danger" type="button" data-dismiss="modal">
                                    <b>Annuler</b>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form class="form-modal" onsubmit="return validCours('JOUR1','HEUREDEBUT1','HEUREFIN1');" action="modifEmploiTemps.php?idEMP=<?php if ($res==1) echo $idemp; else echo $lib->securite_xss($_GET['idET']); ?>" method="POST">
                <div class="modal fade" id="add-emp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #1F85C7;">
                                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">
                                    <i style="color: white;" class="bold glyphicon glyphicon-remove"></i>
                                </button>
                                <h4 class="modal-title" style="color: white;">
                                    <i style="margin-right: 5px;" class="fa fa-plus"></i>
                                    Nouveau cours
                                </h4>
                            </div>
                            <div class="modal-body">
                                <div class="panel-body">
                                    <fieldset>
                                        <div class="col-lg-6">
                                            <label>Matiere:</label>
                                            <select name="IDMATIERE" id="IDMATIERE" required class="validate[required] form-control selectpicker"  data-live-search="true">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($query_rq_matiere2->fetchAll() as $row_rq_matiere) { ?>
                                                    <option value="<?= $row_rq_matiere['IDMATIERE']; ?>">
                                                        <?= $row_rq_matiere['LIBELLE']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" value="<? if($res==1) echo $idemp; else echo $lib->securite_xss($_GET['idET']); ?>" name="idemp">
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Professeur:</label>
                                            <select name="IDINDIVIDU" id="IDINDIVIDU" required class="validate[required] form-control selectpicker"  data-live-search="true">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($query_rq_prof2->fetchAll() as $row_rq_prof) { ?>
                                                    <option value="<?= $row_rq_prof['IDINDIVIDU']; ?>">
                                                        <?= $row_rq_prof['PRENOMS'] . "  " . $row_rq_prof['NOM']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Salle:</label>
                                            <select name="IDSALLE" id="IDSALLE" required class="validate[required] form-control selectpicker"  data-live-search="true">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($query_rq_salle2->fetchAll() as $row_rq_salle) { ?>
                                                    <option value="<?= $row_rq_salle['IDSALL_DE_CLASSE']; ?>">
                                                        <?= $row_rq_salle['NOM_SALLE']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Jour du Cours:</label>
                                            <select name="JOUR" id="JOUR1" required class="validate[required] form-control selectpicker"  data-live-search="true">
                                                <option value="">--Selectionner--</option>
                                                <option value="LUNDI">LUNDI</option>
                                                <option value="MARDI">MARDI</option>
                                                <option value="MERCREDI">MERCREDI</option>
                                                <option value="JEUDI">JEUDI</option>
                                                <option value="VENDREDI">VENDREDI</option>
                                                <option value="SAMEDI">SAMEDI</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                            <label>Heure de debut du Cours:</label>
                                            <input type="time" class="form-control" required id="HEUREDEBUT1" name="HEUREDEBUT"/>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>heure de fin du Cours:</label>
                                            <input type="time" class="form-control" required id="HEUREFIN1" name="HEUREFIN"/>
                                        </div>
                                    </fieldset>
                                    <br/>
                                    <div class="alert alert-danger hidden">
                                        Ajout impossible ! Collision entre les heures de cours.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top-color: #2b4f89;">
                                <button type="submit" class="btn btn-sm btn-success" id="valider">Valider</button>
                                <button class="btn btn-sm btn-default text-danger" type="button" data-dismiss="modal">
                                    <b>Annuler</b></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <script>
                function setAction(idDTT, idmat, idprof, idsal, jour, heurdeb, heurfin) {
            // alert("mat :"+idmat+" prof : "+idprof+" salle :"+idsal+ " "+jour +" "+heurdeb+" "+heurfin);
                    alert(jour);
                    $('.alert').addClass('hidden');
                    $('#form-modif-emp').attr("action", "modifEmploiTemps.php?act=1&idDTT=" + idDTT);
                    $("#js_matier").val(idmat);
                    $("#js_prof").val(idprof);
                    $("#js_salle").val(idsal);
                    $("#JOUR").val(jour);
                    $("#HEUREDEBUT").val(heurdeb)  ;
                    $("#HEUREFIN").val(heurfin);
                }
                function validCours(idJour,idHD,idHF) {
                    var jour = $("#" + idJour)[0].value;
                    var heurdeb = $("#" + idHD)[0].value;
                    var heurfin = $("#" + idHF)[0].value;
                    alert(jour);
                        var elements = $("." + jour);
                        alert(elements.length);
                        if (elements.length != 0) {
                            for (var i = 0; i < elements.length; i++) {
                                if (heurdeb < "08:00" || heurdeb >= heurfin ||
                                    heurdeb < elements[i].children[0].className && heurfin > elements[i].children[0].className ||
                                    heurdeb > elements[i].children[0].className && heurdeb < elements[i].children[1].className ||
                                    heurdeb == elements[i].children[0].className) {
                                    $(".alert").removeClass("hidden");
                                    return false;
                                }
                            }
                            $(".alert").addClass("hidden");
                            return true;
                        }

                }
            </script>
        </div>
    </div>
</div>
</div>
</div>
</div>

<?php include('footer.php'); ?>