<?php
/**
 * HTML2PDF Librairy - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @author      Laurent MINGUET <webmaster@html2pdf.fr>
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */
// get the HTML
ob_start();
//if(isset($_GET['idniveau']) && base64_decode($_GET['idniveau']) != null){
  //  if(base64_decode( $_GET['idniveau']) == 3){
        include('documentDuplique.php');
    //}elseif (base64_decode( $_GET['idniveau']) == 2){
       // include('bulletin_primaire.php');
   // }
//}

$content = ob_get_clean();
// convert in PDF
require_once('html2pdf/html2pdf.class.php');
try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
    $html2pdf->setDefaultFont('Times', 8);
    $html2pdf->writeHTML($content);
    $html2pdf->Output("Bulletin.pdf", 'D');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
