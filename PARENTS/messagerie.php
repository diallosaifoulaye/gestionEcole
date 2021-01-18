<?php
include('header.php');

require_once("../config/Connexion.php");
require_once ("../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

$query = "SELECT MESSAGERIE.IDMESSAGERIE as idmsg,MESSAGERIE.DATE_MESSAGE as datemsg,MESSAGERIE.MESSAGE as msg,MESSAGERIE.OBJET_MSG as objmsg,MESSAGERIE.IDETABLISSEMENT as idetab,INDIVIDU.IDINDIVIDU as idExp,INDIVIDU.PRENOMS as prenom,INDIVIDU.NOM as nom,MESSAGE_DESTINATAIRE.ID as idmsg_recu,MESSAGE_DESTINATAIRE.LECTURE as etatmsg
          FROM MESSAGERIE
          INNER JOIN MESSAGE_DESTINATAIRE ON MESSAGERIE.IDMESSAGERIE = MESSAGE_DESTINATAIRE.IDMESSAGERIE
          INNER JOIN INDIVIDU ON MESSAGERIE.IDINDIVIDU = INDIVIDU.IDINDIVIDU
          WHERE MESSAGE_DESTINATAIRE.IDDEST = " .$_SESSION['id'] . " ORDER BY etatmsg ASC" ;
$stmt = $dbh->prepare($query);
$stmt->execute();
$tabMsgRecu = $stmt->fetchAll(PDO::FETCH_OBJ);

$query = "SELECT MESSAGERIE.IDMESSAGERIE as idmsg,MESSAGERIE.DATE_MESSAGE as datemsg,MESSAGERIE.MESSAGE as msg,MESSAGERIE.OBJET_MSG as objmsg,MESSAGERIE.IDETABLISSEMENT as idetab,CLASSROOM.IDCLASSROOM as idclass,CLASSROOM.LIBELLE as libclass,MESSAGE_DESTINATAIRE.ID as idmsg_dest
          FROM MESSAGERIE
          INNER JOIN MESSAGE_DESTINATAIRE ON MESSAGERIE.IDMESSAGERIE = MESSAGE_DESTINATAIRE.IDMESSAGERIE
          INNER JOIN CLASSROOM ON MESSAGE_DESTINATAIRE.IDDEST = CLASSROOM.IDCLASSROOM
          WHERE MESSAGERIE.IDINDIVIDU = " .$_SESSION['id'] . " ORDER BY datemsg ASC " ;
$stmt = $dbh->prepare($query);
$stmt->execute();
$tabMsgEnv = $stmt->fetchAll(PDO::FETCH_OBJ);

$query = "SELECT CLASSROOM.IDCLASSROOM as idclass,CLASSROOM.LIBELLE as libclass
          FROM CLASSROOM
          INNER JOIN CLASSE_ENSEIGNE ON CLASSROOM.IDCLASSROOM = CLASSE_ENSEIGNE.IDCLASSROM
          INNER JOIN RECRUTE_PROF ON CLASSE_ENSEIGNE.IDRECRUTE_PROF = RECRUTE_PROF.IDRECRUTE_PROF
          INNER JOIN INDIVIDU ON RECRUTE_PROF.IDINDIVIDU = INDIVIDU.IDINDIVIDU
          WHERE INDIVIDU.IDINDIVIDU = ".$_SESSION['id'] ;
$stmt = $dbh->prepare($query);
$stmt->execute();
$tabClass = $stmt->fetchAll(PDO::FETCH_OBJ);

//echo "<pre>";var_dump($tabDoc);exit;

?>
<ul class="breadcrumb">
    <li><a href="#"> PROFESSEUR </a></li>
    <li class="active"> Ma messagerie</li>
</ul>
<!--<div class="row">-->
<!--    <div onmouseover="myEfface();" class="col-lg-offset-10 col-lg-1">-->
<!--        <button class="btn btn-success" data-toggle="modal" href="#modal-add-doc" value="modifier">-->
<!--            Nouveau message-->
<!--        </button>-->
<!--    </div>-->
<!--</div>-->
<div class="row">
    <div class="panel-body">
        <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){
            if(isset($_GET['res']) && $_GET['res']==1)  {?>
                <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?= $_GET['msg']; ?>
                </div>
            <?php  } if(isset($_GET['res']) && $_GET['res']!=1)  {?>
                <div class="alert alert-danger">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                     <?= $_GET['msg']; ?>
                </div>
            <?php } ?>
        <?php } ?>
        <div class="container-fluid">
            <div class="row">
                <div>
                    <p><i class="fa fa-2x fa-inbox" style="color:#E05D1F;margin-left: 15px;margin-top: 15px;">  Boite de reception</i></p>
                </div>
                <div class="col-lg-6">
                    <!--<div class="row">
                        <div class="col-lg-6">
                            <div class="form-group col-lg-offset-6">
                                <label class="radio-inline">
                                    <input type="radio" name="optionsRadiosInline" id="optionsRadiosInline1" value="option1" checked="">
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input onchange="gestionTableMSG();" type="radio" name="optionsRadiosInline" id="optionsRadiosInline1" value="option1" checked="">
                                    <i class="fa fa-2x fa-send-o"></i>
                                </label>
                            </div>
                        </div>
                    </div> -->
                    <br/>
                    <br/>
                    <br/>
                    <div class="row">
                        <table id="customers2" class="table datatable table-striped">
                            <thead>
                            <tr>
                                <th width="10%">&nbsp;</th>
                                <th width="15%">Date</th>
                                <th width="20%" id="personne">Destinataire</th>
                                <th width="20%">Objet</th>
                                <th width="25%">Message</th>
                                <th width="10%" style="text-align: center;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                if(isset($tabMsgRecu) && count($tabMsgRecu)>0){
                                    foreach ($tabMsgRecu as $oneMsgRecu) { ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <?= ($oneMsgRecu->etatmsg == 0) ?
                                                '<img src="../img/icons/msg-close.png" width="31px" height="39px"/>' :
                                                '<img src="../img/icons/msg-open.png" width="31px" height="39px"/>';
                                            ?>
                                        </td>
                                        <td><?= $lib->date_time_fr($oneMsgRecu->datemsg); ?></td>
                                        <td><?= $oneMsgRecu->prenom. " " .$oneMsgRecu->nom; ?></td>
                                        <td><?= $oneMsgRecu->objmsg; ?></td>
                                        <td><?= substr($oneMsgRecu->msg,0,50); ?> . . .</td>
                                        <td style="text-align: center;">
                                            <a href="#" onclick="affichageMSG('<?= "De : ".$oneMsgRecu->prenom. " " .$oneMsgRecu->nom; ?>', '<?= $oneMsgRecu->objmsg; ?>', '<?= $oneMsgRecu->idmsg; ?>', '<?= $oneMsgRecu->idmsg_recu; ?>')" >
                                                <i id="<?= $oneMsgRecu->idmsg_recu; ?>" style="font-size: 25px;" class="glyphicon glyphicon-eye-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php }
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <br/>
                    <br/>
                    <br/>
                    <div class="panel">
                        <div class="panel-body">
                            <div>
                                <p id="cible-msg">De : Aucun message selectionné</p>
                                <p id="objet-msg">Objet : Aucun message selectionné</p>
                            </div>
                            <div class="jumbotron" style="padding-left: 15px;">
                                <p id="contenu-msg">
                                    Aucun message selectionné
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form class="form-modal" action="envoyerMessage.php?idProf=<?= $_SESSION['id']; ?>&idEtab=<?= $_SESSION["etab"]; ?>" enctype="multipart/form-data" method="post">
        <div class="modal fade" id="modal-add-doc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #E95C00;">
                        <button type="button" class="close" onclick="uncheckFile();" aria-hidden="true" data-dismiss="modal">
                            <i class="bold glyphicon glyphicon-remove"></i>
                        </button>
                        <h4 class="modal-title" style="color: white;">
                            <img src="../img/icons/new-msg.png" width="39px" height="39px"/>
                            Nouveau message
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div style="margin-top: -1px;" class="col-lg-12">
                                <div class="bold" style="margin-left: 15px;color: #2b4f89;">
                                    <b>Classe (s)</b>
                                </div>
                                <select class="col-lg-12 selectpicker" id="done" name="idclass[]" required multiple data-done-button="true">
                                    <?php if(isset($tabClass) && count($tabClass)>0) {
                                        foreach ($tabClass as $oneClass) { ?>
                                            <option value="<?= $oneClass->idclass; ?>"><?= $oneClass->libclass; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top: 20px;" class="row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div style="color: #2b4f89;">
                                        <b>Objet</b>
                                    </div>
                                    <input class="col-lg-12" style="height: 28px;" type="text" name="objet" required autocomplete="on"/>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 20px;" class="row">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label style="color: #2b4f89;"><b>Message</b></label>
                                        <div>
                                            <textarea style="height: 400px;" name="message" id="mytextarea"  class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top-color: #2b4f89;">
                        <button type="submit" class="btn btn-sm btn-success">Valider</button>
                        <button class="btn btn-sm btn-default text-danger" type="button" data-dismiss="modal"><b>Annuler</b>  </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>

            function uploadFile(idInput){
                $('#'+idInput).click()
            }

            function checkFile(){
                $('#file-check').removeClass("hidden");
            }

            function uncheckFile(){
                $('#file-check').addClass("hidden");
                $('#file-doc').val(null);
            }

            function gestionTableMSG() {
                var idMsgEnv = $('#msg-env');
                var idMsgRecu = $('#msg-recu');
                var pers = $('#personne');

                if(idMsgEnv.hasClass("vu")){
                    pers.text("Expéditeur") ;
                    idMsgEnv.removeClass("vu");
                    idMsgEnv.addClass("cache hidden");
                    idMsgRecu.removeClass("cache hidden");
                    idMsgRecu.addClass("vu");
                }else{
                    pers.text("Destinataire");
                    idMsgRecu.removeClass("vu");
                    idMsgRecu.addClass("cache hidden");
                    idMsgEnv.removeClass("cache hidden");
                    idMsgEnv.addClass("vu");
                }
            }

            function affichageMSG(cible, objet, idMsg, eye) {

                var elemEye = $("#"+eye);
                var otherEye = $(".glyphicon-eye-close");
                var elemCible = $("#cible-msg");
                var elemObjet = $("#objet-msg");
                var elemContenu = $("#contenu-msg");

                $.ajax({
                    type: "POST",
                    url: "searchMessage.php",
                    data: "IDMSG=" + idMsg,
                    success: function (message) {

                        if(elemEye.hasClass("glyphicon-eye-open")){
                            if(otherEye.length != 0){
                                otherEye.addClass("temp");
                                otherEye = $('.temp');
                                otherEye.removeClass("glyphicon-eye-close");
                                otherEye.addClass("glyphicon-eye-open");
                            }
                            elemEye.removeClass("glyphicon-eye-open");
                            elemEye.addClass("glyphicon-eye-close");
                            elemCible.text(cible);
                            elemObjet.text("Objet : "+objet);
                            elemContenu.html(message);
                        }else {
                            if(otherEye.length != 0){
                                otherEye.addClass("temp");
                                otherEye = $('.temp');
                                otherEye.removeClass("glyphicon-eye-close");
                                otherEye.addClass("glyphicon-eye-open");
                            }
                            elemEye.removeClass("glyphicon-eye-close");
                            elemEye.addClass("glyphicon-eye-open");
                            elemCible.text("De : Aucun message selectionné");
                            elemObjet.text("Objet : Aucun message selectionné");
                            elemContenu.text("Aucun message selectionné");
                        }

                    }
                });


            }

            function myEfface() {

                (!$('#mceu_29').hasClass("hidden")) ? $('#mceu_29').addClass("hidden") : null ;

            }
        </script>
</div>

    <div class="form-group row" >
        <div class="col-sm-5"></div>
        <div class="col-sm-5"></div>
        <div class="col-sm-1"></div>
        <!--  <div class="col-sm-1 btn-primary" style="width: 60px;" >
              <a href="mes_etudiant.php" style="color:#FFFFFF; font-size:14px;vertical-align: middle;line-height: 35px;"> Retour</a>
        </div> -->
    </div>

</div>
</div>
</div>

<?php include('footer.php'); ?>