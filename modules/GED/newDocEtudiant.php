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
    $lib->Restreindre($lib->Est_autoriser(17, $_SESSION['profil']));

$colname_rq_individu = "-1";
if (isset($_GET['IDINDIVIDU'])) {
    $colname_rq_individu = $_GET['IDINDIVIDU'];
}

$query_rq_individu = $dbh->query("SELECT * FROM INDIVIDU WHERE IDINDIVIDU = " . $colname_rq_individu);

$row_rq_individu = $query_rq_individu->fetchObject();

$typeDocEtu = $dbh->query("SELECT * FROM TYPE_DOC_ETU");
if( isset($_POST['createDoc']) && (isset($_FILES['FILE']) && count($_FILES['FILE'])>0 && $_FILES['FILE']["error"] != "4")){
//    echo "<pre>";var_dump($_POST);exit();
    if($lib->uploadFiles( $_FILES['FILE'], "rapportStage/", $_POST['NOM'])){
        $query = "INSERT INTO `DOC_ETUDIANT` (`LIBELLE`, `NOM`, `DATE`, `DESCRIPTION`, `IDTYPEDOCETU`, `ANNEESCOLAIRE`, `ETABLISSEMENT`)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbh->prepare($query);
        $value = [
            $_POST['LIBELLE'],
            $_POST['NOM'].".".pathinfo($_FILES['FILE']['name'], PATHINFO_EXTENSION),
            $_POST['DATEDOC'],
            $_POST['DESCRIPTION'],
            $_POST['IDTYPEDOCETU'],
            $_POST['ANNEESCOLAIRE'],
            $_POST['ETABLISSEMENT']
        ];
        if($stmt->execute($value)){
            $query = "SELECT MAX(IDDOCETU) AS IDDOCETU FROM `DOC_ETUDIANT`";
            $stmt = $dbh->prepare($query);
            $stmt->execute();
            $idDocEtu = $stmt->fetchAll(PDO::FETCH_OBJ);
            $idDocEtu = $idDocEtu[0]->IDDOCETU;

            $query = "INSERT INTO `AFFECTER_DOC_ETU` (`IDINDIVIDU`, `IDDOCETU`) VALUES (?, ?)";
            $stmt = $dbh->prepare($query);
            if($stmt->execute([$_POST['IDINDIVIDU'],$idDocEtu])){
                $msg = "Ajout document réussi";
                $urlredirect = "rapportStage.php?msg=$msg&res=1";
            }
        }else{
            $lib->deleteFiles("/sunuecole/rapportStage/".$_POST['NOM']);
            $msg = "Ajout document échoué";
            $urlredirect = "rapportStage.php?msg=$msg&res=0";
        }
    }else{
        $msg = "Ajout document échoué";
        $urlredirect = "rapportStage.php?msg=$msg&res=0";
    }
    header("Location:$urlredirect");
}
include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">GED</a></li>
    <li>Nouveau document</li>
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
                <div class="row">
                    <div class="col-lg-5">
                        <fieldset class="cadre">
                            <legend> INFORMATIONS PERSONNELLES</legend>
                            <table width="100%" border="0" cellspacing="2">
                                <tr>
                                    <th>MATRICULE</th>
                                    <th>NOM</th>
                                    <th>PRENOM(S)</th>
                                </tr>
                                <tr>
                                    <td><?php echo $row_rq_individu->MATRICULE; ?></td>
                                    <td><?php echo $row_rq_individu->NOM; ?></td>
                                    <td><?php echo $row_rq_individu->PRENOMS; ?></td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                    <div class="col-lg-7">
                        <form action="newDocEtudiant.php" method="post" enctype="multipart/form-data">
                            <fieldset class="cadre">
                                <legend>CREATION DU DOCUMENT</legend>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>TYPE DOCUMENT</label>
                                        <div>
                                            <select name="IDTYPEDOCETU" class="form-control">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($typeDocEtu->fetchAll() as $oneTypDoc) { ?>
                                                    <option
                                                        value="<?php echo $oneTypDoc['IDTYPEDOCETU']; ?>"> <?php echo $oneTypDoc['LIBELLE']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>LIBELLE</label>
                                        <div>
                                            <input type="text" name="LIBELLE" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <div>
                                            <textarea name="DESCRIPTION" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Document</label>
                                         <div>
                                            <input type="file" name="FILE" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-xs-offset-5 col-xs-1">
                                <br/>
                                    <input name="Envoyer" type="submit" value="Valider" class="btn btn-success"/>
                                </div>
                                <input type="hidden" name="ETABLISSEMENT" value="<?= $_SESSION['etab']; ?>"/>
                                <input type="hidden" name="IDINDIVIDU" value="<?= $_GET['IDINDIVIDU']; ?>"/>
                                <input type="hidden" name="DATEDOC" value="<?= \gmstrftime("%Y-%m-%d"); ?>"/>
                                <input type="hidden" name="ANNEESCOLAIRE" value="<?= $_SESSION['ANNEESSCOLAIRE']; ?>"/>
                                <input type="hidden" name="NOM" value="<?= $lib->random(5); ?>"/>
                                <input type="hidden" name="createDoc" value=""/>
                            </fieldset>
                        </form>
                    </div>
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