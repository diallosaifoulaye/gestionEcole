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


$colname_rq_classe = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_classe = $lib->securite_xss($_SESSION['etab']);
}

$cond_class = " ";
if (isset($_GET['classe']) && isset($_GET['periode']) && isset($_GET['IDNIVEAU']) && $lib->securite_xss($_GET['IDNIVEAU']) != "" ) {
    $cond_class = " AND con.IDCLASSROOM =" .$lib->securite_xss($_GET['classe'])." AND con.IDPERIODE=" .$lib->securite_xss($_GET['periode'])." AND cl.IDNIVEAU=" .$lib->securite_xss($_GET['IDNIVEAU']);
}


$colname_rq_controle = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_controle = $lib->securite_xss($_SESSION['etab']);
}


try
{

    $query_rq_controle = $dbh->query("SELECT con.IDCONTROLE, con.LIBELLE_CONTROLE, con.DATEDEBUT, con.DATEFIN, con.IDCLASSROOM, 
                                            con.IDMATIERE, cl.LIBELLE as classe, m.LIBELLE as matiere, con.VALIDER, p.NOM_PERIODE 
                                            FROM CONTROLE con
                                            INNER JOIN CLASSROOM cl ON con.IDCLASSROOM = cl.IDCLASSROOM 
                                            INNER JOIN MATIERE m ON con.IDMATIERE = m.IDMATIERE
                                            INNER JOIN PERIODE p ON con.IDPERIODE = p.IDPERIODE
                                            WHERE con.IDETABLISSEMENT=" . $colname_rq_controle . $cond_class . " 
                                            ORDER BY con.IDCONTROLE DESC");
    $rs_controle = $query_rq_controle->fetchAll(PDO::FETCH_ASSOC);

    $query_rq_niv = $dbh->query("SELECT IDNIVEAU, LIBELLE, IDETABLISSEMENT FROM NIVEAU WHERE IDETABLISSEMENT = " . $colname_rq_classe);
    $rs_niv = $query_rq_niv->fetchAll();
$i= $query_rq_niv;

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
                    <a href="nouveauControle.php" style="color: #0a0a0a;" >
                        <button style="background-color:#DD682B;" class='btn dropdown-toggle' aria-hidden='true'>
                            <i class="fa fa-plus"></i> Nouveau contrôle
                        </button>
                    </a>
                </div>

            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $lib->securite_xss($_GET['msg']) != '') {

                    if (isset($_GET['res']) && $lib->securite_xss($_GET['res']) == 1) { ?>

                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?>

                        </div>

                    <?php } if (isset($_GET['res']) && $lib->securite_xss($_GET['res']) != 1) { ?>

                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                            <?php echo $lib->securite_xss($_GET['msg']); ?>

                        </div>

                    <?php } ?>

                <?php } ?>

                <form id="form" name="form1" method="get" action="gestionControle.php" class="form-inline">
                    <fieldset class="cadre"><legend> FILTRE</legend>

                        <div  class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <label for="nom" class="col-lg-3 col-sm-4 control-label">CYCLE </label>
                            <div class="col-lg-9 col-sm-8">
                                <select name="IDNIVEAU" id="selectNiv" class="form-control selectpicker" data-live-search="true"  onchange="choixClasse(this);choixPeriode(this);"  style="width: 100%;!important;">
                                    <option value="">--Selectionner--</option>

                                    <?php  foreach ($rs_niv as $row_rq_niv) { ?>

                                        <option value=" <?php echo $row_rq_niv['IDNIVEAU']; ?>"><?php echo $row_rq_niv['LIBELLE']; ?>  </option>

                                    <?php } ?>

                                </select>

                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="nom" class="col-lg-3 col-sm-4 control-label">CLASSE </label>
                            <div class="col-lg-9  col-sm-8">
                                <select name="classe" id="classe" class="form-control"  style="width: 100%;!important;"  onchange="buttonControl();" >
                                    <option value="">--Selectionner--</option>
                                </select>
                            </div>
                        </div>



                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="nom" class="col-lg-3 col-sm-4 control-label">PERIODE </label>
                            <div class="col-lg-9  col-sm-8">
                                <select name="periode" id="periode" class="form-control"  style="width: 100%;!important;"  onchange="buttonControl();" >
                                    <option value="">--Selectionner--</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-1">
                            <button type="submit" class="btn btn-primary " id="validerAj" title="Rechercher" style="display: none;">Rechercher</button>
                        </div>


                    </fieldset>
                </form>

                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>

                        <th>#</th>
                        <th>Contrôle</th>
                        <th>Période</th>
                        <th>Date Debut</th>
                        <th>Date Fin</th>
                        <th>Matiere</th>
                        <th>Classe</th>
                        <th>Etat</th>
                        <th>Modifier</th>
                        <th>NOTER</th>
                        <th>VALIDER</th>

                    </tr>
                    </thead>


                    <tbody>
                    <?
                    foreach ($rs_controle as $controle) {

                        $query_rq_note = $dbh->query("SELECT count(NOTE) as nb FROM NOTE WHERE IDCONTROLE = " . $controle['IDCONTROLE']);
                        $rs_rq_note = $query_rq_note->fetchObject();


                        ?>
                        <tr>

                            <td><?php echo $controle['IDCONTROLE']; ?></td>
                            <td><?php echo $controle['LIBELLE_CONTROLE']; ?></td>
                            <td><?php echo $controle['NOM_PERIODE']; ?></td>
                            <td><?php echo $lib->date_time_fr($controle['DATEDEBUT']); ?></td>
                            <td><?php echo $lib->date_time_fr($controle['DATEFIN']); ?></td>
                            <td><?php echo $controle['matiere']; ?></td>
                            <td><?php echo $controle['classe']; ?></td>
                            <td>
                                <?php
                                    echo $controle['VALIDER'] == 1 ? "<span class=\"badge badge-pill badge-success\">VALIDE</span>" : "<span class=\"badge badge-pill badge-danger\">NON VALIDE</span>";
                                ?>
                            </td>
                            <td>
                                <?php if ($controle['VALIDER'] == 0) { ?>
                                <a href="ficheControleModif.php?IDCONTROLE=<?php echo base64_encode($controle['IDCONTROLE']) ; ?>&amp;detail=<?php echo base64_encode(0) ; ?>" data-toggle="tooltip" data-placement="bottom" title="Modifier le controle"><i
                                            class=" glyphicon glyphicon-pencil"></i></a>
                                <?php } else { ?>
                                    <a href="ficheControleModif.php?IDCONTROLE=<?php echo base64_encode($controle['IDCONTROLE']) ; ?>&amp;detail=<?php echo base64_encode(1) ; ?>" data-toggle="tooltip" data-placement="bottom" title="Voir details"><i
                                                class=" glyphicon glyphicon-list"></i></a>
                                <?php } ?>
                            </td>

                            <td>
                                <?php if ($controle['VALIDER'] == 0) { ?>
                                    <a href="noterControle.php?IDCLASSROOM=<?php echo base64_encode($controle['IDCLASSROOM']); ?>&amp;IDCONTROLE=<?php echo base64_encode($controle['IDCONTROLE']) ; ?>" data-toggle="tooltip" data-placement="bottom" title="Noter le controle"><i
                                            class=" glyphicon glyphicon-floppy-save"></i></a>
                                <?php } else { ?>
                                    <a href="detailControle.php?IDCLASSROOM=<?php echo base64_encode($controle['IDCLASSROOM']); ?>&amp;IDCONTROLE=<?php echo base64_encode($controle['IDCONTROLE']); ?>" data-toggle="tooltip" data-placement="bottom" title="Voir details"><i
                                            class=" glyphicon glyphicon-list"></i></a>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($controle['VALIDER'] == 0) {

                                    //var_dump($rs_rq_note->nb .' a '. $query->nb1);

                                    if ($rs_rq_note->nb >= 1) {?>

                                    <a href="validerNote.php?IDCLASSROOM=<?php echo base64_encode($controle['IDCLASSROOM']); ?>&amp;IDCONTROLE=<?php echo base64_encode($controle['IDCONTROLE']); ?>" data-toggle="tooltip" data-placement="bottom" title="Valider les notes"><i
                                            class="glyphicon glyphicon-ok"></i></a>
                                <?php }
                                 }

                                else { ?>
                                    <a href="detailControle.php?IDCLASSROOM=<?php echo base64_encode($controle['IDCLASSROOM']); ?>&amp;IDCONTROLE=<?php echo base64_encode($controle['IDCONTROLE']); ?>" data-toggle="tooltip" data-placement="bottom" title="Voir details"><i
                                            class=" glyphicon glyphicon-list"></i></a>
                                <?php } ?></td>


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
        function choixClasse(elem) {
            var valSel = elem.value;
            $.ajax({
                type: "POST",
                url: "getClasseNive.php",
                data: "NIVEAU=" + valSel,
                success: function (data) {
                    data = JSON.parse(data);
                    $("#classe").html('<option selected="selected" value="">--Selectionner--</option>');
                    $.each(data, function (cle, valeur) {
                        $("#classe").append('<option value="' + valeur.IDCLASSROOM + '">' + valeur.LIBELLE + '</option>');
                    });
                }
            });
        }


        function choixPeriode(elem) {
            var valSel = elem.value;
            $.ajax({
                type: "POST",
                url: "getPeriode.php",
                data: "NIVEAU=" + valSel,
                success: function (data) {
                    data = JSON.parse(data);
                    $("#periode").html('<option selected="selected" value="">--Selectionner--</option>');
                    $.each(data, function (cle, valeur) {
                        $("#periode").append('<option value="' + valeur.IDPERIODE + '">' + valeur.NOM_PERIODE + '</option>');
                    });
                }
            });
        }


        function buttonControl() {
            if(document.getElementById("classe").value!="" && document.getElementById("periode").value!="" ){
                $('#validerAj').css("display","block");
            }else{
                $('#validerAj').css("display","none");
            }
        }
    </script>

<?php include('footer.php'); ?>