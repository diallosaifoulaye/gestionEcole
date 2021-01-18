<?php
/**
 * Created by PhpStorm.
 * User: numherit-dev
 * Date: 23/02/16
 * Time: 18:48
 */

class EmploiTemps
{
    //	IDEMPLOIEDUTEMPS	IDPERIODE 	IDETABLISSEMENT 	IDCLASSROOM
    private $IDEMPLOIEDUTEMPS;
    private $IDPERIODE;
    private $IDETABLISSEMENT;
    private $IDCLASSROOM;



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
    public function getIDEMPLOIEDUTEMPS()
    {
        return $this->IDEMPLOIEDUTEMPS;
    }

    /**
     * @param mixed $IDEMPLOIEDUTEMPS
     */
    public function setIDEMPLOIEDUTEMPS($IDEMPLOIEDUTEMPS)
    {
        $this->IDEMPLOIEDUTEMPS = $IDEMPLOIEDUTEMPS;
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
    public function getIDPERIODE()
    {
        return $this->IDPERIODE;
    }

    /**
     * @param mixed $IDPERIODE
     */
    public function setIDPERIODE($IDPERIODE)
    {
        $this->IDPERIODE = $IDPERIODE;
    }




}