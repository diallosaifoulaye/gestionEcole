<?php
session_start();
require_once("config/Connexion.php");
require_once("config/Librairie.php");
$lib = new Librairie();
require_once("modules/Administration/classe/ConnexionManager.php");

if(isset($_GET['login_sso']) && isset($_GET['password_sso']) && $_GET['sso'] == 1 && $_GET['entite_sso'] == 1)
{
    $_POST['button'] = "Connexion";
    $_POST['login'] = $lib->securite_xss($_GET['login_sso']);
    $_POST['password'] = $lib->securite_xss($_GET['password_sso']);
}
if((isset($_POST['login']) && $_POST['login'] != '') && (isset($_POST['password']) && $_POST['password'] != ''))
{
    $user = new ConnexionManager();
    $Isconncte = $user->Connecter($lib->securite_xss($_POST['login']), $lib->securite_xss($_POST['password']));
    if ($Isconncte == 1)
    {
        $connection = new Connexion();
        $dbh = $connection->Connection();

        $query = "SELECT u.idUtilisateur, u.prenomUtilisateur, u.nomUtilisateur, u.idProfil, u.idEtablissement, e.PREFIXE, e.LOGO, e.NOMETABLISSEMENT_ , e.ADRESSE, e.BP, e.TELEPHONE, e.SIGLE, e.VILLE
                  FROM UTILISATEURS u 
                  INNER JOIN ETABLISSEMENT e ON e.IDETABLISSEMENT = u.idEtablissement
                  INNER JOIN profil p ON p.idProfil = u.idProfil";

        $query .= " WHERE p.etat = 1 AND u.ETAT = 1 AND u.login = ".$lib->GetSQLValueString($_POST['login'],"text") . " AND u.password = ".$lib->GetSQLValueString(md5($_POST['password']), "text");
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $rs_user = $stmt->fetchObject();
        $total_rows = $stmt->rowCount();
        if ($total_rows > 0)
        {
            $_SESSION["id"] = $rs_user->idUtilisateur;
            $_SESSION["nom"] = $rs_user->nomUtilisateur;
            $_SESSION["prenom"] = $rs_user->prenomUtilisateur;
            $_SESSION["profil"] = $rs_user->idProfil;
            $_SESSION["etab"] = $rs_user->idEtablissement;
            $_SESSION['PREFIXE'] = $rs_user->PREFIXE;
            $_SESSION['nomEtablissement'] = $rs_user->NOMETABLISSEMENT_;
            $_SESSION['LOGO'] = $rs_user->LOGO;
            $_SESSION['ADRESSEETAB'] = $rs_user->ADRESSE;
            $_SESSION['BP'] = $rs_user->BP;
            $_SESSION['TELEPHONEETAB'] = $rs_user->TELEPHONE;
            $_SESSION['SIGLE'] = $rs_user->SIGLE;
            $_SESSION['VILLE'] = $rs_user->VILLE;

            $query_rq_annee = $dbh->query("SELECT MAX(IDANNEESSCOLAIRE) as IDANNEESSCOLAIRE FROM ANNEESSCOLAIRE WHERE ETAT = 0 AND IDETABLISSEMENT = " . $rs_user->idEtablissement);
            $row_rq_annee = $query_rq_annee->fetchObject();

            $_SESSION['ANNEESSCOLAIRE'] = $row_rq_annee->IDANNEESSCOLAIRE;
        }
        $urlredirect = "menu.php";
    } else {
        $urlredirect = "index.php?msg=parametre de connexion invalide";
    }
    header("Location:$urlredirect");
}
?>

<!DOCTYPE html>
<html lang="en" class="body-full-height">
<head>
    <!-- META SECTION -->
    <title>SunuEcole - Gestion Administrative des Etablissements Scolaires</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="assets/images/users/user-ecole.jpg" type="image/x-icon"/>
    <!-- END META SECTION -->
    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <!-- EOF CSS INCLUDE -->
</head>
<body>

<div class="login-container">

    <div class="login-box animated fadeInDown">
        <div class="login-logo"></div>
        <br/>
        <br/>

        <div class="login-body">

            <!--<div class="login-title"><strong>CEMAD</strong></div>-->

            <div class="row" align="center">

                <div class="col-md-4"></div>

                <div class="col-md-4" style="padding-bottom: 10px">
                    <img src="assets/images/users/logo-accueil.png" alt="CEMAD" />
                </div>

                <div class="col-md-4"></div>

            </div>

            <form class="form-horizontal" method="post" action="index.php">
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="text" name="login" class="form-control" placeholder="login"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="password" name="password" class="form-control" placeholder="Password"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3">
                        <!--<a href="#" class="btn btn-link btn-block">Mot de passe oublié?</a>-->
                    </div>
                    <div class="col-md-6">
                        <button name='connexion' class="btn btn-num_blue btn-block">Se connecter</button>
                    </div>
                    <div class="col-md-3">
                        <!--<a href="#" class="btn btn-link btn-block">Mot de passe oublié?</a>-->
                    </div>
                </div>
            </form>
        </div>
        <div class="login-footer">
            <div class="pull-left">
                <a href="https://www.numherit.com">&copy; 2019 NUMHERIT SA</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
