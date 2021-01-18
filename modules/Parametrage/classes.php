
<?php 
session_start();
require_once("../../restriction.php");

require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");

$connection =  new Connexion();
$dbh = $connection->Connection();
$lib=new Librairie();

if($lib->securite_xss($_SESSION['profil'])!=1)
$lib->Restreindre($lib->Est_autoriser(48,$lib->securite_xss($_SESSION['profil'])));


$colname_REQ_class = "-1";
if (isset($_SESSION['etab'])) {
  $colname_REQ_class = $lib->securite_xss($_SESSION['etab']);
}

require_once('classe/FiliereManager.php');
$series = new FiliereManager($dbh,'SERIE');
$serie = $series->getFiliere('IDETABLISSEMENT',$lib->securite_xss($_SESSION['etab']));

require_once('classe/NiveauManager.php');
$niveaux=new NiveauManager($dbh,'NIVEAU');
$niveau = $niveaux->getNiveau('IDETABLISSEMENT',$lib->securite_xss($_SESSION['etab']));


$row_REQ_class = $dbh->query("SELECT cl.IDCLASSROOM, cl.LIBELLE, s.LIBSERIE, n.LIBELLE as LIBCYCLE, cl.NBRE_ELEVE, nc.LIBELLE as LIBELLENIVE
                                        FROM CLASSROOM cl 
                                        INNER JOIN SERIE s ON cl.IDSERIE = s.IDSERIE
                                        INNER JOIN NIVEAU n ON s.IDNIVEAU = n.IDNIVEAU
                                        LEFT JOIN NIV_CLASSE nc ON nc.ID = cl.IDNIV
                                        WHERE cl.IDETABLISSEMENT = ".$colname_REQ_class );
?>


<?php include('../header.php'); ?>
               <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Param&eacute;trage</a></li>                    
                    <li>Classes</li>
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
							<button data-toggle="modal" data-target="#ajouter"  
                            style="background-color:#DD682B" class='btn dropdown-toggle', aria-hidden='true' >
                            <i class="fa fa-plus"></i> Nouvelle classe</button>
                            
                        </div>

                    </div>
                    <div class="panel-body">
                    
                    <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){
				 
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
                 
			     <?php } ?>
                    
                     <table id="customers2" class="table datatable">
                         <thead>
                            <tr>
                                <th>Classes</th>
                                <th style="text-align: center !important;">Nombre d'éléves</th>
                                 <th>Niveaux</th>
                                 <th>Série</th>
                                 <th>Cycle</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                            </thead>
                           
                          
                            <tbody>
                           <?php foreach ($row_REQ_class->fetchAll() as $classe){
                                $array = array(
                                    "id" => $classe['IDCLASSROOM']
                                );
                                $param=base64_encode(json_encode($array));
                                ?>
                            <tr>
                                    <td><?php echo $classe['LIBELLE']; ?></td>
                                    <td align="center"><?php echo $classe['NBRE_ELEVE']; ?></td>
                                    <td><?php echo $classe['LIBELLENIVE']; ?></td>
                                    <td><?php echo $classe['LIBSERIE']; ?></td>
                                    <td><?php echo $classe['LIBCYCLE']; ?></td>

                                    <td><a href="modifClasse.php?idClasse=<?php echo $param; ?>"><i class=" glyphicon glyphicon-edit"></i></a></td>
                               
                                    <td>
                                        <a href="suppClasse.php?idClasse=<?php echo $param; ?>"  onclick="return(confirm('Etes-vous sûr de vouloir supprimer cette enregistrement'));"><i class="glyphicon glyphicon-remove"></i></a>
                                    </td>
                            </tr>
                            <?php }  ?>
           
                            </tbody>
                     </table>
    
              
                    </div></div></div>
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
                    <h3 class="panel-title text-center"> Nouvelle classe </h3>
                </div>
                <form action="ajouterClasse.php" method="POST">
         
                    <div class="panel-body">
                        <div class="row">

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CYCLE</label>
                                    <div>
                                        <select name="IDNIVEAU" class="form-control" data-live-search="true" id="selectNiv" onchange="controlButton1();controlButton()">
                                            <option value="">--Selectionner le cycle-- </option>
                                            <?php foreach ($niveau as $niv) { ?>
                                                <option value=" <?php echo $niv->getIDNIVEAU(); ?>"><?php echo $niv->getLIBELLE(); ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>SERIE</label>
                                    <div>
                                        <select name="IDSERIE" class="form-control" data-live-search="true" id="selectSerie" onchange="controlButton_();" required>
                                            <option value="">--Selectionner la série--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>NIVEAU</label>
                                    <div>
                                        <select name="IDNIV" class="form-control" data-live-search="true" id="selectNiveau" onchange="controlButton_();" required>
                                            <option value="">--Selectionner le niveau--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class="col-xs-12">
                                <div class="form-group">
                                <label >LIBELLE</label>
                                <div>
                                    <input type="text" name="LIBELLE" id="LIBELLE" required class="form-control"/>
                                </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                <label >NOMBRE D'ELEVE</label>
                                <div>
                                    <input type="number" name="NBRE_ELEVE" id="NBRE_ELEVE" required class="form-control" min="1"/>
                                </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="form-group">
                                <label>CLASSE D'EXAMEN</label>
                                <div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="EXAMEN" value="1"> OUI
                                            </label>
                                            &nbsp;
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="EXAMEN" value="0" checked> NON
                                            </label>
                                        </div>
                                </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="panel-footer">
                        <button type="reset" class="btn btn-danger">R&eacute;initialiser</button>
                        <input type="hidden" name="IDETABLISSEMENT" value="<?php echo $lib->securite_xss($_SESSION['etab']); ?>" />

                        <button type="submit" id="idvalider" class="btn btn-primary pull-right">Valider</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

       <?php include('footer.php');?>

<script>
    function controlButton() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        //alert(selectedNiv);
        if(selectedNiv != "") {
            $('#idvalider').css("display","none")
            $.ajax({
                method: "POST",
                url: "request.php",
                data: {
                    IDNIVEAU: btoa(selectedNiv)
                }
            }).done(function (data) {
                var data = $.parseJSON(data)
                $('#selectSerie').children('option:not(:first)').remove()
                $('#selectSerie').selectpicker('refresh')
                for (i = 0, len = data.length; i < len; i++){
                    $('#selectSerie').append(new Option(data[i].LIBSERIE, data[i].IDSERIE))
                    $('#selectSerie').selectpicker('refresh')
                }
            })
        }
    }

    function controlButton1() {
        var selectedNiveau = $("#selectNiv").find("option:selected").val()
        //alert(selectedNiveau)
        if(selectedNiveau != "") {
            $('#idvalider').css("display","none")
            $.ajax({
                method: "POST",
                url: "requestNiv.php",
                data: {
                    IDNIVEAU: btoa(selectedNiveau)
                }
            }).done(function (data) {
                var data = $.parseJSON(data)
                $('#selectNiveau').children('option:not(:first)').remove()
                $('#selectNiveau').selectpicker('refresh')
                for (i = 0, len = data.length; i < len; i++){
                    $('#selectNiveau').append(new Option(data[i].LIBELLE, data[i].ID))
                    $('#selectNiveau').selectpicker('refresh')
                }
            })
        }
    }


    $("#selectNiv").on('change', function () {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        if(selectedNiv == "") {
            $('#selectSerie').children('option:not(:first)').remove()
            $('#selectSerie').selectpicker('refresh')

            $('#selectNiveau').children('option:not(:first)').remove()
            $('#selectNiveau').selectpicker('refresh')

            $('#idvalider').css("display","none")
        }
    })


    function controlButton_() {
        var selectedNiv = $("#selectNiv").find("option:selected").val()
        var selectedSer = $("#selectSerie").find("option:selected").val()
        var selectedNive = $("#selectNiveau").find("option:selected").val()

        if(selectedNiv == "" || selectedSer == "" || selectedNive == "") {
            $('#idvalider').css("display","none")
        }else{
            $('#idvalider').css("display","block")
        }
    }

</script>

