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
    $lib->Restreindre($lib->Est_autoriser(25, $lib->securite_xss($_SESSION['profil'])));

$colname_rq_classe = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_classe = $lib->securite_xss($_SESSION['etab']);
}

$query_rq_niv = $dbh->query("SELECT IDNIVEAU, LIBELLE, IDETABLISSEMENT FROM NIVEAU WHERE IDETABLISSEMENT = " . $colname_rq_classe);
$rs_niv = $query_rq_niv->fetchAll();
?>
<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Evaluations</a></li>
    <li>Generation bulletin de notes</li>
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
                <div id="warning" style="display: none">
                    <div class="alert alert-danger">
                        LA GENERATION DES BULLETINS DE NOTES A ÉTÉ DÉJA EFFECTUÉE !!
                    </div>
                </div>

                <form id="form1" name="form1" method="post" action="bulletinClasse.php">
                    <fieldset class="cadre">
                        <legend> G&eacute;n&eacute;ration bulletin par classe</legend>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="nom" class="col-lg-3 col-sm-4 control-label">CYCLE </label>
                                        <div class="col-lg-9 col-sm-8">
                                            <select name="IDNIVEAU" id="selectNiv" class="form-control selectpicker" data-live-search="true"  onchange="controlButton();"  style="width: 100%;!important;">
                                                <option value="">--Selectionner--</option>
                                                <?php  foreach ($rs_niv as $row_rq_niv) { ?>

                                                    <option value=" <?php echo $row_rq_niv['IDNIVEAU']; ?>"><?php echo $row_rq_niv['LIBELLE']; ?>  </option>

                                                <?php } ?>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="col-lg-3"><label>PERIODE</label></div>
                                        <div class="col-lg-9">
                                            <select  name="periode" id="periode" class="form-control" onchange="controlButton_P();" data-live-search="true">
                                                <option value="">--Selectionner--</option>

                                            </select>
                                            <label id="Classe-error" class="error hidden" for="periode" style="display: inline-block;">This field is required.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="col-lg-3"><label>CLASSE</label></div>
                                        <div class="col-lg-9">
                                            <select  name="Classe" id="Classe" class="form-control" onchange="controlButton_C();" data-live-search="true">
                                                <option value="">--Selectionner--</option>

                                            </select>
                                            <label id="Classe-error" class="error hidden" for="Classe" style="display: inline-block;">This field is required.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="valid" style="display: none;">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <input type="button" class="btn btn-success" name="envoyer" value="Valider" id="validerAj" />
                                    </div>
                                </div>
                            </div>
                        </div>



                    </fieldset>
                </form>
                <br><br>

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
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        if(selectedNiv != "") {
            $('#valid').css("display","none")
            $.ajax({
                method: "POST",
                url: "requestCycle.php",
                data: {
                    IDNIVEAU: btoa(selectedNiv)
                }

            }).done(function (data) {
                var data = $.parseJSON(data)
                var classes = data.classes
                var periodes = data.periodes

                $('#Classe').children('option:not(:first)').remove()
                $('#Classe').selectpicker('refresh')
                for (i = 0, len = classes.length; i < len; i++){
                    $('#Classe').append(new Option(classes[i].LIBELLE, classes[i].IDCLASSROOM))
                    $('#Classe').selectpicker('refresh')
                }
                $('#periode').children('option:not(:first)').remove()
                $('#periode').selectpicker('refresh')
                for (i = 0, len = periodes.length; i < len; i++){
                    $('#periode').append(new Option(periodes[i].NOM_PERIODE, periodes[i].IDPERIODE))
                    $('#periode').selectpicker('refresh')
                }
            })
        }
    }
    $("#selectNiv").on('change', function () {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        if(selectedNiv == "") {
            $('#Classe').children('option:not(:first)').remove()
            $('#Classe').selectpicker('refresh')
            $('#periode').children('option:not(:first)').remove()
            $('#periode').selectpicker('refresh')
            $('#valid').css("display","none")
        }
    })
    function controlButton_C() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedC = $("#Classe").find("option:selected").val()
        var selectedM = $("#periode").find("option:selected").val()

        if(selectedNiv == "" || selectedC == "" ||  selectedM == "") {
            $('#valid').css("display","none")
        }else {
            $('#valid').css("display","block")
        }
    }
    function controlButton_P() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedC = $("#Classe").find("option:selected").val()
        var selectedM = $("#periode").find("option:selected").val()

        if(selectedNiv == "" || selectedM == "" || selectedC == "") {
            $('#valid').css("display","none")
        }else{
            $('#valid').css("display","block")
        }
    }

    $('#validerAj').on('click',function () {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedC = $("#Classe").find("option:selected").val()
        var selectedM = $("#periode").find("option:selected").val()

        if(selectedNiv !="" && selectedC !="" && selectedM !="") {
            $.ajax({
                method: "POST",
                url: "requestBulletins.php",
                data: {
                    IDCLASSROOM: btoa(selectedC),
                    IDPERIODE: btoa(selectedM)
                }

            }).done(function (data) {
                var data = $.parseJSON(data)
                if(data == 0) {
                    $('#warning').css('display','none')
                    $("#form1").submit()
                }else {
                    $('#warning').css('display','block')
                }
            })
        }
    })
</script>


