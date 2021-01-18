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
           $conn = new PDO("mysql:host=h2mysql11;dbname=fhbs_numheritlabscom230", "fhbs_sunuecoledb", "68qb5JmA");
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