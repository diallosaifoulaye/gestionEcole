<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 18:48
 */

class DispenseCours
{
    //  IDDISPENSER_COURS 	IDCLASSROOM 	IDINDIVIDU 	DATEDEBUTCOURS 	DATEFINCOURS 	TITRE_COURS 	CONTENUCOURS 	IDSALL_DE_CLASSE 	IDETABLISSEMENT 	IDMATIERE
    private $IDDISPENSER_COURS;
    private $IDCLASSROOM;
    private $IDINDIVIDU;
    private $DATEDEBUTCOURS;
    private $DATEFINCOURS;
    private $TITRE_COURS;
    private $CONTENUCOURS;
    private $IDSALL_DE_CLASSE;
    private $IDETABLISSEMENT;
    private $IDMATIERE;




    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }


    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCONTENUCOURS()
    {
        return $this->CONTENUCOURS;
    }

    /**
     * @param mixed $CONTENUCOURS
     */
    public function setCONTENUCOURS($CONTENUCOURS)
    {
        $this->CONTENUCOURS = $CONTENUCOURS;
    }

    /**
     * @return mixed
     */
    public function getDATEDEBUTCOURS()
    {
        return $this->DATEDEBUTCOURS;
    }

    /**
     * @param mixed $DATEDEBUTCOURS
     */
    public function setDATEDEBUTCOURS($DATEDEBUTCOURS)
    {
        $this->DATEDEBUTCOURS = $DATEDEBUTCOURS;
    }

    /**
     * @return mixed
     */
    public function getDATEFINCOURS()
    {
        return $this->DATEFINCOURS;
    }

    /**
     * @param mixed $DATEFINCOURS
     */
    public function setDATEFINCOURS($DATEFINCOURS)
    {
        $this->DATEFINCOURS = $DATEFINCOURS;
    }

    /**
     * @return mixed
     */
    public function getIDCLASSROOM()
    {
        return $this->IDCLASSROOM;
    }

    /**
     * @param mixed $IDCLASSROOM
     */
    public function setIDCLASSROOM($IDCLASSROOM)
    {
        $this->IDCLASSROOM = $IDCLASSROOM;
    }

    /**
     * @return mixed
     */
    public function getIDDISPENSERCOURS()
    {
        return $this->IDDISPENSER_COURS;
    }

    /**
     * @param mixed $IDDISPENSER_COURS
     */
    public function setIDDISPENSERCOURS($IDDISPENSER_COURS)
    {
        $this->IDDISPENSER_COURS = $IDDISPENSER_COURS;
    }

    /**
     * @return mixed
     */
    public function getIDETABLISSEMENT()
    {
        return $this->IDETABLISSEMENT;
    }

    /**
     * @param mixed $IDETABLISSEMENT
     */
    public function setIDETABLISSEMENT($IDETABLISSEMENT)
    {
        $this->IDETABLISSEMENT = $IDETABLISSEMENT;
    }

    /**
     * @return mixed
     */
    public function getIDINDIVIDU()
    {
        return $this->IDINDIVIDU;
    }

    /**
     * @param mixed $IDINDIVIDU
     */
    public function setIDINDIVIDU($IDINDIVIDU)
    {
        $this->IDINDIVIDU = $IDINDIVIDU;
    }

    /**
     * @return mixed
     */
    public function getIDMATIERE()
    {
        return $this->IDMATIERE;
    }

    /**
     * @param mixed $IDMATIERE
     */
    public function setIDMATIERE($IDMATIERE)
    {
        $this->IDMATIERE = $IDMATIERE;
    }

    /**
     * @return mixed
     */
    public function getIDSALLDECLASSE()
    {
        return $this->IDSALL_DE_CLASSE;
    }

    /**
     * @param mixed $IDSALL_DE_CLASSE
     */
    public function setIDSALLDECLASSE($IDSALL_DE_CLASSE)
    {
        $this->IDSALL_DE_CLASSE = $IDSALL_DE_CLASSE;
    }

    /**
     * @return mixed
     */
    public function getTITRECOURS()
    {
        return $this->TITRE_COURS;
    }

    /**
     * @param mixed $TITRE_COURS
     */
    public function setTITRECOURS($TITRE_COURS)
    {
        $this->TITRE_COURS = $TITRE_COURS;
    }




}