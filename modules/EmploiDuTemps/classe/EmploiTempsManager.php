<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 19:04
 */
require(dirname(__FILE__).'/EmploiTemps.php');
require_once("../manager.php");

class EmploiTempsManager extends manager{
    protected $_listEmploiTempss; // array
    protected $_nbreEmploiTemps;

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
        $this->_listEmploiTempss[] = $obj;
        $this->_nbreEmploiTemps++;

    }

    // liste de tous les abonnements
    public function getEmploiTempss($fields=null)
    {
        $donnees=parent::lister($fields);
        foreach($donnees as $donnee){

            $this->add(new EmploiTemps($donnee));
        }
        return $this->getListEmploiTempss();

    }

    //liste d'un abonnement
    public function getEmploiTemps($idChamp,$idValue)
    {
        $donnees=parent::listerAvecId('',$idChamp,$idValue);
        foreach($donnees as $donnee){

            $this->add(new EmploiTemps($donnee));
        }
        return $this->getListEmploiTempss();
    }


    function insert($values) {
        $donnees=parent::insert($values);
        return $donnees;
    }



    function modifier($values,$idField,$idValue) {
        $info=parent::modifier($values,$idField,$idValue);
        return $info;
    }

    /**
     * @return mixed
     */
    public function getListEmploiTempss()
    {
        return $this->_listEmploiTempss;
    }

    /**
     * @param mixed $listEmploiTempss
     */
    public function setListEmploiTempss($listEmploiTempss)
    {
        $this->_listEmploiTempss = $listEmploiTempss;
    }

    /**
     * @return mixed
     */
    public function getNbreEmploiTemps()
    {
        return $this->_nbreEmploiTemps;
    }

    /**
     * @param mixed $nbreEmploiTemps
     */
    public function setNbreEmploiTemps($nbreEmploiTemps)
    {
        $this->_nbreEmploiTemps = $nbreEmploiTemps;
    }







}
