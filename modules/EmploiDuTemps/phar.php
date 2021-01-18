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
    $etabl = $lib->securite_xss($_SESSION['etab']);
}



$query_rq_phar = $dbh->query("SELECT p.ROWID, p.LIBELLE, p.DESCRIPTION, m.LIBELLE as matiere, n.LIBELLE as cycle
                                                FROM  PHAR p 
                                                INNER JOIN MATIERE m ON p.FK_MATIERE = m.IDMATIERE
                                                INNER JOIN NIV_CLASSE n ON p.FK_NIVEAU = n.ID
                                                WHERE n.IDETABLISSEMENT = " . $etabl);



require_once('../Parametrage/classe/NiveauManager.php');
$niveaux=new NiveauManager($dbh,'NIVEAU');
$niveau = $niveaux->getNiveau('IDETABLISSEMENT',$lib->securite_xss($_SESSION['etab']));


?>



<?php include('header.php'); ?>
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="#">Emploi du Temps</a></li>
        <li>Phar</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">

        <!-- START WIDGETS -->
        <div class="row">

            <div class="panel panel-default">
                <div class="panel-heading">
                    &nbsp;&nbsp;&nbsp;
                    <div class="panel-heading">
                        &nbsp;&nbsp;&nbsp;
                        <div class="btn-group pull-right">
                            <button data-toggle="modal" data-target="#ajouter"
                                    style="background-color:#DD682B" class='btn dropdown-toggle'  aria-hidden='true'>
                                <i class="fa fa-plus"></i> AJOUTER
                            </button>
                        </div>
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


                    <table id="customers2" class="table datatable">

                        <thead>
                        <tr>
                            <th>LIBELLE</th>
                            <th>DESCRIPTION</th>
                            <th>MATIERE</th>
                            <th>NIVEAU</th>
                            <th>MODIFIER</th>
                            <th>SUPPRIMER</th>

                        </tr>
                        </thead>


                        <tbody>

                        <? foreach ($query_rq_phar->fetchAll() as $query_rq) {


                            ?>

                        <tr>
                                <td><?php echo $query_rq['LIBELLE']; ?></td>
                                <td><?php echo $query_rq['DESCRIPTION']; ?></td>
                                <td><?php echo $query_rq['matiere']; ?></td>
                                <td><?php echo $query_rq['cycle']; ?></td>

                            <td><a href="modifPhar.php?idPhar=<?php echo base64_encode($query_rq['ROWID']); ?>"><i class=" glyphicon glyphicon-edit"></i></a></td>

                            <td>
                                <a href="suppPhar.php?idPhar=<?php echo base64_encode($query_rq['ROWID']); ?>"  onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette enregistrement'));"><i class="glyphicon glyphicon-remove"></i></a>
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

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="ajouter" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" style="width: 50%">
            <div class="modal-content">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="panel-title text-center"> Nouveau Phar </h3>
                    </div>
                    <form action="ajouterPhar.php" method="POST">

                        <div class="panel-body">
                            <div class="row">

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>CYCLE</label>

                                        <div>
                                            <select name="NIVEAU" class="form-control" data-live-search="true" id="selectNiv" onchange="controlButton1();controlButton()" >
                                                <option value="">--Selectionner le cycle-- </option>
                                                <?php foreach ($niveau as $niv) { ?>
                                                    <option value=" <?php echo $niv->getIDNIVEAU(); ?>"><?php echo $niv->getLIBELLE(); ?> </option>
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
                                        <label>LIBELLE </label>
                                        <div>
                                            <input type="text" name="LIBELLE" id="LIBELLE" required class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>DESCRIPTION</label>


                                            <div>


                                                <textarea  name="DESCRIPTION" id="mytextarea"  class="form-control" ></textarea>
                                            </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="panel-footer">

                            <button type="reset" class="btn btn-danger">Réinitialiser</button>


                            <button type="submit" id="idvalider" class="btn btn-primary pull-right">AJOUTER</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
<?php include('footer.php'); ?>


<script>


    function controlButton() {
        var selectedMatiere = $("#selectNiv").find("option:selected").val()

        if(selectedMatiere != "") {
            $('#idvalider').css("display","none")
            $.ajax({
                method: "POST",
                url: "requestMatiere.php",
                data: {
                    IDNIVEAU: btoa(selectedMatiere)
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

            $('#selectNiveau').children('option:not(:first)').remove()
            $('#selectNiveau').selectpicker('refresh')

            $('#idvalider').css("display","none")
        }
    })


    function controlButton_() {
        //var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedMatiere = $("#selectedMatiere").find("option:selected").val()
        var selectedNiveau = $("#selectNiveau").find("option:selected").val()

        if(selectedNiv == "" || selectedMatiere == "" || selectedNiveau == "") {
            $('#idvalider').css("display","none")
        }else{
            $('#idvalider').css("display","block")
        }
    }

</script>
