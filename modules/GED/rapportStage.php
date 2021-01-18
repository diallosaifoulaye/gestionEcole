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
    $lib->Restreindre($lib->Est_autoriser(18, $_SESSION['profil']));

$requete = 'SELECT INDIVIDU.IDINDIVIDU, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.TELMOBILE, INDIVIDU.IDINDIVIDU as rapports
            FROM INDIVIDU
              INNER JOIN INSCRIPTION ON INDIVIDU.IDINDIVIDU = INSCRIPTION.IDINDIVIDU
            WHERE INDIVIDU.IDETABLISSEMENT = ' . $_SESSION['etab'] . ' AND INSCRIPTION.IDANNEESSCOLAIRE = ' . $_SESSION['ANNEESSCOLAIRE'] . ' AND INDIVIDU.IDTYPEINDIVIDU=8 ';

$etudiants = $dbh->prepare($requete);
$etudiants->execute();
$etudiants = $etudiants->fetchAll(PDO::FETCH_OBJ);

$requete = 'SELECT DOC_ETUDIANT.*,TYPE_DOC_ETU.LIBELLE AS libTypeDoc
            FROM AFFECTER_DOC_ETU
                INNER JOIN DOC_ETUDIANT ON AFFECTER_DOC_ETU.IDDOCETU = DOC_ETUDIANT.IDDOCETU
                INNER JOIN TYPE_DOC_ETU ON DOC_ETUDIANT.IDTYPEDOCETU = TYPE_DOC_ETU.IDTYPEDOCETU
            WHERE DOC_ETUDIANT.ANNEESCOLAIRE = '.$_SESSION['ANNEESSCOLAIRE'].' AND AFFECTER_DOC_ETU.IDINDIVIDU =?';
foreach($etudiants as $key => $oneEtu){
    $result = $dbh->prepare($requete);
    $result->execute([$oneEtu->IDINDIVIDU]);
    $result = $result->fetchAll(PDO::FETCH_OBJ);
    $etudiants[$key]->rapports = $result;
//    echo "<pre>";var_dump($etudiants[$key]);exit();
}

include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">GED</a></li>
    <li>Rapport de stage</li>
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
                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {
                    if (isset($_GET['res']) && $_GET['res'] == 1) { ?>
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $_GET['msg']; ?>
                        </div>
                    <?php } if (isset($_GET['res']) && $_GET['res'] != 1) { ?>
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $_GET['msg']; ?>
                        </div>
                    <?php }
                } ?>
                <form id="form1" name="form1" method="post" action="">
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
                            <th>TEL</th>
                            <th>NOMBRE DE DOC</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($etudiants as $oneEt) {?>
                            <tr>
                                <td><?= $oneEt->MATRICULE; ?></td>
                                <td><?= $oneEt->PRENOMS; ?></td>
                                <td><?= $oneEt->NOM; ?></td>
                                <td><?= $oneEt->TELMOBILE; ?></td>
                                <td><?= count($oneEt->rapports); ?></td>
                                <td>
                                    <a href="newDocEtudiant.php?IDINDIVIDU=<?= $oneEt->IDINDIVIDU; ?>" style="margin-right: 15px;">
                                        <i class="glyphicon glyphicon-search"></i>
                                    </a>
                                    <a href="newDocEtudiant.php?IDINDIVIDU=<?= $oneEt->IDINDIVIDU; ?>">
                                        <i class="glyphicon glyphicon-plus"></i>
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
<?php include('footer.php');