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
    $lib->Restreindre($lib->Est_autoriser(22, $lib->securite_xss($_SESSION['profil'])));


$colname_rq_etablissement = "-1";
if (isset($_SESSION['etab'])) {
    $colname_rq_etablissement = $lib->securite_xss($_SESSION['etab']);
}

$colname_rq_periode = "-1";
if (isset($_POST['IDPERIODE'])) {
    $colname_rq_periode = $lib->securite_xss($_POST['IDPERIODE']);
}

$colname_rq_idniveau = "-1";
if (isset($_POST['IDNIVEAU'])) {
    $colname_rq_idniveau = $lib->securite_xss($_POST['IDNIVEAU']);
}

$colname_rq_classe = "-1";
if (isset($_POST['IDCLASSROOM'])) {
    $colname_rq_classe = $lib->securite_xss($_POST['IDCLASSROOM']);
}

try
{
    $query_rq_classe=$dbh->query("SELECT IDCLASSROOM, LIBELLE, IDNIVEAU, IDETABLISSEMENT, IDSERIE 
                                        FROM CLASSROOM 
                                        WHERE IDETABLISSEMENT = " . $colname_rq_etablissement." 
                                        AND IDCLASSROOM=".$colname_rq_classe);
    $row_rq_classe=$query_rq_classe->fetchObject();
    $nomclasse=$row_rq_classe->LIBELLE;

    $query_rq_matiere = $dbh->query("SELECT IDMATIERE, LIBELLE, IDETABLISSEMENT 
                                          FROM MATIERE 
                                          WHERE IDETABLISSEMENT = " . $colname_rq_etablissement." 
                                          AND IDNIVEAU=".$colname_rq_idniveau);

    $query_rq_salle = $dbh->query("SELECT IDSALL_DE_CLASSE, NOM_SALLE, IDTYPE_SALLE, IDETABLISSEMENT, NBR_PLACES 
                                         FROM SALL_DE_CLASSE 
                                         WHERE IDETABLISSEMENT = " . $colname_rq_etablissement);


    $query_rq_prof = $dbh->query("SELECT INDIVIDU.IDINDIVIDU, MATRICULE, NOM, PRENOMS, DATNAISSANCE, ADRES, TELMOBILE, TELDOM, COURRIEL, LOGIN, MP, 
                                        CODE, BIOGRAPHIE, PHOTO_FACE, INDIVIDU.IDETABLISSEMENT, IDTYPEINDIVIDU, SEXE, IDTUTEUR, LIEN_PARENTE, ANNEEBAC, NATIONNALITE, 
                                        SIT_MATRIMONIAL, NUMID,  IDSECTEUR, LIEU_TRAVAIL, PATHOLOGIE, ALLERGIE, MEDECIN_TRAITANT, FICHE_RECTO, FICHE_VERSO 
                                        FROM INDIVIDU 
                                        INNER JOIN RECRUTE_PROF ON RECRUTE_PROF.IDINDIVIDU=INDIVIDU.IDINDIVIDU 
                                        INNER JOIN CLASSE_ENSEIGNE ON CLASSE_ENSEIGNE.IDRECRUTE_PROF=RECRUTE_PROF.IDRECRUTE_PROF  
                                        WHERE INDIVIDU.IDETABLISSEMENT = " . $colname_rq_etablissement . " 
                                        AND IDTYPEINDIVIDU=7 
                                        AND CLASSE_ENSEIGNE.IDCLASSROM=".$colname_rq_classe);
}
catch (PDOException $e)
{
    echo -2;
}

if(!isset($_POST['IDPERIODE']) && !isset($_POST['IDPERIODE']) )
    header("Location:accueil.php?msg=Nouveau emploi du temps ajouté avec succes&res=1");

?>


<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Emploi du Temps</a></li>
    <li>Emploi du temps</li>
</ul>
<!-- END BREADCRUMB -->

<div class="page-content-wrap">

    <!-- START WIDGETS -->
    <div class="row">

        <div class="panel panel-default">
            <div class="panel-heading">
                &nbsp;&nbsp;&nbsp;<div class="btn-group pull-right">

                 </div>

            </div>
            <form id="form1" name="form1" onsubmit="return valideForm();" >
            <div class="panel-body">

                <?php if (isset($_GET['msg']) && $_GET['msg'] != '') {

                    if (isset($_GET['res']) && $_GET['res'] == 1) {
                        ?>
                        <div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }
                    if (isset($_GET['res']) && $_GET['res'] != 1) {
                        ?>
                        <div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <?php echo $lib->securite_xss($_GET['msg']); ?></div>
                    <?php }
                    ?>

                <?php } ?>

                <fieldset class="cadre">
                    <legend>Ajout Emploi du temps <?php echo $nomclasse;?></legend>
                        <div class="col-lg-6">

                            <label>Professeur:</label>
                            <select id="IDINDIVIDU" class="validate[required] form-control selectpicker" data-live-search="true"  onchange="getMatiere();" >
                                <option value="">--Selectionner--</option>
                                <?php foreach ($query_rq_prof->fetchAll() as $row_rq_prof) { ?>
                                    <option
                                            value="<?= $row_rq_prof['PRENOMS'] . "  " . $row_rq_prof['NOM']."-".$row_rq_prof['IDINDIVIDU']; ?>"><?php echo $row_rq_prof['PRENOMS'] . "  " . $row_rq_prof['NOM']; ?></option>
                                <?php } ?>
                            </select>

                        </div>

                        <div class="col-lg-6">
                            <label>Matiere:</label>
                            <select id="IDMATIERE"  class="validate[required] form-control">
                                <option value="">--Selectionner--</option>
                            </select>

                        </div>
                        <div class="col-lg-6">
                            <label>Salle:</label>
                            <select id="IDSALL_DE_CLASSE" class="validate[required] form-control selectpicker" data-live-search="true"  >
                                <option value="">--Selectionner--</option>
                                <?php foreach ($query_rq_salle->fetchAll() as $row_rq_salle) { ?>
                                    <option
                                        value="<?= $row_rq_salle['NOM_SALLE']."-".$row_rq_salle['IDSALL_DE_CLASSE']; ?>"><?php echo $row_rq_salle['NOM_SALLE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label>Jour du Cours:</label>
                            <select id="JOUR" class="validate[required] form-control selectpicker"  data-live-search="true" >
                                <option value="">--Selectionner--</option>
                                <option value="LUNDI">LUNDI</option>
                                <option value="MARDI">MARDI</option>
                                <option value="MERCREDI">MERCREDI</option>
                                <option value="JEUDI">JEUDI</option>
                                <option value="VENDREDI">VENDREDI</option>
                                <option value="SAMEDI">SAMEDI</option>
                            </select>
                        </div>

                            <div class="col-lg-6">
                                <label>Heure de debut du Cours:</label>
                                <select id="HEUREDEBUT" class="validate[required] form-control selectpicker"  data-live-search="true" >
                                    <option value="">--Selectionner--</option>
                                    <option value="08:30">08:30</option>
                                    <option value="09:30">09:30</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:30">13:30</option>
                                    <option value="14:30">14:30</option>
                                    <option value="15:30">15:30</option>
                                </select>

                            </div>
                            <div class="col-lg-6">
                                <label>heure de fin du Cours:</label>

                                <select id="HEUREFIN" class="validate[required] form-control selectpicker"  data-live-search="true" >
                                    <option value="">--Selectionner--</option>
                                    <option value="09:30">09:30</option>
                                    <option value="10:30">10:30</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:30">14:30</option>
                                    <option value="15:30">15:30</option>
                                    <option value="16:30">16:30</option>
                                </select>
                                <input type="hidden" value="<?php echo $colname_rq_periode ?>" id="periode"/>

                            </div>


                        <div class="col-lg-12"><br><br>
                            <div class="col-lg-3 pull-left" style="text-align: left">

                            </div>
                            <div class="col-lg-6">&nbsp;</div>

                            <div class="col-lg-3 pull-right" style="text-align: right"><input onclick="insertET(); " type="button" id="valider" value="Ajouter" class=" btn btn-success"/></div>
                        </div>
                </fieldset>

                <br/>
                <div id="alert" class="alert alert-danger hidden">
                    Ajout impossible ! Collision entre les heures de cours.
                </div>
                <br/>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th width="8%">JOUR</th>
                                    <th width="8%">HORRAIRE</th>
                                    <th width="25%">MATIERE</th>
                                    <th width="15%">SALLE</th>
                                    <th width="15%">PROFESSEUR</th>
                                    <th style="text-align: center;" width="6%">SUPPRIMER</th>
                                </tr>
                            </thead>
                            <tbody id="tabET" >

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6 col-lg-offset-5"><br>
                    <input type="submit" onclick="postForm('<?= $lib->securite_xss($_POST['IDPERIODE']); ?>','<?= $lib->securite_xss($_SESSION['etab']); ?>','<?= $lib->securite_xss($_POST['IDCLASSROOM']); ?>');" value="Valider" class="col-lg-3 btn btn-success"/>
                </div>
                <script>
                    function getMatiere() {

                        var valSel =$("#IDINDIVIDU").val();
                        $.ajax({
                            type: "POST",
                            url: "getMatiere.php",
                            data: "IDINDIVIDU=" + valSel,
                            success: function (data) {
                                data = JSON.parse(data);
                                $("#IDMATIERE").html('<option selected="selected" value="">--Selectionner--</option>');
                                $.each(data, function (cle, valeur) {
                                    $("#IDMATIERE").append('<option value="' + valeur.LIBELLE + '-' + valeur.IDMATIERE + '">' + valeur.LIBELLE + '</option>');
                                });
                            }
                        });
                    }

                    var tabET = [];

                    function postForm(idPer, idEtab, idClass) {
                        $.ajax({
                            type: "POST",
                            url: "validerET.php?IDPERIODE="+idPer+"&IDETABLISSEMENT="+idEtab+"&IDCLASSROOM="+idClass,
                            data: "tabET="+tabET.toString(),
                            success: function (data) {
//                                if(data==1){
//
//                                }else {
//
//                                }
                            }
                        });
                    }

                    function valideForm() {
                        return tabET.length!=0;
                    }

                    function getVal(val) {
                        return val.substring(0,val.indexOf("-"));
                    }

                    function getId(val) {
                        return (val.substring(val.indexOf("-") + 1,val.length));
                    }

                    function aleatoire() {
                        var liste = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9"];
                        var result = '';
                        for (var i = 0; i < 5; i++) {
                            result += liste[Math.floor(Math.random() * liste.length)];
                        }
                        return result;
                    }
                    function supprimer(elem) {
                        elem = elem.parentNode.parentNode;
                        var classe = elem.className;
                        for (var i = 0; i < tabET.length; i++) {
                            if(tabET[i].startsWith(classe)){
                                tabET.splice(i, 1);
                            }
                        }
                        elem.remove();
                    }

                    function insertET() {

                        var matiere = $("#IDMATIERE")[0].value;
                        var prof = $("#IDINDIVIDU")[0].value;
                        var salle = $("#IDSALL_DE_CLASSE")[0].value;
                        var jour = $("#JOUR")[0].value;
                        var heuredebut = $("#HEUREDEBUT")[0].value;
                        var heurefin = $("#HEUREFIN")[0].value;
                        var periode = document.getElementById("periode").value;

                        if( matiere == "" || prof == "" || salle == "" || jour == "" || heuredebut == "" || heurefin == ""){
                            alert ("Vous avez omis de renseigner un champ !");
                        }

                        var IDPROFESSEUR = $("#IDINDIVIDU").find("option:selected").val();
                        var IDPROFESSEUR = IDPROFESSEUR.substr(-1);
                        var IDMATIERE = $("#IDMATIERE").find("option:selected").val();
                        var IDMATIERE = IDMATIERE.substr(-1);
                        var IDJOUR = $("#JOUR").find("option:selected").val();
                        var IDJOUR = IDJOUR.substr(0,3);
                        var IDHEUREDEB = $("#HEUREDEBUT").find("option:selected").val();
                        var IDHEUREFIN = $("#HEUREFIN").find("option:selected").val();
                        var INTERVALLETEMPS = parseInt(IDHEUREFIN[1]) - parseInt(IDHEUREDEB[1])

                        if(IDPROFESSEUR !=" " && IDMATIERE !=" " && IDJOUR !=" " && IDHEUREDEB !=" " && IDHEUREFIN !=" " ) {
                            //{
                                $.ajax({
                                    type: "POST",
                                    url: "ProfDejaPris.php",
                                    data: "pass="  + IDPROFESSEUR+"_"+IDMATIERE+"_"+IDJOUR+"_"+IDHEUREDEB+"_"+IDHEUREFIN+"_"+periode,
                                    success: function (data) {
                                        data=JSON.parse(data);
                                        if(data==1){
                                            alert("Désolé, Ce professeur a déja un cours, ce jour à cette heure avec une autre classe.");

                                        }
                                        else{

                                            var elements = $("."+jour);
                                            //alert(elements.length);
                                            if(elements.length != 0){
                                                for(var i = 0 ; i<elements.length ; i++){
                                                    if( heuredebut < "08:00" || heuredebut >= heurefin ||
                                                        heuredebut < elements[i].children[0].className && heurefin > elements[i].children[0].className ||
                                                        heuredebut > elements[i].children[0].className && heuredebut < elements[i].children[1].className ||
                                                        heuredebut == elements[i].children[0].className) {
                                                        $("#alert").removeClass("hidden");
                                                        exit;
                                                    }
                                                }
                                            }
                                            if (INTERVALLETEMPS == 1){
                                                $("#alert").addClass("hidden");
                                                var classe = aleatoire();
                                                var elem =classe+"-"+heuredebut+"-"+heurefin+"-"+jour.substring(0,3)+"-"+getId(matiere)+"-"+getId(salle)+"-"+getId(prof);
                                                //var elem =classe+"-"+heuredebut+"-"+heurefin+"-"+jour+"-"+getId(matiere)+"-"+getId(salle)+"-"+getId(prof);
                                                tabET.push(elem);
                                                var ligne = "<tr class='"+classe+" "+jour+"' >" +
                                                    "<td class='"+heuredebut+"'>"+jour+"</td>" +
                                                    "<td class='"+heurefin+"'>"+heuredebut+" à "+heurefin+"</td>" +
                                                    "<td>"+getVal(matiere)+"</td>" +
                                                    "<td>"+getVal(salle)+"</td>" +
                                                    "<td>"+getVal(prof)+"</td>" +
                                                    "<td style='text-align: center;'>" +
                                                    "<a onclick='supprimer(this)' href='#'>" +
                                                    "<i class='glyphicon glyphicon-trash'></i>" +
                                                    "</a>" +
                                                    "</td>" +
                                                    "</tr>";
                                                $("#tabET").append(ligne);
                                            }else{
                                                alert("Veuillez vérifiez l'intervalle d'heure");
                                            }

                                        }
                                    }
                                });
                          //  } else {
                             //   alert ("Veuillez revoir l'intervalle d'heure choisi);
                            //}



                        }



                    }
                </script>
            </div>
            </form>
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