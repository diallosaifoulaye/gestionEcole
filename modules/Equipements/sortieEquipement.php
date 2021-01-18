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
    $lib->Restreindre($lib->Est_autoriser(16, $_SESSION['profil']));


$colname_rq_equipement = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_equipement = $_SESSION['etab'];
}

$colname_rq_cateEquip="";
if (isset($_POST['selectCat']) && $_POST['selectCat'] != "") {
    $colname_rq_cateEquip = " AND EQUIPEMENT.IDCATEGEQUIP='" . $lib->securite_xss($_POST['selectCat']) . "'";
}
$colname_periode = "";
if (isset($_POST['DU']) && $_POST['DU'] != "" && isset($_POST['AU']) && $_POST['AU'] != "") {
    //var_dump(1);exit;
    $colname_periode = " AND SORTI_EQUIPEMENT.DATE_SORTI>='" . str_replace("/","-",substr($lib->securite_xss($_POST['DU']),0,10)) . "' AND SORTI_EQUIPEMENT.DATE_SORTI<='" . str_replace("/","-",substr($lib->securite_xss($_POST['AU']),0,10)) . "'";
}


$query_rq_equipement = $dbh->query("SELECT * FROM SORTI_EQUIPEMENT,EQUIPEMENT, CATEGEQUIP, UTILISATEURS
WHERE SORTI_EQUIPEMENT.IDETABLISSEMENT = " . $colname_rq_equipement . $colname_rq_cateEquip . $colname_periode." 
AND EQUIPEMENT.IDEQUIPEMENT=SORTI_EQUIPEMENT.ID_EQUIPEMENT 
AND SORTI_EQUIPEMENT.AFFECTAIRE=UTILISATEURS.idUtilisateur
AND CATEGEQUIP.IDCATEGEQUIP=EQUIPEMENT.IDCATEGEQUIP");

require_once('classe/EquipementManager.php');
$categories = new EquipementManager($dbh, 'EQUIPEMENT');


//$categorie = $categories->getEquipement('IDETABLISSEMENT', $_SESSION['etab']);
$query_categorie= $dbh->query("SELECT `IDCATEGEQUIP`, `LIBELLE` FROM `CATEGEQUIP` WHERE IDETABLISSEMENT=". $colname_rq_equipement);
$categorie = $query_categorie->fetchAll();

$query_utilisateur= $dbh->query("SELECT `idUtilisateur`, `matriculeUtilisateur`, `prenomUtilisateur` , `nomUtilisateur` FROM `UTILISATEURS` WHERE ETAT=1 AND idEtablissement=".$colname_rq_equipement );
$utilisateur = $query_utilisateur->fetchAll();

$annee_scolaire = $dbh->query("SELECT * FROM ANNEESSCOLAIRE WHERE IDETABLISSEMENT = " . $colname_rq_equipement);

?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Equipement</a></li>
    <li>Affectation Equipement</li>
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
                    <button data-toggle="modal" data-target="#ajouter"
                    style="background-color:#DD682B" class='btn dropdown-toggle', aria-hidden='true' >
                    <i class="fa fa-plus"></i> Nouvelle Affectation Equipement</button>

                </div>

            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $_GET['msg']; ?></div>
                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $_GET['msg']; ?></div>
                    <?php }
                    ?>

                <?php } ?>


                <form id="form" name="form1" method="post" action="">
                    <fieldset class="cadre">
                        <legend> Filtre</legend>

                        <div class="row">
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>CATEGORIE EQUIPEMENT</label></div>
                                    <div class="col-xs-9">
                                        <select name="selectCat" id="selectCat" class="form-control selectpicker"
                                                data-live-search="true" required >
                                            <option value="">-- Selectionner la categorie--</option>
                                            <?php
                                            foreach ($categorie as $cat) {

                                                ?>
                                                <option
                                                        value=" <?php echo $cat['IDCATEGEQUIP']; ?>"><?php echo $cat['LIBELLE']; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-4">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>DU</label></div>
                                    <div class="col-xs-9">
                                        <input type="text" name="DU" id="date_foo2"  class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <div class="col-xs-3"><label>AU</label></div>
                                    <div class="col-xs-9">
                                        <input type="text" name="AU" id="date_foo"  class="form-control"/>                              `
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
                        <th>CATEGORIES</th>
                        <th>NOM EQUIPEMENT</th>
                        <th>QUANTITE SORTIE</th>
                       <!-- <th>QUANTITE RESTANTE EN STOCK</th>-->
                        <th>DATE D'AFFECTATION</th>
                        <th>AFFECTAIRE</th>
                        <th>NOUVELLE AFFECTATION</th>


                    </tr>
                    </thead>


                    <tbody>
                    <?
                    foreach ($query_rq_equipement->fetchAll() as $categ) {

                        ?>
                        <tr>

                            <td><?php echo $categ['LIBELLE']; ?></td>
                            <td><?php echo $categ['NOMEQUIPEMENT']; ?></td>
                            <td><?php echo $categ['NOMBRE_SORTI']; ?></td>
                            <!--<td><?php /*echo $categ['QTE_RESTANTE']; */?></td>-->
                            <td><?php echo $lib->date_time_fr($categ['DATE_SORTI']); ?></td>
                            <td><?php echo $categ['prenomUtilisateur'].' '.$categ['nomUtilisateur']; ?></td>
                            <td>
                                <a href="ajouterSortieEquipement.php?idequipement= <?php echo $categ['IDEQUIPEMENT'] ?>&amp;stock=<?php echo $categ['QTE_RESTANTE']; ?>"><i
                                        class="glyphicon glyphicon-list"></i></a></td>

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

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="ajouter"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h3 class="panel-title text-center"> Nouvelle Affectation Equipement </h3> <!-- 	ID_SORTI_EQUIPEMENT ID_EQUIPEMENT NOMBRE_SORTI
DATE_SORTI IDETABLISSEMENT -->
                </div>
                <form action="ajouterSortieEquipement.php" method="POST" id="form">

                    <div class="panel-body">
                        <div class="row">


                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CATEGORIE</label>

                                    <div>
                                        <select name="IDCATEGEQUIP" id="IDCATEGEQUIP" class="form-control selectpicker"
                                                data-live-search="true" required onchange="getEquipement();">
                                            <option value="">-- Selectionner la categorie--</option>
                                            <?php
                                            foreach ($categorie as $cat) {

                                                ?>
                                                <option
                                                        value=" <?php echo $cat['IDCATEGEQUIP']; ?>"><?php echo $cat['LIBELLE']; ?> </option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>EQUIPEMENT</label>
                                    <div>
                                        <select name="IDEQUIPEMENT" class="form-control"  required id="IDEQUIPEMENT">
                                            <option value="">-- Selectionner l'equipement --</option>

                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>NOMBRE D'AFFECTATION</label>

                                    <div>
                                        <input type="number" name="NOMBRE_SORTI" id="NOMBRE_SORTI" required
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>AFFECTAIRES</label>

                                    <div>
                                        <select name="AFFECTAIRE" id="AFFECTAIRE" class="form-control selectpicker"
                                                data-live-search="true" required ">
                                        <option value="">-- Selectionner affectaire--</option>
                                        <?php
                                        foreach ($utilisateur as $user) {

                                            ?>
                                            <option
                                                    value=" <?php echo $user['idUtilisateur']; ?>"><?php echo $user['prenomUtilisateur'].' '.$user['nomUtilisateur'].' ('.$user['matriculeUtilisateur'].')'; ?> </option>
                                        <?php } ?>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>DATE D'ATTRIBUTION</label>

                                    <div>
                                        <input type="text" id="date_foo3" name="DATE_SORTI"  class="form-control" required>
                                    </div>
                                </div>
                            </div>



                        </div>


                    </div>
                    <div class="panel-footer">
                        <button type="reset" class="btn btn-danger">Réinitialiser</button>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $_SESSION['etab']; ?>"/>


                        <button type="submit" class="btn btn-primary pull-right">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

    <script>

        function getEquipement() {
            var valSel =$("#IDCATEGEQUIP").val();
            // alert(valSel);
            $.ajax({
                type: "POST",
                url: "getEquipements.php",
                data: "CAT=" + valSel,
                success: function (data) {
                    data = JSON.parse(data);
                    $("#IDEQUIPEMENT").html('<option selected="selected" value="">--Selectionner--</option>');
                    $.each(data, function (cle, valeur) {
                        $("#IDEQUIPEMENT").append('<option value="' + valeur.IDEQUIPEMENT + '">' + valeur.NOMEQUIPEMENT + '</option>');
                    });
                }
            });
        }


    </script>
<?php include('footer.php'); ?>