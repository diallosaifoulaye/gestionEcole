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


$colname_rq_etablissement = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_etablissement = $lib->securite_xss($_SESSION['etab']);
}

$query_rq_niv = $dbh->query("SELECT IDNIVEAU, LIBELLE, IDETABLISSEMENT FROM NIVEAU WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);
$rs_niv = $query_rq_niv->fetchAll();

$query_rq_periode = $dbh->query("SELECT `IDPERIODE`, `NOM_PERIODE`, `DEBUT_PERIODE`, `FIN_FPERIODE`, `IDANNEESSCOLAIRE`, `IDETABLISSEMENT` FROM `PERIODE` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);



?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Emploi du Temps</a></li>
    <li>Emploi du temps</li>
</ul>
<!-- END BREADCRUMB -->

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

                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } ?>

                <form id="form1" name="form1" method="POST" action="ajouterEmploiTemps.php">
                    <fieldset>
                        <div class="row">
                        <div class="col-lg-4">
                                <label>CYCLE:</label>
                                <div>
                                    <select name="IDNIVEAU" id="selectNiv"  class="form-control selectpicker" data-live-search="true"  onchange="choixClasse(); CPeriode(); controlButton_();" required>
                                        <option value="">--Selectionner--</option>
                                        <?php  foreach ($rs_niv as $row_rq_niv) { ?>

                                            <option value=" <?php echo $row_rq_niv['IDNIVEAU']; ?>"><?php echo $row_rq_niv['LIBELLE']; ?>  </option>

                                        <?php } ?>
                                    </select>
                                </div>
                        </div>


                        <div class="col-lg-3">
                            <label for="IDPERIODE">PERIODE</label>
                            <select  name="IDPERIODE" id="IDPERIODE" required onchange="controlButton_();" class="form-control">
                                <option value=""> --Selectionner-- </option>

                            </select>
                        </div>

                        <div class="col-lg-4">
                            <label for="IDCLASSROOOM">CLASSE</label>

                            <select name="IDCLASSROOM" id="IDCLASSROOM" required onchange="controlButton_();" class="form-control">
                                <option value=""> --Selectionner-- </option>

                            </select>
                        </div>
                        <div class="col-lg-1">
                            <label for="IDCLASSROOOM"></label>
                            <input name="valider" type="submit" id="valider" value="Valider" class="btn btn-success" style="display: none;" />
                        </div>
                        </div>
                    </fieldset>
                </form>
                <script type="text/javascript">
                    function choixClasse(){
                        var valSel = $("#selectNiv").val();
                        if(valSel != ""){
                            $.ajax({
                                type: "POST",
                                url: "getClasse.php",
                                data: "NIVEAU=" + valSel,
                                success: function (data) {
                                    data=JSON.parse(data);
                                    $("#IDCLASSROOM").html('<option selected="selected" value="">--Selectionner--</option>');
                                    $.each(data, function(cle, valeur){
                                        $("#IDCLASSROOM").append('<option value="'+valeur.IDCLASSROOM+'">'+valeur.LIBELLE+'</option>');
                                    });
                                }
                            });
                        }
                    }
                    function CPeriode(){
                        var valSel = $("#selectNiv").val();
                        if(valSel != ""){
                            $.ajax({
                                type: "POST",
                                url: "getPeriode.php",
                                data: "NIVEAU=" + valSel,
                                success: function (data) {
                                    data=JSON.parse(data);
                                    $("#IDPERIODE").html('<option selected="selected" value="">--Selectionner--</option>');
                                    $.each(data, function(cle, valeur){
                                        $("#IDPERIODE").append('<option value="'+valeur.IDPERIODE+'">'+valeur.NOM_PERIODE+'</option>');
                                    });
                                }
                            });
                        }
                    }

                    function controlButton_() {
                        var selectNiv = $("#selectNiv").find("option:selected").val();
                        var IDPERIODE = $("#IDPERIODE").find("option:selected").val();
                        var IDCLASSROOM = $("#IDCLASSROOM").find("option:selected").val();
                        if(selectNiv!="" && IDPERIODE!="" && IDCLASSROOM!="") {
                            $.ajax({
                                type: "POST",
                                url: "EmploiTempsExiste.php",
                                data: "pass="  + selectNiv+"_"+IDPERIODE+"_"+IDCLASSROOM,
                                success: function (data) {
                                    data=JSON.parse(data);
                                   if(data==1){
                                       alert("Emploi du temps deja généré pour cette periode");
                                  
                                   }else{
                                       $('#valider').css("display","block");
                                   }
                                }
                            });

                        }else{
                            $('#valider').css("display","none");
                        }
                    }


                </script>
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