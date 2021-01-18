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
    $query = "SELECT DISTINCT CLASSROOM.IDCLASSROOM as idclass,CLASSROOM.LIBELLE as libclass, CLASSROOM.IDNIVEAU AS NIVEAUX,EMPLOIEDUTEMPS.IDPERIODE as idperiode,PERIODE.NOM_PERIODE as periode,EMPLOIEDUTEMPS.IDEMPLOIEDUTEMPS as idET 
              FROM CLASSROOM 
              INNER JOIN EMPLOIEDUTEMPS ON CLASSROOM.IDCLASSROOM = EMPLOIEDUTEMPS.IDCLASSROOM 
              INNER JOIN PERIODE ON EMPLOIEDUTEMPS.IDPERIODE = PERIODE.IDPERIODE GROUP BY CLASSROOM.IDCLASSROOM ";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $tabET = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Emploi du Temps</a></li>
    <li>Emploi du temps</li>
</ul>
<!-- END BREADCRUMB -->
<style>
    #lien{
        background-color:#DD682B;
        color: #332f34;
    }
    #lien:hover{
        color: #F6F6F6;
        text-decoration: none;
    }

</style>

<div class="page-content-wrap">

    <!-- START WIDGETS -->
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                &nbsp;&nbsp;&nbsp;
                <div class="btn-group pull-right">
                    <a id="lien" href="nouveauEmploiTemps.php"
                        class='btn dropdown-toggle' aria-hidden='true'>
                            <i class="fa fa-plus"></i> Nouvel Emploi du temps
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {
                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?>
                        </div>
                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?>
                        </div>
                    <?php }
                    ?>
                <?php } ?>
<!--                <form id="form1" name="form1" method="POST" action="" class="form-inline">-->
<!--                    <fieldset>-->
<!--                        <div class="col-lg-6">-->
<!--                            <label for="CLASSE">CLASSE</label>-->
<!--                            <select name="CLASSE" id="CLASSE">-->
<!--                                <option value=""> --S&eacute;lectionner--</option>-->
<!--                            </select>-->
<!--                        </div>-->
<!--                        <div class="col-lg-1">-->
<!--                            <input name="rechercher" type="submit" id="rechercher" value="Rechercher"-->
<!--                                   class="btn btn-success"/>-->
<!--                        </div>-->
<!--                    </fieldset>-->
<!--                </form>-->
                <table id="customers2" class="table datatable">
                    <thead>
                    <tr>
                        <th>CLASSE</th>
                        <th>PERIODE</th>
                        <th>MODIFIER</th>
                        <th>SUPPRIMER</th>
                        <th>DETAILS</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($tabET as $oneET) { ?>
                        <tr>
                            <td><?php echo $oneET->libclass; ?></td>
                            <td><?php echo $oneET->periode; ?></td>
                            <td>
                                <a href="modifEmploiTemps.php?idClasse=<?= base64_encode($oneET->idclass); ?>&idET=<?= base64_encode($oneET->idET); ?>&IDNIVEAU=<?php echo base64_encode($oneET->NIVEAUX); ?>&idPeriode=<?= base64_encode($oneET->idperiode); ?>&NOM=<?= base64_encode(str_replace(" ","-",$oneET->libclass)); ?>">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </a>
                            </td>
                            <td>
                                <a href="suppEmploiTemps.php?idET=<?= base64_encode($oneET->idET); ?>"
                                   onclick="return(confirm('Etes-vous s&ucirc;r de vouloir supprimer cette entr&eacute;e?'));">
                                    <i class="glyphicon glyphicon-remove"></i>
                                </a>
                            </td>
                            <td>
                                <a href="detailEmploiTempsClasse.php?IDCLASSROOM=<?= base64_encode($oneET->idclass); ?>&amp;IDPERIODE=<?= base64_encode($oneET->idperiode); ?>&amp;IDEMPLOIEDUTEMPS=<?= base64_encode($oneET->idET); ?>&amp;NOM=<?= base64_encode(str_replace(" ","-",$oneET->libclass)); ?>">
                                    <i class="glyphicon glyphicon-list"></i>
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