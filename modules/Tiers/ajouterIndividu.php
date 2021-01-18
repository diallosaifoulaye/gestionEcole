<?php
require_once("../../config/Connexion.php");
require_once("../../config/Librairie.php");

require_once("classe/IndividuManager.php");
require_once("classe/Individu.php");

if (!isset($_SESSION)){
    session_start();
}

$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();



if ($_SESSION['profil'] != 1)
    $lib->Restreindre($lib->Est_autoriser(35, $lib->securite_xss($_SESSION['profil'])));

$individu = new IndividuManager($dbh, 'INDIVIDU');



if (isset($_POST) &&  $_POST!='' && $_POST['IDTYPEINDIVIDU'] == "8") {

        if($_POST['MATRICULE']!= ''&& $_POST['NOM']!= ''&& $_POST['PRENOMS']!= '' && $_POST['TELMOBILE']!=''){
           // var_dump(1);exit;
    $dossier = "../../imgtiers/";
    $fichier = basename($_FILES['PHOTO_FACE']['name']);
    $fichier1 = basename($_FILES['FICHE_RECTO']['name']);
    $fichier2 = basename($_FILES['FICHE_VERSO']['name']);



    if (!empty($fichier)) {

        $res = -1;
        $taille_maxi = 1000000;
        $taille = filesize($_FILES['PHOTO_FACE']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['PHOTO_FACE']['name'], '.');
        if (!empty($fichier1) && !empty($fichier2)) {
                if (move_uploaded_file($_FILES['FICHE_RECTO']['tmp_name'], $dossier . $fichier1) && move_uploaded_file($_FILES['FICHE_VERSO']['tmp_name'], $dossier . $fichier2)) {
                    $_POST['FICHE_RECTO'] = $_FILES['FICHE_RECTO']['name'];
                    $_POST['FICHE_VERSO'] = $_FILES['FICHE_VERSO']['name'];
                   // var_dump($_POST['FICHE_RECTO']." ".$_POST['FICHE_VERSO']);exit;
                }

        }

        if (!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
            $msg = "Vous devez uploader un fichier de type png, gif, jpg, jpeg";
        }
        if ($taille > $taille_maxi) {
            $msg = "Le fichier est trop gros";
        }
        if (!isset($msg)) //S'il n'y a pas d'erreur, on upload
        {
            if (move_uploaded_file($_FILES['PHOTO_FACE']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
            {
                if ($_POST['type'] == "non") { // le parent n'existe pas encore dans la base de donnees
                    $_POST['LOGIN'] = $lib->securite_xss($_POST['COURRIEL']);
                    $_POST['PHOTO_FACE'] = $_FILES['PHOTO_FACE']['name'];
                    $PHOTO_FACETUTEUR = "imgDefaultIndividu.jpg";
                    $_POST['LOGINTUTEUR'] = $lib->securite_xss($_POST['COURRIELTUTEUR']);
                    $passTuteur = $lib->securite_xss($_POST['MPTUTEUR']);
                    $_POST['MPTUTEUR'] = md5($lib->securite_xss($_POST['MPTUTEUR']));

                    $stmt1 = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, IDSECTEUR, LIEN_PARENTE, LIEU_TRAVAIL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $res1 = $stmt1->execute(array($lib->securite_xss($_POST['MATRICULETUTEUR']), $lib->securite_xss($_POST['NOMTUTEUR']), $lib->securite_xss($_POST['PRENOMSTUTEUR']), $lib->securite_xss($_POST['TELMOBILETUTEUR']), $lib->securite_xss($_POST['TELDOMTUTEUR']), $lib->securite_xss($_POST['COURRIELTUTEUR']), $lib->securite_xss($_POST['LOGINTUTEUR']), $lib->securite_xss($_POST['MPTUTEUR']), $lib->securite_xss($_POST['CODETUTEUR']), $PHOTO_FACETUTEUR, $lib->securite_xss($_POST['IDETABLISSEMENT']), 9, $lib->securite_xss($_POST['IDSECTEUR']), $lib->securite_xss($_POST['LIEN_PARENTE']), $lib->securite_xss($_POST['LIEU_TRAVAIL'])));
                    if ($res1 == 1) {
                        $lib->envoiLoginParent($lib->securite_xss($_POST['COURRIELTUTEUR']), $lib->securite_xss($_POST['PRENOMSTUTEUR']) . " " . $lib->securite_xss($_POST['NOMTUTEUR']), $lib->securite_xss($_POST['LOGINTUTEUR']), $passTuteur);
                    }

                    $id_tuteur = $dbh->lastInsertId();
                    $pass = $lib->securite_xss($_POST['MP']);
                    $_POST['MP'] = md5($lib->securite_xss($_POST['MP']));
                    $stmt = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE, IDTUTEUR, ANNEEBAC, NATIONNALITE, SIT_MATRIMONIAL, NUMID, PATHOLOGIE, ALLERGIE, MEDECIN_TRAITANT, FICHE_RECTO, FICHE_VERSO, LIEU_NAISSANCE, PERE, MERE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $res = $stmt->execute(array($lib->securite_xss($_POST['MATRICULE']), $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['PRENOMS']), $lib->securite_xss($_POST['DATNAISSANCE']), $lib->securite_xss($_POST['ADRES']), $lib->securite_xss($_POST['TELMOBILE']), $lib->securite_xss($_POST['TELDOM']), $lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['LOGIN']), $lib->securite_xss($_POST['MP']), $lib->securite_xss($_POST['CODE']), $lib->securite_xss($_POST['BIOGRAPHIE']), $lib->securite_xss($_POST['PHOTO_FACE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDTYPEINDIVIDU']), $lib->securite_xss($_POST['SEXE']), $id_tuteur, $lib->securite_xss($_POST['ANNEEBAC']), $lib->securite_xss($_POST['NATIONNALITE']), $lib->securite_xss($_POST['SIT_MATRIMONIAL']), $lib->securite_xss($_POST['NUMID']), $lib->securite_xss($_POST['PATHOLOGIE']), $lib->securite_xss($_POST['ALLERGIE']), $lib->securite_xss($_POST['MEDECIN_TRAITANT']), $lib->securite_xss($_POST['FICHE_RECTO']), $lib->securite_xss($_POST['FICHE_VERSO']), $lib->securite_xss($_POST['LIEU_NAISSANCE']), $lib->securite_xss($_POST['PERE']), $lib->securite_xss($_POST['MERE'])));


                    $id_eleve = $dbh->lastInsertId();

                    $requete = $dbh->prepare("INSERT INTO PARENT(idParent, ideleve) VALUES (?, ?)");
                    $resultReq = $requete->execute(array($id_tuteur, $id_eleve));

                    if ($res == 1) {
                        $lib->envoiLoginEleve($lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['PRENOMS']) . " " . $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['LOGIN']), $pass);
                        $msg = "Ajout effectué avec succés";
                        $urlredirect = "accueil.php?msg=$msg&res=$res";
                    } else {
                        $msg = "Ajout effectué avec echec";
                        $urlredirect = "accueil.php?msg=$msg&res=$res";
                    }

                } else {  // le parent existe deja dans la base

                    $_POST['LOGIN'] = $lib->securite_xss($_POST['COURRIEL']);
                    $_POST['PHOTO_FACE'] = $_FILES['PHOTO_FACE']['name'];
                    $pass = $lib->securite_xss($_POST['MP']);
                    $_POST['MP'] = md5($lib->securite_xss($_POST['MP']));


                    //var_dump($_POST);exit;
                    $stmt = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE, IDTUTEUR, ANNEEBAC, NATIONNALITE, SIT_MATRIMONIAL, NUMID, PATHOLOGIE, ALLERGIE, MEDECIN_TRAITANT, FICHE_RECTO, FICHE_VERSO, LIEU_NAISSANCE, PERE, MERE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $res = $stmt->execute(array($lib->securite_xss($_POST['MATRICULE']), $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['PRENOMS']), $lib->securite_xss($_POST['DATNAISSANCE']), $lib->securite_xss($_POST['ADRES']), $lib->securite_xss($_POST['TELMOBILE']), $lib->securite_xss($_POST['TELDOM']), $lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['LOGIN']), $lib->securite_xss($_POST['MP']), $lib->securite_xss($_POST['CODE']), $lib->securite_xss($_POST['BIOGRAPHIE']), $lib->securite_xss($_POST['PHOTO_FACE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDTYPEINDIVIDU']), $lib->securite_xss($_POST['SEXE']), $id_tuteur, $lib->securite_xss($_POST['ANNEEBAC']), $lib->securite_xss($_POST['NATIONNALITE']), $lib->securite_xss($_POST['SIT_MATRIMONIAL']), $lib->securite_xss($_POST['NUMID']), $lib->securite_xss($_POST['PATHOLOGIE']), $lib->securite_xss($_POST['ALLERGIE']), $lib->securite_xss($_POST['MEDECIN_TRAITANT']), $lib->securite_xss($_POST['FICHE_RECTO']), $lib->securite_xss($_POST['FICHE_VERSO']), $lib->securite_xss($_POST['LIEU_NAISSANCE']), $lib->securite_xss($_POST['PERE']), $lib->securite_xss($_POST['MERE'])));


                    $id_eleve = $dbh->lastInsertId();
                    /*$query = $dbh->prepare("UPDATE  INDIVIDU SET LIEN_PARENTE =:LIEN_PARENTE WHERE IDINDIVIDU =:idParent");
                    $res2 = $query->execute(array("LIEN_PARENTE"=>$lib->securite_xss($_POST['LIEN_PARENTE']), "idParent"=>$lib->securite_xss($_POST['idParent'])));*/
                    if ($res == 1) {
                        $requete = $dbh->prepare("INSERT INTO PARENT(idParent, ideleve) VALUES (?, ?)");
                        $resultReq = $requete->execute(array($lib->securite_xss($_POST['idParent']), $id_eleve));
                        $lib->envoiLoginEleve($lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['PRENOMS']) . " " . $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['LOGIN']), $pass);
                        $msg = "Ajout effectué avec succés";
                        $urlredirect = "accueil.php?msg=$msg&res=$res";
                    } else {
                        $msg = "Ajout effectué avec echec";
                        $urlredirect = "accueil.php?msg=$msg&res=$res";
                    }

                }

            } else //Sinon (la fonction renvoie FALSE).
            {
                $msg = "Echec de l'upload !";

            }
        }
    } else {

        if (!empty($fichier1) && !empty($fichier2)) {
                if (move_uploaded_file($_FILES['FICHE_RECTO']['tmp_name'], $dossier . $fichier1) && move_uploaded_file($_FILES['FICHE_VERSO']['tmp_name'], $dossier . $fichier2)) {
                    $_POST['FICHE_RECTO'] = $_FILES['FICHE_RECTO']['name'];
                    $_POST['FICHE_VERSO'] = $_FILES['FICHE_VERSO']['name'];
                }
        }

        if ($_POST['type'] == "non") { // le parent n'existe pas encore dans la base

            $_POST['LOGIN'] = $lib->securite_xss($_POST['COURRIEL']);
            $_POST['PHOTO_FACE'] = "imgDefaultIndividu.jpg";
            $PHOTO_FACETUTEUR = "imgDefaultIndividu.jpg";
            $_POST['LOGINTUTEUR'] = $lib->securite_xss($_POST['COURRIELTUTEUR']);
            $passTuteur = $lib->securite_xss($_POST['MPTUTEUR']);
            $_POST['MPTUTEUR'] = md5($lib->securite_xss($_POST['MPTUTEUR']));


            $stmt1 = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, LIEN_PARENTE, LIEU_TRAVAIL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $res1 = $stmt1->execute(array($lib->securite_xss($_POST['MATRICULETUTEUR']), $lib->securite_xss($_POST['NOMTUTEUR']), $lib->securite_xss($_POST['PRENOMSTUTEUR']), $lib->securite_xss($_POST['TELMOBILETUTEUR']), $lib->securite_xss($_POST['TELDOMTUTEUR']), $lib->securite_xss($_POST['COURRIELTUTEUR']), $lib->securite_xss($_POST['LOGINTUTEUR']), $lib->securite_xss($_POST['MPTUTEUR']), $lib->securite_xss($_POST['CODETUTEUR']), $PHOTO_FACETUTEUR, $lib->securite_xss($_POST['IDETABLISSEMENT']), 9, $lib->securite_xss($_POST['LIEN_PARENTE']), $lib->securite_xss($_POST['LIEU_TRAVAIL'])));

            if ($res1 == 1) {
                $lib->envoiLoginParent($lib->securite_xss($_POST['COURRIELTUTEUR']), $lib->securite_xss($_POST['PRENOMSTUTEUR']) . " " . $lib->securite_xss($_POST['NOMTUTEUR']), $lib->securite_xss($_POST['LOGINTUTEUR']), $passTuteur);
            }


            $id_tuteur = $dbh->lastInsertId();
            $pass = $lib->securite_xss($_POST['MP']);
            $_POST['MP'] = md5($lib->securite_xss($_POST['MP']));
            $stmt = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE, IDTUTEUR, ANNEEBAC, NATIONNALITE, SIT_MATRIMONIAL, NUMID, PATHOLOGIE, ALLERGIE, MEDECIN_TRAITANT, FICHE_RECTO, FICHE_VERSO, LIEU_NAISSANCE, PERE, MERE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $res = $stmt->execute(array($lib->securite_xss($_POST['MATRICULE']), $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['PRENOMS']), $lib->securite_xss($_POST['DATNAISSANCE']), $lib->securite_xss($_POST['ADRES']), $lib->securite_xss($_POST['TELMOBILE']), $lib->securite_xss($_POST['TELDOM']), $lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['LOGIN']), $lib->securite_xss($_POST['MP']), $lib->securite_xss($_POST['CODE']), $lib->securite_xss($_POST['BIOGRAPHIE']), $lib->securite_xss($_POST['PHOTO_FACE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDTYPEINDIVIDU']), $lib->securite_xss($_POST['SEXE']), $id_tuteur, $lib->securite_xss($_POST['ANNEEBAC']), $lib->securite_xss($_POST['NATIONNALITE']), $lib->securite_xss($_POST['SIT_MATRIMONIAL']), $lib->securite_xss($_POST['NUMID']), $lib->securite_xss($_POST['PATHOLOGIE']), $lib->securite_xss($_POST['ALLERGIE']), $lib->securite_xss($_POST['MEDECIN_TRAITANT']), $lib->securite_xss($_POST['FICHE_RECTO']), $lib->securite_xss($_POST['FICHE_VERSO']), $lib->securite_xss($_POST['LIEU_NAISSANCE']), $lib->securite_xss($_POST['PERE']), $lib->securite_xss($_POST['MERE'])));

            $id_eleve = $dbh->lastInsertId();

            $requete = $dbh->prepare("INSERT INTO PARENT(idParent, ideleve) VALUES (?, ?)");
            $resultReq = $requete->execute(array($id_tuteur, $id_eleve));


            if ($res == 1) {
                $lib->envoiLoginEleve($lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['PRENOMS']) . " " . $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['LOGIN']), $pass);
                $msg = "Ajout effectué avec succés";
                $urlredirect = "accueil.php?msg=$msg&res=$res";
            } else {
                $msg = "Ajout effectué avec echec";
                $urlredirect = "accueil.php?msg=$msg&res=$res";
            }

        } else { // le parent existe deja dans la base

            $_POST['LOGIN'] = $lib->securite_xss($_POST['COURRIEL']);
            $_POST['PHOTO_FACE'] = "imgDefaultIndividu.jpg";
            $pass = $lib->securite_xss($_POST['MP']);
            $_POST['MP'] = md5($lib->securite_xss($_POST['MP']));


            $stmt = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE, IDTUTEUR, ANNEEBAC, NATIONNALITE, SIT_MATRIMONIAL, NUMID, PATHOLOGIE, ALLERGIE, MEDECIN_TRAITANT, FICHE_RECTO, FICHE_VERSO, LIEU_NAISSANCE, PERE, MERE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $res = $stmt->execute(array($lib->securite_xss($_POST['MATRICULE']), $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['PRENOMS']), $lib->securite_xss($_POST['DATNAISSANCE']), $lib->securite_xss($_POST['ADRES']), $lib->securite_xss($_POST['TELMOBILE']), $lib->securite_xss($_POST['TELDOM']), $lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['LOGIN']), $lib->securite_xss($_POST['MP']), $lib->securite_xss($_POST['CODE']), $lib->securite_xss($_POST['BIOGRAPHIE']), $lib->securite_xss($_POST['PHOTO_FACE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDTYPEINDIVIDU']), $lib->securite_xss($_POST['SEXE']), $id_tuteur, $lib->securite_xss($_POST['ANNEEBAC']), $lib->securite_xss($_POST['NATIONNALITE']), $lib->securite_xss($_POST['SIT_MATRIMONIAL']), $lib->securite_xss($_POST['NUMID']), $lib->securite_xss($_POST['PATHOLOGIE']), $lib->securite_xss($_POST['ALLERGIE']), $lib->securite_xss($_POST['MEDECIN_TRAITANT']), $lib->securite_xss($_POST['FICHE_RECTO']), $lib->securite_xss($_POST['FICHE_VERSO']), $lib->securite_xss($_POST['LIEU_NAISSANCE']), $lib->securite_xss($_POST['PERE']), $lib->securite_xss($_POST['MERE'])));

            $id_eleve = $dbh->lastInsertId();

           /* $query = $dbh->prepare("UPDATE  INDIVIDU SET LIEN_PARENTE =:LIEN_PARENTE WHERE IDINDIVIDU =:idParent");
            $res2 = $query->execute(array("LIEN_PARENTE"=>$lib->securite_xss($_POST['LIEN_PARENTE']), "idParent"=>$lib->securite_xss($_POST['idParent'])));*/
            if ($res == 1){

                $requete = $dbh->prepare("INSERT INTO PARENT(idParent, ideleve) VALUES (?, ?)");
                $resultReq = $requete->execute(array($lib->securite_xss($_POST['idParent']), $id_eleve));
                $lib->envoiLoginEleve($lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['PRENOMS']) . " " . $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['LOGIN']), $pass);
                $msg = "Ajout effectué avec succés";
                $urlredirect = "accueil.php?msg=$msg&res=$res";
            } else {
                $msg = "Ajout effectué avec echec";
                $urlredirect = "accueil.php?msg=$msg&res=$res";
            }
        }

    }
   // var_dump($urlredirect);exit;

    $msg1=null;
    header("Location:$urlredirect");

}else{
        $msg1 = "Veillez saisir les champs";

        $urlredirect1 = "accueil.php?msg1=$msg1";
        header("Location:$urlredirect1");
}
}
if (isset($_POST) && $_POST!='' && $_POST['IDTYPEINDIVIDU'] == "7"){
    if ($_POST['MATRICULE']!= '' && $_POST['NOM']!= '' && $_POST['PRENOMS']!= '' && $_POST['TELMOBILE']!='' )
 {
    $dossier = "../../imgtiers/";
    $fichier = basename($_FILES['PHOTO_FACE']['name']);
    if (!empty($fichier)) {


        $res = -1;
        $taille_maxi = 1000000;
        $taille = filesize($_FILES['PHOTO_FACE']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['PHOTO_FACE']['name'], '.');

        if (!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
            $msg = "Vous devez uploader un fichier de type png, gif, jpg, jpeg";
        }
        if ($taille > $taille_maxi) {
            $msg = "Le fichier est trop gros";
        }
        if (!isset($msg)) //S'il n'y a pas d'erreur, on upload
        {
            if (move_uploaded_file($_FILES['PHOTO_FACE']['tmp_name'], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
            {

                $_POST['LOGIN'] = $lib->securite_xss($_POST['COURRIEL']);
                $_POST['PHOTO_FACE'] = $_FILES['PHOTO_FACE']['name'];


                $stmt = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE, ANNEEBAC, NATIONNALITE, SIT_MATRIMONIAL, NUMID, DIPLOMES, DISCIPLINE, ANNEE, DATE_ARRIVE_CEMAD, FILIERE_ENSEIGNE, ID_NIVEAU, DUREE_ENSEIGNEMENT, ENGAGEMENT, RAISON_ENGAGEMENT, LIEU_NAISSANCE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $res = $stmt->execute(array($lib->securite_xss($_POST['MATRICULE']), $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['PRENOMS']), $lib->securite_xss($_POST['DATNAISSANCE']), $lib->securite_xss($_POST['ADRES']), $lib->securite_xss($_POST['TELMOBILE']), $lib->securite_xss($_POST['TELDOM']), $lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['LOGIN']), md5($lib->securite_xss($_POST['MP'])), $lib->securite_xss($_POST['CODE']), $lib->securite_xss($_POST['BIOGRAPHIE']), $lib->securite_xss($_POST['PHOTO_FACE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDTYPEINDIVIDU']), $lib->securite_xss($_POST['SEXE']), $lib->securite_xss($_POST['ANNEEBAC']), $lib->securite_xss($_POST['NATIONNALITE']), $lib->securite_xss($_POST['SIT_MATRIMONIAL']), $lib->securite_xss($_POST['NUMID']), $lib->securite_xss($_POST['DIPLOMES']), $lib->securite_xss($_POST['DISCIPLINE']), $lib->securite_xss($_POST['ANNEE']), $lib->securite_xss($_POST['DATE_ARRIVE_CEMAD']), $lib->securite_xss($_POST['FILIERE_ENSEIGNE']), $lib->securite_xss($_POST['ID_NIVEAU']), $lib->securite_xss($_POST['DUREE_ENSEIGNEMENT']), $lib->securite_xss($_POST['ENGAGEMENT']), $lib->securite_xss($_POST['RAISON_ENGAGEMENT']), $lib->securite_xss($_POST['LIEU_NAISSANCE'])));

                if ($res == 1) {
                    $lib->envoiLoginProfesseur($lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['PRENOMS']) . " " . $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['LOGIN']), $lib->securite_xss($_POST['MP']));
                    $msg = "Ajout effectué avec succés";
                    $urlredirect = "accueil.php?msg=$msg&res=$res";
                } else {
                    $msg = "Ajout effectué avec echec";
                    $urlredirect = "accueil.php?msg=$msg&res=$res";
                }
            } else //Sinon (la fonction renvoie FALSE).
            {
                $msg = "Echec de l'upload !";

            }
        }
    } else {
        $_POST['LOGIN'] = $lib->securite_xss($_POST['COURRIEL']);
        $_POST['PHOTO_FACE'] = "imgDefaultIndividu.jpg";

        $stmt = $dbh->prepare("INSERT INTO INDIVIDU(MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, CODE, BIOGRAPHIE, PHOTO_FACE, IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE,  ANNEEBAC, NATIONNALITE, SIT_MATRIMONIAL, NUMID, DIPLOMES, DISCIPLINE, ANNEE, DATE_ARRIVE_CEMAD, FILIERE_ENSEIGNE, ID_NIVEAU, DUREE_ENSEIGNEMENT, ENGAGEMENT, RAISON_ENGAGEMENT, LIEU_NAISSANCE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res = $stmt->execute(array($lib->securite_xss($_POST['MATRICULE']), $lib->securite_xss($_POST['NOM']), $lib->securite_xss($_POST['PRENOMS']), $lib->securite_xss($_POST['DATNAISSANCE']), $lib->securite_xss($_POST['ADRES']), $lib->securite_xss($_POST['TELMOBILE']), $lib->securite_xss($_POST['TELDOM']), $lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['LOGIN']), md5($lib->securite_xss($_POST['MP'])), $lib->securite_xss($_POST['CODE']), $lib->securite_xss($_POST['BIOGRAPHIE']), $lib->securite_xss($_POST['PHOTO_FACE']), $lib->securite_xss($_POST['IDETABLISSEMENT']), $lib->securite_xss($_POST['IDTYPEINDIVIDU']), $lib->securite_xss($_POST['SEXE']), $lib->securite_xss($_POST['ANNEEBAC']), $lib->securite_xss($_POST['NATIONNALITE']), $lib->securite_xss($_POST['SIT_MATRIMONIAL']), $lib->securite_xss($_POST['NUMID']), $lib->securite_xss($_POST['DIPLOMES']), $lib->securite_xss($_POST['DISCIPLINE']), $lib->securite_xss($_POST['ANNEE']), $lib->securite_xss($_POST['DATE_ARRIVE_CEMAD']), $lib->securite_xss($_POST['FILIERE_ENSEIGNE']), $lib->securite_xss($_POST['ID_NIVEAU']), $lib->securite_xss($_POST['DUREE_ENSEIGNEMENT']), $lib->securite_xss($_POST['ENGAGEMENT']), $lib->securite_xss($_POST['RAISON_ENGAGEMENT']), $lib->securite_xss($_POST['LIEU_NAISSANCE'])));

        if ($res == 1) {
            $lib->envoiLoginProfesseur($lib->securite_xss($_POST['COURRIEL']), $lib->securite_xss($_POST['PRENOMS']) . " " . $lib->securite_xss($_POST['NOM']),$lib->securite_xss( $_POST['LOGIN']), $lib->securite_xss($_POST['MP']));
            $msg = "Ajout effectué avec succés";
            $urlredirect = "accueil.php?msg=$msg&res=$res";
        } else {
            $msg = "Votre ajout a échoué";
            $urlredirect = "accueil.php?msg=$msg&res=$res";
        }

    }
    $msg1=null;
    header("Location:$urlredirect");

}else{
    $msg1 = "Veillez saisir les champs";
    //var_dump("3".$msg1);exit;
    $urlredirect1 = "accueil.php?msg1=$msg1";
    header("Location:$urlredirect1");
}
}