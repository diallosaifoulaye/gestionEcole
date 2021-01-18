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
    $lib->Restreindre($lib->Est_autoriser(20, $lib->securite_xss($_SESSION['profil'])));


$colname_Etab_rq_individu = "-1";
if (isset($_SESSION['etab'])) {
    $colname_Etab_rq_individu = $lib->securite_xss($_SESSION['etab']);
}
$colname_anne_rq_individu = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $colname_anne_rq_individu = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}
/*
$query_rq_individu = $dbh->query("SELECT RECRUTE_PROF.IDRECRUTE_PROF, RECRUTE_PROF.TARIF_HORAIRE, RECRUTE_PROF.VOLUME_HORAIRE, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, MATIERE.LIBELLE as matiere, CLASSROOM.LIBELLE as classe, INDIVIDU.PHOTO_FACE, INDIVIDU.IDINDIVIDU 
FROM RECRUTE_PROF, INDIVIDU, MATIERE, CLASSROOM, CLASSE_ENSEIGNE 
WHERE RECRUTE_PROF.IDETABLISSEMENT=" . $colname_Etab_rq_individu . " 
AND RECRUTE_PROF.IDINDIVIDU=INDIVIDU.IDINDIVIDU 
AND RECRUTE_PROF.IDMATIERE=MATIERE.IDMATIERE 
AND RECRUTE_PROF.IDRECRUTE_PROF = CLASSE_ENSEIGNE.IDRECRUTE_PROF 
AND CLASSE_ENSEIGNE.IDCLASSROM = CLASSROOM.IDCLASSROOM 

AND RECRUTE_PROF.IDANNEESSCOLAIRE=" . $colname_anne_rq_individu . " GROUP BY INDIVIDU.IDINDIVIDU ");*/



$query_rq_individu = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.IDINDIVIDU 
FROM RECRUTE_PROF, INDIVIDU
WHERE RECRUTE_PROF.IDETABLISSEMENT=" . $colname_Etab_rq_individu . " 
AND RECRUTE_PROF.IDINDIVIDU=INDIVIDU.IDINDIVIDU 
AND RECRUTE_PROF.IDANNEESSCOLAIRE=" . $colname_anne_rq_individu . " GROUP BY INDIVIDU.IDINDIVIDU ");

?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Evaluations</a></li>
    <li>Gestion des contrôles</li>
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

                <?php if (isset($_GET['msg']) && $lib->securite_xss($_GET['msg']) != '') {

                    if (isset($_GET['res']) && $lib->securite_xss($_GET['res']) == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }
                    if (isset($_GET['res']) && $lib->affichage_xss($_GET['res']) != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } ?>

                <form id="form" name="form1" method="POST" action="ficheControle.php" class="form-inline">
                    <fieldset class="cadre">
                        <legend> FILTRE</legend>


                        <div class="form-group col-lg-12">
                            <div class="col-md-9">
                                <div class="col-sm-2" style="margin-top:7px;margin-right:-30px">
                                    <label for="exampleInputName2">Choisir professeur :</label>
                                </div>
                                <div class="col-sm-10">
                                    <select name="IDINDIVIDU" id="IDINDIVIDU" class="form-control selectpicker" data-live-search="true" onclick="controlButton();">
                                        <option value="">--Selectionner--</option>
                                        <?php foreach ($query_rq_individu->fetchAll() as $row_rq_individu) { ?>
                                            <option
                                                    value="<?php echo base64_encode($row_rq_individu['IDINDIVIDU']) ?>"><?php echo $row_rq_individu['PRENOMS']."  "; ?><?php echo $row_rq_individu['NOM'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary" id="idvalider" style="display: none">Rechercher</button>
                            </div>

                        </div>

                    </fieldset>
                </form>


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

<script>
    function controlButton() {
        var selectedNiv = $("#IDINDIVIDU").find("option:selected").text()

        if(selectedNiv == "--Selectionner--") {
            $('#idvalider').css("display","none")
        }else{
            $('#idvalider').css("display","block")
        }
    }

</script>
