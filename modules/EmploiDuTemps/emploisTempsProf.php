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


$colname_rq_individu = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_individu = $lib->securite_xss($_SESSION['etab']);
}

try
{
    $query_rq_indiv = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.IDINDIVIDU, INDIVIDU.PHOTO_FACE 
                                             FROM INDIVIDU 
                                             WHERE IDETABLISSEMENT = " . $colname_rq_individu . " 
                                             AND IDTYPEINDIVIDU=7 ");

    if(isset($_POST['IDINDIVIDU']) && $_POST['IDINDIVIDU']!='')
    {
        $query_rq_individu = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.IDINDIVIDU, INDIVIDU.PHOTO_FACE 
                                                    FROM INDIVIDU 
                                                    WHERE IDETABLISSEMENT = " . $colname_rq_individu . " 
                                                    AND IDTYPEINDIVIDU=7 
                                                    AND INDIVIDU.IDINDIVIDU=" .$lib->securite_xss($_POST['IDINDIVIDU']));
    }else{

        $query_rq_individu = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.IDINDIVIDU, INDIVIDU.PHOTO_FACE 
                                                    FROM INDIVIDU 
                                                    WHERE IDETABLISSEMENT = " . $colname_rq_individu . " 
                                                    AND IDTYPEINDIVIDU=7 ");
    }
}
catch (PDOException $e)
{
    echo -2;
}
?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Emploi du Temps</a></li>
    <li>Emploi du temps des professeurs</li>
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
                <form id="form" name="form1" method="post" action="emploisTempsProf.php" class="form-inline">
                    <fieldset class="cadre"><legend> FILTRE</legend>


                        <div class="form-group col-lg-8">
                            <div class="col-lg-2"> <label>Enseignants</label></div>
                            <div class="col-lg-6"><select name="IDINDIVIDU" id="IDINDIVIDU" class="form-control selectpicker" data-live-search="true">
                                    <option value="">--Selectionner--</option>
                                    <?php foreach ($query_rq_indiv->fetchAll() as $indiv){ ?>
                                        <option value="<?php echo $indiv['IDINDIVIDU']; ?>"  <?php if($indiv['IDINDIVIDU']==$lib->securite_xss($_POST['IDINDIVIDU'])) echo "selected"; ?>><?php echo $indiv['PRENOMS']; ?> - <?php echo $indiv['NOM']; ?></option>
                                    <?php } ?>
                                </select></div>
                        </div>


                        <div class="form-group col-lg-offset-1 col-lg-1">

                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>

                    </fieldset>
                </form>




                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>

                        <th>MATRICULE</th>
                        <th>PRENOMS</th>
                        <th>NOM</th>

                        <th>DETAILS</th>

                    </tr>
                    </thead>


                    <tbody>
                    <? foreach ($query_rq_individu->fetchAll() as $row_rq_individu) { ?>
                        <tr>
                            <td><?php echo $row_rq_individu['MATRICULE']; ?></td>
                            <td><?php echo $row_rq_individu['PRENOMS']; ?></td>
                            <td><?php echo $row_rq_individu['NOM']; ?></td>

                            <td>
                                <a href="detailEmploiTempsProfs.php?IDINDIVIDU=<?= base64_encode($row_rq_individu['IDINDIVIDU']) ; ?>&NOM=<?= base64_encode($row_rq_individu['PRENOMS']  ."-".$row_rq_individu['NOM']); ?>"><i
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

<?php include('footer.php'); ?>