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
    $lib->Restreindre($lib->Est_autoriser(23, $_SESSION['profil']));


$colname_rq_dispensercours = "-1";
if (isset($_GET['IDDISPENSER_COURS'])) {
    $colname_rq_dispensercours = $lib->securite_xss($_GET['IDDISPENSER_COURS']);
}

try
{
    $query_rq_dispensercours = $dbh->query("SELECT DISPENSER_COURS.IDDISPENSER_COURS, DISPENSER_COURS.IDCLASSROOM, DISPENSER_COURS.IDINDIVIDU, DISPENSER_COURS.DATE, DISPENSER_COURS.HEUREDEBUTCOURS, DISPENSER_COURS.HEUREFINCOURS, DISPENSER_COURS.TITRE_COURS, DISPENSER_COURS.CONTENUCOURS, DISPENSER_COURS.IDSALL_DE_CLASSE, MATIERE.LIBELLE as matiere, CLASSROOM.LIBELLE as classroom, SALL_DE_CLASSE.NOM_SALLE, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.TELMOBILE
                                                  FROM DISPENSER_COURS, MATIERE, CLASSROOM, SALL_DE_CLASSE, INDIVIDU
                                                  WHERE IDDISPENSER_COURS = " . $colname_rq_dispensercours . "
                                                  AND DISPENSER_COURS.ANNEESCOLAIRE = ".$lib->securite_xss($_SESSION['ANNEESSCOLAIRE']) . "
                                                  AND DISPENSER_COURS.IDMATIERE = MATIERE.IDMATIERE
                                                  AND DISPENSER_COURS.IDCLASSROOM = CLASSROOM.IDCLASSROOM
                                                  AND DISPENSER_COURS.IDSALL_DE_CLASSE = SALL_DE_CLASSE.IDSALL_DE_CLASSE
                                                  AND DISPENSER_COURS.IDINDIVIDU = INDIVIDU.IDINDIVIDU");
    $row_rq_dispensercours = $query_rq_dispensercours->fetchObject();
    $colname_rq_individu = $row_rq_dispensercours->IDCLASSROOM;


    $query_rq_individu = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.TELMOBILE, INDIVIDU.IDINDIVIDU, INDIVIDU.PHOTO_FACE
                                              FROM AFFECTATION_ELEVE_CLASSE, INDIVIDU
                                              WHERE IDCLASSROOM = " . $colname_rq_individu . " 
                                              AND AFFECTATION_ELEVE_CLASSE.IDINDIVIDU = INDIVIDU.IDINDIVIDU
                                              GROUP BY INDIVIDU.IDINDIVIDU");
    $rs_ind = $query_rq_individu->fetchAll();
}
catch (PDOException $e)
{
    echo -2;
}

$_SESSION['eleve_presente'] = array();
$cocher = '   ';


?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Evaluations</a></li>
    <li>Mise &aacute; jour des cours</li>
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
                            <?php echo $lib->securite_xss($_GET['msg']); ?>
                        </div>

                    <?php } if (isset($_GET['res']) && $_GET['res'] != 1) { ?>

                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?>
                        </div>

                    <?php }  ?>

                <?php } ?>


                <fieldset class="cadre">
                    <legend> Fiche de pr&eacute;sence : <?php echo $row_rq_dispensercours->TITRE_COURS; ?></legend>

                    <table width="100%" cellpadding="5" cellspacing="5">
                        <tr>
                            <th>Professeur : </th>
                            <td><?php echo $row_rq_dispensercours->PRENOMS; ?> <?php echo $row_rq_dispensercours->NOM; ?></td>
                        </tr>

                        <tr>
                            <th>Cours : </th>
                            <td><?php echo $row_rq_dispensercours->TITRE_COURS; ?></td>
                        </tr>


                        <tr>
                            <th>Date : </th>
                            <td><?php echo $lib->date_fr($row_rq_dispensercours->DATE); ?></td>
                        </tr>
                        <tr>
                            <th>Heure : </th>
                            <td><b>de</b> <?php echo $row_rq_dispensercours->HEUREDEBUTCOURS; ?>
                                <b>à</b> <?php echo $row_rq_dispensercours->HEUREFINCOURS; ?></td>
                        </tr>


                        <tr>
                            <th>Mati&egrave;re : </th>
                            <td><?php echo $row_rq_dispensercours->matiere; ?></td>
                        </tr>

                        <tr>
                            <th>Classe : </th>
                            <td><?php echo $row_rq_dispensercours->classroom; ?></td>
                        </tr>

                        <tr>
                            <th>Salle : </th>
                            <td><?php echo $row_rq_dispensercours->NOM_SALLE; ?></td>
                        </tr>

                        <tr>
                            <th>Contenu du cours : </th>
                            <td><?php echo html_entity_decode($row_rq_dispensercours->CONTENUCOURS); ?></td>
                        </tr>
                    </table>
                </fieldset>
                <br><br>

                <form id="form1" name="form1" method="post" action="presences.php">

                    <table class="table table-responsive table-striped">
                        <tr>
                            <th>##</th>
                            <th>Matricule</th>
                            <th>Pr&eacute;noms</th>
                            <th>Nom</th>
                            <th>T&eacute;l</th>
                            <th>Pr&eacute;sent(e)
                                <span style="position: relative;left: 35px;">
                                    Tout cocher<input onchange="var i=0;while(i<$('.presences').length){$('.presences')[i].checked = $(this)[0].checked;i++;}" type="checkbox" name="presences-all" style=" position: relative; left: 12px;">
                                </span>
                            </th>
                        </tr>
                        <?php
                        foreach ($rs_ind as $row_rq_individu)
                        {
                            array_push($_SESSION['eleve_presente'], $row_rq_individu['IDINDIVIDU']);
                            $colname_rq_si_eleve_presente = $row_rq_dispensercours->IDDISPENSER_COURS;

                            try
                            {
                                $query_rq_si_eleve_presente = $dbh->query("SELECT IDPRESENCE_ELEVES, ETAT_PRESENCE, IDETABLISSEMENT, IDDISPENSER_COURS, IDINDIVIDU 
                                                                                 FROM PRESENCE_ELEVES 
                                                                                 WHERE IDDISPENSER_COURS = ".$colname_rq_si_eleve_presente."  
                                                                                 AND IDINDIVIDU = " . $row_rq_individu['IDINDIVIDU']);
                                $row_rq_si_eleve_presente = $query_rq_si_eleve_presente->fetchAll();
                                $totalRows_rq_si_eleve_presente = $query_rq_si_eleve_presente->rowCount();
                            }
                            catch (PDOException $e){
                                echo -2;
                            }

                            if ($totalRows_rq_si_eleve_presente > 0) {
                                $cocher = ' checked="checked" ';
                            } else {
                                $cocher = ' ';
                            }
                            ?>
                            <tr>
                                <td valign="middle"><img name="" src="../../imgtiers/<?php echo $row_rq_individu['PHOTO_FACE']; ?>" width="20px" height="20px" alt=""/></td>
                                <td><?php echo $row_rq_individu['MATRICULE']; ?></td>
                                <td><?php echo $row_rq_individu['PRENOMS']; ?></td>
                                <td><?php echo $row_rq_individu['NOM']; ?></td>
                                <td><?php echo $row_rq_individu['TELMOBILE']; ?></td>
                                <td>
                                    <input type="checkbox" class="presences" name="presences<?php echo $row_rq_individu['IDINDIVIDU']; ?>" id="presences<?php echo $row_rq_individu['IDINDIVIDU']; ?>" <?php echo $cocher; ?> />
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><input name="IDDISPENSER_COURS" type="hidden" id="IDDISPENSER_COURS" value="<?php echo $row_rq_dispensercours->IDDISPENSER_COURS; ?>"/></td>
                            <td><input type="submit" name="valider" id="valider" class="btn btn-success" value="Valider feuille de présence"/></td>
                        </tr>


                    </table>
                </form>


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