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

if ($lib->securite_xss($_SESSION['profil']) != 1)
    $lib->Restreindre($lib->Est_autoriser(34, $lib->securite_xss($_SESSION['profil'])));


if ((isset($_POST["envoyer"])) && ($lib->securite_xss($_POST["envoyer"]) != "") && ($lib->securite_xss($_POST["DATE"]) != "") && ($lib->securite_xss($_POST["MONTANT"]) != ""))
{
    try
    {
        $req = $dbh->prepare("INSERT INTO DEPENSE (DATE_REGLEMENT,  MONTANT, MOTIF, IDTYPEPAIEMENT, IDETABLISSEMENT, IDANNEESCOLAIRE, REFERENCE, NUM_CHEQUE, FK_BANQUE) 
                                        VALUES (:DATE_REGLEMENT, :MONTANT, :MOTIF, :IDTYPEPAIEMENT, :IDETABLISSEMENT, :IDANNEESCOLAIRE, :REFERENCE, :NUM_CHEQUE, :FK_BANQUE)");
                                        $req->bindParam(':DATE_REGLEMENT', $lib->securite_xss($_POST['DATE']), PDO::PARAM_STR);
                                        $req->bindParam(':MONTANT', $lib->securite_xss($_POST['MONTANT']), PDO::PARAM_INT);
                                        $req->bindParam(':MOTIF', $lib->securite_xss($_POST['MOTIF']), PDO::PARAM_STR);
                                        $req->bindParam(':IDTYPEPAIEMENT', $lib->securite_xss($_POST['IDTYPEPAIEMENT']), PDO::PARAM_INT);
                                        $req->bindParam(':IDETABLISSEMENT', $lib->securite_xss($_SESSION['etab']), PDO::PARAM_INT);
                                        $req->bindParam(':IDANNEESCOLAIRE', $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']), PDO::PARAM_INT);
                                        $req->bindParam(':REFERENCE', $lib->securite_xss($_POST['REFERENCE']), PDO::PARAM_STR);
                                        $req->bindParam(':NUM_CHEQUE', $lib->securite_xss($_POST['NUM_CHEQUE']), PDO::PARAM_STR);
                                        $req->bindParam(':FK_BANQUE', $lib->securite_xss($_POST['FK_BANQUE']), PDO::PARAM_INT);

        $res = $req->execute();
        if($res == 1)
        {
            $mes = "Enregistrement reussie";
        }
        else {
            $mes = "enregistrement echouee";
        }
    }
    catch (Exception $e)
    {
        echo -2;
    }

    header("Location: depenses.php?res=".$lib->securite_xss($res) . "&msg=".$lib->securite_xss($mes));
}


$colname_rq_depense = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_depense = $lib->securite_xss($_SESSION['etab']);
}
$colname2_rq_depense = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $colname2_rq_depense = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}

$query_rq_depense = $dbh->query("SELECT * FROM DEPENSE, TYPE_PAIEMENT 
                                           WHERE TYPE_PAIEMENT.id_type_paiment = DEPENSE.IDTYPEPAIEMENT  
                                           AND DEPENSE.IDETABLISSEMENT = " . $colname_rq_depense . " 
                                           AND DEPENSE.IDANNEESCOLAIRE = " . $colname2_rq_depense);
//
$query_type_banque = $dbh->query("SELECT ROWID, LABEL FROM BANQUE WHERE ETAT = 1");

$query_type_payement = $dbh->query("SELECT id_type_paiment, libelle_paiement FROM TYPE_PAIEMENT");

$colname_rq_mnt = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_mnt = $lib->securite_xss($_SESSION['etab']);
}

$query_rq_mnt = $dbh->query("SELECT sum(DEPENSE.MONTANT) as montant 
                                       FROM DEPENSE 
                                       WHERE DEPENSE.IDETABLISSEMENT = " . $colname_rq_mnt . " 
                                       AND DEPENSE.IDANNEESCOLAIRE = " . $colname2_rq_depense);

$row_rq_mnt = $query_rq_mnt->fetchObject();


?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">TIERS</a></li>
    <li>Dépenses</li>
</ul>
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
                <?php if (isset($_GET['msg']) && $lib->securite_xss($_GET['msg']) != '') {
                    if (isset($_GET['res']) && $lib->securite_xss($_GET['res']) == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>

                    <?php } if (isset($_GET['res']) && $lib->securite_xss($_GET['res']) != 1) { ?>

                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>

                    <?php } ?>

                <?php } ?>

                <form id="form1" name="form1" method="post" action="">

                    <fieldset class="cadre">
                        <legend> Nouvelle d&eacute;pense</legend>
                        <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <label>DATE</label>
                                            <input type="text" name="DATE" id="date_foo" required class="form-control" value="<?php echo $lib->securite_xss($_POST['DATE']); ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <label>MONTANT</label>
                                            <input type="number" name="MONTANT" id="MONTANT" required class="form-control" value="<?php echo $lib->securite_xss($_POST['MONTANT']); ?>"/>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <br>
                        <div class="row">

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <label>MOTIF</label>
                                        <textarea name="MOTIF" id="MOTIF" class="form-control" rows="2"><?php echo $lib->securite_xss($_POST['MOTIF']); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <label class="control-label">REFERENCE DEPENSE</label>
                                            <input type="text" name="REFERENCE" id="REFERENCE" class="form-control" value="<?php echo $lib->securite_xss($_POST['REFERENCE']); ?>"/>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <br>
                        <div class="row" >

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label">TYPE DE PAIEMENT</label>
                                        <select name="IDTYPEPAIEMENT" id="typePayement" required class="form-control">
                                            <option value="" disabled="disabled" selected="selected">Selectionner le type de paiement</option>
                                            <?php foreach ($query_type_payement->fetchAll() as $item_payement) { ?>
                                                <option value="<?php echo $item_payement['id_type_paiment']; ?>"><?php echo $item_payement['libelle_paiement']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div id="lotTypePayement" style="display: none" class="col-lg-8">

                                    <div class="col-lg-4">
                                        <label id="titre_id_type_banque"></label>
                                        <input type="text" class="form-control" name="NUM_CHEQUE" required>
                                    </div>

                                    <div class="col-lg-4">
                                        <label for="id_type_banque" class="control-label">BANQUE</label>
                                        <select name="FK_BANQUE" id="id_type_banque" required class="form-control">
                                            <option value="" disabled="disabled" selected="selected">choisir la banque</option>
                                            <?php foreach ($query_type_banque->fetchAll() as $row_type_banque) { ?>
                                                <option value="<?php echo $row_type_banque['ROWID']; ?>"><?php echo $row_type_banque['LABEL']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4"></div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <div>
                                        <input type="submit" class="btn btn-success" name="envoyer" value="Valider"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4"></div>
                        </div>

                    </fieldset>

                </form>


                <table id="customers2" class="table datatable">

                    <thead>
                        <tr>

                            <th>DATE</th>
                            <th>MONTANT</th>
                            <th>MOTIF</th>
                            <th>TYPE PAYEMENT</th>
                            <th>DETAIL</th>


                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($query_rq_depense->fetchAll() as $row_rq_depense) { ?>

                        <tr>

                            <td><?php echo $lib->date_fr($row_rq_depense['DATE_REGLEMENT']); ?></td>
                            <td><?php echo $lib->nombre_form($row_rq_depense['MONTANT']); ?></td>
                            <td><?php echo $row_rq_depense['MOTIF']; ?></td>
                            <td><?php echo $row_rq_depense['libelle_paiement']; ?></td>
                            <td ><a href="detailDepenses.php?IDREGLEMENT=<?php echo base64_encode($row_rq_depense['IDREGLEMENT']);  ?>"><i class="glyphicon glyphicon-search"></i></a></td>


                        </tr>

                    <?php } ?>

                    </tbody>
                </table>


                <div>

                    <br/>
                    <br/>
                    <table class="table table-bordered table-responsive table-striped" style="width:28%">
                        <tr>

                            <th width="271">MONTANT TOTAL DES DEPENSES:</th>
                            <td><?php echo $lib->nombre_form($row_rq_mnt->montant); ?></td>
                        </tr>
                    </table>
                </div>

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
    $("#typePayement").on('change', function ()
    {
        var selectedPay = $("#typePayement").find("option:selected").val()
        if(selectedPay != 2)
        {
            if(selectedPay == 1)
            {
                $('#titre_id_type_banque').text("NUMERO CHEQUE")
            }else{
                $('#titre_id_type_banque').text("NUMERO VIREMENT")
            }
            $('#lotTypePayement').css('display', 'block')
        }
        else {
            $('#lotTypePayement').css('display', 'none')
            $('#id_type_banque').prop('selectedIndex',0);
        }
    })
</script>
