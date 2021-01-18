
<?php
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();


if($_SESSION['profil']!=1)
    $lib->Restreindre($lib->Est_autoriser(11,$lib->securite_xss($_SESSION['profil'])));


/*require_once("classe/AnneeScolaireManager.php");
require_once("classe/AnneeScolaire.php");
$niv=new AnneeScolaireManager($dbh,'ANNEESSCOLAIRE');*/


//$idDepense = "-1";
/*if (isset($_GET['idAnnee'])) {
    $colname_rq_annee_etab = base64_decode($lib->securite_xss($_GET['idAnnee']));
}*/
if (isset($_GET['IDREGLEMENT'])) {
    $idDepense = $lib->securite_xss(base64_decode($_GET['IDREGLEMENT']));

}
$query_rq_depense = $dbh->query("SELECT DEPENSE.IDREGLEMENT, DATE_REGLEMENT, MONTANT, MOTIF,  TYPE_PAIEMENT.libelle_paiement as typePaiemnet, REFERENCE, NUM_CHEQUE, BANQUE.LABEL as banque
                                              FROM  DEPENSE
                                              INNER JOIN TYPE_PAIEMENT ON TYPE_PAIEMENT.id_type_paiment = DEPENSE.IDTYPEPAIEMENT 
                                              INNER JOIN BANQUE ON BANQUE.ROWID = DEPENSE.FK_BANQUE 
                                              WHERE DEPENSE.IDREGLEMENT = ".$idDepense);
$query_rq_depense = $query_rq_depense->fetchObject();
//var_dump($query_rq_depense);die;

/*foreach($query_rq_depense->fetchAll() as $row_rq_annee_etab){

    $id=$row_rq_annee_etab['IDANNEESSCOLAIRE'];
    $libelle=$row_rq_annee_etab['LIBELLE_ANNEESSOCLAIRE'];
    $debut=$row_rq_annee_etab['DATE_DEBUT'];
    $fin=$row_rq_annee_etab['DATE_FIN'];
    $etat=$row_rq_annee_etab['ETAT'];
}


if(isset($_POST) && $_POST !=null) {


    $res = $niv->modifier($lib->securite_xss_array($_POST),'IDANNEESSCOLAIRE',$lib->securite_xss($_POST['IDANNEESSCOLAIRE']));
    if ($res==1) {
        $msg="Modification effectuée avec succés";

    }
    else{
        $msg="Modification effectuée avec echec";
    }
    header("Location: anneesScolaires.php?msg=".$lib->securite_xss($msg)."&res=".$lib->securite_xss($res));
}

$query_rq_individu_annee_scolaire = $dbh->query("SELECT COUNT(i.IDINSCRIPTION) as nbre, n.LIBELLE, s.LIBSERIE, i.IDSERIE, i.IDNIVEAU 
                                            FROM INSCRIPTION i
                                            INNER JOIN NIVEAU n ON i.IDNIVEAU = n.IDNIVEAU
                                            INNER JOIN SERIE s ON s.IDSERIE = i.IDSERIE 
                                            INNER JOIN INDIVIDU id ON  id.IDINDIVIDU = i.IDINDIVIDU 
                                            INNER JOIN ANNEESSCOLAIRE a ON a.IDANNEESSCOLAIRE = i.IDANNEESSCOLAIRE
                                            WHERE i.IDANNEESSCOLAIRE=".$colname_rq_annee_etab."
                                            AND a.ETAT = 0
                                            GROUP BY i.IDSERIE, i.IDNIVEAU");

*/?><!--

-->
<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Param&eacute;trage</a></li>
    <li>Dépenses</li>
    <li>Details</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <!--                                        <div class="white-box">-->
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Detail</span></a></li>

                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content" style="margin-top: 30px;">
                            <div role="tabpanel" class="tab-pane active" id="detail">
                                <div class="col-lg-offset-3 col-md-6 col-lg-offset-3">

                                        <table class="table table-responsive table-striped" >
                                            <tr>
                                                <td><strong>DATE REGLEMENT</strong></td>
                                                <td style="text-align: right;"><?php  echo $lib->date_fr($query_rq_depense->DATE_REGLEMENT);  ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>MONTANT</strong></td>
                                                <td style="text-align: right;"><?php  echo $query_rq_depense->MONTANT;  ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>MOTIF</strong></td>
                                                <td style="text-align: right;"><?php  echo $query_rq_depense->MOTIF;  ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>TYPE DE PAYEMENT</strong></td>
                                                <td style="text-align: right;"><?php  echo $query_rq_depense->typePaiemnet;  ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>REFERENCE</strong></td>
                                                <td style="text-align: right;"><?php  echo $query_rq_depense->REFERENCE;  ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>NUMERO CHEQUE</strong></td>
                                                <td style="text-align: right;"><?php  echo $query_rq_depense->NUM_CHEQUE;  ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>BANQUE</strong></td>
                                                <td style="text-align: right;"><?php  echo $query_rq_depense->banque;  ?></td>
                                            </tr>
                                            <tr>
                                                <td></td>

                                                <td style="text-align: right;">
                                                    <a href="depenses.php"><input type="button" class="btn btn-success" value="Retour"/></a>

                                                </td>
                                            </tr>
                                        </table>


                                </div>


                        </div>
                        <!--                                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->

<!--<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="confirmer" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="panel-title text-center"> CONFIRMATION </h3> <!--IDNIVEAU LIBELLE IDETABLISSEMENT-->
               <!-- </div>
                <div class="panel-body">
                    <div class="row" id="message">
                        SOUHAITEZ VOUS CLÔTURER L'ANNÉE SCOLAIRE ?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ANNULER</button>
                    <button type="button" class="btn btn-primary" id="cloturer">VALIDER</button>
                </div>
            </div>
-->
        </div>
    </div>
</div>-->
<?php include('footer.php'); ?>

<script>
    $(function () {
        $('#myTab li:first-child a').tab('show')
    })

    $('#cloturer').on('click', function () {
        //$('#confirmer').modal('hide')
        $.ajax({
            method: "POST",
            url: "cloreAnnee.php",
            data: {
                IDANNEESCOLAIRE: btoa(<?php echo $id;?>)
            }

        }).done(function (data) {
            if(data == 1) {
                console.log("ok");
                location.reload()
            }else if(data == -1){
                console.log('echec cloture')
                $('#message').text('Echec cloture !!')
            }else if(data == -2) {
                console.log(data)
                console.log('echec new year')
            }
        })
    })




</script>
