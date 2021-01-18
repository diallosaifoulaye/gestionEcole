<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 18:48
 */

class TypeDoc
{
    //	IDTYPEDOCADMIN LIBELLE CONTENU IDETABLISSEMENT IDMODELE_DOC
    private $IDTYPEDOCADMIN;
    private $LIBELLE;
    private $CONTENU;
    private $IDETABLISSEMENT;
    private $IDMODELE_DOC;
    private $IDTYPEINDIVIDU;




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
    public function getCONTENU()
    {
        return $this->CONTENU;
    }

    /**
     * @param mixed $CONTENU
     */
    public function setCONTENU($CONTENU)
    {
        $this->CONTENU = $CONTENU;
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
    public function getIDMODELEDOC()
    {
        return $this->IDMODELE_DOC;
    }

    /**
     * @param mixed $IDMODELE_DOC
     */
    public function setIDMODELEDOC($IDMODELE_DOC)
    {
        $this->IDMODELE_DOC = $IDMODELE_DOC;
    }

    /**
     * @return mixed
     */
    public function getIDTYPEDOCADMIN()
    {
        return $this->IDTYPEDOCADMIN;
    }

    /**
     * @param mixed $IDTYPEDOCADMIN
     */
    public function setIDTYPEDOCADMIN($IDTYPEDOCADMIN)
    {
        $this->IDTYPEDOCADMIN = $IDTYPEDOCADMIN;
    }

    /**
     * @return mixed
     */
    public function getLIBELLE()
    {
        return $this->LIBELLE;
    }

    /**
     * @param mixed $LIBELLE
     */
    public function setLIBELLE($LIBELLE)
    {
        $this->LIBELLE = $LIBELLE;
    }

    /**
     * @return mixed
     */

    public function getIDTYPEINDIVIDU()
    {
        return $this->IDTYPEINDIVIDU;
    }

    /**
     * @param mixed $IDTYPEINDIVIDU
     */
    public function setIDTYPEINDIVIDU($IDTYPEINDIVIDU)
    {
        $this->IDTYPEINDIVIDU = $IDTYPEINDIVIDU;
    }


}