<?php
session_start();
require_once("../config/Connexion.php");
require_once("../config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(21, $_SESSION['profil']));

$colname_rq_mensualite = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_mensualite = $lib->securite_xss($_SESSION['etab']);
}

$idAnnescolaire = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
    $idAnnescolaire = $lib->securite_xss($_SESSION['ANNEESSCOLAIRE']);
}

if (isset($_SESSION['nomEtablissement'])) {
    $etab = $lib->securite_xss($_SESSION['nomEtablissement']);
}

if(isset($_GET) && $_GET != '')
{
    $idtypeDoc = -1;
    if (isset($_GET['typeDoc']))
    {
        $idtypeDoc = $lib->securite_xss(base64_decode($_GET['typeDoc'])) ;
    }

    $idindividu = -1;
    if (isset($_GET['idindividu']))
    {
        $idindividu = $lib->securite_xss(base64_decode(($_GET['idindividu']))) ;
    }

    $idTypeindividu = -1;
    if (isset($_GET['idTypeindividu']))
    {
        $idTypeindividu = $lib->securite_xss(base64_decode(($_GET['idTypeindividu']))) ;
    }


    try
    {

        $query_typeDoc = $dbh->query("SELECT LIBELLE, CONTENU
        FROM TYPEDOCADMIN 
        WHERE IDTYPEDOCADMIN = ".$idtypeDoc."
        AND IDETABLISSEMENT=".$colname_rq_mensualite);

        $query_Doc = $query_typeDoc->fetchObject();
        $nom_doc = $query_Doc->LIBELLE;
        $contenu_doc = $query_Doc->CONTENU;

        if($idTypeindividu == 8){

            $query_individu = $dbh->query("SELECT I.NOM, I.PRENOMS, I.DATNAISSANCE , I.SEXE, I.LIEU_NAISSANCE, I.IDTYPEINDIVIDU, I.PERE, I.MERE, CL.LIBELLE
        FROM INDIVIDU I 
        INNER JOIN AFFECTATION_ELEVE_CLASSE A ON A.IDINDIVIDU = I.IDINDIVIDU
        INNER JOIN CLASSROOM CL ON CL.IDCLASSROOM = A.IDCLASSROOM
        WHERE I.IDINDIVIDU = ".$idindividu." 
        AND A.IDANNEESSCOLAIRE =".$idAnnescolaire." 
        AND A.IDETABLISSEMENT=".$colname_rq_mensualite);

            $query_rq_individu = $query_individu->fetchObject();
            $nom_individu = $query_rq_individu->NOM;
            $prenom_individu = $query_rq_individu->PRENOMS;
            $naissance_individu = $query_rq_individu->DATNAISSANCE;
            $sexe_individu = $query_rq_individu->SEXE;
            $classe = $query_rq_individu->LIBELLE;
            $lieuNaissance = $query_rq_individu->LIEU_NAISSANCE;
            $pere = $query_rq_individu->PERE;
            $mere = $query_rq_individu->MERE;
            $idTypeIndividu = $query_rq_individu->IDTYPEINDIVIDU;



        }
        else
        {

            $query_individu = $dbh->query("SELECT I.NOM, I.PRENOMS, I.DATNAISSANCE , I.SEXE, I.LIEU_NAISSANCE, I.IDTYPEINDIVIDU, I.DATE_ARRIVE_CEMAD, R.IDANNEESSCOLAIRE, R.IDETABLISSEMENT
            FROM INDIVIDU I 
            INNER JOIN RECRUTE_PROF R ON R.IDINDIVIDU = I.IDINDIVIDU
            WHERE I.IDINDIVIDU = ".$idindividu." 
            AND R.IDANNEESSCOLAIRE =".$idAnnescolaire." 
            AND R.IDETABLISSEMENT=".$colname_rq_mensualite);



            $query_rq_individu = $query_individu->fetchObject();

            $nomProf = $query_rq_individu->NOM;
            $prenomProf = $query_rq_individu->PRENOMS;
            $dateArrivee = $query_rq_individu->DATE_ARRIVE_CEMAD;
            $dateArrivee = date("d/m/Y");


        }

        $query_annee = $dbh->query("SELECT LIBELLE_ANNEESSOCLAIRE FROM ANNEESSCOLAIRE WHERE IDANNEESSCOLAIRE = ".$idAnnescolaire);
        $query_result_annee = $query_annee->fetchObject();
        $nom_annee = $query_result_annee->LIBELLE_ANNEESSOCLAIRE;


        $datetime = date("d/m/Y");
         //POUR ELEVE
        $contenu_doc = str_replace( '[IDENTITE_ELEVE]', $prenom_individu.' '.$nom_individu,$contenu_doc);
        $contenu_doc = str_replace( '[DATENAISSANCE]', $naissance_individu, $contenu_doc);
        $contenu_doc = str_replace( '[ETABLISSEMENT]', $etab , $contenu_doc);
        $contenu_doc = str_replace( '[DATE_AUJOURDHUI]', $datetime , $contenu_doc);
        $contenu_doc = str_replace( '[CLASSE]', $classe , $contenu_doc);
        $contenu_doc = str_replace( '[ANNEE_SCOLAIRE]', $nom_annee , $contenu_doc);
        $contenu_doc = str_replace( '[VILLE_NAISSANCE]', $lieuNaissance , $contenu_doc);
        $contenu_doc = str_replace( '[PERE_ELEVE]', $pere , $contenu_doc);
        $contenu_doc = str_replace( '[MERE_ELEVE]', $mere , $contenu_doc);
        if($sexe_individu ==0){
            $contenu_doc = str_replace( '[GENRE_ELEVE]', 'fille' , $contenu_doc);
            $contenu_doc = str_replace( '[PRONOM_ELEVE]', 'Elle' , $contenu_doc);

        }else{
            $contenu_doc = str_replace( '[GENRE_ELEVE]', 'fils' , $contenu_doc);
            $contenu_doc = str_replace( '[PRONOM_ELEVE]', 'Il' , $contenu_doc);

        }

        //POUR PROFESSEUR
        $contenu_doc = str_replace( '[PRENOM_PROFESSEUR]', $prenomProf.' '.$nomProf , $contenu_doc);
        $contenu_doc = str_replace( '[Date_ARRIVEE_PROF]', $dateArrivee , $contenu_doc);
        $contenu_doc = str_replace( '[VILLE]', $_SESSION['VILLE'] , $contenu_doc);





    }
    catch (PDOException $e)
    {
        echo $e;
    }
    ?>

    <style>
        .title {
            border: solid 1px #0a0a0a;
            text-align: center !important;
            padding: 7px 0px 7px 0px !important;
            margin: 3px 0px 0px 0px !important;
        }

        .sousTitle {
            border-top: solid 0.5px #0a0a0a !important;
            border-bottom: solid 0.5px #0a0a0a !important;
            border-right: solid 0.5px #0a0a0a !important;
            border-left: solid 0.5px #0a0a0a !important;
        }

        .sousTitleCenter {
            text-align: center !important;
        }
        td {
            padding: 2px;
        }

    </style>
    <page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm">
                <page_header>
                    <strong style="text-align: center !important;">ANNEE SCOLAIRE : <?php echo $nom_annee; ?></strong>
                </page_header>


        <table border="0"  align="center" width="100px;" cellspacing="0" cellpadding="0">
            <tbody>

            <tr>
                <td align="left" valign="top" class="Titre"><img src="../logo_etablissement/<?php echo $_SESSION['LOGO']; ?>" height="60" /></td>

                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td align="right" style="font-size: 10px !important; text-align: right !important">
                    <strong><?php echo $_SESSION['SIGLE']; ?></strong><br/>
                    <?php echo $_SESSION['nomEtablissement']; ?><br/>
                    <strong>BP :</strong> <?php echo $_SESSION['BP']; ?> - <strong>Tél: </strong><?php echo $_SESSION['TELEPHONEETAB']; ?><br/><br/><br/>
                </td>

            </tr>



            <tr>
                <td colspan="3" align="center" valign="middle" class="title"><strong><?php echo strtoupper($nom_doc) ;?></strong></td>
            </tr>



            <tr>
                <td colspan="3"><br/><br/><?php echo ($contenu_doc);?>  </td>

            </tr>


            </tbody>
        </table>

    </page>

<?php
}
?>


