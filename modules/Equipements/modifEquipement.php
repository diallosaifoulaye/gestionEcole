<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(15, $_SESSION['profil']));

require_once("classe/EquipementManager.php");
require_once("classe/Equipement.php");
$niv = new EquipementManager($dbh, 'EQUIPEMENT');

$colname_rq_ue_etab = "-1";
if (isset($_GET['idEquipement'])) {
    $colname_rq_ue_etab = $lib->securite_xss($_GET['idEquipement']);
}

$colname_rq_equipement = "-1";
if (isset($_SESSION['etab'])) {
    $colname_etab = $lib->securite_xss($_SESSION['etab']);
}

try
{
    $query_type_categorie = $dbh->query("SELECT IDCATEGEQUIP, LIBELLE 
                                                   FROM CATEGEQUIP 
                                                   WHERE IDETABLISSEMENT = $colname_etab");

    $categorie = $query_type_categorie->fetchAll();


    $query_rq_ue_etab = $dbh->query("SELECT e.IDEQUIPEMENT, e.FACTURE, e.MONTANT, e.DATE, e.NOMEQUIPEMENT, e.QTE, e.QTE_RESTANTE, e.IDCATEGEQUIP,
                                                e.REFERENCE, e.MODELE, e.MARQUE,  e.DATE_ACQUISITION
                                               FROM EQUIPEMENT e 
                                               WHERE e.IDEQUIPEMENT = $colname_rq_ue_etab");

    foreach ($query_rq_ue_etab->fetchAll() as $row_rq_ue_etab)
    {
        $id = $row_rq_ue_etab['IDEQUIPEMENT'];
        $NOMEQUIPEMENT = $row_rq_ue_etab['NOMEQUIPEMENT'];
        $IDCATEGEQUIP = $row_rq_ue_etab['IDCATEGEQUIP'];
        $QTE = $row_rq_ue_etab['QTE'];
        $REFERENCE = $row_rq_ue_etab['REFERENCE'];
        $MODELE = $row_rq_ue_etab['MODELE'];
        $MARQUE = $row_rq_ue_etab['MARQUE'];
        $DATE_ACQUISITION = $row_rq_ue_etab['DATE_ACQUISITION'];



    }
}
catch (PDOException $e){
    echo -2;
}



if (isset($_POST) && $_POST != null) {
    $_POST['QTE_RESTANTE'] = $lib->securite_xss($_POST['QTE']);
    $res = $niv->modifier($lib->securite_xss_array($_POST), 'IDEQUIPEMENT', $lib->securite_xss($_POST['IDEQUIPEMENT']));
    if ($res == 1)
    {
        $msg = "modification reussie";
    }
    else
    {
        $msg = "modification echouee";
    }
    header("Location: inventaireEquipement.php?msg=" . $lib->securite_xss($msg) . "&res=" . $lib->securite_xss($res));
}
?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Equipement</a></li>
    <li>Inventaire equipement</li>
</ul><div class="panel panel-default">
            <div class="panel-heading">
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">

                <form action="modifEquipement.php" method="POST">

                    <div class="row">

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>CATEGORIE</label>

                                <div>
                                    <select name="IDCATEGEQUIP" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">--Selectionner la categorie--</option>

                                        <?php foreach ($categorie as $cat) { ?>

                                            <option value="<?php echo $cat['IDCATEGEQUIP']; ?>" <?php if($IDCATEGEQUIP === $cat['IDCATEGEQUIP']) echo "selected"; ?>>

                                                <?php echo $cat['LIBELLE']; ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>REFERENCE</label>

                                <div>
                                    <input type="text" name="REFERENCE" id="REFERENCE" required class="form-control" value="<?php echo $REFERENCE; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>NOM EQUIPEMENT</label>

                                <div>
                                    <input type="text" name="NOMEQUIPEMENT" id="NOMEQUIPEMENT" required class="form-control" value="<?php echo $NOMEQUIPEMENT; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>QUANTITE</label>

                                <div>
                                    <input type="number" name="QTE" id="QTE" required class="form-control" value="<?php echo $QTE; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>MODELE</label>

                                <div>
                                    <input type="text" name="MODELE" id="MODELE" required class="form-control" value="<?php echo $MODELE; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>MARQUE</label>

                                <div>
                                    <input type="text" name="MARQUE" id="MARQUE" required class="form-control" value="<?php echo $MARQUE; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>DATE_ACQUISITION</label>

                                <div>
                                    <input type="date" name="DATE_ACQUISITION" id="DATE_ACQUISITION" required class="form-control" value="<?php echo $DATE_ACQUISITION; ?>"/>
                                </div>
                            </div>
                        </div>
                        <br><br>


                        <div class="col-lg-12">
                            <br/>
                            <div class="col-lg-offset-5"><input type="submit" class="btn btn-success" value="Modifier"/></div>

                        </div>

                    </div>

                    <input type="hidden" name="IDEQUIPEMENT" value="<?php echo $id; ?>"/>
                    <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $lib->securite_xss($_SESSION['etab']); ?>"/>
                    <input type="hidden" name="DATE" value="<?php echo date('Y-m-d h:i:s'); ?>"/>


                </form>

            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->


<?php include('footer.php'); ?>