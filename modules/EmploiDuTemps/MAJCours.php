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
    $lib->Restreindre($lib->Est_autoriser(23, $_SESSION['profil']));


$colname_type = " ";
if (isset($_POST['CLASSE']) && $_POST['CLASSE'] != " ")
{
    $colname_type = "  AND cl.IDCLASSROOM = " . $lib->securite_xss($_POST['CLASSE']);

}
if (empty($_POST['CLASSE'])) {
    $colname_type = "";

}
$colname_matiere = " ";
if (isset($_POST['MATIERE']) && $_POST['MATIERE'] != " ")
{
    $colname_matiere = "  AND m.IDMATIERE = " . $lib->securite_xss($_POST['MATIERE']);

}
if (empty($_POST['MATIERE'])) {
    $colname_matiere = "";

}


$colname_rq_dispenser_cours = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_dispenser_cours = $lib->securite_xss($_SESSION['etab']);
}

try
{
    $query_rq_dispenser_cours = $dbh->query("SELECT d.IDDISPENSER_COURS, d.IDCLASSROOM, d.IDINDIVIDU, d.DATE, d.HEUREDEBUTCOURS, d.HEUREFINCOURS, d.TITRE_COURS, d.IDMATIERE, 
                                                        d.IDETABLISSEMENT, m.LIBELLE AS NOM_MATIRE, i.PRENOMS, cl.LIBELLE as nom_classe, i.NOM, d.MOIS
                                                        FROM DISPENSER_COURS d 
                                                        INNER JOIN MATIERE m ON d.IDMATIERE = m.IDMATIERE 
                                                        INNER JOIN INDIVIDU i ON d.IDINDIVIDU = i.IDINDIVIDU, CLASSROOM cl
                                                        WHERE d.ANNEESCOLAIRE = " . $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']) . "
                                                        AND d.IDETABLISSEMENT = " . $colname_rq_dispenser_cours . "
                                                        AND d.IDCLASSROOM = cl.IDCLASSROOM" . $colname_type . $colname_matiere);
    $rs_cours = $query_rq_dispenser_cours->fetchAll();

    $query_rq_classe_liste = $dbh->query("SELECT IDCLASSROOM, LIBELLE, IDNIVEAU, IDETABLISSEMENT, IDSERIE 
                                                    FROM CLASSROOM 
                                                    WHERE CLASSROOM.IDETABLISSEMENT = " . $colname_rq_dispenser_cours . " 
                                                    GROUP BY LIBELLE ORDER BY LIBELLE ASC ");

    $rs_classe = $query_rq_classe_liste->fetchAll();


    $query_rq_matiere = $dbh->query("SELECT IDMATIERE, LIBELLE, IDETABLISSEMENT FROM MATIERE WHERE MATIERE.IDETABLISSEMENT = " . $colname_rq_dispenser_cours);
    $rs_matiere = $query_rq_matiere->fetchAll();

    $query_rq_niveau = $dbh->query("SELECT IDNIVEAU,LIBELLE FROM NIVEAU WHERE IDETABLISSEMENT = $colname_rq_dispenser_cours");
    $rs_nivau = $query_rq_niveau->fetchAll();
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
    <li>Mise &aacute; jour des cours</li>
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

                    <a href="newMAJ.php">
                        <button
                            style="background-color:#DD682B" class='btn dropdown-toggle' , aria-hidden='true'>
                            <i class="fa fa-plus"></i> Nouveau
                        </button>
                    </a>

                </div>

            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>

                    <?php } if (isset($_GET['res']) && $_GET['res'] != 1) { ?>

                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>

                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }  ?>

                <?php } ?>

                <form id="form1" name="form1" method="POST" action="" class="form-inline">
                    <fieldset class="cadre">
                        <legend>Filtre</legend>
                        <div class="col-lg-12">
                            <div class="col-lg-4">
                                <label class="control-label">CYCLE</label>
                                <select name="IDNIVEAU" id="selectNiv" class="form-control selectpicker" data-live-search="true"  title="VEUILLEZ CHOISIR UN CYCLE" onchange="controlButton();">
                                    <option value="">VEUILLEZ CHOISIR UN CYCLE</option>
                                    <?php foreach ($rs_nivau as $niveau) { ?>
                                        <option
                                                value="<?php echo $niveau['IDNIVEAU']; ?>"><?php echo $niveau['LIBELLE']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <label class="control-label">CLASSE</label>
                                <select name="CLASSE" id="CLASSE" class="form-control selectpicker" data-live-search="true" onchange="controlButton_C();">
                                    <option value="">VEUILLEZ CHOISIR UNE CLASSE</option>
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <label class="control-label">MATIERE</label>
                                <select name="MATIERE" id="MATIERE" class="form-control selectpicker" data-live-search="true" onchange="controlButton_M();">
                                    <option value="">VEUILLEZ CHOISIR UNE MATIERE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12" style="margin-top: 10px; display: none;" id="valid">
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-success">Rechercher</button>
                            </div>
                        </div>

                    </fieldset>
                </form>

                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure de Debut</th>
                        <th>Heure de Fin</th>
                        <th>Mois</th>
                        <th>Mati&egrave;re</th>
                        <th>Classe</th>
                        <th>Professeur</th>
                        <th>Pr&eacute;sence</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                    </thead>


                    <tbody>
                    <?php foreach ($rs_cours as $row_rq_dispenser_cours) { ?>
                        <tr>
                            <td><?php echo $lib->date_fr($row_rq_dispenser_cours['DATE']); ?></td>
                            <td><?php echo $row_rq_dispenser_cours['HEUREDEBUTCOURS']; ?></td>
                            <td><?php echo $row_rq_dispenser_cours['HEUREFINCOURS']; ?></td>
                            <td><?php echo $lib->affiche_mois($row_rq_dispenser_cours['MOIS']); ?></td>
                            <td><?php echo $row_rq_dispenser_cours['NOM_MATIRE']; ?></td>
                            <td><?php echo $row_rq_dispenser_cours['nom_classe']; ?></td>
                            <td><?php echo $row_rq_dispenser_cours['PRENOMS'].' '.$row_rq_dispenser_cours['NOM']; ?></td>
                            <td>
                                <a href="presenceCours.php?IDDISPENSER_COURS=<?php echo $row_rq_dispenser_cours['IDDISPENSER_COURS']; ?>">
                                    <i class="glyphicon glyphicon-list"></i>
                                </a>
                            </td>

                            <td>
                                <a href="modifCours.php?IDDISPENSER_COURS=<?php echo $row_rq_dispenser_cours['IDDISPENSER_COURS']; ?>">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </a>
                            </td>

                            <td>
                                <a href="suppCours.php?IDDISPENSER_COURS=<?php echo $row_rq_dispenser_cours['IDDISPENSER_COURS']; ?>" onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette entrée?'));">
                                    <i class="glyphicon glyphicon-remove"></i>
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
                var matieres = data.matieres
                console.log(data)
                $('#CLASSE').children('option:not(:first)').remove()
                $('#CLASSE').selectpicker('refresh')
                for (i = 0, len = classes.length; i < len; i++){
                    $('#CLASSE').append(new Option(classes[i].LIBELLE, classes[i].IDCLASSROOM))
                    $('#CLASSE').selectpicker('refresh')
                }
                $('#MATIERE').children('option:not(:first)').remove()
                $('#MATIERE').selectpicker('refresh')
                for (i = 0, len = matieres.length; i < len; i++){
                    $('#MATIERE').append(new Option(matieres[i].LIBELLE, matieres[i].IDMATIERE))
                    $('#MATIERE').selectpicker('refresh')
                }
            })
        }
    }

    $("#selectNiv").on('change', function () {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        if(selectedNiv == "") {
            $('#CLASSE').children('option:not(:first)').remove()
            $('#CLASSE').selectpicker('refresh')
            $('#MATIERE').children('option:not(:first)').remove()
            $('#MATIERE').selectpicker('refresh')
            $('#valid').css("display","none")
        }
    })
    function controlButton_C() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedC = $("#CLASSE").find("option:selected").val()
        var selectedM = $("#MATIERE").find("option:selected").val()

        if(selectedNiv == "" || selectedC == "" ) {
            $('#valid').css("display","none")
        }else {
            $('#valid').css("display","block")
        }
    }
    function controlButton_M() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedC = $("#CLASSE").find("option:selected").val()
        var selectedM = $("#MATIERE").find("option:selected").val()

        if(selectedNiv == "" || selectedM == "") {
            $('#valid').css("display","none")
        }else{
            $('#valid').css("display","block")
        }
    }

</script>
