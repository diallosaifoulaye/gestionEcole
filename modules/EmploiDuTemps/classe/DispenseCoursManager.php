<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 19:04
 */
require(dirname(__FILE__).'/DispenseCours.php');
require_once("../manager.php");

class DispenseCoursManager extends manager{
    protected $_listDispenseCourss; // array
    protected $_nbreDispenseCours;

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
        $this->_listDispenseCourss[] = $obj;
        $this->_nbreDispenseCours++;

    }

    // liste de tous les abonnements
    public function getDispenseCourss($fields=null)
    {
        $donnees=parent::lister($fields);
        foreach($donnees as $donnee){

            $this->add(new DispenseCours($donnee));
        }
        return $this->getListDispenseCourss();

    }

    //liste d'un abonnement
    public function getDispenseCours($idChamp,$idValue)
    {
        $donnees=parent::listerAvecId('',$idChamp,$idValue);
        foreach($donnees as $donnee){

            $this->add(new DispenseCours($donnee));
        }
        return $this->getListDispenseCourss();
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
    public function getListDispenseCourss()
    {
        return $this->_listDispenseCourss;
    }

    /**
     * @param mixed $listAbonnements
     */
    public function setListDispenseCourss($listDispenseCourss)
    {
        $this->_listDispenseCourss = $listDispenseCourss;
    }

    /**
     * @return mixed
     */
    public function getNbreDispenseCours()
    {
        return $this->_nbreDispenseCours;
    }

    /**
     * @param mixed $nbreAbonnement
     */
    public function setNbreDispenseCours($nbreDispenseCours)
    {
        $this->_nbreDispenseCours = $nbreDispenseCours;
    }





}
