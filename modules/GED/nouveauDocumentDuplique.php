
<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("../../restriction.php");


require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1)
    $lib->Restreindre($lib->Est_autoriser(18,$_SESSION['profil']));


$colname_rq_doc = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_doc = $_SESSION['etab'];
}

$query_rq_doc = $dbh->query("SELECT * FROM DOCADMIN WHERE IDETABLISSEMENT = ".$colname_rq_doc);

$colname_matricule="";
if(isset($_POST['MATRICULE'])&& $_POST['MATRICULE']!="")
{
    $colname_matricule=" AND INDIVIDU.MATRICULE like '%".$_POST['MATRICULE']."%' ";
}
$colname_prenom="";
if(isset($_POST['PRENOMS'])&& $_POST['PRENOMS']!="")
{
    $colname_prenom=" AND INDIVIDU.PRENOMS like '%".$_POST['PRENOMS']."%' ";
}
$colname_nom="";
if(isset($_POST['NOM'])&& $_POST['NOM']!="")
{
    $colname_nom=" AND INDIVIDU.NOM like '%".$_POST['NOM']."%' ";
}
$colname_tel="";
if(isset($_POST['TELMOBILE'])&& $_POST['TELMOBILE']!="")
{
    $colname_tel=" AND INDIVIDU.TELMOBILE like '%".$_POST['TELMOBILE']."%' ";
}



$colname_rq_etudiant = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_etudiant = $_SESSION['etab'];
}

$colname_type="";

if(isset($_POST['idProfil']) && isset($_POST['search']) && $_POST['idProfil']!="" && $_POST['search'] == "ok" ) {
    $colname_type = "  AND INDIVIDU.IDTYPEINDIVIDU=" . $lib->securite_xss($_POST['idProfil']. " ORDER BY PRENOMS, NOM ASC");
} else{
    $colname_type = " AND INDIVIDU.IDTYPEINDIVIDU = 8 ORDER BY PRENOMS, NOM ASC";
}

//$query_rq_etudiant = $dbh->query("SELECT INDIVIDU.IDINDIVIDU, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.TELMOBILE FROM INDIVIDU WHERE INDIVIDU.IDETABLISSEMENT = ".$colname_rq_etudiant." AND INDIVIDU.IDINDIVIDU  IN(SELECT   INDIVIDU.IDINDIVIDU FROM INDIVIDU,INSCRIPTION WHERE INDIVIDU.IDINDIVIDU = INSCRIPTION.IDINDIVIDU ) AND INDIVIDU.IDTYPEINDIVIDU=8 ".$colname_matricule.$colname_prenom.$colname_nom.$colname_tel);

$query_rq_etudiant = $dbh->query("SELECT `IDINDIVIDU`,`MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `TELDOM`, `COURRIEL`, `LOGIN`, `MP`, `CODE`, `BIOGRAPHIE`, `PHOTO_FACE`, INDIVIDU.IDETABLISSEMENT, INDIVIDU.IDTYPEINDIVIDU, `SEXE`, `IDTUTEUR`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`, `IDSECTEUR`, TYPEINDIVIDU.LIBELLE AS TYPE 
                                            FROM `INDIVIDU` INNER JOIN TYPEINDIVIDU ON INDIVIDU.IDTYPEINDIVIDU = TYPEINDIVIDU.IDTYPEINDIVIDU 
                                            WHERE  INDIVIDU.IDETABLISSEMENT = ".$colname_rq_etudiant." ".$colname_type);


$query_rq_profil = $dbh->query("SELECT IDTYPEINDIVIDU, LIBELLE, IDETABLISSEMENT FROM TYPEINDIVIDU WHERE IDTYPEINDIVIDU in (7, 8)");

?>


<?php include('header.php'); ?>
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

                <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){

                    if(isset($_GET['res']) && $_GET['res']==1)
                    {?>
                        <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $_GET['msg']; ?></div>
                    <?php  }
                    if(isset($_GET['res']) && $_GET['res']!=1)
                    {?>
                        <div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $_GET['msg']; ?></div>
                    <?php }
                    ?>

                <?php } ?>

                <form id="form1" name="form1" method="post" action="" >
                    <fieldset class="cadre"><legend> Filtre</legend>


                        <div class="row">



                            <div class="col-xs-6">
                                <div class="col-lg-2"> <label>Type d'individu</label></div>
                                <div class="col-lg-4">
                                    <select name="idProfil" id="idProfil" class="form-control selectpicker" data-live-search="true">
                                        <option value="">--Selectionner--</option>
                                        <?php foreach ($query_rq_profil->fetchAll() as $prof) { ?>

                                            <option value="<?php echo $prof['IDTYPEINDIVIDU']; ?>"  <?php if($prof['IDTYPEINDIVIDU']==$_POST['idProfil']) echo "selected"; ?>  ><?php echo $prof['LIBELLE'];  ?></option>

                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group col-lg-offset-1 col-lg-1">

                                <button type="submit" name="search" value="ok" class="btn btn-primary">Rechercher</button>
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
                        <th>CREER</th>

                    </tr>
                    </thead>


                    <tbody>
                    <?
                    foreach ($query_rq_etudiant->fetchAll() as $row_rq_etudiant){
                        /*$even=new Evenement();*/
                        ?>
                        <tr>

                            <td ><?php echo $row_rq_etudiant['MATRICULE']; ?></td>
                            <td ><?php echo $row_rq_etudiant['PRENOMS']; ?></td>
                            <td ><?php echo $row_rq_etudiant['NOM']; ?></td>
                            <td ><?php echo $row_rq_etudiant['TELMOBILE']; ?></td>
                            <td ><a href="newDocDuplique.php?IDINDIVIDU=<?php echo $lib->securite_xss(base64_encode($row_rq_etudiant['IDINDIVIDU']));?>&IDTYPEINDIVIDU=<?php echo $lib->securite_xss(base64_encode($row_rq_etudiant['IDTYPEINDIVIDU']));?>"><i class="glyphicon glyphicon-plus"></i></a></td>


                        </tr>
                    <?php }  ?>

                    </tbody>
                </table>


            </div></div></div>
    <!-- END WIDGETS -->



</div>
<!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->


<?php include('footer.php'); ?>