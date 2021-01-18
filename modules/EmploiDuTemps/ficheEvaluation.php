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

$colname_rq_classe = "-1";
if (isset($_GET['IDINDIVIDU'])) {
    $colname_rq_classe = $_GET['IDINDIVIDU'];
}
$coln_anne_rq_classe = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $coln_anne_rq_classe = $_SESSION['ANNEESSCOLAIRE'];
}

$query_an = $dbh->query("SELECT IDANNEESSCOLAIRE, LIBELLE_ANNEESSOCLAIRE, DATE_DEBUT, DATE_FIN, ETAT, IDETABLISSEMENT 
FROM ANNEESSCOLAIRE WHERE IDANNEESSCOLAIRE=" . $coln_anne_rq_classe);
$row_rq_an = $query_an->fetchObject();
$date_debut = $row_rq_an->DATE_DEBUT;
$date_fin = $row_rq_an->DATE_FIN;

$query_rq_classe = $dbh->query("SELECT rp.IDRECRUTE_PROF, ce.IDCLASSROM, cl.LIBELLE 
                                        FROM RECRUTE_PROF rp, CLASSROOM cl, CLASSE_ENSEIGNE ce
                                        WHERE rp.IDRECRUTE_PROF = ce.IDRECRUTE_PROF 
                                        AND ce.IDCLASSROM = cl.IDCLASSROOM 
                                        AND rp.IDINDIVIDU =" . $colname_rq_classe . " 
                                        GROUP BY cl.IDCLASSROOM");

//echo $query_rq_classe;
//echo $query_rq_profeseur;

$colname_rq_salle_classe = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_salle_classe = $_SESSION['etab'];
}

$query_rq_salle_classe = $dbh->query("SELECT * FROM SALL_DE_CLASSE WHERE IDETABLISSEMENT = " . $colname_rq_salle_classe);


$colname_rq_matiere = "-1";
if (isset($_GET['IDINDIVIDU'])) {
    $colname_rq_matiere = $_GET['IDINDIVIDU'];
}
$colnameann_rq_matiere = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $colnameann_rq_matiere = $_SESSION['ANNEESSCOLAIRE'];
}

$query_rq_matiere = $dbh->query("SELECT RECRUTE_PROF.IDRECRUTE_PROF, MATIERE.LIBELLE, MATIERE.IDMATIERE 
                                            FROM RECRUTE_PROF, MATIERE, MATIERE_ENSEIGNE 
                                            WHERE  RECRUTE_PROF.IDINDIVIDU = MATIERE_ENSEIGNE.ID_INDIVIDU
                                            AND MATIERE_ENSEIGNE.ID_MATIERE = MATIERE.IDMATIERE 
                                            AND RECRUTE_PROF.IDINDIVIDU=" . $colname_rq_matiere);


$colname_rq_profeseur = "-1";
if (isset($_GET['IDINDIVIDU'])) {
    $colname_rq_profeseur = $_GET['IDINDIVIDU'];
}

$query_rq_profeseur = $dbh->query("SELECT * FROM INDIVIDU WHERE IDINDIVIDU = " . $colname_rq_profeseur);
$row_rq_profeseur = $query_rq_profeseur->fetchObject();


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
    <div class="row">

        <div class="panel panel-default">
            <div class="panel-heading">
                &nbsp;&nbsp;&nbsp;

                <div class="btn-group pull-right">


                </div>

            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) { ?>

                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?>
                        </div>

                    <?php } if (isset($_GET['res']) && $_GET['res'] != 1) {?>

                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?>
                        </div>

                    <?php } ?>

                <?php } ?>

                <fieldset class="cadre">
                    <legend>Le cours dispense
                        par <?php echo $row_rq_profeseur->PRENOMS; ?>  <?php echo $row_rq_profeseur->NOM; ?></legend>

                    <form action="validerFicheEvaluation.php" method="post" name="form1" id="form1">


                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="">MOIS</label>

                                <div>
                                    <select name="MOIS" class="form-control" required>
                                        <option value="">--- Selectionner le mois ---</option>

                                        <?php

                                        function debug($var){

                                            return $var;

                                        }

                                        $date1 = new DateTime($date_debut);
                                        $date2 = new DateTime($date_fin);

                                        $mois = array();
                                        $mois[] =  $date1->format('m-Y');
                                        while($date1 <= $date2){
                                            $date1->add(new DateInterval("P1M"));
                                            $mois[] = $date1->format('m-Y');

                                        }
                                        foreach (debug($mois) as $row) { ?>

                                            <option  value="<?php echo $row; ?>"><?php echo $lib->affiche_mois($row); ?></option>

                                        <?php  } ?>

                                    </select>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-6">
                            <label>Classe</label>
                            <select name="IDCLASSROOM" class="form-control" required>
                                <option value="">--Selectionner classe--</option>
                                <?php foreach ($query_rq_classe->fetchAll() as $row_rq_classe) { ?>
                                    <option value="<?php echo $row_rq_classe['IDCLASSROM'] ?>"><?php echo $row_rq_classe['LIBELLE'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label>Matiere</label>
                            <select name="IDMATIERE" class="form-control" required>
                                <option value="">--Selectionner matiére--</option>
                                <?php foreach ($query_rq_matiere->fetchAll() as $row_rq_matiere) { ?>
                                    <option
                                            value="<?php echo $row_rq_matiere['IDMATIERE'] ?>"><?php echo $row_rq_matiere['LIBELLE'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label>Date du cours</label>
                            <input name="DATE" id="date_foo" type="text" class="form-control" required/>
                        </div>

                        <div class="col-lg-6">
                            <div class="col-lg-6">
                                <label>Heure de début</label>
                                <input name="HEUREDEBUTCOURS" type="time" class="form-control" required/>
                            </div>
                            <div class="col-lg-6">
                                <label>Heure de fin</label>
                                <input name="HEUREFINCOURS" type="time" class="form-control" required/>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label>Salle</label>
                            <select name="IDSALL_DE_CLASSE" class="form-control" required>
                                <option value="">--Selectionner--</option>
                                <?php foreach ($query_rq_salle_classe->fetchAll() as $row_rq_salle_classe) { ?>
                                    <option
                                            value="<?php echo $row_rq_salle_classe['IDSALL_DE_CLASSE'] ?>"><?php echo $row_rq_salle_classe['NOM_SALLE'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label>Titre cours</label>
                            <input name="TITRE_COURS" type="text" class="form-control" required/>
                        </div>

                        <div class="col-lg-12">
                            <label>Contenu du cours</label>
                            <textarea name="CONTENUCOURS" id="mytextarea" required="required"></textarea>
                        </div>

                        <div class="col-lg-4"><br>

                        </div>

                        <div class="col-lg-4"><br>
                            <button type="reset" class="btn btn-warning">Réinitialiser</button>
                        </div>

                        <div class="col-lg-4 pull-right"><br>
                            <button type="submit" class="btn btn-success">Valider</button>
                        </div>


                        <input type="hidden" name="IDINDIVIDU" value="<?php echo $lib->securite_xss($_GET['IDINDIVIDU']); ?>"/>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $lib->securite_xss($_SESSION['etab']); ?>"/>
                        <input type="hidden" name="ANNEESCOLAIRE" value="<?php echo $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']); ?>"/>

                    </form>

                </fieldset>




            </div>
        </div>
    </div>
    <!-- END WIDGETS -->


</div>
<!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->

<?php include('footer.php'); ?>