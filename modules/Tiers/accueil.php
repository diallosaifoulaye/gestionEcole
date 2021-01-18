
<?php
if (!isset($_SESSION)){
    session_start();
}

require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1)
    $lib->Restreindre($lib->Est_autoriser(35,$lib->securite_xss($_SESSION['profil'])));

require_once('../Parametrage/classe/ProfileManager.php');
$profiles=new ProfileManager($dbh,'profil');
$profil = $profiles->getProfiles();


$query_rq_profil = $dbh->query("SELECT IDTYPEINDIVIDU, LIBELLE, IDETABLISSEMENT FROM TYPEINDIVIDU WHERE IDTYPEINDIVIDU in (7, 8, 9)");
$query_rq_profil2 = $dbh->query("SELECT IDTYPEINDIVIDU, LIBELLE, IDETABLISSEMENT FROM TYPEINDIVIDU WHERE IDTYPEINDIVIDU in (7, 8)");
$query_etab = "SELECT IDANNEESSCOLAIRE, LIBELLE_ANNEESSOCLAIRE, `DATE_DEBUT`, `DATE_FIN`, `ETAT`, `IDETABLISSEMENT` FROM `ANNEESSCOLAIRE` WHERE IDANNEESSCOLAIRE=".$lib->securite_xss($_SESSION["ANNEESSCOLAIRE"]);
$stmt = $dbh->prepare($query_etab);
$stmt->execute();
$result = $stmt->fetchObject();

$colname_rq_individu = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_individu = $lib->securite_xss($_SESSION['etab']);
}




$colname_type=" ";

if(isset($_POST['idProfil']) && $_POST['idProfil']!="" ) {
    $colname_type = "  AND INDIVIDU.IDTYPEINDIVIDU=" . $lib->securite_xss($_POST['idProfil']. " ORDER BY PRENOMS, NOM ASC");
} else{
    $colname_type = "  ORDER BY PRENOMS, NOM ASC";
}
$query_rq_individu = $dbh->query("SELECT `IDINDIVIDU`,`MATRICULE`, `NOM`, `PRENOMS`, `DATNAISSANCE`, `ADRES`, `TELMOBILE`, `TELDOM`, `COURRIEL`, `LOGIN`, `MP`, `CODE`, `BIOGRAPHIE`, `PHOTO_FACE`, INDIVIDU.IDETABLISSEMENT, INDIVIDU.IDTYPEINDIVIDU, `SEXE`, `IDTUTEUR`, `ANNEEBAC`, `NATIONNALITE`, `SIT_MATRIMONIAL`, `NUMID`, `IDSECTEUR`, TYPEINDIVIDU.LIBELLE AS TYPE FROM `INDIVIDU` INNER JOIN TYPEINDIVIDU ON INDIVIDU.IDTYPEINDIVIDU = TYPEINDIVIDU.IDTYPEINDIVIDU WHERE INDIVIDU.IDETABLISSEMENT = ".$colname_rq_individu." ".$colname_type);


$query_rq_tuteur = $dbh->query("SELECT INDIVIDU.MATRICULE, INDIVIDU.NOM, INDIVIDU.PRENOMS, INDIVIDU.ADRES, INDIVIDU.TELMOBILE, INDIVIDU.COURRIEL, TYPEINDIVIDU.IDTYPEINDIVIDU, TYPEINDIVIDU.LIBELLE, INDIVIDU.IDINDIVIDU, INDIVIDU.PHOTO_FACE, INDIVIDU.LIEU_TRAVAIL FROM INDIVIDU, TYPEINDIVIDU WHERE INDIVIDU.IDTYPEINDIVIDU=TYPEINDIVIDU.IDTYPEINDIVIDU AND INDIVIDU.IDTYPEINDIVIDU=9 AND INDIVIDU.IDETABLISSEMENT=$colname_rq_individu");

$secteurActivite = $dbh->query("SELECT `IDSECTEUR`, `LIBELLE` FROM `secteur_activite`");


include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">TIERS</a></li>
    <li>Liste des individus</li>
</ul>
<!-- END BREADCRUMB -->
<script>
    function myEfface()
    {
        (!$('#mceu_29').hasClass("hidden")) ? $('#mceu_29').addClass("hidden") : null ;
    }
</script>
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

    <!-- START WIDGETS -->
    <div class="row">

        <div class="panel panel-default">
            <div class="panel-heading">
                &nbsp;&nbsp;&nbsp;

                <div class="btn-group pull-right">
                    <button data-toggle="modal" data-target="#ajouter"
                            onmouseover="$(document).ready(myEfface());" style="background-color:#DD682B" class='btn dropdown-toggle', aria-hidden='true' >
                        <i class="fa fa-plus"></i> Nouvel individu</button>

                </div>

            </div>
            <div class="panel-body">

                <?php  if(isset($_GET['msg']) && $_GET['msg']!= ''){

                    if(isset($_GET['res']) && $_GET['res']==1)
                    {?>
                        <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php  }
                    if(isset($_GET['res']) && $_GET['res']!=1)
                    {?>
                        <div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } elseif (isset($_GET['msg1']) &&  $_GET['msg1']!= ''){   ?>

                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $lib->securite_xss($_GET['msg1']); ?>
                    </div>
                <?php } ?>

                <form id="form" name="form1" method="post" action="accueil.php" class="form-inline">
                    <fieldset class="cadre"><legend> FILTRE</legend>


                        <div class="form-group col-lg-6">
                            <div class="col-lg-2"> <label>Type d'individu</label></div>
                            <div class="col-lg-4">
                                <select name="idProfil" id="idProfil" class="form-control selectpicker" data-live-search="true">
                                    <option value="">--Selectionner--</option>
                                    <?php foreach ($query_rq_profil->fetchAll() as $prof) { ?>

                                        <option value="<?php echo $prof['IDTYPEINDIVIDU']; ?>"  <?php if($prof['IDTYPEINDIVIDU']==$_POST['idProfil']) echo "selected"; ?>><?php echo $prof['LIBELLE']; ?></option>

                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group col-lg-offset-1 col-lg-1">

                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>

                    </fieldset>
                </form>

                <table id="customers2" class="table datatable">

                    <thead>
                    <tr>

                        <th>MATRICULE</th>
                        <th>PRENOM</th>
                        <th>NOM</th>
                        <th>TELEPHONE MOBILE</th>
                        <th>TYPE INDIVIDU</th>
                        <th>DETAIL</th>

                    </tr>
                    </thead>


                    <tbody>

                    <?php foreach ($query_rq_individu->fetchAll() as $individu){ ?>

                        <tr>

                            <td><a href=""><?php echo $individu['MATRICULE']; ?></a></td>
                            <td><?php echo $individu['PRENOMS']; ?></td>
                            <td><?php echo $individu['NOM']; ?></td>
                            <td><?php echo $individu['TELMOBILE']; ?></td>
                            <td><?php echo $individu['TYPE']; ?></td>

                            <td><a href="detailIndividu.php?idIndividu=<?php echo base64_encode($individu['IDINDIVIDU']); ?>"><i class="glyphicon glyphicon-search"></i></a></td>

                        </tr>

                    <?php }  ?>

                    </tbody>
                </table>
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

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="ajouter" data-keyboard="false" data-backdrop="static" >
    <script>
        var test = false;
        var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');

        function validateMail() {
            var mailteste = $('#COURRIEL');
            if ((mailteste.val()).trim()!= "" && reg.test(mailteste.val())) {
                $.ajax({
                    type: "POST",
                    url: "testerEmail.php",
                    data: "COURRIEL=" + mailteste.val(),
                    success: function (data) {
                        if (data == 1) {
                            //email existant
                            $(".infoEmail").removeClass("hidden");
                            mailteste.addClass("invalide");
                            $('#validerAj').addClass("disabled");
                            test = false;
                        } else {
                            //email inexistant
                            $(".infoEmail").addClass("hidden");
                            mailteste.removeClass("invalide");
                            $('#validerAj').removeClass("disabled");
                            test = true;
                        }
                    }
                });
            } else {
                mailteste.addClass("invalide");
                $('#validerAj').addClass("disabled");
                test = false;
                alert("Veuillez saisir un email valide")
            }
        }

        function validateType()
        {
            var valSel = $('#IDTYPEINDIVIDU')[0].value;
            if(valSel != "non-sel"){
                $.ajax({
                    type: "POST",
                    url: "genereMatricule.php",
                    data: "IDTYPEINDIVIDU=" + valSel,
                    success: function (data) {
                     data=JSON.parse(data);
                        document.getElementById('MATRICULE').value=data;
                        if(valSel==8){
                            document.getElementById('MATRICULETUTEUR').value='CEMAD'+data;
                            document.getElementById("recto").style.display= "inline";
                            document.getElementById("verso").style.display= "inline";
                            document.getElementById("patho").style.display= "inline";
                            document.getElementById("fildProf").style.display= "none";
                            document.getElementById("pere").style.display= "inline";
                            document.getElementById("mere").style.display= "inline";

                        }else{
                            document.getElementById("recto").style.display= "none";
                            document.getElementById("verso").style.display= "none";
                            document.getElementById("patho").style.display= "none";
                            document.getElementById("fildProf").style.display= "inline";
                            document.getElementById("pere").style.display= "none";
                            document.getElementById("mere").style.display= "none";
                        }
                        (test) ? $('#validerAj').removeClass("disabled") : null ;
                    }
                });


            }else {
                alert("Veuillez choisir un type d'individu valide");
            }
        }

        function validateFormOver()
        {
            var valSel = $('#IDTYPEINDIVIDU')[0].value;
            var mailteste = $('#COURRIEL').val();
            var nom = $('#NOM').val();
            var prenom = $('#PRENOMS').val();
            var telmobile = $('#TELMOBILE').val();
           // alert(nom);
            if(valSel == "non-sel" && reg.test(mailteste)){
                $('#validerAj').addClass("disabled");
                alert("Veuillez choisir un type d'individu valide");
            }else if(valSel != "non-sel" && !reg.test(mailteste) ){
                $('#validerAj').addClass("disabled");
                alert("Veuillez saisir un courriel valide")
            }else if(valSel == "non-sel" && !reg.test(mailteste) && nom=='' && prenom=='' && telmobile=='') {
                $('#validerAj').addClass("disabled");
                alert("Veuillez choisir un type d'individu et saisir un courriel valide nom, prénom, téléphone mobile")
             }
        }
    </script>
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="panel-title text-center"> Nouvel individu </h3>
                </div>
                <form action="ajouterIndividu.php" method="POST" id="form" name="form" enctype="multipart/form-data">
                    <div class="panel-body">
                        <div class="row">

                            <?php $leCodebarre = $lib->generer_code_barre(); ?>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>Type individu : </label>
                                    <div>
                                        <select name="IDTYPEINDIVIDU" id="IDTYPEINDIVIDU" class="form-control"  required  onChange="validateType();tuteur();">
                                            <option value="non-sel">--Selectionner--</option>

                                            <?php foreach ($query_rq_profil2->fetchAll() as $profile){ ?>

                                                <option value="<?php echo $profile['IDTYPEINDIVIDU']; ?>"><?php echo $profile['LIBELLE']; ?> </option>

                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>


                            <fieldset class="cadre"> <legend >INFORMATIONS GENERALES</legend>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>MATRICULE</label>
                                        <div>
                                            <input type="text" name="MATRICULE" id="MATRICULE" required class="form-control" readonly/>
                                            <input type="hidden" name="CODE" id="CODE" required class="form-control" value="<?php echo $leCodebarre; ?>" readonly/>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>PRENOMS</label>
                                        <div>
                                            <input type="text" name="PRENOMS" id="PRENOMS" required class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>NOM</label>
                                        <div>
                                            <input type="text" name="NOM" id="NOM" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>DATE DE NAISSANCE</label>
                                        <div>
                                            <input type="text" name="DATNAISSANCE" id="date_foo" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>LIEU DE NAISSANCE</label>
                                        <div>
                                            <input type="text" name="LIEU_NAISSANCE" id="LIEU_NAISSANCE"  class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div id="pere" class="col-xs-12" style="display: none">
                                    <div class="form-group">
                                        <label>PÈRE</label>
                                        <div>
                                            <input type="text" name="PERE" id="PERE" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div id="mere" class="col-xs-12" style="display: none">
                                    <div class="form-group">
                                        <label>MÈRE</label>
                                        <div>
                                            <input type="text" name="MERE" id="MERE" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>ADRESSE</label>
                                        <div>
                                            <input type="text" name="ADRES" id="ADRES" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>TELEPHONE MOBILE</label>
                                        <div>
                                            <input type="number" name="TELMOBILE" id="TELMOBILE" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>SEXE</label>
                                        <div>
                                            <input type="radio" name="SEXE" value="1" />&nbsp;Masculin
                                            &nbsp;&nbsp;&nbsp;<input name="SEXE" type="radio" value="0" checked="checked" />&nbsp;Feminin
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>TELEPHONE DOMICILE</label>
                                        <div>
                                            <input type="number" name="TELDOM" id="TELDOM"  class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>COURRIEL</label>
                                        <div>
                                            <input type="text" onchange="validateMail();" name="COURRIEL" id="COURRIEL" required class="form-control"/>
                                        </div>
                                        <div class="hidden infoEmail" style="color: red;">Cet email existe déja !</div>
                                    </div>
                                </div>



                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>BIOGRAPHIE</label>
                                        <div>
                                            <textarea  name="BIOGRAPHIE" id="mytextarea"  class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>ANNEE BAC</label>
                                        <div>
                                            <input type="number" name="ANNEEBAC" id="ANNEEBAC"  class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label >PAYS</label>
                                        <div>
                                            <select name="NATIONNALITE" id="NATIONNALITE">
                                                <option value="22">SENEGAL</option>

                                                <?php
                                                $requete= $dbh->query("select ROWID, LIBELLE from PAYS");
                                                foreach ( $requete->fetchAll() as $row_rq_pays){
                                                    ?>
                                                    <option value="<?php echo $row_rq_pays['ROWID']?>"><?php echo $row_rq_pays['LIBELLE']?></option>
                                                    <?php
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label >SITUATION MATRIMONIALE</label>
                                        <div>
                                            <select name="SIT_MATRIMONIAL" id="SIT_MATRIMONIAL">
                                                <option value="">--Selectionner--</option>
                                                <option value="Celibataire sans enfant">C&eacute;libataire sans enfant</option>
                                                <option value="Celibataire avec enfant">C&eacute;libataire avec enfant</option>
                                                <option value="Marié(e) sans enfant">Mari&eacute;(e) sans enfant</option>
                                                <option value="Marié(e) avec enfant">Mari&eacute;(e) avec enfant</option>
                                                <option value="Veuf(ve) sans enfant">Veuf(ve) sans enfant</option>
                                                <option value="Veuf(ve) avec enfant">Veuf(ve) avec enfant</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label >NUMERO IDENTIFICATION</label>
                                        <div>
                                            <input type="text" name="NUMID" id="NUMID" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">


                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label >PHOTO</label>
                                            <div>
                                                <input type="file" name="PHOTO_FACE" id="PHOTO_FACE"  class="form-control"/>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4" id="recto" style="display: none">
                                        <div class="form-group">
                                            <label >FICHE INSCRIPTION RECTO</label>
                                            <div>
                                                <input type="file" name="FICHE_RECTO" id="FICHE_RECTO"  class="form-control"/>

                                            </div>
                                        </div>
                                    </div> <div class="col-xs-4" id="verso" style="display: none">
                                        <div class="form-group">
                                            <label >FICHE INSCRIPTION VERSO</label>
                                            <div>
                                                <input type="file" name="FICHE_VERSO" id="FICHE_VERSO"  class="form-control"/>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                <div class="col-xs-12" id="patho" style="display: none">
                                    <div class="col-xs-4" >
                                        <div class="form-group">
                                            <label >PATHOLOGIE</label>
                                            <div>
                                                <textarea  name="PATHOLOGIE" id="mytextarea1"  class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4" >
                                        <div class="form-group">
                                            <label >ALLERGIE</label>
                                            <div>
                                                <textarea  name="ALLERGIE" id="mytextarea2"  class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div> <div class="col-xs-4">
                                        <div class="form-group">
                                            <label >MEDECIN TRAITANT</label>
                                            <div>
                                                <textarea  name="MEDECIN_TRAITANT" id="mytextarea3"  class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div> <div class="col-xs-12" id="patho" style="display: none">
                                    <div class="col-xs-4" >
                                        <div class="form-group">
                                            <label >PATHOLOGIE</label>
                                            <div>
                                                <textarea  name="PATHOLOGIE" id="mytextarea1"  class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4" >
                                        <div class="form-group">
                                            <label >ALLERGIE</label>
                                            <div>
                                                <textarea  name="ALLERGIE" id="mytextarea2"  class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div> <div class="col-xs-4">
                                        <div class="form-group">
                                            <label >MEDECIN TRAITANT</label>
                                            <div>
                                                <textarea  name="MEDECIN_TRAITANT" id="mytextarea3"  class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>


                            </fieldset>
                            <div class="row" id="fildProf" style="display:none;">
                                <fieldset class="cadre"> <legend >DIPLOME ET DATE D'OBTENTION</legend>
                                    <div class="col-xs-12">
                                        <div class="col-xs-4" >
                                            <div class="form-group">
                                                <label >DIPLOMES</label>
                                                <div>
                                                    <textarea  name="DIPLOMES" id="mytextarea4"  class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4" >
                                            <div class="form-group">
                                                <label >DISCIPLINE</label>
                                                <div>
                                                    <textarea  name="DISCIPLINE" id="mytextarea5"  class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div> <div class="col-xs-4">
                                            <div class="form-group">
                                                <label >ANNEE</label>
                                                <div>
                                                    <textarea  name="ANNEE" id="mytextarea6"  class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>


                                </fieldset>

                                <fieldset class="cadre"> <legend >SITUATION ADMINISTRATIVE</legend>
                                    <div class="col-xs-12">
                                        <div class="col-xs-3" >
                                            <div class="form-group">
                                                <label >DATE D'ARRIVEE AU CEMAD</label>
                                                <div>
                                                    <input type="text" name="DATE_ARRIVE_CEMAD" id="date_foo2" required class="form-control"/>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3" >
                                            <div class="form-group">
                                                <label >FILIERE(S) ENSEIGNEE(S)</label>
                                                <div>
                                                    <textarea  name="FILIERE_ENSEIGNE"  class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                    <label >NIVEAU OU CYCLE</label>
                                                    <div>
                                                        <select name="ID_NIVEAU" id="ID_NIVEAU" class="selectpicker"  data-live-search="true">
                                                            <option value="">-- Selectionner --</option>
                                                            <?php
                                                            $requete= $dbh->query("SELECT `IDNIVEAU`, `LIBELLE` FROM `NIVEAU`");
                                                            foreach ( $requete->fetchAll() as $row_rq_niv){
                                                                ?>
                                                                <option value="<?php echo $row_rq_niv['IDNIVEAU']?>"><?php echo $row_rq_niv['LIBELLE']?></option>
                                                                <?php
                                                            }
                                                            ?>

                                                        </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-3" >
                                            <div class="form-group">
                                                <label >DUREE DANS L'ENSEIGNEMENT</label>
                                                <div>
                                                    <input type="number" name="DUREE_ENSEIGNEMENT" min="0" id="DUREE_ENSEIGNEMENT" required class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </fieldset>


                                <fieldset class="cadre"> <legend >ENGAGEMENT ANNEE SCOLAIRE <?php echo $result->LIBELLE_ANNEESSOCLAIRE;?></legend>
                                    <div class="col-xs-12">
                                        <div class="col-xs-6" >
                                            <div class="form-group">
                                                <label >VOUS ENGAGEZ VOUS POUR <?php echo $result->LIBELLE_ANNEESSOCLAIRE;?></label>
                                                    <div class="form-group">
                                                         <input type="radio" name="ENGAGEMENT" value="1" />&nbsp;OUI
                                                        &nbsp;&nbsp;&nbsp;<input name="ENGAGEMENT" type="radio" value="0" checked="checked" />&nbsp;NON
                                                    </div>

                                            </div>
                                        </div>
                                        <div class="col-xs-6" >
                                            <div class="form-group">
                                                <label >QUELS SONT LES RAISONS? </label>
                                                <div>
                                                    <textarea  name="RAISON_ENGAGEMENT" id="mytextarea7"  class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>



                                </fieldset>


                            </div>



                            <!-- IDINDIVIDU MATRICULE NOM PRENOMS DATNAISSANCE ADRES TELMOBILE TELDOM COURRIEL LOGIN MP CODE BIOGRAPHIE PHOTO_FACE IDETABLISSEMENT IDTYPEINDIVIDU SEXE IDPERE IDMERE IDTUTEUR
                  ANNEEBAC NATIONNALITE SIT_MATRIMONIAL NUMID -->

                            <div class="row" id="tuteur" style="visibility: hidden;display:none;">
                                <fieldset class="cadre"><legend> TUTEUR</legend>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <input type="radio" name="type" value="oui" onclick="tuteur2();">&nbsp;&nbsp;existant</input> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="type" value="non" onclick="tuteur2();">&nbsp;&nbsp;inexistant</input>
                                        </div>
                                    </div>

                                </fieldset>


                            </div>

                            <div class="row" id="tuteur2" style="visibility: hidden;display:none;">
                                <div class="form-group col-lg-12">
                                    <label >TUTEUR</label>
                                    <select name="idParent" id="idParent" class="form-control selectpicker"  data-live-search="true">
                                        <option value="">--Selectionner--</option>
                                        <?php foreach ($query_rq_tuteur->fetchAll() as $prof){ ?>
                                            <option value="<?php echo $prof['IDINDIVIDU'];?>" ><?php echo $prof['MATRICULE']."  ".$prof['PRENOMS']."  ".$prof['NOM']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                               <!-- <div class="col-xs-12">
                                    <div class="form-group">
                                        <input type="radio" name="LIEN_PARENTE" value="1" >&nbsp;&nbsp;PERE</input> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="radio" name="LIEN_PARENTE" value="2">&nbsp;&nbsp;MERE</input>
                                        <input type="radio" name="LIEN_PARENTE" value="3" >&nbsp;TUTEUR</input>
                                    </div>
                                </div>-->
                            </div>
                            <div class="row" id="tuteur3" style="visibility: hidden;display:none;">
                                <?php
                                $codebarreTuteur = $lib->generer_code_barre();
                               // $matriculeTuteur = $_SESSION['PREFIXE'].$leMatricule;
                                ?>

                                <fieldset class="cadre"> <legend >INFORMATIONS TUTEUR</legend>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <input type="radio" name="LIEN_PARENTE" value="1" >&nbsp;&nbsp;PERE</input> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="LIEN_PARENTE" value="2">&nbsp;&nbsp;MERE</input>
                                            <input type="radio" name="LIEN_PARENTE" value="3" >&nbsp;&nbsp;TUTEUR</input>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >MATRICULE</label>
                                            <div>
                                                <input type="text" name="MATRICULETUTEUR" id="MATRICULETUTEUR" required class="form-control"  readonly/>
                                                <input type="hidden" name="CODETUTEUR" id="CODETUTEUR" required class="form-control" value="<?php echo $codebarreTuteur; ?>" readonly/>

                                            </div>
                                        </div>
                                    </div>

                                  <!--  <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >CODE BARRE</label>
                                            <div>
                                                <input type="text" name="CODETUTEUR" id="CODETUTEUR" required class="form-control" value="<?php /*echo $codebarreTuteur; */?>" readonly/>
                                            </div>
                                        </div>
                                    </div>-->

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >NOM TUTEUR</label>
                                            <div>
                                                <input type="text" name="NOMTUTEUR" id="NOMTUTEUR" required class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >PRENOMS TUTEUR</label>
                                            <div>
                                                <input type="text" name="PRENOMSTUTEUR" id="PRENOMSTUTEUR" required class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >TEL MOBILE TUTEUR</label>
                                            <div>
                                                <input type="text" name="TELMOBILETUTEUR" id="TELMOBILETUTEUR" required class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >TEL DOMICILE</label>
                                            <div>
                                                <input type="text" name="TELDOMTUTEUR" id="TELDOMTUTEUR"  class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >COURRIEL TUTEUR</label>
                                            <div>
                                                <input type="text" name="COURRIELTUTEUR" id="COURRIELTUTEUR" required class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>SECTEUR D'ACTIVITE</label>
                                            <select name="IDSECTEUR" class="form-control selectpicker"  data-live-search="true">
                                                <option value="">--Selectionner--</option>
                                                <?php foreach ($secteurActivite->fetchAll(PDO::FETCH_OBJ) as $oneSect) { ?>
                                                    <option value="<?= $oneSect->IDSECTEUR; ?>"><?= $oneSect->LIBELLE; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label >LIEU DE TRAVAIL</label>
                                            <div>
                                                <input type="text" name="LIEU_TRAVAIL" id="LIEU_TRAVAIL" required class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="LOGINTUTEUR" value="" />
                                    <input type="hidden" name="MPTUTEUR" value="<?php echo $lib->mot_de_passe(); ?>" />

                                </fieldset>

                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="reset" class="btn btn-danger">R&eacute;initialiser</button>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $_SESSION['etab']; ?>" />
                        <input type="hidden" name="LOGIN" value="" />
                        <input type="hidden" name="MP" value="<?php echo $lib->mot_de_passe(); ?>" />
                        <button onmouseover="validateFormOver();" type="submit" id="validerAj" class="btn btn-primary pull-right">Valider</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <style>
        .invalide {
            box-shadow: 0 0 1em red;
        }
    </style>
</div>
<?php include('footer.php'); ?>