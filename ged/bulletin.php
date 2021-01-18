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

    if(isset($_GET) && $_GET != '')
    {
        $indivClass = array();
        $idclassroom = "-1";
        if (isset($_GET['idclassroom']))
        {
            $idclassroom = $lib->securite_xss(base64_decode($_GET['idclassroom'])) ;
        }
        $idperiode = "-1";
        if (isset($_GET['idperiode']))
        {
            $idperiode = $lib->securite_xss(base64_decode($_GET['idperiode'])) ;
        }

        try
        {
            $query_periode = $dbh->query("SELECT NOM_PERIODE FROM PERIODE WHERE IDPERIODE = ".$idperiode);
            $query_result_periode = $query_periode->fetchObject();
            $nom_periode = $query_result_periode->NOM_PERIODE;

            $query_annee = $dbh->query("SELECT LIBELLE_ANNEESSOCLAIRE FROM ANNEESSCOLAIRE WHERE IDANNEESSCOLAIRE = ".$idAnnescolaire);
            $query_result_annee = $query_annee->fetchObject();
            $nom_annee = $query_result_annee->LIBELLE_ANNEESSOCLAIRE;

            $query_Indiv = $dbh->query("SELECT IDINDIVIDU FROM AFFECTATION_ELEVE_CLASSE WHERE IDCLASSROOM = ".$idclassroom);
            while ($query_Indiv_result = $query_Indiv->fetchObject()) {
                array_push($indivClass, $query_Indiv_result->IDINDIVIDU);
            }
        }
        catch (PDOException $e)
        {
            echo -2;
        }

        for($i=0;$i<count($indivClass);$i++)
        {
            try
            {
                $query_Indiv_civilite = $dbh->query("SELECT I.PRENOMS, I.NOM, I.MATRICULE, I.DATNAISSANCE, C.LIBELLE, B.ROWID, B.TOTAL_COEF, B.TOTAL_POINT, B.MOYENNE_SEM AS MOY_GENE, B.RANG, B.IDINDIVIDU
                                                            FROM INDIVIDU I 
                                                            INNER JOIN BULLETIN B ON  I.IDINDIVIDU = B.IDINDIVIDU
                                                            INNER JOIN CLASSROOM C ON C.IDCLASSROOM = B.IDCLASSROOM
                                                            WHERE B.IDCLASSROOM=".$idclassroom." 
                                                            AND B.IDPERIODE=".$idperiode." 
                                                            AND B.IDANNEE=".$idAnnescolaire." 
                                                            AND B.IDINDIVIDU=".$indivClass[$i]." 
                                                            AND B.IDETABLISSEMENT=".$colname_rq_mensualite);

                $query_Indiv_civilite_result = $query_Indiv_civilite->fetchObject();

                $req_check = $dbh->query("SELECT NOM_PERIODE, MOY, MOYENNE_PERIODE.IDPERIODE 
                                                FROM MOYENNE_PERIODE 
                                                INNER JOIN PERIODE ON PERIODE.IDPERIODE = MOYENNE_PERIODE.IDPERIODE 
                                                WHERE IDINDIVIDU='".$indivClass[$i]."' 
                                                AND IDCLASSROOM='".$idclassroom."' 
                                                AND IDANNEE='".$idAnnescolaire."' 
                                                AND MOYENNE_PERIODE.IDETABLISSEMENT='".$colname_rq_mensualite."'");
                $res_check = $req_check->fetchObject();
                $rowcount =$req_check->rowCount();

                $query = $dbh->query("SELECT M.LIBELLE AS MATIERE, D.COEF, D.MOY_CONTROLE,D.COMPOSITION,D.MOYENNE_SEM
                                            FROM DETAIL_BULLETIN D INNER JOIN MATIERE M ON M.IDMATIERE = D.MATIERE
                                            WHERE D.FK_BULLETIN =".$query_Indiv_civilite_result->ROWID);
                $rs_detail = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            catch (PDOException $e)
            {
                echo -2;
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

                <table border="0" align="center" width="100%" cellspacing="0" cellpadding="0">
                    <tbody>

                    <tr>
                        <td align="left" valign="top" class="Titre"><img src="../logo_etablissement/<?php echo $_SESSION['LOGO']; ?>" height="60" /></td>
                        <td colspan="3" align="center" valign="middle">

                        </td>
                        <td colspan="3" align="center" style="font-size: 10px !important; text-align: center !important">
                            <strong><?php echo $_SESSION['SIGLE']; ?></strong><br/>
                            <?php echo $_SESSION['nomEtablissement']; ?><br/>
                            <strong>BP :</strong> <?php echo $_SESSION['BP']; ?> - <strong>Tél: </strong><?php echo $_SESSION['TELEPHONEETAB']; ?><br/>
                        </td>
                        <br>
                    </tr>


                    <tr>
                        <?php
                            if(isset($_GET['idniveau']) && base64_decode($_GET['idniveau']) != null)
                            {
                                if(base64_decode($_GET['idniveau']) == 3){ ?>
                                    <td colspan="10" align="center" valign="middle" class="title"><strong>BULLETIN DE NOTES DU : </strong> <?php echo strtoupper($nom_periode) ;?> </td>
                                <?php }
                                if(base64_decode($_GET['idniveau']) == 2){ ?>
                                    <td colspan="10" align="center" valign="middle" class="title"><strong>COMPOSITION DU : </strong> <?php echo strtoupper($nom_periode) ;?> </td>
                                <?php }
                            }
                        ?>

                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>


                    <tr>
                        <td width="32%">
                            <?php echo '<strong>Prénom et Nom: </strong>'.$query_Indiv_civilite_result->PRENOMS.' '.$query_Indiv_civilite_result->NOM; ?>
                        </td>
                        <td colspan="3">&nbsp;</td>
                        <td colspan="3" align="right">
                            <?php echo '<strong>Matricule : </strong>'. $query_Indiv_civilite_result->MATRICULE ;?>
                        </td>
                    </tr>
                    <tr>
                        <td width="32%">
                            <?php echo '<strong>Classe de : </strong>'. $query_Indiv_civilite_result->LIBELLE; ?>
                        </td>
                        <td colspan="3"></td>
                        <td colspan="3" align="right">
                            <?php echo '<strong>Date de Naissance : </strong>'. $lib->date_franc($query_Indiv_civilite_result->DATNAISSANCE) ;?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <?php
                        if(isset($_GET['idniveau']) && base64_decode($_GET['idniveau']) != null)
                        {
                            if(base64_decode($_GET['idniveau']) == 3){?>
                                <tr>
                                    <td class="sousTitle sousTitleCenter"><strong>Matieres</strong></td>
                                    <td class="sousTitle" width="7%"  align="center" valign="middle" nowrap="nowrap"><strong>Coef</strong></td>
                                    <td class="sousTitle" width="8%" align="center" valign="middle" nowrap="nowrap"><strong>Moy Cont</strong></td>
                                    <td class="sousTitle" width="7%"  align="center" valign="middle" nowrap="nowrap"><strong>Comp</strong></td>
                                    <td class="sousTitle" width="6%" align="center" valign="middle" nowrap="nowrap"><strong>Moy. Sem</strong></td>
                                    <td class="sousTitle" colspan="5" align="center" valign="middle" nowrap="nowrap"><strong>Appreciations des professeurs</strong></td>
                                </tr>

                                <?php foreach ($rs_detail as $rows) { ?>
                                <tr>
                                    <td class="sousTitle"> <?php echo $rows['MATIERE']; ?></td>
                                    <td class="sousTitle" align="center" valign="middle" class="sousTitle"><?php echo $rows['COEF']; ?></td>
                                    <td class="sousTitle" align="center" align="center" valign="middle" nowrap="nowrap"><?php echo number_format((float)$rows['MOY_CONTROLE'], 2, '.', ''); ?></td>
                                    <td class="sousTitle" align="center" valign="middle"><?php echo $rows['COMPOSITION']; ?></td>
                                    <td class="sousTitle" align="center" valign="middle"><?php echo number_format((float)$rows['MOYENNE_SEM'], 2, '.', ''); ?></td>
                                    <td class="sousTitle" colspan="5" align="center" valign="middle">&nbsp;</td>
                                </tr>

                                        <?php } ?>
                                <tr>
                                    <td colspan="10">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="sousTitle" align="left" valign="middle"><strong>TOTAL COEFFICIENTS</strong></td>
                                    <td class="sousTitle" colspan="3" align="center" valign="middle"><?php echo $query_Indiv_civilite_result->TOTAL_COEF;?></td>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="sousTitle" align="left" valign="middle"><strong>TOTAL POINTS</strong></td>
                                    <td class="sousTitle" colspan="3" align="center" valign="middle"><?php echo $query_Indiv_civilite_result->TOTAL_POINT ;?></td>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="sousTitle" align="left" valign="middle"><strong>MOYENNE <?php echo $nom_periode ;?></strong></td>
                                    <td class="sousTitle" colspan="3" align="center" valign="middle"><?php echo $query_Indiv_civilite_result->MOY_GENE ;?></td>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="sousTitle" align="left" valign="middle"><strong>RANG DE L'ELEVE</strong></td>
                                    <td class="sousTitle" colspan="3" align="center" valign="middle"> <?php echo $query_Indiv_civilite_result->RANG ; ?></td>
                                    <td colspan="3">&nbsp;</td>
                                </tr>


                                <tr>
                                    <td colspan="10">&nbsp;</td>
                                </tr>
                                <?php
                                if($rowcount > 0 && $res_check->IDPERIODE != $idperiode){
                                    $name_periode = $res_check->NOM_PERIODE;
                                    $moyenne_ = $res_check->MOY;
                                    $moyenne_annuelle = ($moyenne_ + $query_Indiv_civilite_result->MOY_GENE ) / 2;
                                    ?>
                                    <tr>
                                        <td class="sousTitle" align="left" valign="middle"><strong>MOYENNE <?php echo $name_periode; ?> </strong></td>
                                        <td class="sousTitle" colspan="3" align="center" valign="middle"><?php echo number_format((float)$moyenne_, 2, '.', ''); ?></td>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td class="sousTitle" align="left" valign="middle"><strong>MOYENNE ANNUELLE</strong></td>
                                        <td class="sousTitle" colspan="3" align="center" valign="middle"><?php echo number_format((float)$moyenne_annuelle, 2, '.', ''); ?></td>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>

                        <?php }
                            }
                                elseif (base64_decode($_GET['idniveau']) == 2){?>
                                    <tr>
                                        <td class="sousTitle sousTitleCenter"><strong>Matieres</strong></td>
                                        <td class="sousTitle" width="7%"  align="center" valign="middle" nowrap="nowrap"><strong>Base</strong></td>
                                        <td class="sousTitle" width="8%" align="center" valign="middle" nowrap="nowrap"><strong>Compo 1</strong></td>
                                        <td class="sousTitle" width="7%"  align="center" valign="middle" nowrap="nowrap"><strong>Compo 2</strong></td>
                                        <td class="sousTitle" width="6%" align="center" valign="middle" nowrap="nowrap"><strong>Compo 3</strong></td>
                                        <td class="sousTitle" colspan="5" align="center" valign="middle" nowrap="nowrap"></td>
                                    </tr>
                                <?php }
                        }
                    ?>


                    <tr>
                        <td colspan="10">&nbsp;</td>
                    </tr>


                    <tr>
                        <td class="sousTitle" align="center" valign="middle"><strong>Appreciation du travail</strong></td>
                        <td colspan="1" rowspan="7" align="center" valign="middle">&nbsp;</td>
                        <td class="sousTitle" colspan="3" align="center" valign="middle"><strong>Conduite</strong></td>
                        <td colspan="1" rowspan="7" align="center" valign="middle"></td>
                        <td class="sousTitle" colspan="1"  align="center" valign="middle"><strong>Decision conseil</strong></td>
                    </tr>



                    <tr>
                        <td class="sousTitle" align="left" valign="middle">Félicitations</td>
                        <td class="sousTitle" colspan="3" align="left" valign="middle">Trés bonne</td>
                        <td class="sousTitle" align="left" valign="middle">Passe en Classe Sup</td>
                    </tr>


                    <tr>
                        <td class="sousTitle" align="left" valign="middle">Encouragement</td>
                        <td class="sousTitle" colspan="3" align="left" valign="middle">Bonne</td>
                        <td class="sousTitle" align="left" valign="middle">Autorisé(e) à redoubler</td>
                    </tr>
                    <tr>
                        <td class="sousTitle" align="left" valign="middle">Tableau d'honneur</td>
                        <td class="sousTitle" colspan="3" align="left" valign="middle">A améliorer</td>
                        <td class="sousTitle" align="left" valign="middle">Cours de Vacances obligatoire</td>
                    </tr>
                    <tr>
                        <td class="sousTitle" align="left" valign="middle">Moyen</td>
                        <td class="sousTitle" colspan="3" align="left" valign="middle">Avertissement</td>
                        <td class="sousTitle" align="left" valign="middle">Non repris</td>
                    </tr>
                    <tr>
                        <td class="sousTitle" align="left" valign="middle">Insuffisant</td>
                        <td class="sousTitle" colspan="3" align="left" valign="middle">Blame</td>
                        <td colspan="2" rowspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="sousTitle" align="left" valign="middle">Peut mieux faire</td>
                        <td class="sousTitle" colspan="3" align="left" valign="middle">Risque l'exclusion</td>
                    </tr>
                    <tr>
                        <td colspan="10">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="left" valign="middle"></td>
                        <td colspan="3" align="left" valign="middle"><strong>LA DIRECTION</strong></td>
                    </tr>

                    </tbody>
                </table>
            </page>


            <?php
        }
    }
?>


