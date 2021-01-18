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
    $lib->Restreindre($lib->Est_autoriser(20, $lib->securite_xss($_SESSION['profil'])));


$colname_rq_individu = "-1";
if (isset($_GET['IDCLASSROOM'])) {
    $colname_rq_individu = $lib->securite_xss(base64_decode($_GET['IDCLASSROOM']));

}

$colname_annee_rq_individu = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $colname_annee_rq_individu = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}


$query_rq_individu = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.TELMOBILE, INDIVIDU.IDINDIVIDU FROM AFFECTATION_ELEVE_CLASSE, INDIVIDU WHERE AFFECTATION_ELEVE_CLASSE.IDCLASSROOM=" . $colname_rq_individu . " AND AFFECTATION_ELEVE_CLASSE.IDINDIVIDU=INDIVIDU.IDINDIVIDU AND AFFECTATION_ELEVE_CLASSE.IDANNEESSCOLAIRE=" . $colname_annee_rq_individu);


// la classe
$colname_rq_classe = "-1";
if (isset($_GET['IDCLASSROOM'])) {
    $colname_rq_classe = $lib->securite_xss(base64_decode($_GET['IDCLASSROOM']));
}

$query_rq_classe = $dbh->query("SELECT * FROM CLASSROOM WHERE IDCLASSROOM = " . $colname_rq_classe);
$row_rq_classe = $query_rq_classe->fetchObject();

$colname_rq_controle = "-1";
if (isset($_GET['IDCONTROLE'])) {
    $colname_rq_controle = $lib->securite_xss(base64_decode($_GET['IDCONTROLE']));
}


$query_rq_controle = $dbh->query("SELECT * FROM CONTROLE WHERE IDCONTROLE = " . $colname_rq_controle);

$row_rq_controle = $query_rq_controle->fetchObject();


?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Evaluations</a></li>
    <li>Gestion des contrôles</li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

    <!-- START WIDGETS -->
    <div class="row">

        <div class="panel panel-default">
            <div class="panel-heading">
                &nbsp;&nbsp;&nbsp;

               <!-- <div class="btn-group pull-right">
                    <a href="valider.php?IDCONTROLE=<?php /*echo base64_encode($row_rq_controle->IDCONTROLE); */?>">
                        <button style="background-color:#DD682B" class='btn dropdown-toggle' aria-hidden='true'><i
                                class="glyphicon glyphicon-ok"></i>Valider notes
                        </button>
                    </a>


                </div>-->

            </div>
            <div class="panel-body">

                <?php

                if (isset($_GET['msg']) && $lib->securite_xss($_GET['msg']) != '') {

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

                <div><h4 style="color:#DD682B"> Notes du contrôle
                        <b><?php echo $row_rq_controle->LIBELLE_CONTROLE; ?></b> de la classe
                        <b><?php echo $row_rq_classe->LIBELLE; ?></b></h4>
                </div>

                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>

                        <th>MATRICULE</th>
                        <th>PRENOMS</th>
                        <th>NOM</th>
                        <th>TEL</th>
                        <th>NOTE</th>
                        <th>&nbsp;</th>


                    </tr>
                    </thead>


                    <tbody>

                    <?php
                    foreach ($query_rq_individu->fetchAll() as $row_rq_individu) {



                        $colname_rq_note_etudiant = "-1";
                        if (isset($_GET['IDCONTROLE'])) {
                            $colname_rq_note_etudiant = $lib->securite_xss(base64_decode($_GET['IDCONTROLE']));
                        }

                        $colname_individu_rq_note_etudiant = $row_rq_individu['IDINDIVIDU'];


                        $query_rq_note_etudiant = $dbh->query("SELECT NOTE FROM NOTE WHERE IDCONTROLE = " . $colname_rq_note_etudiant . " AND NOTE.IDINDIVIDU=" . $colname_individu_rq_note_etudiant);

                        $row_rq_note_etudiant = $query_rq_note_etudiant->fetchObject();


                        $note = $row_rq_note_etudiant->NOTE;
                        ?>

                        <tr>
                            <form method="POST" action="misJourNote.php" onsubmit="return confirmation();">
                                <td><?php echo $row_rq_individu['MATRICULE']; ?></td>
                                <td><?php echo $row_rq_individu['PRENOMS']; ?></td>
                                <td><?php echo $row_rq_individu['NOM']; ?></td>
                                <td><?php echo $row_rq_individu['TELMOBILE']; ?></td>
                                <td>
                                    <input type="number" name="note<?php echo base64_encode($row_rq_individu['IDINDIVIDU']); ?>"
                                           class="form-control" id="note" value="<?php echo $note == '' ? 0 : $note; ?>" min="0" required/>
                                </td>
                                <td>
                                    <input type="hidden" name="individu"
                                           value="<?php echo base64_encode($row_rq_individu['IDINDIVIDU']); ?>"/>
                                    <input type="hidden" name="IDCONTROLE" id="IDCONTROLE"
                                           value="<?PHP echo $_GET['IDCONTROLE']; ?>"/>
                                    <input type="hidden" name="IDCLASSROOM" id="IDCLASSROOM"
                                           value="<?PHP echo $_GET['IDCLASSROOM']; ?>"/>
                                    <input  type='submit' class="form-control" value="NOTER" name="NOTER" style="background: darkorange;" />
                                </td>
                            </form>
                        </tr>

                    <?php } ?>

                    </tbody>
                </table>
                <div class="btn-group col-lg-offset-5">
                    <a href="gestionControle.php" class="btn btn-warning" role="button">RETOUR</a>
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

<script type="text/javascript">
    function confirmation() {
        var str = 'Voulez vous valider la note?';
        return confirm(str);
    }
</script>
