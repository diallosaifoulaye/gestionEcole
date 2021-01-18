<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(17, $_SESSION['profil']));

require_once("classe/TypeDocManager.php");
require_once("classe/TypeDoc.php");
$type = new TypeDocManager($dbh, 'TYPEDOCADMIN');


$colname_rq_annee_etab = "-1";
if (isset($_GET['IDTYPEDOC'])) {
    $colname_rq_annee_etab = $lib->securite_xss(base64_decode($_GET['IDTYPEDOC']));
}
//var_dump($colname_rq_annee_etab);die;
//<!-- IDANNEESSCOLAIRE LIBELLE_ANNEESSOCLAIRE DATE_DEBUT DATE_FIN IDETABLISSEMENT -->

$query_rq_annee_etab = $dbh->query("SELECT * FROM TYPEDOCADMIN WHERE IDTYPEDOCADMIN =". $colname_rq_annee_etab);
$query_rq_profil = $dbh->query("SELECT IDTYPEINDIVIDU, LIBELLE, IDETABLISSEMENT FROM TYPEINDIVIDU WHERE IDTYPEINDIVIDU in (7, 8)");

foreach ($query_rq_annee_etab->fetchAll() as $row_rq_annee_etab) {

    $id = $row_rq_annee_etab['IDTYPEDOCADMIN'];
    $libelle = $row_rq_annee_etab['LIBELLE'];
    $contenu = $row_rq_annee_etab['CONTENU'];
    $typeIndividu = $row_rq_annee_etab['IDTYPEINDIVIDU'];

}


if (isset($_POST) && $_POST != null) {

    $res = $type->modifier($_POST, 'IDTYPEDOCADMIN', $_POST['IDTYPEDOCADMIN']);
    if ($res == 1) {
        $msg = "Modification effectuée avec succès";

    } else {
        $msg = "Votre modification a échoué";
    }
    echo "<meta http-equiv='refresh' content='0;URL=accueil.php?msg=$msg&res=$res'>";
}
?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">GED</a></li>
    <li>Type document</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">


        <div class="panel panel-default">

            <div class="panel-heading">

            </div>

            <div class="panel-body">

                <form action="modifTypeDoc.php" method="POST">

                    <div class="row">

                        <!-- IDTYPEDOCADMIN LIBELLE CONTENU IDETABLISSEMENT IDMODELE_DOC -->

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>Type individu : </label>
                                <div>
                                    <select name="IDTYPEINDIVIDU" id="IDTYPEINDIVIDU" class="form-control"  required>
                                        <option value="non-sel">--Selectionner--</option>

                                        <?php foreach ($query_rq_profil->fetchAll() as $prof) { ?>

                                            <option value="<?php echo $prof['IDTYPEINDIVIDU']; ?>"  <?php if($prof['IDTYPEINDIVIDU']==$typeIndividu) echo "selected"; ?>  ><?php echo $prof['LIBELLE'];  ?></option>

                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>LIBELLE</label>

                                <div>
                                    <input type="text" name="LIBELLE" id="LIBELLE" required class="form-control"
                                           value="<?php echo $libelle; ?>"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>CONTENU</label>

                                <div>
                            <textarea name="CONTENU" id="mytextarea"
                                      class="form-control"> <?php echo $contenu; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <fieldset class="cadre">
                                <legend> LEGEND</legend>
                                <table width="100%" height="244" border="0" cellpadding="0" cellspacing="1" bgcolor="#F3F3F3">
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
                                        <td class="textNormalJustifier">[VILLE_NAISSANCE]</td>
                                        <td>Ville de naissance</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[LIEU_NAISSANCE]</td>
                                        <td>Lieu de naissance</td>
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
                                    <tr>
                                        <td class="textNormalJustifier">[ANNEE_SCOLAIRE]</td>
                                        <td class="textNormalJustifier">Année scolaire</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[GENRE_ELEVE]</td>
                                        <td class="textNormalJustifier">Sexe</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[PERE_ELEVE]</td>
                                        <td class="textNormalJustifier">Père de l'élève</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[MERE_ELEVE]</td>
                                        <td class="textNormalJustifier">Mère de l'élève</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[PRONOM_ELEVE]</td>
                                        <td class="textNormalJustifier">Pronom personnel de l'élève</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[PRENOM_PROFESSEUR]</td>
                                        <td class="textNormalJustifier">Nom du professeur</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[Date_ARRIVEE_PROF]</td>
                                        <td class="textNormalJustifier">Date d'arrivée du prof</td>
                                    </tr>
                                    <tr>
                                        <td class="textNormalJustifier">[VILLE]</td>
                                        <td class="textNormalJustifier">Ville</td>
                                    </tr>
                                </table>
                            </fieldset>
                            <br><br>


                            <div class="col-lg-12">

                                <div class="col-lg-offset-10"><input type="submit" class="btn btn-success" value="modifier"/>
                                </div>

                            </div>

                        </div>

                        <input type="hidden" name="IDTYPEDOCADMIN" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $_SESSION['etab']; ?>"/>


                </form>

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