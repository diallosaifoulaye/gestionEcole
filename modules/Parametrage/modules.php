
<?php 
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();

if($_SESSION['profil']!=1) 
$lib->Restreindre($lib->Est_autoriser(5,$lib->securite_xss($_SESSION['profil'])));


$colname_rq_modules = "-1";
if (isset($_SESSION['etab'])) {
  $colname_rq_modules = $lib->securite_xss($_SESSION['etab']);
}

try
{
    $query_rq_modules = $dbh->query("SELECT LIBELLE,BASE_NOTES,IDNIVEAU,IDMATIERE FROM MATIERE WHERE IDETABLISSEMENT = ".$colname_rq_modules." ORDER BY LIBELLE ASC");
    $rs_module = $query_rq_modules->fetchAll();
}
catch (PDOException $e)
{
    echo -2;
}

$query_REQ_niveau = $dbh->query("SELECT LIBELLE,IDNIVEAU FROM NIVEAU ");
$res_niveau = $query_REQ_niveau->fetchAll();


//var_dump($res_niveau);die();
?>

<?php include('header.php'); ?>
<!-- START BREADCRUMB -->
        <ul class="breadcrumb">
                    <li><a href="#">Param&eacute;trage</a></li>                    
                    <li>Mati&egrave;res</li>
                </ul>
                <!-- END BREADCRUMB -->  
                <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
                    <!-- START WIDGETS -->                    
                    <div class="row">
                    
                    <div class="panel panel-default">
                    <div class="panel-heading">
                        &nbsp;&nbsp;&nbsp;

                        <div class="btn-group pull-right">
							<button data-toggle="modal" data-target="#ajouter" style="background-color:#DD682B" class='btn dropdown-toggle', aria-hidden='true'>
                                <i class="fa fa-plus"></i> Nouvelle mati&egrave;re
                            </button>
                            
                        </div>

                    </div>

                    <div class="panel-body">

                        <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){

                            if(isset($_GET['res']) && $_GET['res'] == 1) { ?>

                                <div class="alert alert-success">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <?php echo $lib->securite_xss($_GET['msg']); ?>
                                </div>

                            <?php  } if(isset($_GET['res']) && $_GET['res'] != 1) { ?>

                                <div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <?php echo $lib->securite_xss($_GET['msg']); ?>
                                </div>

                            <?php } ?>

                        <?php } ?>
                        
                        <table id="customers2" class="table datatable">
                        
                            <thead>
                            <tr>
                                <th>LIBELLE</th>
                                <th>NOTE DE BASE</th>
                                <th>CYCLE</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                            </thead>
                          
                            <tbody>
                                <?php foreach($rs_module as  $row_rq_modules) {?>
                                <tr>
                                        <td><?php echo $row_rq_modules['LIBELLE']; ?></td>
                                        <td><?php echo $row_rq_modules['BASE_NOTES']; ?></td>
                                        <td>
                                            <?php
                                            $query_REQ_niveau_ = $dbh->query("SELECT LIBELLE,IDNIVEAU FROM NIVEAU WHERE IDNIVEAU=".$row_rq_modules['IDNIVEAU']);
                                            $res_niveau_ = $query_REQ_niveau_->fetchObject();
                                                echo $res_niveau_->LIBELLE;
                                            ?>
                                        </td>

                                        <td><a href="modifMatiere.php?idMatiere=<?php echo base64_encode($row_rq_modules['IDMATIERE']); ?>"><i class=" glyphicon glyphicon-edit"></i></a></td>

                                        <td><a href="suppMatiere.php?idMatiere=<?php echo base64_encode($row_rq_modules['IDMATIERE']); ?>" onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette enregistrement'));"><i class="glyphicon glyphicon-remove"></i></a></td>
                                </tr>
                                <?php } ?>
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
            <div class="modal-dialog" style="width: 50%">
                <div class="modal-content">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="panel-title text-center"> Nouvelle mati&egrave;re </h3> <!--IDMATIERE LIBELLE IDETABLISSEMENT-->
                        </div>
                        <form action="ajouterMatiere.php" method="post">

                            <div class="panel-body">
                                <div class="row">

                                    <div class="col-xs-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>LIBELLE</label>
                                                <div>
                                                    <input type="text" name="LIBELLE" id="LIBELLE" required class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>NOTE DE BASE</label>
                                                <div>
                                                    <input type="number" name="BASE_NOTES" id="BASE_NOTES" required class="form-control" min="0"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>CYCLE</label>
                                                <div>
                                                    <select name="IDNIVEAU" id="NIVEAU" class="form-control selectpicker" onchange="controlButton()">
                                                        <option value="">CHOISIR UN CYCLE</option>
                                                        <?php
                                                        foreach ( $res_niveau as $cycle) {
                                                            echo "<option value='".$cycle['IDNIVEAU']."'>".$cycle['LIBELLE']."</option>";
                                                        }
                                                        ;?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-xs-12">
                                        <fieldset class="cadre" id="mat" style="display: none">
                                        </fieldset>
                                    </div>
                                </div>

                            </div>
                            <div class="panel-footer">
                                <button type="reset" class="btn btn-danger">R&eacute;initialiser</button>
                                <input type="hidden" name="IDETABLISSEMENT" value="<?php echo base64_encode($lib->securite_xss($_SESSION["etab"])); ?>" />
                                <button type="submit" class="btn btn-primary pull-right" id="idvalider" style="display: none">Valider</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


<?php include('footer.php'); ?>
<script>
    $( document ).ready(function() {
       /* $('#tabMatCoef').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/French.json"
            }
        })*/
    })

    function controlButton() {
        var selectedNiv = $("#NIVEAU").find("option:selected").val()
        if(selectedNiv != "") {
            $('#idvalider').css("display","block")
            $.ajax({
                method: "POST",
                url: "request.php",
                data: {
                    IDNIVEAU: btoa(selectedNiv)
                }
            }).done(function (data) {
                var data = $.parseJSON(data)
                $('#mat').empty()
                //$('#CLASSROOM').children('option:not(:first)').remove()
                $('#mat').append('<legend>COEFFICIENT PAR SÉRIE</legend>');
                for (i = 0, len = data.length; i < len; i++){
                    $('#mat').append('<div class="col-md-6" style="margin-top: 5px"><div class="form-group"><label>'+data[i].LIBSERIE+'</label><div>' +
                        '<input type="number" name="coef_[]" id="COEFFICIENT_'+data[i].IDSERIE+'" class="form-control" placeholder="coefficient" min="0"/>' +
                        '<input type="hidden" name="serie_[]" value="'+data[i].IDSERIE+'"/>' +
                        '</div></div></div>')
                }
                $('#mat').css('display','block')
            })
        }else {
            $('#mat').empty()
            $('#mat').css('display','none')
            $('#idvalider').css("display","none")
        }
    }

    /*$('#ajouter').on('hidden.bs.modal', function (e) {
        $('#NIVEAU').children('option:not(:first)').remove()
    })*/

</script>
