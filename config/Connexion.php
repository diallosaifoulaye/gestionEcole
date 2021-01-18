<?php
/**
 * Created by PhpStorm.
 * User: developpeur3
 * Date: 01/02/2016
 * Time: 10:58
 */
class Connexion

{
    function Connection(){

        $conn = NULL;


        try{
           $conn = new PDO("mysql:host=*******;dbname=*******", "******", "*********");
		   // $conn = new PDO("mysql:host=localhost;dbname=dakarapid", "root", "root");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e){
            return -1;
        }


    }



    public function CloseConnexion($dbh)
    {
        $dbh=null;

    }


}
?>
