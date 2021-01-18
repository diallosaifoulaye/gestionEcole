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

require_once("classe/EtablissementManager.php");
require_once("classe/Etablissement.php");
$etab = new EtablissementManager($dbh, 'ETABLISSEMENT');

if (isset($_POST) && $_POST != null) {
    if ($_FILES['LOGO']['error'] != 4) {
        $dossier = '../../logo_etablissement/';
        $fichier = basename($_FILES['LOGO']['name']);
        $fichier = $colname_rq_etablissement.$fichier;
     // echo "<pre>"; var_dump($fichier); exit;
        $res = -1;
        $taille_maxi = 1000000;
        $taille = filesize($_FILES['LOGO']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['LOGO']['name'], '.');
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

            if (move_uploaded_file($_FILES['LOGO']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
            {

               $_POST['LOGO'] = $fichier;
                //var_dump($_POST); die;
                $res = $etab->modifier($lib->securite_xss_array($_POST), 'IDETABLISSEMENT', $lib->securite_xss($_POST['IDETABLISSEMENT']));
                if ($res == 1) {
                    $msg = 'Modification effectuée avec succés';
                } else {
                    $msg = 'Modification effectuée avec echec';
                }


            } else //Sinon (la fonction renvoie FALSE).
            {
                $msg = 'Echec de l\'upload !';

            }
        }

    } else {
        //var_dump($_POST); die;
        $res = $etab->modifier($lib->securite_xss_array($_POST), 'IDETABLISSEMENT', $lib->securite_xss($_POST['IDETABLISSEMENT']));
        if ($res == 1) {
            $msg = 'Modification effectuée avec succés';
        } else {
            $msg = 'Modification effectuée avec echec';
        }
    }


    header("Location: accueil.php?msg=" . $msg . "&res=" . $res);
}

?>