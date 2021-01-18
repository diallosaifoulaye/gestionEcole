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
    //var_dump($_SESSION['etab']);exit;
$colname_rq_emploi_du_temps_classe = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_emploi_du_temps_classe = $lib->securite_xss($_SESSION['etab']);
}
$query_rq_niv = $dbh->query("SELECT IDNIVEAU, LIBELLE, IDETABLISSEMENT FROM NIVEAU WHERE IDETABLISSEMENT = " . $colname_rq_emploi_du_temps_classe);
$rs_niv = $query_rq_niv->fetchAll();

$query_rq_class=$dbh->query("SELECT `IDCLASSROOM`, `LIBELLE`, `IDNIVEAU`, `IDETABLISSEMENT`, `IDSERIE` FROM `CLASSROOM` WHERE IDETABLISSEMENT = " . $colname_rq_emploi_du_temps_classe);
if (isset($_POST['IDCLASSROOM']) && $_POST['IDCLASSROOM']!=''){
   // var_dump("SELECT DISTINCT CLASSROOM.IDCLASSROOM as idclass,CLASSROOM.LIBELLE, NIVEAU.LIBELLE AS LIBNIVEAU, SERIE.LIBSERIE, CLASSROOM.IDCLASSROOM,EMPLOIEDUTEMPS.IDCLASSROOM,PERIODE.NOM_PERIODE, EMPLOIEDUTEMPS.IDPERIODE FROM CLASSROOM,EMPLOIEDUTEMPS, NIVEAU, SERIE,PERIODE WHERE CLASSROOM.IDETABLISSEMENT = " . $colname_rq_emploi_du_temps_classe . " AND CLASSROOM.IDCLASSROOM = " . $_POST['IDCLASSROOM'] . " AND CLASSROOM.IDNIVEAU=NIVEAU.IDNIVEAU AND  CLASSROOM.IDSERIE=SERIE.IDSERIE AND EMPLOIEDUTEMPS.IDCLASSROOM=CLASSROOM.IDCLASSROOM AND EMPLOIEDUTEMPS.IDPERIODE=PERIODE.IDPERIODE AND EMPLOIEDUTEMPS.IDPERIODE=PERIODE.IDPERIODE");exit;
    $query_rq_emploi_du_temps_classe = $dbh->query("SELECT DISTINCT CLASSROOM.IDCLASSROOM as idclass,CLASSROOM.LIBELLE, NIVEAU.LIBELLE AS LIBNIVEAU, NIVEAU.IDNIVEAU AS NIVEAUX, SERIE.LIBSERIE, CLASSROOM.IDCLASSROOM,EMPLOIEDUTEMPS.IDCLASSROOM,PERIODE.NOM_PERIODE, EMPLOIEDUTEMPS.IDPERIODE 
FROM CLASSROOM,EMPLOIEDUTEMPS, NIVEAU, SERIE,PERIODE WHERE CLASSROOM.IDETABLISSEMENT = " . $colname_rq_emploi_du_temps_classe . " 
AND CLASSROOM.IDCLASSROOM = " . $_POST['IDCLASSROOM'] . " AND CLASSROOM.IDNIVEAU=NIVEAU.IDNIVEAU 
AND  CLASSROOM.IDSERIE=SERIE.IDSERIE AND EMPLOIEDUTEMPS.IDCLASSROOM=CLASSROOM.IDCLASSROOM 
AND EMPLOIEDUTEMPS.IDPERIODE=PERIODE.IDPERIODE AND EMPLOIEDUTEMPS.IDPERIODE=PERIODE.IDPERIODE");
}else{

    $query_rq_emploi_du_temps_classe = $dbh->query("SELECT DISTINCT CLASSROOM.IDCLASSROOM as idclass,CLASSROOM.LIBELLE, NIVEAU.LIBELLE AS LIBNIVEAU, NIVEAU.IDNIVEAU AS NIVEAUX, SERIE.LIBSERIE, CLASSROOM.IDCLASSROOM,EMPLOIEDUTEMPS.IDCLASSROOM,PERIODE.NOM_PERIODE, EMPLOIEDUTEMPS.IDPERIODE FROM
 CLASSROOM,EMPLOIEDUTEMPS, NIVEAU, SERIE,PERIODE WHERE CLASSROOM.IDETABLISSEMENT = " . $colname_rq_emploi_du_temps_classe . " 
 AND CLASSROOM.IDNIVEAU=NIVEAU.IDNIVEAU AND  CLASSROOM.IDSERIE=SERIE.IDSERIE AND EMPLOIEDUTEMPS.IDCLASSROOM=CLASSROOM.IDCLASSROOM 
 AND EMPLOIEDUTEMPS.IDPERIODE=PERIODE.IDPERIODE AND EMPLOIEDUTEMPS.IDPERIODE=PERIODE.IDPERIODE");


}
?>

<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Emploi du Temps</a></li>
    <li>Emploi du temps des classes</li>
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
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    if (isset($_GET['res']) && $lib->securite_xss($_GET['res']) != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } ?>

                <form id="form" name="form1" method="post" action="emploisTempsClasse.php" class="form-inline">
                    <fieldset class="cadre"><legend> FILTRE</legend>
                        <div class="form-group col-lg-5">
                            <div class="col-lg-2">
                                <label>CYCLE:</label></div>
                                <div class="col-lg-10">
                                <select name="IDNIVEAU" id="selectNiv" class="form-control selectpicker" data-live-search="true"  onchange="choixClasse(); controlButton_();" required>
                                    <option value="">--Selectionner--</option>
                                    <?php  foreach ($rs_niv as $row_rq_niv) { ?>
                                        <option value=" <?php echo $row_rq_niv['IDNIVEAU']; ?>"><?php echo $row_rq_niv['LIBELLE']; ?>  </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-5">
                            <div class="col-lg-2"> <label>Classe : </label></div>
                            <div class="col-lg-10"><select name="IDCLASSROOM" id="IDCLASSROOM" class="form-control" onchange="controlButton_();"  style="width: 100%" >
                                    <option value="">--Selectionner--</option>

                                </select></div>
                        </div>
                        <div class="form-group col-lg-offset-1 col-lg-1">
                            <button type="submit" class="btn btn-primary" id="rech" style="display: none;">Rechercher</button>
                        </div>

                    </fieldset>
                </form>

                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>
                        <th>CLASSE</th>
                        <th>NIVEAU</th>
                        <th>PERIODE</th>
                        <th>MATIERE</th>
                        <th>DETAILS</th>

                    </tr>
                    </thead>


                    <tbody>
                    <? foreach ($query_rq_emploi_du_temps_classe->fetchAll() as $row_rq_emploi_du_temps_classe) { ?>
                        <tr>
                            <td><?php echo $row_rq_emploi_du_temps_classe['LIBELLE']; ?></td>
                            <td><?php echo $row_rq_emploi_du_temps_classe['LIBNIVEAU']; ?></td>
                            <td><?php echo $row_rq_emploi_du_temps_classe['NOM_PERIODE']; ?></td>
                            <td><?php echo $row_rq_emploi_du_temps_classe['LIBSERIE']; ?></td>
                            <td>
                                <a href="detailEmploiTempsClasse.php?IDCLASSROOM=<?php echo base64_encode($row_rq_emploi_du_temps_classe['IDCLASSROOM']) ; ?>&amp;IDPERIODE=<?php echo base64_encode($row_rq_emploi_du_temps_classe['IDPERIODE']); ?>&amp;IDNIVEAU=<?php echo base64_encode($row_rq_emploi_du_temps_classe['NIVEAUX']); ?>&amp;NOM=<?php echo base64_encode(str_replace(" ","-",$row_rq_emploi_du_temps_classe['LIBELLE'])); ?>">
                                    <i class="glyphicon glyphicon-list"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>


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
<script>

    function choixClasse() {
        var valSel = $("#selectNiv").val();
        if (valSel != '') {
            $.ajax({
                type: "POST",
                url: "getClasse.php",
                data: "NIVEAU=" + valSel,
                success: function (data) {
                    data = JSON.parse(data);
                    $("#IDCLASSROOM").html('<option selected="selected" value="">--Selectionner--</option>');
                    $.each(data, function (cle, valeur) {
                        $("#IDCLASSROOM").append('<option value="' + valeur.IDCLASSROOM + '">' + valeur.LIBELLE + '</option>');
                    });
                }
            });
        }
    }

    function controlButton_() {
        var selectNiv = $("#selectNiv").find("option:selected").val();
        var selectClasse = $("#IDCLASSROOM").find("option:selected").val();
        if(selectNiv!='' && selectClasse!='') {
            $('#rech').css("display", "block");
        }else{
            $('#rech').css("display", "none");
        }
    }
</script>
<?php include('footer.php'); ?>