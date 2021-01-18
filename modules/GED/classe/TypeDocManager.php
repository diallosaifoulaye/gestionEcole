<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 19:04
 */
require(dirname(__FILE__).'/TypeDoc.php');
require_once("../manager.php");

class TypeDocManager extends manager{
    protected $_listTypeDocs; // array
    protected $_nbreTypeDoc;

    /**
     * EvenementManager constructor.
     * @param $_db
     * @param $_table
     */
    public function __construct($db,$table)
    {
        parent::__construct($db,$table);
    }
    public function add($obj)
    {
        $this->_listTypeDocs[] = $obj;
        $this->_nbreTypeDoc++;

    }

    // liste de tous les abonnements
    public function getTypeDocs($fields=null)
    {
        $donnees=parent::lister($fields);
        foreach($donnees as $donnee){

            $this->add(new TypeDoc($donnee));
        }
        return $this->getListTypeDocs();

    }

    //liste d'un abonnement
    public function getTypeDoc($idChamp,$idValue)
    {
        $donnees=parent::listerAvecId('',$idChamp,$idValue);
        foreach($donnees as $donnee){

            $this->add(new TypeDoc($donnee));
        }
        return $this->getListTypeDocs();
    }


    function insert($values) {
        $donnees=parent::insert($values);
        return $donnees;
    }



    function modifier($values,$idField,$idValue) {
        $info=parent::modifier($values,$idField,$idValue);
        return $info;
    }

    public function supprimer($fieldId,$idValue)
    {
        $info=parent::supprimer($fieldId,$idValue);
        return $info;
    }

    /**
     * @return mixed
     */
    public function getListTypeDocs()
    {
        return $this->_listTypeDocs;
    }

    /**
     * @param mixed $listAbonnements
     */
    public function setListTypeDocs($listTypeDocs)
    {
        $this->_listTypeDocs = $listTypeDocs;
    }

    /**
     * @return mixed
     */
    public function getNbreTypeDoc()
    {
        return $this->_nbreTypeDoc;
    }

    /**
     * @param mixed $nbreAbonnement
     */
    public function setNbreTypeDoc($nbreTypeDoc)
    {
        $this->_nbreTypeDoc = $nbreTypeDoc;
    }





}
