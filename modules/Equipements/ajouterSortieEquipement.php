<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once("../../restriction.php");
require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");

require_once("classe/SortieEquipementManager.php");
require_once("classe/SortieEquipement.php");

$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(16, $lib->securite_xss($_SESSION['profil'])));

$sortie = new SortieEquipementManager($dbh, 'SORTI_EQUIPEMENT');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $reste = $lib->securite_xss($_POST['QTE_STOCK']) - $lib->securite_xss($_POST['NOMBRE_SORTI']);
    //var_dump($reste);exit;

//echo "<pre>";var_dump($lib->GetSQLValueString(date("Y-m-d"), "date"));exit;
    $insertSQL = $dbh->prepare("INSERT INTO SORTI_EQUIPEMENT (ID_SORTI_EQUIPEMENT, ID_EQUIPEMENT, NOMBRE_SORTI, DATE_SORTI, IDETABLISSEMENT, AFFECTAIRE) VALUES (?, ?, ?, ?, ?, ?)");
    $res1 = $insertSQL->execute(array($lib->securite_xss($_POST['ID_SORTI_EQUIPEMENT']),$lib->securite_xss($_POST['ID_EQUIPEMENT']),$lib->securite_xss($_POST['NOMBRE_SORTI']),date("Y-m-d"),$lib->securite_xss($_POST['IDETABLISSEMENT']),$lib->securite_xss($_POST['AFFECTAIRE'])));


    $query = sprintf("UPDATE EQUIPEMENT SET QTE_RESTANTE =:QTE_RESTANTE WHERE IDEQUIPEMENT =:IDEQUIPEMENT");
    $result = $dbh->prepare($query);
    $idequip=$lib->securite_xss($_POST['ID_EQUIPEMENT']);
    $result->bindParam(":QTE_RESTANTE", $reste);
    $result->bindParam(":IDEQUIPEMENT", $idequip);
    $res2 = $result->execute();


    if($res1>0 && $res2>0){
        //var_dump($res2);exit;
        $insertGoTo = "sortieEquipement.php";
        $insertGoTo .= ($res1 && $res2) ?  "?res=1&msg=Affectation éffectuée avec succes" : "?res=-1&msg=L'affectation a échoué";
    }

    header("Location:$insertGoTo");

}

$colname_rq_equip = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_equip = $lib->securite_xss($_SESSION['etab']);
}

$colname2_rq_equip = "-1";
if (isset($_GET['idequipement']) && $_GET['idequipement']!='') {
    $colname2_rq_equip = $lib->securite_xss($_GET['idequipement']);
}else{
    $colname2_rq_equip=$lib->securite_xss($_POST['IDEQUIPEMENT']);
}
$stock="-1";
if(isset($_GET['stock']) && $_GET['stock']='') {
    $stock = $lib->securite_xss($_GET['stock']);
}else{
    $stock=$lib->securite_xss($_POST['QTE_STOCK']);
}

$query_rq_equip = sprintf("SELECT * FROM EQUIPEMENT,CATEGEQUIP WHERE EQUIPEMENT.IDETABLISSEMENT = %s AND  CATEGEQUIP.IDCATEGEQUIP=EQUIPEMENT.IDCATEGEQUIP AND EQUIPEMENT.IDEQUIPEMENT=%s", $lib->GetSQLValueString($colname_rq_equip, "int"), $lib->GetSQLValueString($colname2_rq_equip, "int"));
$rq_equip = $dbh->query($query_rq_equip);
$row_rq_equip = $rq_equip->fetchObject();

$query_utilisateur= $dbh->query("SELECT `idUtilisateur`, `matriculeUtilisateur`, `prenomUtilisateur` , `nomUtilisateur` FROM `UTILISATEURS` WHERE ETAT=1 AND idEtablissement=".$colname_rq_equip );
$utilisateur = $query_utilisateur->fetchAll();

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
                &nbsp;&nbsp;&nbsp
                <!--<div class="btn-group pull-right">
                    <button data-toggle="modal" data-target="#ajouter"
                    style="background-color:#DD682B" class='btn dropdown-toggle', aria-hidden='true' >
                    <i class="fa fa-plus"></i> Nouvelle Sortie Equipement</button>

                </div>-->

            </div>
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->affichage_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } ?>


                <div class="row">
                    <div class="col-lg-6">
                        <fieldset class="cadre">
                            <legend>Information sur l'equipement</legend>

                            <table class="table table-responsive table-bordered table-bordered">

                                <tr>
                                    <td>CATEGORIE EQUIPEMENT:</td>
                                    <td><?php echo $row_rq_equip->LIBELLE; ?></td>
                                </tr>

                                <tr>
                                    <td>NOM EQUIPEMENT:</td>
                                    <td><?php echo $row_rq_equip->NOMEQUIPEMENT; ?></td>
                                </tr>
                                <tr>
                                    <td>QUANTITE INITIALE EN STOCK:</td>
                                    <td><?php echo $row_rq_equip->QTE; ?></td>
                                </tr>
                                <tr>
                                    <td>QUANTITE RESTANTE EN STOCK:</td>
                                    <td><?php echo $row_rq_equip->QTE_RESTANTE; ?></td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>

                    <div class="col-lg-6">

                        <fieldset class="cadre">
                            <legend>Affectation équipement</legend>
                            <form action="" method="post" name="form1" id="form1">
                                <table class="table table-responsive table-bordered table-bordered">
                                    <tr>
                                        <td>AFFECTAIRE:</td>
                                        <td>


                                            <select name="AFFECTAIRE" id="AFFECTAIRE" class="form-control selectpicker"
                                                    data-live-search="true" required ">
                                            <option value="">-- Selectionner affectaire--</option>
                                            <?php
                                            foreach ($utilisateur as $user) {

                                                ?>
                                                <option value=" <?php echo $user['idUtilisateur']; ?>"  <?php if($_POST['AFFECTAIRE']==$user['idUtilisateur']) echo "selected";?>    >
                                                    <?php echo $user['prenomUtilisateur'].' '.$user['nomUtilisateur'].' ('.$user['matriculeUtilisateur'].')'; ?>
                                                </option>
                                            <?php } ?>
                                            </select>


                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NOMBRE AFFECTATION</td>
                                        <td>
                                            <input name="NOMBRE_SORTI" id="NOMBRE_SORTI" type="number" required class="form-control" <?php if($_POST['NOMBRE_SORTI']!='') echo "value=".$_POST['NOMBRE_SORTI'];?>
                                                  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>DATE D'ATTRIBUTION:</td>
                                        <td><input name="DATE_SORTI" type="text" disabled class="form-control"
                                                   value="<?php echo date("Y-m-d");?>"/></td>
                                    </tr>

                                    <tr>

                                        <td>
                                            <input type="hidden" name="QTE_STOCK" id="QTE_STOCK"
                                                   value="<?php echo $row_rq_equip->QTE_RESTANTE; ?>"/>
                                            <input type="hidden" name="ID_SORTI_EQUIPEMENT" value=""/>
                                            <input type="hidden" name="ID_EQUIPEMENT"
                                                   value="<?php echo $colname2_rq_equip ?>"/>
                                            <input type="hidden" name="IDETABLISSEMENT"
                                                   value="<?php echo $_SESSION['etab'] ?>"/>
                                            <input type="hidden" name="MM_insert" value="form1"/></td>
                                        <td><input type="button" value="Enregistrer" name="btn" onclick="comparerStock();"
                                                   class="btn btn-success"/></td>
                                    </tr>
                                </table>
                            </form>
                        </fieldset>
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
<script>
    function comparerStock() {
      var nbrsSorti=$("#NOMBRE_SORTI").val();
      var qtiteStock=$("#QTE_STOCK").val();
      if (parseInt(nbrsSorti)>parseInt(qtiteStock)){
          alert("il n'y a plus assez d'equipement en stock ");
          $("#form1").reset();
      }else{
          $("#form1").submit();
      }

    }
</script>
<?php include('footer.php'); ?>