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
    $lib->Restreindre($lib->Est_autoriser(15, $_SESSION['profil']));

$colname_rq_equipement = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_equipement = $lib->securite_xss($_SESSION['etab']);
}

$query_rq_equipement = $dbh->query("SELECT e.IDEQUIPEMENT, e.FACTURE, e.MONTANT, e.DATE, e.NOMEQUIPEMENT, e.QTE, e.QTE_RESTANTE, c.LIBELLE, e.REFERENCE
                                              FROM EQUIPEMENT e 
                                              INNER JOIN CATEGEQUIP c ON e.IDCATEGEQUIP = c.IDCATEGEQUIP,
                                              WHERE e.IDETABLISSEMENT = " . $colname_rq_equipement);



require_once('classe/CategorieEquipementManager.php');
$categories = new CategorieEquipementManager($dbh, 'CATEGEQUIP');
$categorie = $categories->getCategorieEquipement('IDETABLISSEMENT', $lib->securite_xss($_SESSION['etab']));

?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Equipement</a></li>
    <li>Inventaire Equipement</li>
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
                            style="background-color:#DD682B" class='btn dropdown-toggle'  aria-hidden='true'>
                        <i class="fa fa-plus"></i> Nouvel Equipement
                    </button>
                </div>
            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) { ?>

                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>

                    <?php } if (isset($_GET['res']) && $_GET['res'] != 1) {?>

                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>

                    <?php } ?>

                <?php } ?>

                <table id="customers2" class="table datatable">
                    <thead>
                    <tr>
                        <th>CATEGORIE</th>
                        <th>REFERENCE</th>
                        <th>NOM EQUIPEMENT</th>
                        <th style="text-align: center !important;">QUANTITE INITIALE</th>
                        <th style="text-align: center !important;">QUANTITE EN STOCK</th>
                        <th>MODIFIER</th>
                        <th>SUPPRIMER</th>

                    </tr>
                    </thead>


                    <tbody>

                    <?php foreach ($query_rq_equipement->fetchAll() as $categ) { ?>

                        <tr>

                            <td><?php echo $categ['LIBELLE']; ?></td>
                            <td><?php echo $categ['REFERENCE']; ?></td>
                            <td><?php echo $categ['NOMEQUIPEMENT']; ?></td>
                            <td style="text-align: center !important;"><?php echo $categ['QTE']; ?></td>
                            <td style="text-align: center !important;"><?php echo $categ['QTE_RESTANTE']; ?></td>

                            <td>
                                <a href="modifEquipement.php?idEquipement=<?php echo $categ['IDEQUIPEMENT']; ?>">
                                    <i class=" glyphicon glyphicon-edit"></i>
                                </a>
                            </td>

                            <td>
                                <a href="suppEquipement.php?idEquipement=<?php echo $categ['IDEQUIPEMENT']; ?>" onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette enregistrement'));">
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

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="ajouter" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="panel-title text-center"> Nouvel Equipement </h3>
                </div>
                <form action="ajouterEquipement.php" method="POST" id="form">

                    <div class="panel-body">
                        <div class="row">


                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CATEGORIE</label>

                                    <div>
                                        <select name="IDCATEGEQUIP" class="form-control selectpicker" data-live-search="true" required>

                                            <option value="">--Selectionner la categorie--</option>

                                            <?php foreach ($categorie as $cat) { ?>

                                                <option value="<?php echo $cat->getIDCATEGEQUIP(); ?>"><?php echo $cat->getLIBELLE(); ?> </option>

                                            <?php } ?>

                                        </select>

                                    </div>
                                </div>

                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>REFERENCE </label>
                                    <div>
                                        <input type="text" name="REFERENCE" id="REFERENCE" required class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>NOM EQUIPEMENT</label>
                                    <div>
                                        <input type="text" name="NOMEQUIPEMENT" id="NOMEQUIPEMENT" required class="form-control"/>
                                    </div>
                                </div>
                            </div>


                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>QUANTITE</label>
                                    <div>
                                        <input type="number" name="QTE" id="QTE" required class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>MODELE </label>
                                    <div>
                                        <input type="text" name="MODELE" id="MODELE"  class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>MARQUE </label>
                                    <div>
                                        <input type="text" name="MARQUE" id="MARQUE"  class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>DATE ACQUISITION </label>
                                    <div>
                                        <input type="date" name="DATE_ACQUISITION" id="DATE_ACQUISITION" required class="form-control" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="panel-footer">

                        <button type="reset" class="btn btn-danger">Réinitialiser</button>

                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $lib->securite_xss($_SESSION['etab']); ?>"/>
                        <input type="hidden" name="ANNEESSCOLAIRE" value="<?php echo $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);  ?>"/>

                        <input type="hidden" name="DATE" value="<?php echo date('Y-m-d h:i:s'); ?>"/>
                        <input type="hidden" name="QTE_RESTANTE" value="0"/>

                        <button type="submit" class="btn btn-primary pull-right">Valider</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php include('footer.php'); ?>