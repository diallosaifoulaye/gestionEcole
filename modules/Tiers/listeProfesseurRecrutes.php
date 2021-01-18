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
    $lib->Restreindre($lib->Est_autoriser(41, $lib->securite_xss($_SESSION['profil'])));


$etablissement = "-1";
if (isset($_SESSION['etab'])) {
    $etablissement = $lib->securite_xss($_SESSION['etab']);
}

$colname_anne_rq_individu = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $colname_anne_rq_individu = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}

$colname_matricule = "";
if (isset($_POST['MATRICULE']) && $_POST['MATRICULE'] != "") {
    $colname_matricule = " AND INDIVIDU.MATRICULE='" . $lib->securite_xss($_POST['MATRICULE']) . "'";
}

$colname_prenom = "";
if (isset($_POST['PRENOMS']) && $_POST['PRENOMS'] != "") {
    $colname_prenom = " AND INDIVIDU.PRENOMS='" . $lib->securite_xss($_POST['PRENOMS']) . "'";
}
$colname_nom = "";
if (isset($_POST['NOM']) && $_POST['NOM'] != "") {
    $colname_nom = " AND INDIVIDU.NOM='" . $lib->securite_xss($_POST['NOM']) . "'";
}
$colname_tel = "";
if (isset($_POST['TELMOBILE']) && $_POST['TELMOBILE'] != "") {
    $colname_tel = " AND INDIVIDU.TELMOBILE='" . $lib->securite_xss($_POST['TELMOBILE']) . "'";
}


try
{
    $query_rq_individu = $dbh->query("SELECT RECRUTE_PROF.IDRECRUTE_PROF, RECRUTE_PROF.TARIF_HORAIRE, RECRUTE_PROF.VOLUME_HORAIRE, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.TELMOBILE, INDIVIDU.PHOTO_FACE 
                                            FROM RECRUTE_PROF, INDIVIDU
                                            WHERE RECRUTE_PROF.IDETABLISSEMENT= " . $etablissement . " 
                                            AND RECRUTE_PROF.IDINDIVIDU=INDIVIDU.IDINDIVIDU 
                                            AND RECRUTE_PROF.IDANNEESSCOLAIRE = " . $colname_anne_rq_individu . $colname_matricule . $colname_prenom . $colname_nom . $colname_tel);

    $rs_individu = $query_rq_individu->fetchAll();

    $query_rq_professeur = $dbh->query("SELECT `IDINDIVIDU`, `MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `IDETABLISSEMENT`, `IDTYPEINDIVIDU`, `SEXE` 
                                                  FROM INDIVIDU WHERE IDETABLISSEMENT = " . $etablissement . " 
                                                  AND INDIVIDU.IDTYPEINDIVIDU = 7 AND IDINDIVIDU NOT IN (SELECT IDINDIVIDU FROM RECRUTE_PROF WHERE IDANNEESSCOLAIRE = ".$colname_anne_rq_individu." AND IDETABLISSEMENT = ".$etablissement.")");
    $rs_prof = $query_rq_professeur->fetchAll();

    $query_rq_module = $dbh->query("SELECT IDMATIERE, LIBELLE, IDETABLISSEMENT FROM MATIERE WHERE IDETABLISSEMENT = " . $etablissement);
    $rs_module = $query_rq_module->fetchAll();

    $query_rq_classroom = $dbh->query("SELECT IDCLASSROOM, LIBELLE, IDNIVEAU, IDETABLISSEMENT, IDSERIE 
                                       FROM CLASSROOM WHERE IDETABLISSEMENT = " . $etablissement);

    $forfaits = $dbh->query("SELECT ROWID, LIBELLE, NBRE_JOUR, MONTANT, IDETABLISSEMENT 
                             FROM FORFAIT_PROFESSEUR WHERE IDETABLISSEMENT = " . $etablissement);
    $rs_forfait = $forfaits->fetchAll(PDO::FETCH_OBJ);

    $query_rq_niv = $dbh->query("SELECT IDNIVEAU, LIBELLE, IDETABLISSEMENT FROM NIVEAU WHERE IDETABLISSEMENT = " . $etablissement);
    $rs_niv = $query_rq_niv->fetchAll();
}
catch (PDOException $e)
{
    echo -2;
}


?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">TIERS</a></li>
    <li>Professeurs recrut&eacute;s</li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

    <!-- START WIDGETS -->
    <div class="row">

        <div class="panel panel-default">
            <div class="panel-heading">


                <div class="btn-group pull-center">


                    <a href="../../ged/prof_recruter.php">
                        <button style="background-color:#DD682B" class='btn btn-success'>

                            <i class="fa fa-plus"></i> Impression
                        </button>
                    </a>

                </div>
                <div class="btn-group pull-right">
                    <button data-toggle="modal" data-target="#ajouter"
                            style="background-color:#DD682B" class='btn dropdown-toggle' , aria-hidden='true'>
                        <i class="fa fa-plus"></i> Nouveau recrutement
                    </button>

                </div>

            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } ?>

                <form id="form" name="form1" method="post" action="">
                    <fieldset class="cadre">
                        <legend> Filtre</legend>

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>MATRICULE</label></div>
                                    <div class="col-xs-9">
                                        <input type="text" name="MATRICULE" id="MATRICULE" class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>PRENOMS</label></div>
                                    <div class="col-xs-9">
                                        <input type="text" name="PRENOMS" id="PRENOMS" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>NOM</label></div>
                                    <div class="col-xs-9">
                                        <input type="text" name="NOM" id="NOM" class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-6">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>TELMOBILE</label></div>
                                    <div class="col-xs-9">
                                        <input type="text" name="TELMOBILE" id="TELMOBILE" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-xs-offset-6 col-xs-1">
                                <div class="form-group">

                                    <div>
                                        <input type="submit" class="btn btn-success" value="Rechercher"/>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </fieldset>
                </form>

                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>

                        <th>MATRICULE</th>
                        <th>PRENOMS</th>
                        <th>NOM</th>
                        <th>TELMOBILE</th>
                        <th>MODIFIER</th>
                        <th>DETAIL</th>
                        <th>SUPPRIMER</th>

                    </tr>
                    </thead>


                    <tbody>

                        <?php foreach ($rs_individu as $individu) { ?>

                            <tr>

                                <td><?php echo $individu['MATRICULE']; ?></td>
                                <td><?php echo $individu['PRENOMS']; ?></td>
                                <td><?php echo $individu['NOM']; ?></td>
                                <td><?php echo $individu['TELMOBILE']; ?></td>

                                <td><a href="modifRecrut.php?IDRECRUTE_PROF=<?php echo base64_encode($individu['IDRECRUTE_PROF']) ; ?>"><i class=" glyphicon glyphicon-edit"></i></a></td>

                                <td><a href="detailRecrut.php?IDRECRUTE_PROF=<?php echo base64_encode($individu['IDRECRUTE_PROF']) ; ?>"><i class=" glyphicon glyphicon-search"></i></a></td>
                                <td><a href="suppRecrut.php?IDRECRUTE_PROF=<?php echo base64_encode($individu['IDRECRUTE_PROF']) ; ?>" onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette enregistrement'));"><i class="glyphicon glyphicon-remove"></i></a></td>

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
        function forfaitcheck() {

            var ele = document.getElementsByName('TYPES');

            for(i = 0; i < ele.length; i++) {
                if(ele[i].checked)
                    var types=ele[i].value;

                if (types==1) {
                    document.getElementById("forfait").style.visibility = 'visible';
                    document.getElementById("forfait").style.display = 'block';

                    document.getElementById("tarif").style.visibility = 'hidden';
                    document.getElementById("tarif").style.display = 'none';
                    document.getElementById("volume").style.visibility = 'hidden';
                    document.getElementById("volume").style.display = 'none';
                }
                else {
                    document.getElementById("forfait").style.visibility = 'hidden';
                    document.getElementById("forfait").style.display = 'none';

                    document.getElementById("tarif").style.visibility = 'visible';
                    document.getElementById("tarif").style.display = 'block';
                    document.getElementById("volume").style.visibility = 'visible';
                    document.getElementById("volume").style.display = 'block';
                }

            }



        }

        function choixClasse(){
            var valSel = $("#selectNiv").val();

            $.ajax({
                type: "POST",
                url: "getClasseNive.php",
                data: "NIVEAU=" + valSel,
                success: function (data) {
                    data = JSON.parse(data);
                    $("#IDCLASSROOM1").empty();
                    $.each(data, function (cle, valeur) {

                        $("#IDCLASSROOM1").append('<div class="col-md-4" style="padding-bottom: 15px;"><input type="checkbox" name="IDCLASSROOM[]" id="IDCLASSROOM" onclick="controlButton_();" value="' + valeur.IDCLASSROOM + '">'  +' '+ valeur.LIBELLE +'</div>');

                    });
                }
            });

          /* if(valSel == 1 || valSel == 2){
                $.ajax({
                    type: "POST",
                    url: "getClasseNive.php",
                    data: "NIVEAU=" + valSel,
                    success: function (data) {
                        data = JSON.parse(data);
                        $("#IDCLASSROOM1").html('<option selected="selected" value="">--Selectionner--</option>');
                        $.each(data, function (cle, valeur) {
                            $("#IDCLASSROOM1").append('<option value="' + valeur.IDCLASSROOM + '">' + valeur.LIBELLE + '</option>');
                        });
                    }
                });
                document.getElementById("classeUnique").style.display = 'block';
                document.getElementById("classeMultiple").style.display = 'none';
            }else{

                $.ajax({
                    type: "POST",
                    url: "getClasseNive.php",
                    data: "NIVEAU=" + valSel,
                    success: function (data) {
                        data = JSON.parse(data);
                        $.each(data, function (cle, valeur) {
                            $("#IDCLASSROOM2").append('<option value="' + valeur.IDCLASSROOM + '">' + valeur.LIBELLE + '</option>');
                        });
                    }
                });
                document.getElementById("classeUnique").style.display = 'none';
                document.getElementById("classeMultiple").style.display = 'block';
            }*/
        }

        $(function () {
            $('.selectpicker').selectpicker();
        });


        function controlButton_() {
            var selectIndividu = $("#selectIndividu").find("option:selected").val();
            var selectMatiere1 = $("#IDMATIERE").val();
            var selectNiv = $("#selectNiv").find("option:selected").val();
            var ele = document.getElementsByName('TYPES');
            var selectVhoraire=document.getElementById('VOLUME_HORAIRE').value;
            var selectThoraire=document.getElementById('TARIF_HORAIRE').value;
            var selectFkforfait=document.getElementById('FK_FORFAIT').value;
            var selectF='';
            var selectClasse = $("#IDCLASSROOM").val();

           //alert(selectMatiere1);
            for(i = 0; i < ele.length; i++) {
                if (ele[i].checked)
                   var selecttypes = ele[i].value;
                if(selectIndividu == "" || selectMatiere1 == "" || selectNiv=="" || selectClasse=="" || selecttypes=="" ) {

                    $('#idsuivant').css("display","none")
                }else{
                    if(selecttypes==0) {
                        selectF = selectThoraire;
                       // alert(selectF);

                    } else if(selecttypes==1){

                        selectF=selectFkforfait;
                        //alert(selectF);
                    }

                    if(selectF=="") $('#idsuivant').css("display","none");else $('#idsuivant').css("display","block");
                }

            }

        }

        function CMatiere(){
            var valSel =$("#selectNiv").val();
           // alert(valSel);
                $.ajax({
                    type: "POST",
                    url: "getMatiereNive.php",
                    data: "NIVEAU=" + valSel,
                    success: function (data) {
                        data = JSON.parse(data);
                      //  $("#selectMatiere").html('<option selected="selected" value="">--Selectionner--</option>');
                        $("#selectMatiere").empty();
                        $.each(data, function (cle, valeur) {

                            $("#selectMatiere").append('<div class="col-md-4" style="padding-bottom: 15px;"><input type="checkbox" name="IDMATIERE[]" id="IDMATIERE" value="' + valeur.IDMATIERE + '" onclick="controlButton_();">'  +' '+ valeur.LIBELLE +'</div>');

                        });
                    }
                });
        }

   </script>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="ajouter"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h3 class="panel-title text-center"> Nouveau recrutement </h3>
                </div>
                <form action="ajouterRecrut.php" method="POST" id="form">

                    <div class="panel-body">
                        <div class="row">

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>PROFESSEUR:</label>

                                    <div>
                                        <select name="IDINDIVIDU" id="selectIndividu" class="form-control selectpicker" data-live-search="true" required onchange="controlButton_();">
                                            <option value="">--Selectionner--</option>
                                            <?php
                                            foreach ($rs_prof as $row_rq_professeur) {
                                                ?>
                                                <option
                                                    value="<?php echo $row_rq_professeur['IDINDIVIDU']; ?>"><?php echo $row_rq_professeur['PRENOMS']; ?>  <?php echo $row_rq_professeur['NOM'] ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CYCLE:</label>
                                    <div>
                                         <select name="IDNIVEAU" id="selectNiv" class="form-control selectpicker" data-live-search="true"  onchange="choixClasse(); CMatiere(); controlButton_();" required>
                                            <option value="">--Selectionner--</option>
                                            <?php  foreach ($rs_niv as $row_rq_niv) { ?>
                                                
                                                <option value=" <?php echo $row_rq_niv['IDNIVEAU']; ?>"><?php echo $row_rq_niv['LIBELLE']; ?>  </option>

                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>MATIERE :</label>
                                    <div id="selectMatiere">
                                       <!-- <select name="IDMATIERE" id="selectMatiere" class="form-control" data-live-search="true" onchange="controlButton_();">
                                            <option value="">--Selectionner--</option>

                                        </select>-->
                                    </div>
                                </div>
                            </div>


                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CLASSE:</label>
                                    <div id="IDCLASSROOM1" >

                                    </div>
                                </div>
                            </div>
                          <!--  <div class="col-xs-12" id="classeMultiple" style="display: none">
                                <div class="form-group">
                                    <label>CLASSE:</label>
                                    <div>
                                        <select name="IDCLASSROOM2[]"  id="IDCLASSROOM2" multiple data-style="bg-white rounded-pill px-4 py-3 shadow-sm" class="form-control" onchange="controlButton_();" >

                                        </select>
                                    </div>
                                </div>
                            </div>-->
                            <br/>
                            <div class="col-xs-12">
                                <label>&nbsp;</label>

                                <div class="form-group">
                                            <input type="radio" name="TYPES" value="0" id="TYPE1" onclick="forfaitcheck(); controlButton_();">&nbsp;&nbsp;TAUX HORAIRES</input> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="TYPES" value="1" id="TYPE2" onclick="forfaitcheck(); controlButton_();">&nbsp;&nbsp;FORFAITS</input>
                                        </div>
                                    </div>

                            <div class="col-xs-12" id="tarif" style="display: none">
                                <div class="form-group">
                                    <label>TARIF HORAIRE</label>

                                    <div>
                                        <input type="number" name="TARIF_HORAIRE" id="TARIF_HORAIRE" required class="form-control" onclick="controlButton_();"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12" id="volume" style="display: none">
                                <div class="form-group">
                                    <label>VOLUME HORAIRE</label>

                                    <div>
                                        <input type="number" name="VOLUME_HORAIRE" id="VOLUME_HORAIRE" required class="form-control" onclick="controlButton_();"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12" id="forfait" style="display: none">
                                <div class="form-group">
                                    <label>FORFAIT</label>
                                    <select name="FK_FORFAIT" id="FK_FORFAIT" class="form-control selectpicker"  data-live-search="true" onchange="controlButton_();">
                                        <option value="0">--Selectionner--</option>
                                        <?php foreach ($rs_forfait as $onef ){ ?>
                                            <option value="<?= $onef->ROWID; ?>"><?= $onef->LIBELLE; ?>-<?= number_format($onef->MONTANT,0,""," "); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>


                    </div>

                    <div class="panel-footer">
                        <button type="reset" class="btn btn-danger">R&eacute;initialiser</button>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $lib->securite_xss($_SESSION['etab']); ?>"/>
                        <input type="hidden" name="IDANNEESSCOLAIRE"
                               value="<?php echo $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']); ?>"/>
                        <button type="submit" class="btn btn-primary pull-right" id="idsuivant" style="display: none">Valider</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<?php include('footer.php'); ?>