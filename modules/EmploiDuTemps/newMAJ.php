
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
$lib->Restreindre($lib->Est_autoriser(23,$_SESSION['profil']));

$colname_Etab_rq_individu = "-1";
if (isset($_SESSION['etab'])) {
  $colname_Etab_rq_individu = $_SESSION['etab'];
}
$colname_anne_rq_individu = "-1";
if (isset($_SESSION['ANNEESSCOLAIRE'])) {
  $colname_anne_rq_individu = $_SESSION['ANNEESSCOLAIRE'];
}

$query_rq_individu2 = $dbh->query("SELECT rp.IDRECRUTE_PROF, rp.TARIF_HORAIRE, rp.VOLUME_HORAIRE, i.MATRICULE, i.NOM, i.PRENOMS, m.LIBELLE as matiere, cl.LIBELLE as classe, i.PHOTO_FACE, i.IDINDIVIDU 
                                              FROM RECRUTE_PROF rp, INDIVIDU i, MATIERE m, CLASSROOM cl, CLASSE_ENSEIGNE ce, MATIERE_ENSEIGNE me
                                              WHERE rp.IDETABLISSEMENT = ".$colname_Etab_rq_individu." 
                                              AND rp.IDINDIVIDU = i.IDINDIVIDU
                                              AND rp.IDINDIVIDU = me.ID_INDIVIDU
                              
                                              AND me.ID_MATIERE = m.IDMATIERE 
                                              AND rp.IDRECRUTE_PROF = ce.IDRECRUTE_PROF 
                                              AND cl.IDCLASSROOM = ce.IDCLASSROM 
                                              AND rp.IDANNEESSCOLAIRE = ".$colname_anne_rq_individu." 
                                              GROUP BY i.IDINDIVIDU ");





$query_rq_individu = $dbh->query("SELECT rp.IDRECRUTE_PROF, rp.TARIF_HORAIRE, rp.VOLUME_HORAIRE, i.MATRICULE, i.NOM, i.PRENOMS, m.LIBELLE as matiere, cl.LIBELLE as classe, i.PHOTO_FACE, i.IDINDIVIDU 
                                            FROM RECRUTE_PROF rp, INDIVIDU i, MATIERE m, CLASSROOM cl, CLASSE_ENSEIGNE ce, MATIERE_ENSEIGNE me
                                            WHERE rp.IDETABLISSEMENT=".$colname_Etab_rq_individu." 
                                            AND rp.IDINDIVIDU = i.IDINDIVIDU 
                                            AND rp.IDINDIVIDU = me.ID_INDIVIDU
                              
                                            AND me.ID_MATIERE = m.IDMATIERE 
                                            
                                            AND rp.IDRECRUTE_PROF = ce.IDRECRUTE_PROF  
                                            AND ce.IDCLASSROM = cl.IDCLASSROOM  
                                            
                                            GROUP BY i.IDINDIVIDU ");



?>


<?php include('header.php'); ?>
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Evaluations</a></li>                    
                    <li>Mise a jour des cours</li>
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
							
                           
                            
                        </div>

                    </div>
                    <div class="panel-body">
                    
                    <?php if(isset($_GET['msg']) && $_GET['msg']!= ''){
				 
				  if(isset($_GET['res']) && $_GET['res']==1) 
						  {?>
						  <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						  <?php echo $_GET['msg']; ?></div>
						 <?php  }
						  if(isset($_GET['res']) && $_GET['res']!=1) 
						  {?>
						  <div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> 
						  <?php echo $_GET['msg']; ?></div>
						  <?php }
						  ?>
                 
			     <?php } ?>
                 
                 <form id="form1" name="form1" method="GET" action="ficheEvaluation.php" class="form-inline">
                      <fieldset class="cadre"><legend>Filtre</legend>
                      <div class="col-lg-12">
                       <div class="col-lg-8">
                              <div class="col-lg-2">
                              <label class="control-label">Choisir professeur</label>
                              </div>
                              <div class="col-lg-10">
                              <select name="IDINDIVIDU" id="IDINDIVIDU" class="form-control" required>
                                <option value=""> --Selectionner--</option>
                                <?php foreach($query_rq_individu->fetchAll() as $row_rq_individu){  ?>
                                <option value="<?php echo $row_rq_individu['IDINDIVIDU']?>"><?php echo $row_rq_individu['PRENOMS']; ?> <?php echo $row_rq_individu['NOM']?></option>
                                <?php } ?>
                              </select>
                              </div> 
                       </div>
                        
                         
                          <div class="col-lg-offset-1 col-lg-1">             
                             <button  type="submit" class="btn btn-success" >Rechercher</button>
                          </div>
                          </div>
                          
                      </fieldset>
               </form>
                    
                     
    
              
                    </div></div></div>
                    <!-- END WIDGETS -->                    
                    
                   
                    
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->

        <?php include('footer.php'); ?>