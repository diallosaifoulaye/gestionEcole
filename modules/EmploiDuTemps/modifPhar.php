<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

/*if ($lib->securite_xss($_SESSION['profil']) != 1)
    $lib->Restreindre($lib->Est_autoriser(48, $lib->securite_xss($_SESSION['profil'])));*/

$colname_REQ_class = "-1";
if (isset($_SESSION['etab'])) {
    $colname_REQ_class = $lib->securite_xss($_SESSION['etab']);
}

//require_once('classe/FiliereManager.php');
//$series = new FiliereManager($dbh, 'SERIE');
//$serie = $series->getFiliere('IDETABLISSEMENT', $colname_REQ_class);

require_once('../Parametrage/classe/NiveauManager.php');
$niveaux=new NiveauManager($dbh,'NIVEAU');
$niveau = $niveaux->getNiveau('IDETABLISSEMENT',$lib->securite_xss($_SESSION['etab']));




$colname_rq_phar = "-1";
if (isset($_GET['idPhar']))
{
    $param=base64_decode($lib->securite_xss($_GET['idPhar']));

    $query_rq_phar1= $dbh->query("SELECT ROWID, LIBELLE, DESCRIPTION, FK_MATIERE, FK_NIVEAU, FK_CYCLE
                                                FROM PHAR 
                                                WHERE ROWID =". $param);

    foreach ($query_rq_phar1->fetchAll() as $row_rq_phar)
    {
        $id = $row_rq_phar['ROWID'];
        $libelle = $row_rq_phar['LIBELLE'];
        $description = $row_rq_phar['DESCRIPTION'];
        $matiere = $row_rq_phar['FK_MATIERE'];
        $idniveau = $row_rq_phar['FK_NIVEAU'];
        $cycle= $row_rq_phar['FK_CYCLE'];
    }

}

if (isset($_POST) && $_POST != null) {

    $libelle=$lib->securite_xss($_POST["LIBELLE"]);
    $description=strip_tags( html_entity_decode ($lib->securite_xss($_POST["DESCRIPTION"])));
    $matiere=$lib->securite_xss($_POST["FK_MATIERE"]);
    $niveau=$lib->securite_xss($_POST["FK_NIVEAU"]);
    $CYCLE =$lib->securite_xss($_POST["CYCLE"]);
    $idphar = $lib->securite_xss($_POST['ROWID']);
    unset($_POST['ROWID']);

    $query = "UPDATE PHAR SET LIBELLE = ?, DESCRIPTION = ?, FK_MATIERE = ?, FK_NIVEAU = ?, FK_CYCLE = ? WHERE ROWID = " .$idphar;
    $stmt = $dbh->prepare($query);
    $resultat= $stmt->execute(array($libelle, $description, $matiere, $niveau, $CYCLE));


    if ($resultat == true)
    {
        $res = 1;
        $msg = "Modification effectuée avec succés";

    }
    else {
        $res = 0;
        $msg = "Votre mofication a échouée";
    }
    header("Location: phar.php?msg=" . $lib->securite_xss($msg) . "&res=" . $lib->securite_xss($res));
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
                <form action="modifPhar.php" method="POST">

                    <div class="row">


                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>CYCLE</label>

                                <div>
                                    <select name="CYCLE" class="form-control selectpicker" onchange="controlButton();controlButton1();" data-live-search="true" id="selectNiv">
                                        <option value="">--Selectionner le cylce--</option>

                                        <?php foreach ($niveau as $niv) { ?>

                                            <option value=" <?php echo $niv->getIDNIVEAU(); ?>" <?php if ($cycle == $niv->getIDNIVEAU()) echo "selected" ?> ><?php echo $niv->getLIBELLE(); ?> </option>

                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>MATIERE</label>
                                <div>
                                    <select name="FK_MATIERE" class="form-control" data-live-search="true" id="selectMatiere" onchange="controlButton_();" required>
                                        <option value="">--Selectionner la matiere--</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>NIVEAU</label>
                                <div>
                                    <select name="FK_NIVEAU" class="form-control" data-live-search="true" id="selectNiveau" onchange="controlButton_();" required>
                                        <option value="">--Selectionner le niveau--</option>
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
                                <label>DESCRIPTION</label>

                                <div>
                                    <input type="text" name="DESCRIPTION" id="mytextarea" required class="form-control"
                                           value="<?php echo $description; ?>"/>
                                </div>
                            </div>
                        </div>






                        <br><br>


                        <div class="col-lg-12">
                            <br/>
                            <div class="col-lg-offset-5" id="idvalider"><input type="submit" class="btn btn-success" value="Modifier"/></div>

                        </div>

                    </div>

                    <input type="hidden" name="ROWID" value="<?php echo $id; ?>"/>



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
            url: "requestMatiere.php",
            data: {
                IDNIVEAU: btoa(<?php echo $cycle; ?>)
            }
        }).done(function (data) {

            var data = $.parseJSON(data)
            $('#selectMatiere').children('option:not(:first)').remove()
            $('#selectMatiere').selectpicker('refresh')
            for (i = 0, len = data.length; i < len; i++){
                $('#selectMatiere').append(new Option(data[i].LIBELLE, data[i].IDMATIERE))
                $('#selectMatiere').selectpicker('refresh')
            }
            $("#selectMatiere option").each(function(){
                if ($(this).val() == "<?php echo $matiere ;?>")
                    $(this).attr("selected","selected");
            })
            $('#selectMatiere').selectpicker('refresh')
        })
    });

    $(function() {
        $.ajax({
            method: "POST",
            url: "requestNiveau.php",
            data: {
                IDNIVEAU: btoa(<?php echo $cycle; ?>)
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
                if ($(this).val() == "<?php echo $idniveau ;?>")
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
                url: "requestMatiere.php",
                data: {
                    IDNIVEAU: btoa(selectedNiv)
                }

            }).done(function (data) {
                var data = $.parseJSON(data)
                $('#selectMatiere').children('option:not(:first)').remove()
                $('#selectMatiere').selectpicker('refresh')
                for (i = 0, len = data.length; i < len; i++){
                    $('#selectMatiere').append(new Option(data[i].LIBELLE, data[i].IDMATIERE))
                    $('#selectMatiere').selectpicker('refresh')
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
                url: "requestNiveau.php",
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
            $('#selectMatiere').children('option:not(:first)').remove()
            $('#selectMatiere').selectpicker('refresh')


            $('#selectNiveau').children('option:not(:first)').remove()
            $('#selectNiveau').selectpicker('refresh')

            $('#idvalider').css("display","none")
        }
    })

    function controlButton_() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedSer = $("#selectMatiere").find("option:selected").val()
        var selectedNiveau = $("#selectNiveau").find("option:selected").val()

        if(selectedNiv == "" || selectedSer == "" || selectedNiveau == "") {
            $('#idvalider').css("display","none")
        }else{
            $('#idvalider').css("display","block")
        }
    }

</script>