<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($lib->securite_xss($_SESSION['profil']) != 1)
    $lib->Restreindre($lib->Est_autoriser(48, $lib->securite_xss($_SESSION['profil'])));

$colname_REQ_class = "-1";
if (isset($_SESSION['etab'])) {
    $colname_REQ_class = $lib->securite_xss($_SESSION['etab']);
}

require_once('classe/FiliereManager.php');
$series = new FiliereManager($dbh, 'SERIE');
$serie = $series->getFiliere('IDETABLISSEMENT', $colname_REQ_class);

require_once('classe/NiveauManager.php');
$niveaux = new NiveauManager($dbh, 'NIVEAU');
$niveau = $niveaux->getNiveau('IDETABLISSEMENT', $colname_REQ_class);

require_once("classe/ClasseManager.php");
require_once("classe/Classe.php");
$niv = new ClasseManager($dbh, 'CLASSROOM');


$colname_rq_classe_etab = "-1";
if (isset($_GET['idClasse']))
{
    $param=json_decode(base64_decode($lib->securite_xss($_GET['idClasse'])));
    $colname_rq_classe_etab=$param->id;


}

$query_rq_classe_etab = $dbh->query("SELECT IDCLASSROOM, LIBELLE, IDNIVEAU, IDETABLISSEMENT, IDSERIE, NBRE_ELEVE, EXAMEN, IDNIV 
                                                FROM CLASSROOM 
                                                WHERE IDCLASSROOM = $colname_rq_classe_etab");
foreach ($query_rq_classe_etab->fetchAll() as $row_rq_classe_etab)
{
    $id = $row_rq_classe_etab['IDCLASSROOM'];
    $libelle = $row_rq_classe_etab['LIBELLE'];
    $idNiveau = $row_rq_classe_etab['IDNIVEAU'];
    $idSerie = $row_rq_classe_etab['IDSERIE'];
    $NBRE_ELEVE= $row_rq_classe_etab['NBRE_ELEVE'];
    $examen= $row_rq_classe_etab['EXAMEN'];
    $idniv = $row_rq_classe_etab['IDNIV'];
}


if (isset($_POST) && $_POST != null) {
    $idclass = $lib->securite_xss($_POST['IDCLASSROOM']);
    unset($_POST['IDCLASSROOM']);
    $res = $niv->modifier($lib->securite_xss_array($_POST), 'IDCLASSROOM', $idclass);
    if ($res == 1)
    {
        $msg = "Modification effectuée avec succés";

    }
    else {
        $msg = "Votre mofication a échouée";
    }
    header("Location: classes.php?msg=" . $lib->securite_xss($msg) . "&res=" . $lib->securite_xss($res));
}

?>


<?php include('../header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Param&eacute;trage</a></li>
    <li>Classe</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->


<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">

        <div class="panel panel-default">
            <div class="panel-body">
                <form action="modifClasse.php" method="POST">

                    <div class="row">


                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>CYCLE</label>
                                <div>
                                    <select name="IDNIVEAU" class="form-control selectpicker" onchange="controlButton();controlButton1();" data-live-search="true" id="selectNiv">
                                        <option value="">--Selectionner le cylce--</option>

                                        <?php foreach ($niveau as $niv) { ?>

                                            <option value=" <?php echo $niv->getIDNIVEAU(); ?>" <?php if ($idNiveau == $niv->getIDNIVEAU()) echo "selected" ?> ><?php echo $niv->getLIBELLE(); ?> </option>

                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>SERIE</label>
                                <div>
                                    <select name="IDSERIE" class="form-control selectpicker" data-live-search="true" id="selectSerie" onchange="controlButton_()">
                                        <option value="">--Selectionner série--</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>NIVEAUX</label>
                                <div>
                                    <select name="IDNIV" class="form-control selectpicker" data-live-search="true" id="selectNiveau" onchange="controlButton_()">
                                        <option value="">--Selectionner niveau--</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>LIBELLE</label>

                                <div>
                                    <input type="text" name="LIBELLE" id="LIBELLE" required class="form-control"
                                           value="<?php echo $libelle; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>NOMBRE D'ELEVE</label>

                                <div>
                                    <input type="number" name="NBRE_ELEVE" id="LIBELLE" required class="form-control"
                                           value="<?php echo $NBRE_ELEVE; ?>"/>
                                </div>
                            </div>
                        </div>



                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>CLASSE D'EXAMEN</label>
                                <div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="EXAMEN" value="1" <?php if($examen == 1) echo 'checked'; ?>> OUI
                                        </label>
                                        &nbsp;
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="EXAMEN" value="0" <?php if($examen == 0) echo 'checked'; ?>> NON
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <br><br>


                        <div class="col-lg-12">
                            <br/>
                            <div class="col-lg-offset-5" id="idvalider"><input type="submit" class="btn btn-success" value="Modifier"/></div>

                        </div>

                    </div>

                    <input type="hidden" name="IDCLASSROOM" value="<?php echo $id; ?>"/>
                    <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $lib->securite_xss($_SESSION['etab']); ?>"/>


                </form>
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

    $(function() {
        $.ajax({
            method: "POST",
            url: "request.php",
            data: {
                IDNIVEAU: btoa(<?php echo $idNiveau; ?>)
            }
        }).done(function (data) {
            var data = $.parseJSON(data)
            $('#selectSerie').children('option:not(:first)').remove()
            $('#selectSerie').selectpicker('refresh')
            for (i = 0, len = data.length; i < len; i++){
                $('#selectSerie').append(new Option(data[i].LIBSERIE, data[i].IDSERIE))
                $('#selectSerie').selectpicker('refresh')
            }
            $("#selectSerie option").each(function(){
                if ($(this).val() == "<?php echo $idSerie ;?>")
                    $(this).attr("selected","selected");
            })
            $('#selectSerie').selectpicker('refresh')
        })
    });

    $(function() {
        $.ajax({
            method: "POST",
            url: "requestNiv.php",
            data: {
                IDNIVEAU: btoa(<?php echo $idNiveau; ?>)
            }
        }).done(function (data) {
            var data = $.parseJSON(data)
            $('#selectNiveau').children('option:not(:first)').remove()
            $('#selectNiveau').selectpicker('refresh')
            for (i = 0, len = data.length; i < len; i++){
                $('#selectNiveau').append(new Option(data[i].LIBELLE, data[i].ID))
                $('#selectNiveau').selectpicker('refresh')
            }
            $("#selectNiveau option").each(function(){
                if ($(this).val() == "<?php echo $idniv ;?>")
                    $(this).attr("selected","selected");
            })
            $('#selectNiveau').selectpicker('refresh')
        })
    });

    function controlButton() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        if(selectedNiv != "") {
            $('#idvalider').css("display","none")
            $.ajax({
                method: "POST",
                url: "request.php",
                data: {
                    IDNIVEAU: btoa(selectedNiv)
                }

            }).done(function (data) {
                var data = $.parseJSON(data)
                $('#selectSerie').children('option:not(:first)').remove()
                $('#selectSerie').selectpicker('refresh')
                for (i = 0, len = data.length; i < len; i++){
                    $('#selectSerie').append(new Option(data[i].LIBSERIE, data[i].IDSERIE))
                    $('#selectSerie').selectpicker('refresh')
                }
            })
        }
    }

    function controlButton1() {
        var selectedNiveau = $("#selectNiv").find("option:selected").val()
        if(selectedNiveau != "") {
            $('#idvalider').css("display","none")
            $.ajax({
                method: "POST",
                url: "requestNiv.php",
                data: {
                    IDNIVEAU: btoa(selectedNiveau)
                }
            }).done(function (data) {
                var data = $.parseJSON(data)
                $('#selectNiveau').children('option:not(:first)').remove()
                $('#selectNiveau').selectpicker('refresh')
                for (i = 0, len = data.length; i < len; i++){
                    $('#selectNiveau').append(new Option(data[i].LIBELLE, data[i].ID))
                    $('#selectNiveau').selectpicker('refresh')
                }
            })
        }
    }

    $("#selectNiv").on('change', function () {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        if(selectedNiv == "") {
            $('#selectSerie').children('option:not(:first)').remove()
            $('#selectSerie').selectpicker('refresh')


            $('#selectNiveau').children('option:not(:first)').remove()
            $('#selectNiveau').selectpicker('refresh')

            $('#idvalider').css("display","none")
        }
    })

    function controlButton_() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedSer = $("#selectSerie").find("option:selected").val()
        var selectedNiv_class = $("#selectNiveau").find("option:selected").val()

        if(selectedNiv == "" || selectedSer == "" || selectedNiv_class == "") {
            $('#idvalider').css("display","none")
        }else{
            $('#idvalider').css("display","block")
        }
    }

</script>