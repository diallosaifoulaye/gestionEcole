
<?php
if (!isset($_SESSION)){
    session_start();
}

//require_once("restriction.php");


require_once("../../config/Connexion.php");
require_once ("../../config/Librairie.php");
$connection =  new Connexion();
$dbh = $connection->Connection();
$lib =  new Librairie();


$colname_rq_etablissement = "-1";
if (isset($_SESSION['etab'])) {
  $colname_rq_etablissement = $_SESSION['etab'];
}

$query_rq_etablissement = $dbh->query("SELECT * FROM ETABLISSEMENT WHERE IDETABLISSEMENT = ".$colname_rq_etablissement);
$row_rq_etablissement = $query_rq_etablissement->fetchObject();
$totalRows_rq_etablissement = $query_rq_etablissement->rowCount();
//print_r($row_rq_etablissement);die();

$query_rq_pays = $dbh->query("SELECT * FROM PAYS");
/* styles PDOStatement::fetch */
$row_rq_pays = $query_rq_pays ->fetchObject();
$totalRows_rq_pays = $query_rq_pays->rowCount();

?>


<?php include('header.php'); ?>
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">GED</a></li>                    
                    <li>Bulletin de notes</li>
                </ul>
                <!-- END BREADCRUMB -->                       
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
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
                    
                    <!-- START WIDGETS -->                    
                    <div class="row">
                        
                        
        
 
                      
                    </div>
                    <!-- END WIDGETS -->                    
                    
                   
                    
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->

        <?php include('footer.php'); ?>