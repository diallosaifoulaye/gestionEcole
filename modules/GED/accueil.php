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

$colname_rq_typ_doc = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_typ_doc = $_SESSION['etab'];
}

$query_rq_typ_doc = $dbh->query("SELECT D.IDTYPEDOCADMIN, D.LIBELLE, I.LIBELLE as lib
                                FROM TYPEDOCADMIN D
                                INNER JOIN TYPEINDIVIDU I ON I.IDTYPEINDIVIDU = D.IDTYPEINDIVIDU
                                WHERE D.IDETABLISSEMENT = " . $colname_rq_typ_doc . " ORDER BY LIBELLE ASC");

$query_rq_profil = $dbh->query("SELECT IDTYPEINDIVIDU, LIBELLE, IDETABLISSEMENT FROM TYPEINDIVIDU WHERE IDTYPEINDIVIDU in (7, 8)");


?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">GED</a></li>
    <li>Type de documents</li>
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
                            style="background-color:#DD682B" class='btn dropdown-toggle' aria-hidden='true'>
                        <i class="fa fa-plus"></i> Nouveau type de document
                    </button>
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


                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>

                        <th width="80%">TYPE DE DOCUMENT</th>
                        <th width="80%">TYPE DE D'INDIVIDU</th>

                        <th>MODIFIER</th>
                        <th>SUPPRIMER</th>

                    </tr>
                    </thead>


                    <tbody>
                    <?
                    foreach ($query_rq_typ_doc->fetchAll() as $document) {
                        /*$even=new Evenement();*/
                        ?>
                        <tr>

                            <td><?php echo $document['LIBELLE']; ?></td>
                            <td><?php echo $document['lib']; ?></td>


                            <td><a href="modifTypeDoc.php?IDTYPEDOC=<?php echo $lib->securite_xss(base64_encode($document['IDTYPEDOCADMIN'])); ?>"><i
                                        class=" glyphicon glyphicon-edit"></i></a></td>

                            <td><a href="suppTypeDoc.php?IDTYPEDOC=<?php echo $lib->securite_xss(base64_encode($document['IDTYPEDOCADMIN'])); ?>"
                                   onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette enregistrement'));"><i
                                        class="glyphicon glyphicon-remove"></i></a></td>

                        </tr>
                    <?php }  ?>

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
                    <h3 class="panel-title text-center"> Nouveau type de document</h3>
                </div>
                <form action="ajouterTypeDoc.php" method="POST" id="form" enctype="multipart/form-data">

                    <div class="panel-body">
                        <div class="row">

                            <!-- IDTYPEDOCADMIN LIBELLE CONTENU IDETABLISSEMENT IDMODELE_DOC -->

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>Type individu : </label>
                                    <div>
                                        <select name="IDTYPEINDIVIDU" id="IDTYPEINDIVIDU" class="form-control"  required>
                                            <option value="">--Selectionner--</option>

                                            <?php foreach ($query_rq_profil->fetchAll() as $prof) { ?>

                                                <option value="<?php echo $prof['IDTYPEINDIVIDU']; ?>"  <?php if($prof['IDTYPEINDIVIDU']==$_POST['idProfil']) echo "selected"; ?>  ><?php echo $prof['LIBELLE'];  ?></option>

                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>Type de document</label>

                                    <div>
                                        <input type="text" name="LIBELLE" id="LIBELLE" required class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CONTENU</label>

                                    <div>
                                        <textarea name="CONTENU" id="mytextarea" class="form-control"> </textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <fieldset>
                                    <legend> LEGEND</legend>
                                    <table width="100%" height="244" border="0" cellpadding="0" cellspacing="1"
                                           bgcolor="#F3F3F3">
                                        <tr>
                                            <th width="46%" class="titretablo">A INSERER</th>
                                            <th width="54%" class="titretablo">DESCRIPTION</th>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[IDENTITE_DIRECTEUR]</td>
                                            <td>Prénoms et nom du directeur</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[ETABLISSEMENT]</td>
                                            <td>Nom de l'établissement</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[VILLE]</td>
                                            <td>Ville</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[IDENTITE_ELEVE]</td>
                                            <td>Prénoms et nom de l'élève</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[DATENAISSANCE]</td>
                                            <td>Date de naissance</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[DATE_INSCRIPTION]</td>
                                            <td>Date l'inscription de l'élève ou étudiant</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[CLASSE]</td>
                                            <td>Classe de l'individu</td>
                                        </tr>
                                        <tr>
                                            <td class="textNormalJustifier">[DATE_AUJOURDHUI]</td>
                                            <td class="textNormalJustifier">Date du jour</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>


                        </div>


                    </div>
                    <div class="panel-footer">
                        <button type="reset" class="btn btn-danger">Réinitialiser</button>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $_SESSION['etab']; ?>"/>
                        <input type="hidden" name="IDMODELE_DOC" value=""/>
                        <button type="submit" class="btn btn-primary pull-right">Valider</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php include('footer.php'); ?>