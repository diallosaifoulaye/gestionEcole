<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(23, $_SESSION['profil']));

require_once("classe/DispenseCoursManager.php");
require_once("classe/DispenseCours.php");
$cours = new DispenseCoursManager($dbh, 'DISPENSER_COURS');


$colname_rq_cours_etab = "-1";
if (isset($_GET['IDDISPENSER_COURS'])) {
    $colname_rq_cours_etab = $lib->securite_xss($_GET['IDDISPENSER_COURS']);
}
$query_rq_cours_etab = $dbh->query("SELECT IDDISPENSER_COURS, IDCLASSROOM, IDINDIVIDU, DATE, HEUREDEBUTCOURS, HEUREFINCOURS, 
                                              TITRE_COURS, CONTENUCOURS, IDSALL_DE_CLASSE, IDETABLISSEMENT, IDMATIERE, ANNEESCOLAIRE, MOIS 
                                              FROM DISPENSER_COURS WHERE IDDISPENSER_COURS = $colname_rq_cours_etab");
foreach ($query_rq_cours_etab->fetchAll() as $row_rq_cours_etab) {
    $id = $row_rq_cours_etab['IDDISPENSER_COURS'];
    $IDCLASSROOM = $row_rq_cours_etab['IDCLASSROOM'];
    $IDINDIVIDU = $row_rq_cours_etab['IDINDIVIDU'];
    $DATE = $row_rq_cours_etab['DATE'];
    $DATEDEBUTCOURS = $row_rq_cours_etab['HEUREDEBUTCOURS'];
    $DATEFINCOURS = $row_rq_cours_etab['HEUREFINCOURS'];
    $TITRE_COURS = $row_rq_cours_etab['TITRE_COURS'];
    $CONTENUCOURS = $row_rq_cours_etab['CONTENUCOURS'];
    $IDSALL_DE_CLASSE = $row_rq_cours_etab['IDSALL_DE_CLASSE'];
    $IDETABLISSEMENT = $row_rq_cours_etab['IDETABLISSEMENT'];
    $IDMATIERE = $row_rq_cours_etab['IDMATIERE'];
}

$colname_Etab_rq_individu = "-1";
if (isset($_SESSION['etab'])) {
    $colname_Etab_rq_individu = $lib->securite_xss($_SESSION['etab']);
}

try
{
    $query_rq_salle_classe = $dbh->query("SELECT IDSALL_DE_CLASSE, NOM_SALLE, IDTYPE_SALLE, IDETABLISSEMENT, NBR_PLACES 
                                                FROM SALL_DE_CLASSE 
                                                WHERE IDETABLISSEMENT = " . $colname_Etab_rq_individu);
    $rs_classe = $query_rq_salle_classe->fetchAll();

    $query_rq_profeseur = $dbh->query("SELECT * FROM INDIVIDU WHERE IDINDIVIDU = " . $IDINDIVIDU);
    $row_rq_profeseur = $query_rq_profeseur->fetchObject();

    $query_rq_matiere = $dbh->query("SELECT MATIERE.LIBELLE, MATIERE.IDMATIERE FROM  MATIERE WHERE MATIERE.IDMATIERE =" . $IDMATIERE);
    $row_rq_matiere = $query_rq_matiere->fetchObject();

    $query_rq_classe = $dbh->query("SELECT CLASSROOM.LIBELLE FROM CLASSROOM WHERE CLASSROOM.IDCLASSROOM = " . $IDCLASSROOM);
    $row_rq_classe = $query_rq_classe->fetchObject();
}
catch (PDOException $e)
{
    echo -2;
}




?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Evaluations</a></li>
    <li>Mise a jour des cours</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
        <fieldset class="cadre">
            <legend> Professeur: <?php echo $row_rq_profeseur->PRENOMS; ?>  <?php echo $row_rq_profeseur->NOM; ?>
                matiere: <?php echo $row_rq_matiere->LIBELLE; ?>
                classe: <?php echo $row_rq_classe->LIBELLE; ?> </legend>
            <form action="updateCours.php?IDDISPENSER_COURS=<?= $id; ?>" method="POST">

                <div class="row">

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>DATE</label>

                            <div>
                                <input type="text" id="date_foo" name="DATE" required class="form-control"
                                       value="<?php echo str_replace("-","/",$DATE); ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>HEURE DE DEBUT</label>

                            <div>
                                <input type="time" name="HEUREDEBUTCOURS" required class="form-control"
                                       value="<?php echo $DATEDEBUTCOURS; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>HEURE DE FIN</label>

                            <div>
                                <input type="time" name="HEUREFINCOURS" required class="form-control"
                                       value="<?php echo $DATEFINCOURS; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>TITRE_COURS</label>

                            <div>
                                <input type="text" id="TITRE_COURS" name="TITRE_COURS" required class="form-control"
                                       value="<?php echo $TITRE_COURS; ?>"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>CONTENUCOURS</label>

                            <div>
                                <textarea id="mytextarea" name="CONTENUCOURS" required
                                          class="form-control"> <?php echo $CONTENUCOURS; ?></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>SALLE DE CLASSE</label>

                            <div>
                                <select name="IDSALL_DE_CLASSE" class="form-control">
                                    <?php
                                    foreach ($rs_classe as $salle) {

                                        ?>
                                        <option
                                            value=" <?php echo $salle['IDSALL_DE_CLASSE']; ?>" <?php if ($IDSALL_DE_CLASSE == $salle['IDSALL_DE_CLASSE']) echo "selected" ?>><?php echo $salle['NOM_SALLE']; ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="col-lg-12"><br>
                        <div class="col-lg-offset-10"><input type="submit" class="btn btn-success" value="modifier"/></div>
                    </div>
                </div>
            </form>

        </fieldset>


        <!-- END WIDGETS -->


    </div>
        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->


<?php include('footer.php'); ?>