<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();


if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(3, $lib->securite_xss($_SESSION['profil'])));

$colname_rq_etablissement = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_etablissement = $lib->securite_xss($_SESSION['etab']);
}

$query_rq_etablissement = $dbh->query("SELECT `IDETABLISSEMENT`, `NOMETABLISSEMENT_`, `SIGLE`, `ADRESSE`, `TELEPHONE`, `REGLEMENTINTERIEUR`, `VILLE`, `PAYS`, `DEVISE`, `FAX`, `MAIL`, `SITEWEB`, `LOGO`, `CAPITAL`, `FORM_JURIDIQUE`, `RC`, `NINEA`, `NUM_TV`, `PREFIXE` FROM `ETABLISSEMENT` WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);
$row_rq_etablissement = $query_rq_etablissement->fetchObject();
$totalRows_rq_etablissement = $query_rq_etablissement->rowCount();

?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Param&eacute;trage</a></li>
    <li>&Eacute;tablissement</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">

        <form method="post" name="form1" action="updateEtablissement.php" enctype="multipart/form-data">
            <fieldset class="cadre">
                <legend class="libelle_champ">Informations g&eacute;n&eacute;rales</legend>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">&Eacute;tablissement</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="NOMETABLISSEMENT_" name="NOMETABLISSEMENT_"
                                       value="<?php echo  $row_rq_etablissement->NOMETABLISSEMENT_; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Adresse</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="ADRESSE" name="ADRESSE"
                                       value="<?php echo $row_rq_etablissement->ADRESSE; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">T&eacute;l&eacute;phone</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="TELEPHONE" name="TELEPHONE"
                                       value="<?php echo $row_rq_etablissement->TELEPHONE; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Ville</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="VILLE" name="VILLE"
                                       value="<?php echo $row_rq_etablissement->VILLE; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Email</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="MAIL" name="MAIL"
                                       value="<?php echo $row_rq_etablissement->MAIL; ?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Sigle</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="SIGLE" name="SIGLE"
                                       value="<?php echo $row_rq_etablissement->SIGLE; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Fax</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="FAX" name="FAX"
                                       value="<?php echo $row_rq_etablissement->FAX; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Pays</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="PAYS" name="PAYS"
                                       value="<?php echo $row_rq_etablissement->PAYS; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Site web</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="SITEWEB" name="SITEWEB"
                                       value="<?php echo $row_rq_etablissement->SITEWEB; ?>">
                            </div>
                        </div>


                    </div>
                </div>

            </fieldset>
            <fieldset class="cadre">
                <legend class="libelle_champ">Identifiants r&eacute;glementaires</legend>
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Devise</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="DEVISE" name="DEVISE"
                                       value="<?php echo $row_rq_etablissement->DEVISE; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Capital Social</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="CAPITAL" name="CAPITAL"
                                       value="<?php echo $row_rq_etablissement->CAPITAL; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Statut juridique</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="FORM_JURIDIQUE" name="FORM_JURIDIQUE"
                                       value="<?php echo $row_rq_etablissement->FORM_JURIDIQUE; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">RC</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="RC" name="RC"
                                       value="<?php echo $row_rq_etablissement->RC; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">NINEA</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="NINEA" name="NINEA"
                                       value="<?php echo $row_rq_etablissement->NINEA; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">TVA</label>

                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="NUM_TV" name="NUM_TV"
                                       value="<?php echo $row_rq_etablissement->NUM_TV; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 form-control-label">Préfixe</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="PREFIXE" name="PREFIXE"
                                       value="<?php echo $row_rq_etablissement->PREFIXE; ?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="form-group row">

                            <div class="col-sm-12">
                                <img src="../../logo_etablissement/<?php echo $row_rq_etablissement->LOGO; ?>" alt=""
                                     name="" width="70%"/>
                                <input type="file" name="LOGO" id="LOGO" class="form-control">
                            </div>
                        </div>


                    </div>
                </div>
            </fieldset>


            <div class="row">
                <div>
                    <input type="hidden" name="IDETABLISSEMENT"
                           value="<?php echo $row_rq_etablissement->IDETABLISSEMENT; ?>">

                </div>
                <div class="col-lg-3 col-lg-offset-4">
                    <button type="submit" class="form-control" value="modifier">Modifier</button>
                </div>
            </div>


        </form>


        <!-- END WIDGETS -->

    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->


<?php include('footer.php'); ?>