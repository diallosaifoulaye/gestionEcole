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
    $lib->Restreindre($lib->Est_autoriser(18, $lib->securite_xss($_SESSION['profil'])));

include('header.php');

if ((isset($_POST["Envoyer"])) && ($_POST["Envoyer"] == "Valider")) {

    require_once("classe/TypeDocManager.php");
    require_once("classe/TypeDoc.php");
    $type = new TypeDocManager($dbh, 'DOCADMIN');
    unset($_POST["Envoyer"]);
        $dossier = '../../document/';
        $fichier = basename($_FILES['FICHIER']['name']);
        $res = -1;
        $taille_maxi = 1000000;
        $taille = filesize($_FILES['FICHIER']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg','.pdf','.txt');
        $extension = strrchr($_FILES['FICHIER']['name'], '.');
        //Début des vérifications de sécurité...
        if (!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
            $msg = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg';
        }
        if ($taille > $taille_maxi) {
            $msg = 'Le fichier est trop gros';
        }
        if (!isset($msg)) //S'il n'y a pas d'erreur, on upload
        {
            $upload = move_uploaded_file($_FILES['FICHIER']['tmp_name'], $dossier . $fichier);

            if ($upload === true) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
            {
                $info = new \SplFileInfo($fichier);
                $new_name =$lib->securite_xss($_POST['IDINDIVIDU']). '-' .date("YmdHis") . '.' . $info->getExtension();
                rename($dossier . $fichier, $dossier . $new_name);
                $fichier = $new_name;
                $_POST['NOM'] = $fichier;
                //var_dump($_POST); die;
                $res = $type->insert($lib->securite_xss_array($_POST));

                if ($res == 1) {
                    $msg = 'Insertion effectuée avec succés';
                } else {
                    $msg = 'Insertion effectuée avec echec';
                }
            } else //Sinon (la fonction renvoie FALSE).
            {
                $msg = 'Echec de l\'upload !';
            }

        }
        else{
            $msg = 'Echec Insertion';
        }
         header("Location: listeDocuments.php?msg=" . $lib->securite_xss($msg) . "&res=" . $lib->securite_xss($res));
}

$colname_rq_doc = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_doc = $lib->securite_xss($_SESSION['etab']);
}
$query_rq_doc = $dbh->query("SELECT COUNT(DOCADMIN.IDDOCADMIN) as nb , TYPEDOCADMIN.LIBELLE, INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, DOCADMIN.IDINDIVIDU, DOCADMIN.IDDOCADMIN
FROM DOCADMIN, TYPEDOCADMIN, INDIVIDU 
WHERE DOCADMIN.IDETABLISSEMENT = " . $colname_rq_doc . " 
AND DOCADMIN.IDTYPEDOCADMIN=TYPEDOCADMIN.IDTYPEDOCADMIN 
AND INDIVIDU.IDINDIVIDU=DOCADMIN.IDINDIVIDU 
GROUP BY INDIVIDU.IDINDIVIDU, TYPEDOCADMIN.IDTYPEDOCADMIN");
?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">GED</a></li>
    <li>Liste des documents</li>
</ul>
<!-- END BREADCRUMB -->
<style>
    .mybutton{
        background-color:#DD682B;
        color: #332f34;
    }
    .mybutton:hover{
        color: #F6F6F6;
        text-decoration: none;
    }
</style>
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">
        <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="btn-group pull-right">
                        <tr>
                            <td>
                                <a href="nouveauDocument.php">
                                    <button class='btn dropdown-toggle'>
                                        <i class="fa fa-plus"></i> Joindre document
                                    </button>
                                </a>
                            </td>
                            <td>
                                <a href="nouveauDocumentDuplique.php">
                                    <button class='btn dropdown-toggle'>
                                        <i class="fa fa-plus"></i> Générer document
                                    </button>
                                </a>
                            </td>
                        </tr>
                    </div>
                </div>

            <div class="panel-body">
                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {
                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert"
                                                            aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>

                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?>

                        </div>
                    <?php }
                    ?>
                <?php } ?>
                <table id="customers2" class="table datatable">
                    <thead>
                    <tr>
                        <th>MATRICULE</th>
                        <th>NOM</th>
                        <th>PRENOMS</th>
                        <th>TYPE DOCUMENT</th>
                        <th>NOMBRE DE DOCUMENT</th>
                        <th>DETAIL</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach ($query_rq_doc->fetchAll() as $document) {
                        /*$even=new Evenement();*/
                        ?>
                        <tr>
                            <td><?php echo $document['MATRICULE']; ?></td>
                            <td><?php echo $document['PRENOMS']; ?></td>
                            <td><?php echo $document['NOM']; ?></td>
                            <td><?php echo $document['LIBELLE']; ?></td>
                            <td><?php echo $document['nb']; ?></td>
                            <td ><a href="detailDocument.php?IDINDIVIDU=<?php echo base64_encode($document['IDINDIVIDU']);  ?>"><i class="glyphicon glyphicon-search"></i></a></td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a id="pdf-file" href="" class="hidden" target="_blank">    </a>
    <!-- END WIDGETS -->
</div>
<!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->
<?php include('footer.php');
if (isset($_POST["IDTYPEDOCADMIN"])) {
    echo "
        <script>
            ($('#pdf-file'))[0].href = 'http://www.numherit-labs.com/sunuecole/ged/type_doc_pdf.php?IDTYPEDOCADMIN=".$_POST['IDTYPEDOCADMIN']."&IDETABLISSEMENT=".$_POST['IDETABLISSEMENT']."&id_eleve=".$_POST['IDINDIVIDU']."';
             ($('#pdf-file'))[0].click();
            //window.open('http://www.numherit-labs.com/sunuecole/ged/type_doc_pdf.php?IDTYPEDOCADMIN=&IDETABLISSEMENT=&id_eleve=&amp;do_pdf=1&amp;id=12','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
        </script>
    ";
}
?>

