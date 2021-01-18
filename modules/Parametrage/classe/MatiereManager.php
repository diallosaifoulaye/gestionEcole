<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 19:04
 */
require(dirname(__FILE__).'/Matiere.php');
require_once("../manager.php");

class MatiereManager extends manager{
    protected $_listMatieres; // array
    protected $_nbreMatiere;

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
        $this->_listMatieres[] = $obj;
        $this->_nbreMatiere++;

    }

    // liste de tous les abonnements
    public function getMatieres($fields=null)
    {
        $donnees=parent::lister($fields);
        foreach($donnees as $donnee){

            $this->add(new Matiere($donnee));
        }
        return $this->getListMatieres();

    }

    //liste d'un abonnement
    public function getMatiere($idChamp,$idValue)
    {
        $donnees=parent::listerAvecId('',$idChamp,$idValue);
        foreach($donnees as $donnee){

            $this->add(new Matiere($donnee));
        }
        return $this->getListMatieres();
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
    public function getListMatieres()
    {
        return $this->_listMatieres;
    }

    /**
     * @param mixed $listAbonnements
     */
    public function setListMatieres($listMatieres)
    {
        $this->_listMatieres = $listMatieres;
    }

    /**
     * @return mixed
     */
    public function getNbreMatiere()
    {
        return $this->_nbreMatiere;
    }

    /**
     * @param mixed $nbreAbonnement
     */
    public function setNbreMatiere($nbreMatiere)
    {
        $this->_nbreMatiere = $nbreMatiere;
    }





}
