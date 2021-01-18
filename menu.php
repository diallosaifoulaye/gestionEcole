<?php

if (!isset($_SESSION)) {
    session_start();
}
require_once("restriction_menu.php");

require_once("config/Connexion.php");
require_once("config/Librairie.php");
$connection = new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();

$profil = $lib->securite_xss($_SESSION['profil']);

?>


<!DOCTYPE html>
<html lang="en" class="body-full-height">
<head>
    <!-- META SECTION -->
    <title>CEMAD - Gestion Administrative des Etablissements Scolaires</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="icon" href="assets/images/users/user-ecole.jpg" type="image/x-icon"/>
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <!-- EOF CSS INCLUDE -->

    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet"/>
</head>
<body>


<div class="login-container2" style="background-color: #ffffff;">
    <div class="login-box2 animated fadeInDown">
        <div class="table-responsive login-logo2" style="position: absolute;top: 0px;">
            <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                <li class="over" style="padding-top: 10px; width: 150px;height: 100%;">
                    <center><span class="bolder" style="color: white;font-size: 24px;">CEMAD</span></center>
                </li>
                <!-- SEARCH -->
                <li class="btn-group-lg" style="padding-top: 10px;">
                    <div class="plugin-date" id="plugin-date" style="font-size:18px; color:#fff;margin-left: 10px;margin-right: 10px;">Loading...</div>
                </li>
                <li class="btn-group-lg" style="padding-top: 10px;">
                    <div class="plugin-clock" style="font-size:18px; color:#fff;"> 00:00</div>
                </li>
                <!-- END SEARCH -->
                <!-- SIGN OUT -->
                <?php if (!isset($_SESSION['client'])) { ?>
                    <li class=" pull-right">
                        <a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span></a>
                    </li>
                    <!-- END SIGN OUT -->
                    <li class="pull-right" style="padding-top: 10px;">
                        <span
                                style="font-size:14px; color:#fff;">Bienvenue <?php echo $lib->affichage_xss($_SESSION['prenom']) . " " . $lib->affichage_xss($_SESSION['nom']); ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="login-body2">

            <div class="content-wrapper" id="menu">
                <div class="container-fluid">
                    <div class="row">

                        <?php if ($profil == 1 || $lib->Acces_module($profil, 8) == 1) { ?>

                            <div class="col-md-2 col-md-push-2 out" id="param" onmouseout="lienOut('param');">
                                <div class="col-md-12" onmouseover="lienOver('param');" style="cursor: pointer;">
                                    <br/>
                                    <center><a href="modules/Parametrage/accueil.php">
                                            <img class="icones" src="assets/img/icone/logoParam.png" width="80" height="80" alt=""/>
                                            <span class="texte">PARAMETRES / PROFILS</span>
                                        </a>
                                    </center>
                                    <br/>
                                </div>
                            </div>

                        <?php } if ($profil == 1 || $lib->Acces_module($profil, 1) == 1) { ?>

                            <div class="col-md-2 col-md-push-3 out" id="tiers" onmouseout="lienOut('tiers');">
                                <div onmouseover="lienOver('tiers');" style="cursor: pointer;">
                                    <br/>
                                    <center>
                                        <a href="modules/Tiers/accueil.php">
                                            <div>
                                                <img src="assets/img/icone/logoTiers.png" width="80" height="80" alt="" class="icones"/>
                                                <span class="texte"> TIERS </span>
                                            </div>
                                        </a>
                                    </center>
                                    <br/>
                                </div>
                            </div>

                        <?php } if ($profil == 1 || $lib->Acces_module($profil, 2) == 1) { ?>

                            <div class="col-md-2 col-md-push-4 out" id="treso" onmouseout="lienOut('treso');">
                                <div onmouseover="lienOver('treso');" style="cursor: pointer;">
                                    <a href="modules/Tresorerie/accueil.php">
                                        <br/>
                                        <center>
                                            <div>

                                                <img src="assets/img/icone/logoTres.png" width="80" height="80" class="icones" alt=""/>
                                                <span class="texte">TRESORERIE</span>

                                            </div>
                                        </center>
                                        <br/>
                                    </a>
                                </div>
                            </div>

                        <?php } ?>

                        <br>
                    </div>
                </div>

                <br>


                <?php //if(1 ==2 ){?>


                    <div class="container-fluid">
                        <div class="row">

                            <?php if ($profil == 1 || $lib->Acces_module($profil, 3) == 1) { ?>
                                <div class="col-md-2 col-md-push-2 out" id="empt" onmouseout="lienOut('empt');">
                                    <div class="col-md-12" onmouseover="lienOver('empt');" style="cursor: pointer;">
                                        <br/>
                                        <center>
                                            <a href="modules/EmploiDuTemps/accueil.php">
                                                <img src="assets/img/icone/logoEmp.png" alt="" width="80" height="80" class="icones"/>
                                                <span class="texte">EMPLOIS DU TEMPS</span>
                                            </a>
                                        </center>
                                        <br/>
                                    </div>
                                </div>

                            <?php } if ($profil == 1 || $lib->Acces_module($profil, 4) == 1) { ?>

                                <div class="col-md-2 col-md-push-3 out" id="eval" onmouseout="lienOut('eval');">
                                    <div onmouseover="lienOver('eval');" style="cursor: pointer;">
                                        <br/>
                                        <center>
                                            <a href="modules/Evaluations/accueil.php">
                                                <div>
                                                    <img src="assets/img/icone/logoEval.png" width="80" height="80" alt="" class="icones"/>
                                                    <span class="texte">EVALUATIONS</span>
                                                </div>
                                            </a>
                                        </center>
                                        <br/>
                                    </div>
                                </div>

                            <?php } if ($profil == 1 || $lib->Acces_module($profil, 5) == 1) { ?>

                                <div class="col-md-2 col-md-push-4 out" id="ged" onmouseout="lienOut('ged');">
                                    <div onmouseover="lienOver('ged');" style="cursor: pointer;">
                                        <a href="modules/GED/accueil.php">
                                            <br/>
                                            <center>
                                                <div>
                                                    <img src="assets/img/icone/logoGed.png" width="80" height="80" class="icones" alt=""/>
                                                    <span class="texte">GED</span>
                                                </div>
                                            </center>
                                            <br/>
                                        </a>
                                    </div>
                                </div>

                            <?php } ?>

                            <br>
                        </div>
                    </div>

                    <br>

                    <div class="container-fluid">
                        <div class="row">

                            <?php if ($profil == 1 || $lib->Acces_module($profil, 6) == 1) { ?>

                                <div class="col-md-2 col-md-push-2 out" id="equip" onmouseout="lienOut('equip');">
                                    <div class="col-md-12" onmouseover="lienOver('equip');" style="cursor: pointer;">
                                        <br/>
                                        <center>
                                            <a href="modules/Equipements/accueil.php">
                                                <img src="assets/img/icone/ic_scolarite.png" width="80" height="80" alt="" class="icones"/>
                                                <span class="texte">EQUIPEMENTS</span>
                                            </a>
                                        </center>
                                        <br/>
                                    </div>
                                </div>

                            <?php } if ($profil == 1 || $lib->Acces_module($profil, 7) == 1) { ?>

                                <div class="col-md-2 col-md-push-3 out" id="sanc" onmouseout="lienOut('sanc');">
                                    <div onmouseover="lienOver('sanc');" style="cursor: pointer;">
                                        <br/>
                                        <center>
                                            <a href="modules/Sanctions/accueil.php">
                                                <div>
                                                    <img src="assets/img/icone/logoSanc.png" width="80" height="80" alt="" class="icones"/>
                                                    <span class="texte">SANCTIONS</span>
                                                </div>
                                            </a>
                                        </center>
                                        <br/>
                                    </div>
                                </div>

                            <?php } if ($profil == 1 || $lib->Acces_module($profil, 9) == 1) { ?>

                                <div class="col-md-2 col-md-push-4 out" id="tabbord" onmouseout="lienOut('tabbord');">
                                    <div onmouseover="lienOver('tabbord');" style="cursor: pointer;">
                                        <a href="modules/Reporting/accueil.php">
                                            <br/>
                                            <center>
                                                <div>
                                                    <img src="assets/img/icone/logoRep.png" width="80" height="80" class="icones" alt=""/>
                                                    <span class="texte">TABLEAU DE BORD</span>
                                                </div>
                                            </center>
                                            <br/>
                                        </a>
                                    </div>
                                </div>

                            <?php } ?>
                            <br>
                        </div>
                    </div>


                <?php //} ?>



            </div>

            <!--<div class="footer-section">
                <div class="col-lg-offset-0">
                    <a href="https://www.numherit.com">&copy; 2019 NUMHERIT</a>
                </div>
            </div>-->
            <footer class="footer text-center"> <a href="https://www.numherit.com">&copy; 2019 NUMHERIT SA</a></footer>

        </div>

    </div>


    <!-- MESSAGE BOX END MESSAGE BOX-->
    <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
        <div class="mb-container">
            <div class="mb-middle">
                <div class="mb-title"><span class="fa fa-sign-out"></span> Se <strong>deconnecter</strong> ?</div>
                <div class="mb-content">
                    <p>Êtes vous sur de vouloir vous déconnecter ?</p>
                </div>
                <div class="mb-footer">
                    <div class="pull-right">
                        <a href="logout.php" class="btn btn-success btn-lg">Oui</a>
                        <button class="btn btn-default btn-lg mb-control-close">Non</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- START PRELOADS -->
    <audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
    <audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
    <!-- END PRELOADS -->

    <!-- START SCRIPTS -->
    <!-- START PLUGINS -->
    <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
    <!-- END PLUGINS -->

    <script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- END THIS PAGE PLUGINS-->


    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>


    <!-- START TEMPLATE
    <script type="text/javascript" src="js/settings.js"></script>
    -->
    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>

    <style>
        .out {
            background-color: #eaeaea;
        }

        .over {
            background-color: #fdc117;
        }
        .footer{
            color: #58666e;
            left: 0;
            padding: 20px 30px;
            position: fixed;
            right: 0;
            background: #ffffff;
            margin-top: 20px;
        }
    </style>
    <script type="text/javascript">
        function lienOver(elem) {
            elem = $("#" + elem);
            elem.removeClass("out");
            elem.addClass("over");
        }
        function lienOut(elem) {
            elem = $("#" + elem);
            elem.removeClass("over");
            elem.addClass("out");
        }
    </script>


    <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->

</body>
</html>