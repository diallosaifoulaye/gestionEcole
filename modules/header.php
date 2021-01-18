<?php
require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib = new Librairie();
$profily = $lib->securite_xss($_SESSION['profil']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- META SECTION -->
    <title>CEMAD - Gestion Administrative des Etablissements Scolaires</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="../../assets/images/users/user-ecole.jpg" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="../../css/theme-default.css"/>
    <link rel="stylesheet" type="text/css" id="theme" href="../../datetimepicker/jquery.datetimepicker.css"/>
    <!-- EOF CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="../../css/bootstrap-select.min.css"/>

    <script src='//cdn.tinymce.com/4/tinymce.min.js'></script>
    <script>
        tinymce.init({
            selector: '#mytextarea'
        });
    </script>
    <style>
        .cadre {
            background-color: #ffffff;
            width: 100%;
            margin: 0 auto 15px auto;
            padding: 10px;
            border: 1px solid #2589C5;

            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 5px;
            behavior: url(border-radius.htc);

            background-image: -moz-linear-gradient(top, #ffffff, #f4f4f4);
            background-image: -webkit-gradient(linear, left top, left bottom, from(#ffffff), to(#f4f4f4));
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#ffffff,endColorstr=#f4f4f4);
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#ffffff,endColorstr=#f4f4f4)";
        }

        legend {
            color: #1f85c7;
        }
    </style>
</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container">

    <!-- START PAGE SIDEBAR -->
    <div class="page-sidebar">
        <!-- START X-NAVIGATION -->
        <ul class="x-navigation">
            <li class="xn-logo" style="display: none;">
                <a href="../../menu.php">CEMAD</a>
                <a href="#" class="x-navigation-control"></a>
            </li>
            <li class="xn-profile">
                <a href="../../menu.php" class="profile-mini">
                    <img src="../../assets/images/users/user-ecole.jpg" alt="sunuEcole"/>
                </a>
                <div class="profile">
                    <div class="profile-image">
                        <a href="../../menu.php"><img src="../../assets/images/users/user-ecole.jpg" alt="sunuEcole"/></a>
                    </div>
                    <div class="profile-data">
                        <div class="profile-data-name"><?php echo $lib->securite_xss($_SESSION['prenom'])." ".$lib->securite_xss($_SESSION['nom']); ?></div>
                        <?php


                        $query= "SELECT profil from profil where idProfil= ".$lib->securite_xss($_SESSION['profil']);
                        $stmt = $dbh->prepare($query);
                        $stmt->execute();
                        $rs_user = $stmt->fetchObject();
                        $annee_scolaire = $dbh->query("SELECT IDANNEESSCOLAIRE, LIBELLE_ANNEESSOCLAIRE, DATE_DEBUT, DATE_FIN, IDETABLISSEMENT 
                                                                          FROM ANNEESSCOLAIRE 
                                                                          WHERE ETAT = 0 AND IDETABLISSEMENT = " . $lib->securite_xss($_SESSION['etab']));
                        ?>
                        <div class="profile-data-title"><?php echo $rs_user->profil; ?>
                            <p><a style="color: #ffffff;hover::blue;" href="modifUserPwd.php">Changer mot de passe</a></p>
                        </div>
                        <div style="margin: 50px auto 0; width: 150px; color: white; font-weight: bolder;"
                             onclick="mySelectAction(this);">
                            <span>Année scolaire</span>
                            <select name="annee_scolaire" id="annee_scolaire" onchange="mySelectValueChange();"
                                    class="form-control" data-live-search="true">

                                <?php foreach ($annee_scolaire->fetchAll() as $oneAnnee) { ?>

                                    <option value="<?php echo $oneAnnee['IDANNEESSCOLAIRE']; ?>" <?php if ($oneAnnee['IDANNEESSCOLAIRE'] == $_SESSION['ANNEESSCOLAIRE']) echo "selected"; ?>><?php echo $oneAnnee['LIBELLE_ANNEESSOCLAIRE']; ?></option>

                                <?php } ?>

                            </select>
                        </div>
                    </div>
                    <div class="profile-controls">
                    </div>
                </div>
            </li>

            <?php if ($profily == 1 || $lib->Acces_module($profily, 8) == 1) { ?>

                <li class="xn-openable active">
                    <a href="../Parametrage/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">PARAMETRAGE</span></a>
                    <ul>
                        <?php if ($profily == 1 || $lib->Est_autoriser(3,$profily) == 1) { ?>

                            <li><a href="../Parametrage/accueil.php"><span class="fa fa-heart"></span> Information etablissement</a></li>

                        <?php } if ($profily == 1 || $lib->Est_autoriser(1,$profily) == 1) { ?>

                            <li><a href="../Parametrage/profile.php"><span class="fa fa-heart"></span> Gestion Profil</a></li>

                        <?php }  if ($profily == 1 || $lib->Est_autoriser(2,$profily) == 1) { ?>

                            <li><a href="../Parametrage/utilisateurs.php"><span class="fa fa-heart"></span> Gestion utilisateur</a></li>

                        <?php }  if ($profily == 1 || $lib->Est_autoriser(11,$profily) == 1) { ?>

                            <li><a href="../Parametrage/anneesScolaires.php"><span class="fa fa-heart"></span> Gestion des annees scolaires</a></li>

                        <?php }  if ($profily == 1 || $lib->Est_autoriser(49,$profily) == 1) { ?>

                            <li><a href="../Parametrage/niveaux.php"><span class="fa fa-heart"></span> Gestion des cycles</a></li>

                            <li><a href="../Parametrage/niv.php"><span class="fa fa-heart"></span> Gestion des niveaux</a></li>

                        <?php } if ($profily == 1 || $lib->Est_autoriser(12,$profily) == 1) { ?>

                            <li><a href="../Parametrage/periodeScolaires.php"><span class="fa fa-heart"></span> Gestion des periodes scolaires</a></li>

                        <?php }  if ($profily == 1 || $lib->Est_autoriser(4,$profily) == 1) { ?>

                            <li><a href="../Parametrage/filieres.php"><span class="fa fa-heart"></span> Gestion des séries</a></li>

                        <?php }  if ($profily == 1 || $lib->Est_autoriser(5,$profily) == 1) { ?>

                            <li><a href="../Parametrage/modules.php"><span class="fa fa-heart"></span> Gestion des matiéres</a></li>

                        <?php }  if ($profily == 1 || $lib->Est_autoriser(48,$profily) == 1) { ?>

                            <li><a href="../Parametrage/classes.php"><span class="fa fa-heart"></span> Gestion des classes</a></li>

                        <?php } if ($profily == 1 || $lib->Est_autoriser(8,$profily) == 1) { ?>

                            <li><a href="../Parametrage/typeSalle.php"><span class="fa fa-heart"></span> Type de salle</a></li>

                            <li><a href="../Parametrage/salle.php"><span class="fa fa-heart"></span> Gestion des salles de cours</a></li>

                        <?php } if ($profily == 1 || $lib->Est_autoriser(7,$profily) == 1) { ?>

                            <li><a href="../Parametrage/fraisInscription.php"><span class="fa fa-heart"></span> Gestion des frais d'inscription</a></li>

                            <li><a href="../Parametrage/sectionTransport.php"><span class="fa fa-heart"></span> Section transport</a></li>

                            <li><a href="../Parametrage/forfaitProf.php"><span class="fa fa-heart"></span> Forfait professeur</a></li>

                            <li><a href="../Parametrage/banque.php"><span class="fa fa-heart"></span> Liste des banques</a></li>

                            <li><a href="../Parametrage/modePaiement.php"><span class="fa fa-heart"></span> Mode de paiement</a></li>

                            <li><a href="../Parametrage/secteur.php"><span class="fa fa-heart"></span> Secteur d'activités </a></li>

                        <?php } if ($profily == 1 || $lib->Est_autoriser(9,$profily) == 1) { ?>

                            <li><a href="../Parametrage/joursFeries.php"><span class="fa fa-heart"></span> Gestion des vacances</a></li>

                        <?php } if ($profily == 1 || $lib->Est_autoriser(10,$profily) == 1) { ?>

                            <li><a href="../Parametrage/reglementInterieur.php"><span class="fa fa-heart"></span> Réglement intérieur de l'école</a></li>

                        <?php }  ?>

                    </ul>
                </li>

            <?php  }  if ($profily == 1 || $lib->Acces_module($profily, 1) == 1) { ?>


                <li class="xn-openable">
                    <a href="../Tiers/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">TIERS</span></a>
                    <ul>

                        <?php if ($profily == 1 || $lib->Est_autoriser(35,$profily) == 1) { ?>
                            <li><a href="../Tiers/accueil.php"><span class="fa fa-list"></span> Liste des individus</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(36,$profily) == 1) { ?>
                            <li><a href="../Tiers/inscription.php"><span class="glyphicon glyphicon-user"></span> Inscriptions</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(52,$profily) == 1) { ?>
                            <li><a href="../Tiers/reinscription.php"><span class="glyphicon glyphicon-user"></span> Réinscription</a></li>
                        <?php }  ?>

                        <?php if ($profily == 1 || $lib->Est_autoriser(50,$profily) == 1) { ?>
                            <li><a href="../Tiers/exonoration.php"><span class="glyphicon glyphicon-user"></span> Exonoration</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(37,$profily) == 1) { ?>
                            <li><a href="../Tiers/historiqueInscription.php"><span class="fa fa-history"></span> Historique inscriptions</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(38,$profily) == 1) { ?>
                            <li><a href="../Tiers/listeEtudiant.php"><span class="fa fa-list"></span>Liste des &eacute;léves </a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(39,$profily) == 1) { ?>
                            <li><a href="../Tiers/listeEtudiantClasse.php"><span class="fa fa-list"></span> Liste &eacute;léves / classe</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(40,$profily) == 1) { ?>
                            <li><a href="../Tiers/listeProfesseur.php"><span class="fa fa-list"></span>Liste des professeurs </a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(41,$profily) == 1) { ?>
                            <li><a href="../Tiers/listeProfesseurRecrutes.php"><span class="fa fa-list"></span> Professeurs recrut&eacute;s</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(42,$profily) == 1) { ?>
                            <li><a href="../Tiers/listePersonnelAdministratif.php"><span class="fa fa-list"></span>Personnels Administratifs </a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(43,$profily) == 1) { ?>
                            <li><a href="../Tiers/listeTuteurs.php"><span class="fa fa-list"></span> Liste des Tuteurs</a></li>
                        <?php }  ?>
                        <?php if ($profily == 1 || $lib->Est_autoriser(44,$profily) == 1) { ?>
                            <li><a href="../Tiers/impression.php"><span class="fa fa-print"></span> Impression</a></li>
                        <?php }  ?>

                    </ul>
                </li>


            <?php } if ($profily == 1 || $lib->Acces_module($profily, 2) == 1) { ?>


                <li class="xn-openable">
                    <a href="../Tresorerie/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">TRESORERIE</span></a>

                    <ul>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 24,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/facturation.php"><span class="fa fa-heart"></span> Factures</a></li>
                        <?php }  ?>

                        <?php  if ($profily == 1 || $lib->Est_autoriser( 25,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/generationFacture.php"><span class="fa fa-download"></span> G&eacute;n&eacute;ration de factures</a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 26,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/accueil.php"><span class="fa fa-heart"></span> Mensualit&eacute;s</a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 29,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/paiementProfesseur.php"><span class="glyphicon glyphicon-user"></span> Paiement des professeurs</a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 30,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/previsionBudgetaire.php"><span class="fa fa-heart"></span>Pr&eacute;vision budg&eacute;taire </a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 31,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/situationFinanciere.php"><span class="fa fa-heart"></span>Situation financi&egrave;re </a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 32,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/historiqueJournalier.php"><span class="fa fa-history"></span> Historique journalier</a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 32,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/historiquePeriodique.php"><span class="fa fa-history"></span> Historique Périodique</a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 33,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/paiementPersonnel.php"><span class="fa fa-heart"></span>Paiement du Personnel </a></li>
                        <?php }  ?>
                        <?php  if ($profily == 1 || $lib->Est_autoriser( 34,$profily) == 1) { ?>
                            <li><a href="../Tresorerie/depenses.php"><span class="fa fa-heart"></span> D&eacute;penses</a></li>
                        <?php }  ?>
                    </ul>

                </li>


            <?php }  ?>

            <?php if(1==2) {?>


                <?php if ($profily == 1 || $lib->Acces_module($profily, 3) == 1) { ?>

                    <li class="xn-openable">

                        <a href="../EmploiDuTemps/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">EMPLOI DU TEMPS</span></a>
                        <ul>
                            <?php if ($profily == 1 || $lib->Est_autoriser(22,$profily) == 1) { ?>

                                <li><a href="../EmploiDuTemps/accueil.php"><span class="fa fa-calendar"></span> Emplois du temps</a></li>

                                <li><a href="../EmploiDuTemps/emploisTempsClasse.php"><span class="fa fa-calendar"></span>
                                        Emplois du temps des <span style="margin-left: 33px;">classes</span>
                                    </a>
                                </li>

                                <li><a href="../EmploiDuTemps/emploisTempsProf.php"><span class="fa fa-calendar"></span>
                                        Emplois du temps des <span style="margin-left: 33px;">professeurs</span>
                                    </a>
                                </li>



                            <?php } if ($profily == 1 || $lib->Est_autoriser(2,$profily) == 1) { ?>

                                <li><a href="../EmploiDuTemps/MAJCours.php"><span class="fa fa-heart"></span>Mise &aacute; jour cours </a></li>

                            <?php } ?>

                        </ul>

                    </li>

                <?php } if ($profily == 1 || $lib->Acces_module($profily, 4) == 1) { ?>


                    <li class="xn-openable">

                        <a href="../Evaluations/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">EVALUATION</span></a>

                        <ul>
                            <?php  if ($profily == 1 || $lib->Est_autoriser( 19,$profily) == 1) { ?>

                                <li><a href="../Evaluations/accueil.php"><span class="fa fa-heart"></span> Type de contr&ocirc;le</a></li>

                            <?php } if ($profily == 1 || $lib->Est_autoriser( 20,$profily) == 1) { ?>

                                <li><a href="../Evaluations/gestionControle.php"><span class="glyphicon glyphicon-user"></span> Gestion des contr&ocirc;les</a></li>

                            <?php } if ($profily == 1 || $lib->Est_autoriser( 21,$profily) == 1) { ?>

                                <li><a href="../Evaluations/GenebulletinNotes.php"><span class="glyphicon glyphicon-list-alt"></span>Generation bulletin notes </a></li>

                                <li><a href="../Evaluations/bulletinNotes.php"><span class="glyphicon glyphicon-list-alt"></span>Bulletin de notes </a></li>

                            <?php }  ?>
                        </ul>

                    </li>


                <?php } if ($profily == 1 || $lib->Acces_module($profily, 5) == 1) { ?>


                    <li class="xn-openable">

                        <a href="../GED/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">GED</span></a>

                        <ul>
                            <?php  if ($profily == 1 || $lib->Est_autoriser($profily, 17) == 1) { ?>
                                <li><a href="../GED/accueil.php"><span class="fa fa-heart"></span> Type de document</a></li>
                            <?php }  ?>
                            <?php  if ($profily == 1 || $lib->Est_autoriser($profily, 18) == 1) { ?>
                                <li><a href="../GED/listeDocuments.php"><span class="glyphicon glyphicon-user"></span> Liste des documents</a></li>
                            <?php }  ?>
                        </ul>
                    </li>


                <?php } if ($profily == 1 || $lib->Acces_module($profily, 6) == 1) { ?>


                    <li class="xn-openable">

                        <a href="../Equipements/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">EQUIPEMENT</span></a>

                        <ul>
                            <?php if ($profily == 1 || $lib->Est_autoriser($profily, 14) == 1) { ?>

                                <li><a href="../Equipements/accueil.php"><span class="fa  fa-wrench"></span> Categorie Equipement</a></li>

                            <?php } if ($profily == 1 || $lib->Est_autoriser($profily, 15) == 1) { ?>

                                <li><a href="../Equipements/inventaireEquipement.php"><span class="fa  fa-wrench"></span> Inventaire Equipement</a></li>

                            <?php } if ($profily == 1 || $lib->Est_autoriser($profily, 16) == 1) { ?>

                                <li><a href="../Equipements/sortieEquipement.php"><span class="fa  fa-wrench"></span> Sortie Equipement</a></li>

                            <?php }  ?>
                        </ul>

                    </li>


                <?php } if ($profily == 1 || $lib->Acces_module($profily, 7) == 1) { ?>


                    <li class="xn-openable">

                        <a href="../Sanctions/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">SANCTION</span></a>

                        <ul>
                            <?php  if ($profily == 1 || $lib->Est_autoriser($profily, 13) == 1) { ?>

                                <li><a href="../Sanctions/typeSanction.php"><span class="fa fa-heart"></span>Type de sanctions</a></li>

                            <?php } if ($profily == 1 || $lib->Est_autoriser($profily, 13) == 1) { ?>

                                <li><a href="../Sanctions/accueil.php"><span class="fa fa-heart"></span> Historique des sanctions</a></li>

                            <?php }  ?>
                        </ul>

                    </li>

                <?php } if ($profily == 1 || $lib->Acces_module($profily, 9) == 1) { ?>

                    <li class="xn-openable">

                        <a href="../Reporting/accueil.php"><span class="fa fa-files-o"></span> <span class="xn-text">REPORTING</span></a>

                        <ul>
                            <?php  if ($profily == 1 || $lib->Est_autoriser($profily, 47) == 1) { ?>

                                <li><a href="../Reporting/accueil.php"><span class="fa fa-dashboard"></span> Reporting</a></li>

                            <?php }  ?>
                        </ul>

                    </li>

                <?php } ?>


            <?php } ?>

        </ul>
        <!-- END X-NAVIGATION -->
    </div>
    <!-- END PAGE SIDEBAR -->

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- START X-NAVIGATION VERTICAL -->
        <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
            <!-- TOGGLE NAVIGATION -->
            <li class="xn-icon-button">
                <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
            </li>
            <!-- END TOGGLE NAVIGATION -->
            <!-- SEARCH -->
            <li class="btn-group-lg"><br>
                <div class="plugin-date" id="plugin-date" style="font-size:18px; color:#fff;">Loading...</div>
            </li>
            <li class="btn-group-lg"><br>
                <div> &nbsp;&nbsp;</div>
            </li>
            <li class="btn-group-lg"><br>
                <div class="plugin-clock" style="font-size:18px; color:#fff;"> 00:00</div>
            </li>
            <!-- END SEARCH -->
            <!-- SIGN OUT -->
            <?php if (!isset($_SESSION['client'])) { ?>
                <li class="xn-icon-button pull-right">
                    <a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span></a>
                </li>
                <!-- END SIGN OUT -->
                <li class="xn-icon-button pull-right"><br>
                    <?php $nom = $lib->securite_xss($_SESSION['nom']); ?>
                    <?php $prenom = $lib->securite_xss($_SESSION['prenom']); ?>
                    <span style="font-size:14px; color:#fff;">Bienvenue <?php echo  $prenom." ".$nom; ?> </span>
                </li>
            <?php } ?>
        </ul>
        <!-- END X-NAVIGATION VERTICAL -->

