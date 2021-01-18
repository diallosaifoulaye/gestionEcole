<?php
class Librairie
{
    private $pdo;
    public function __construct()
    {
        // Instanciation de la classe connexion
//        $this->pdo = new PDO("mysql:host=localhost;dbname=sunuecole", "seeyni-faay", "passer123");
        $this->pdo = new PDO("mysql:host=h2mysql11;dbname=fhbs_numheritlabscom230", "fhbs_sunuecoledb", "68qb5JmA");
    }
    /******************************Fin*********************/
    function code_identification()
    {
        // on declare une chaine de caractÃ¨res
        $chaine = "0123456789";
//nombre de caractÃ¨res dans le mot de passe
        $nb_caract = 6;
// on fait une variable contenant le futur pass
        $pass = "";
//on fait une boucle
        for ($u = 1; $u <= $nb_caract; $u++) {
//on compte le nombre de caractÃ¨res prÃ©sents dans notre chaine
            $nb = strlen($chaine);
// on choisie un nombre au hasard entre 0 et le nombre de caractÃ¨res de la chaine
            $nb = mt_rand(0, ($nb - 1));
// on ajoute la lettre a la valeur de $pass
            $pass .= $chaine[$nb];
        }
// on retourne le rÃ©sultat :
        return $pass;
    }
    function si_facture_existe($mois, $inscription)
    {
        $rq_result = $this->pdo->query(" SELECT IDFACTURE FROM FACTURE WHERE  MOIS='" . $mois . "' AND IDINSCRIPTION=" . $inscription);
        $row = $rq_result->fetchObject();
        $total_Rows = $rq_result->rowCount();
        if ($total_Rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function Generer_facture($mois, $montant, $inscription, $etablissement, $annescolaire)
    {
        if ($this->si_facture_existe($mois, $inscription) == 0) {
            $num_facture = "FACT" . $this->code_identification() . $etablissement;
            $INSERT = "INSERT INTO FACTURE(IDFACTURE, NUMFACTURE, MOIS, MONTANT, DATEREGLMT, IDINSCRIPTION, IDETABLISSEMENT, MT_VERSE, MT_RELIQUAT, ETAT, IDANNEESCOLAIRE) ";
            $INSERT .= " VALUES ('','" . $num_facture . "','" . $mois . "',$montant,'" . date('Y-m-d') . "',$inscription,$etablissement,0,$montant,0,$annescolaire)";
            $rq_autorise = $this->pdo->query($INSERT);
        }
    }
    // generer numero compte
    public function genererNumCompte()
    {
        $this->con = $this->pdo->getConnexion();
        if ($this->con != NULL) {
            try {
                $code_carte = -1;
                $found = 0;
                while ($found == 0) {
                    $code_carte = rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
                    $query = sprintf("SELECT idCompte FROM compte WHERE numeroCompte = :code");
                    $result = $this->con->prepare($query);
                    $result->bindParam("code", $code_carte);
                    $result->execute();
                    $totalRows = $result->rowcount();
                    if ($totalRows == 0) {
                        break;
                    }
                }
                $this->con = null;
                return $code_carte;
            } catch (Exception $e) {
                return -2003;
            }
        } else {
            return -2004;
        }
    }
    public function genererNumTransaction($sigle)
    {
        try {
            $code_carte = -1;
            $found = 0;
            while ($found == 0) {
                $code_carte = $sigle . rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
                $query = sprintf("SELECT IDMENSUALITE FROM MENSUALITE WHERE numtransac =:code_carte");
                //$row=$query->fetchObject();
                $result = $this->pdo->prepare($query);
                $result->bindParam("code_carte", $code_carte);
                $result->execute();
                $rs_execute = $result->fetchObject();
                $totalRows = $result->rowcount();
                if ($totalRows == 0) {
                    $found = 1;
                    break;
                }
            }
            return $code_carte;
        } catch (Exception $e) {
            return -2003;
        }
    }
    public function genererReference()
    {
//$characts    = 'abcdefghijklmnopqrstuvwxyz';
        //$characts   .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characts = '1234567890';
        $code_aleatoire = '';
        for ($i = 0; $i < 7; $i++)    //10 est le nombre de caractères
        {
            $code_aleatoire .= substr($characts, rand() % (strlen($characts)), 1);
        }
        return $code_aleatoire;
    }
// convertir en date
    public function date_franc($date)
    {
        $date_fr = "";
        if ($date != "") {
            $date_en = substr($date, 0, 10);
            $aa = substr($date, 0, 4);
            $mm = substr($date, 5, 2);
            $jj = substr($date, 8, 2);
            ////////////////
            $date_fr = $jj . " / " . $mm . " / " . $aa;
        }
        return $date_fr;
    }
    public function date_time_franc($date)
    {
        $date_fr = "";
        if ($date != "") {
            $date_en = substr($date, 0, 10);
            $aa = substr($date, 0, 4);
            $mm = substr($date, 5, 2);
            $jj = substr($date, 8, 2);
            $heure = substr($date, 11, 8);
            ////////////////
            $date_fr = $jj . " / " . $mm . " / " . $aa . " " . $heure;
        }
        return $date_fr;
    }
    function date_time_fr($date)
    {
        $date_fr = "";
        if ($date != "") {
            $date_en = substr($date, 0, 10);
            $jj = substr($date, 8, 2);
            $mm = substr($date, 5, 2);
            $aa = substr($date, 0, 4);
            $heure = substr($date, 11, 8);
            //mois en lettre
            switch ($mm) {
                case "01":
                    $mm = "Jan";
                    break;
                case "02":
                    $mm = "Fev";
                    break;
                case "03":
                    $mm = "Mar";
                    break;
                case "04":
                    $mm = "Avr";
                    break;
                case "05":
                    $mm = "Mai";
                    break;
                case "06":
                    $mm = "Jui";
                    break;
                case "07":
                    $mm = "Juil";
                    break;
                case "08":
                    $mm = "Aout";
                    break;
                case "09":
                    $mm = "Sept";
                    break;
                case "10":
                    $mm = "Oct";
                    break;
                case "11":
                    $mm = "Nov";
                    break;
                case "12":
                    $mm = "Dec";
                    break;
            }
            ////////////////
            $date_fr = $jj . " / " . $mm . " / " . $aa . " " . $heure;
        }
        return $date_fr;
    }
    function date_fr($date)
    {
        $date_fr = "";
        if ($date != "") {
            $jj = substr($date, 8, 2);
            $mm = substr($date, 5, 2);
            $aa = substr($date, 0, 4);
            //mois en lettre
            switch ($mm) {
                case "01":
                    $mm = "Jan";
                    break;
                case "02":
                    $mm = "Fev";
                    break;
                case "03":
                    $mm = "Mar";
                    break;
                case "04":
                    $mm = "Avr";
                    break;
                case "05":
                    $mm = "Mai";
                    break;
                case "06":
                    $mm = "Jui";
                    break;
                case "07":
                    $mm = "Juil";
                    break;
                case "08":
                    $mm = "Aout";
                    break;
                case "09":
                    $mm = "Sept";
                    break;
                case "10":
                    $mm = "Oct";
                    break;
                case "11":
                    $mm = "Nov";
                    break;
                case "12":
                    $mm = "Dec";
                    break;
            }
            ////////////////
            $date_fr = $jj . " / " . $mm . " / " . $aa;
        }
        return $date_fr;
    }

    function affiche_mois($mois)
    {
        $mm = substr($mois, 0, 2);
        $aa = substr($mois, 3, 4);
        //mois en lettre
        switch ($mm) {
            case "01":
                $mm = "JANVIER";
                break;
            case "02":
                $mm = "FEVRIER";
                break;
            case "03":
                $mm = "MARS";
                break;
            case "04":
                $mm = "AVRIL";
                break;
            case "05":
                $mm = "MAI";
                break;
            case "06":
                $mm = "JUIN";
                break;
            case "07":
                $mm = "JUILLET";
                break;
            case "08":
                $mm = "AOUT";
                break;
            case "09":
                $mm = "SEPTEMBRE";
                break;
            case "10":
                $mm = "OCTOBRE";
                break;
            case "11":
                $mm = "NOVEMBRE";
                break;
            case "12":
                $mm = "DECEMBRE";
                break;
        }    ////////////////
        return $mm . " / " . $aa;
    }
    function affiche_periode($date)
    {
        $date_fr = "";
        if ($date != "") {
            $jj = substr($date, 8, 2);
            $mm = substr($date, 5, 2);
            //mois en lettre
            switch ($mm) {
                case "01":
                    $mm = "Jan";
                    break;
                case "02":
                    $mm = "Fev";
                    break;
                case "03":
                    $mm = "Mar";
                    break;
                case "04":
                    $mm = "Avr";
                    break;
                case "05":
                    $mm = "Mai";
                    break;
                case "06":
                    $mm = "Jui";
                    break;
                case "07":
                    $mm = "Juil";
                    break;
                case "08":
                    $mm = "Aout";
                    break;
                case "09":
                    $mm = "Sept";
                    break;
                case "10":
                    $mm = "Oct";
                    break;
                case "11":
                    $mm = "Nov";
                    break;
                case "12":
                    $mm = "Dec";
                    break;
            }
            ////////////////
            $date_fr = $jj . " / " . $mm ;
        }
        return $date_fr;
    }
    //LE MOIS
    function Le_mois($mm)
    {
        //mois en lettre
        switch ($mm) {
            case "01":
                $mm = "JANVIER";
                break;
            case "02":
                $mm = "FEVRIER";
                break;
            case "03":
                $mm = "MARS";
                break;
            case "04":
                $mm = "AVRIL";
                break;
            case "05":
                $mm = "MAI";
                break;
            case "06":
                $mm = "JUIN";
                break;
            case "07":
                $mm = "JUILLET";
                break;
            case "08":
                $mm = "AOUT";
                break;
            case "09":
                $mm = "SEPTEMBRE";
                break;
            case "10":
                $mm = "OCTOBRE";
                break;
            case "11":
                $mm = "NOVEMBRE";
                break;
            case "12":
                $mm = "DECEMBRE";
                break;
        }    ////////////////
        $date_fr = $mm;
        return $date_fr;
    }
    public function cleanCut($string, $length, $cutString = ' ...')
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        $str = substr($string, 0, $length - strlen($cutString) + 1);
        return substr($str, 0, strrpos($str, ' ')) . $cutString;
    }
    public function date_eng($date)
    {
        $date_fr = "";
        if ($date != "") {
            $date_en = substr($date, 0, 10);
            $jj = substr($date, 0, 2);
            $mm = substr($date, 3, 2);
            $aa = substr($date, 6, 4);

            ////////////////
            $date_fr = $aa . "-" . $mm . "-" . $jj;
        }
        return $date_fr;
    }
    /*************************************************************************
     ******************** securité contre les failles XSS ***********************
     ***************************************************************************/
    public function securite_xss($string)
    {
        $string = htmlspecialchars($string);
        $string = strip_tags($string);
        return $string;
    }


    public function securite_xss_array(array $array)
    {
        foreach ($array as $key => $value) {
            if (!\is_array($value)) $array[$key] = self::securite_xss($value);
            else self::securite_xss_array($value);
        }
        return $array;
    }


    /**************************************************************************
     ******************** securité contre les failles XSS ***********************
     ***************************************************************************/
    function affichage_xss($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        return $string;
    }
    public function nombre_form($nombre)
    {
        return @number_format($nombre, 0, ',', ' ');
    }
    public function securite_bdd($string)
    {
        if (ctype_digit($string)) {
            $string = intval($string);
        } else {
            $string = mysql_real_escape_string($string);
            $string = addcslashes($string, '%_');
        }
        return $string;
    }
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }
        // $theValue = @function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                $theValue = @htmlspecialchars($theValue);
                $theValue = @strip_tags($theValue);
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                $thesValue = @htmlspecialchars($theValue);
                $theValue = @strip_tags($theValue);
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                $theValue = @htmlspecialchars($theValue);
                $theValue = @strip_tags($theValue);
                break;
        }
        return $theValue;
    }




    /********************************************************************************
     ************** envoi parametre acces  ******************
     ********************************************************************************/
    function envoiLogin($destinataire, $prenom, $login, $password)
    {
        $sujet = "Création compte sunuEcole"; //Sujet du mail
        $de_mail = "no-reply@sunuEcole.com";
        $vers_nom = $prenom;
        $vers_mail = $destinataire;
        $message = "<table width='550px' border='0'>";
        $message .= "<tr>";
        $message .= "<td> Cher " . $vers_nom . ", </td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'><p>Votre compte  à l'application sunuEcole vient d'&ecirc;tre cree.<br />";
        $message .= "Vous pourrez d&eacute;sormais vous connecter au portail avec les param&egrave;tres d'acc&egrave;s suivants :<br />";
        $message .= "Login : " . $login . "<br />";
        $message .= "Mot de passe : " . $password . "<br />";
        $message .= "<a target='_blank' href='http://www.numherit-labs.com/sunuecole'>Cliquer sur ce lien pour acc&eacute;der &aacute; votre compte.</a>";
        $message .= "<br />";
        $message .= "</p></td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'>Nous vous rappelons que vos param&egrave;tres de connexion sont confidentiels .</td>";
        $message .= "</tr>";
        $message .= "</table>";
        /** Envoi du mail **/
        $entete = "Content-type: text/html; charset=utf8\r\n";
        $entete .= "To: $vers_nom<$vers_mail> \r\n";
        $entete .= "From:sunuEcole <no-reply@sunuEcole.com>\r\n";
        mail($destinataire, $sujet, $message, $entete);
    }
    function envoiNewMDP($destinataire, $prenom, $login, $password)
    {
        $sujet = "Création compte sunuEcole"; //Sujet du mail
        $de_mail = "no-reply@sunuEcole.com";
        $vers_nom = $prenom;
        $vers_mail = $destinataire;
        $message = "<table width='550px' border='0'>";
        $message .= "<tr>";
        $message .= "<td> Cher " . $vers_nom . ", </td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'><p>Votre mot de passe a &eacute;t&eacute; reg&eacute;n&eacute;r&eacute;.<br />";
        $message .= "Vous pourrez d&eacute;sormais vous connecter au portail avec les nouveaux param&egrave;tres d'acc&egrave;s suivants :<br />";
        $message .= "Login : " . $login . "<br />";
        $message .= "Mot de passe : " . $password . "<br />";
        $message .= "<a target='_blank' href='http://www.numherit-labs.com/sunuecole'>Cliquer sur ce lien pour acc&eacute;der &aacute; votre compte.</a>";
        $message .= "<br />";
        $message .= "</p></td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'>Nous vous rappelons que vos param&egrave;tres de connexion sont confidentiels .</td>";
        $message .= "</tr>";
        $message .= "</table>";
        /** Envoi du mail **/
        $entete = "Content-type: text/html; charset=utf8\r\n";
        $entete .= "To: $vers_nom<$vers_mail> \r\n";
        $entete .= "From:sunuEcole <no-reply@sunuEcole.com>\r\n";
        mail($destinataire, $sujet, $message, $entete);
    }
    function envoiLoginProfesseur($destinataire, $prenom, $login, $password)
    {
        $sujet = "Création compte sunuEcole"; //Sujet du mail
        $de_mail = "no-reply@sunuEcole.com";
        $vers_nom = $prenom;
        $vers_mail = $destinataire;
        $message = "<table width='550px' border='0'>";
        $message .= "<tr>";
        $message .= "<td> Cher " . $vers_nom . ", </td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'><p>Votre compte  à l'application sunuEcole vient d'&ecirc;tre cr&eacute;e.<br />";
        $message .= "Vous pourrez d&eacute;sormais vous connecter au portail avec les param&egrave;tres d'acc&egrave;s suivants :<br />";
        $message .= "Login : " . $login . "<br />";
        $message .= "Mot de passe : " . $password . "<br />";
        $message .= "<a target='_blank' href='http://www.numherit-labs.com/sunuecole/PROFESSEURS/'>Cliquer sur ce lien pour acc&eacute;der &aacute; votre compte.</a>";
        $message .= "<br />";
        $message .= "</p></td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'>Nous vous rappelons que vos param&egrave;tres de connexion sont confidentiels .</td>";
        $message .= "</tr>";
        $message .= "</table>";
        /** Envoi du mail **/
        $entete = "Content-type: text/html; charset=utf8\r\n";
        $entete .= "To: $vers_nom<$vers_mail> \r\n";
        $entete .= "From:sunuEcole <no-reply@sunuEcole.com>\r\n";
        mail($destinataire, $sujet, $message, $entete);
    }
    function envoiLoginEleve($destinataire, $prenom, $login, $password)
    {
        $sujet = "Création compte sunuEcole"; //Sujet du mail
        $de_mail = "no-reply@sunuEcole.com";
        $vers_nom = $prenom;
        $vers_mail = $destinataire;
        $message = "<table width='550px' border='0'>";
        $message .= "<tr>";
        $message .= "<td> Cher " . $vers_nom . ", </td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'><p>Votre compte  à l'application sunuEcole vient d'&ecirc;tre cr&eacute;e.<br />";
        $message .= "Vous pourrez d&eacute;sormais vous connecter au portail avec les param&egrave;tres d'acc&egrave;s suivants :<br />";
        $message .= "Login : " . $login . "<br />";
        $message .= "Mot de passe : " . $password . "<br />";
        $message .= "<a target='_blank' href='http://www.numherit-labs.com/sunuecole/ELEVES/'>Cliquer sur ce lien pour acc&eacute;der &aacute; votre compte.</a>";
        $message .= "<br />";
        $message .= "</p></td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'>Nous vous rappelons que vos param&egrave;tres de connexion sont confidentiels .</td>";
        $message .= "</tr>";
        $message .= "</table>";
        /** Envoi du mail **/
        $entete = "Content-type: text/html; charset=utf8\r\n";
        $entete .= "To: $vers_nom<$vers_mail> \r\n";
        $entete .= "From:sunuEcole <no-reply@sunuEcole.com>\r\n";
        mail($destinataire, $sujet, $message, $entete);
    }
    function envoiLoginParent($destinataire, $prenom, $login, $password)
    {
        $sujet = "Création compte sunuEcole"; //Sujet du mail
        $de_mail = "no-reply@sunuEcole.com";
        $vers_nom = $prenom;
        $vers_mail = $destinataire;
        $message = "<table width='550px' border='0'>";
        $message .= "<tr>";
        $message .= "<td> Cher " . $vers_nom . ", </td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'><p>Votre compte  à l'application sunuEcole vient d'&ecirc;tre cr&eacute;e.<br />";
        $message .= "Vous pourrez d&eacute;sormais vous connecter au portail avec les param&egrave;tres d'acc&egrave;s suivants :<br />";
        $message .= "Login : " . $login . "<br />";
        $message .= "Mot de passe : " . $password . "<br />";
        $message .= "<a target='_blank' href='http://www.numherit-labs.com/sunuecole/PARENTS/'>Cliquer sur ce lien pour acc&eacute;der &aacute; votre compte.</a>";
        $message .= "<br />";
        $message .= "</p></td>";
        $message .= "</tr>";
        $message .= "<tr>";
        $message .= "<td align='left' valign='top'>Nous vous rappelons que vos param&egrave;tres de connexion sont confidentiels .</td>";
        $message .= "</tr>";
        $message .= "</table>";
        /** Envoi du mail **/
        $entete = "Content-type: text/html; charset=utf8\r\n";
        $entete .= "To: $vers_nom<$vers_mail> \r\n";
        $entete .= "From:sunuEcole <no-reply@sunuEcole.com>\r\n";
        mail($destinataire, $sujet, $message, $entete);
    }
    function Est_autoriser($action, $profil)
    {
        try {
            $query = "SELECT valide FROM affectationDroit WHERE action=:action AND profil=:profil AND valide=1 ";
            $result = $this->pdo->prepare($query);
            $result->bindParam("action", $action);
            $result->bindParam("profil", $profil);
            $result->execute();
            $rs_execute = $result->fetchObject();
            $totalRows = $result->rowCount();
            if ($totalRows > 0) {
                return 1;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }
// restreindre l'access  a la page
    function Restreindre($valide)
    {
        if ($valide != 1) {
            $MM_restrictGoTo = '../../menu.php';
            header("Location: " . $MM_restrictGoTo);
        }
    }
//Activer un module
    function Acces_module($profil, $module)
    {
        try {
            $query = "SELECT aff.valide, a.module
			FROM affectationDroit aff INNER JOIN ACTION a ON aff.action = a.idAction
			WHERE aff.profil=:profil AND a.module=:module";
            $result = $this->pdo->prepare($query);
            $result->bindParam("profil", $profil);
            $result->bindParam("module", $module);
            $result->execute();
            $rs_execute = $result->fetchObject();
            $totalRows = $result->rowCount();
            if ($totalRows > 0) {
                return 1;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }
    public function random($car)
    {
        $string = "";
        $chaine = "abcdefghijklmnpqrstuvwxy1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        srand((double)microtime() * 1000000);
        for ($i = 0; $i < $car; $i++) {
            $string .= $chaine[rand() % strlen($chaine)];
        }
        return $string;
    }
    // generer mot de passe
    public function mot_de_passe()
    {
        return $this->random(8);
    }
    // generer mot de passe
    public function getId()
    {
        return $this->random(5);
    }
    function generer_code_barre()
    {
        $LeCodebarre = "7";
        $i = 0;
        for ($i = 0; $i < 12; $i++) {
            $LeCodebarre = $LeCodebarre . rand(0, 9);
        }
        return $LeCodebarre;
    }
// extraire une chaine
    function extraire_chaine($chaine, $pos1, $nbre)
    {
        return substr($chaine, $pos1, $nbre);
    }

    function uploadFiles( $paramFiles = [], $url = "", $nameFile = "" )
    {
        if(count($paramFiles)>0 && $paramFiles["error"] != "4" && $url != "" ) {
            $url = str_replace("config",$url,__DIR__);
            $nameFile .= ($nameFile == "") ? $this->random(5).".".pathinfo($paramFiles['name'], PATHINFO_EXTENSION) : ".".pathinfo($paramFiles['name'], PATHINFO_EXTENSION);
            return (move_uploaded_file($paramFiles['tmp_name'], $url . $nameFile ));
        }
        return false;
    }

    function deleteFiles( $url = "" )
    {
        return ($url != "") ? unlink($url) : false;
    }

     public function getMaxMatricule()
     {
         try
         {
                  $query = sprintf("SELECT MATRICULE FROM INDIVIDU oder by IDINDIVIDU DESC");
                  $result = $this->pdo->prepare($query);
                  $result->execute();
                  $a =  $result->fetchObject();
                  return $a->MATRICULE;
         }
         catch (PDOException $exception)
         {
                  return -1;
         }
     }


    public function tokenSMSOrange()
    {

        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.orange.com/oauth/v2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Authorization: Basic VUhmUHB2QWt4NlN0c0FZY0V2N2N1QkNidmU4VEIwZEg6S3ZONkRkOU5uTFY2VGNlag==";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        else{
            $messages = '';
            $res = 0;
            $json = json_decode($result);
            if(array_key_exists('token_type', $json) && array_key_exists('access_token', $json) && array_key_exists('expires_in', $json)){
                $date_debut = date('Y-m-d H:i:s');
                $date_fin = date('Y-m-d', strtotime(date($date_debut)) + (int)$json->{'expires_in'});

                try
                {
                    $query_rq_service = "UPDATE operateur SET token = :token, date_fin = :expire WHERE rowid = 1";
                    $service = $this->pdo->prepare($query_rq_service);
                    $service->bindParam('token', $json->{'access_token'});
                    $service->bindParam('expire', $date_fin);
                    $service->execute();
                    if($service->rowCount() === 1)
                        $res = 1;
                }
                catch (PDOException $e){
                    $messages .= $e;
                }
            }
            if($res === 0){
                $messages .= "<br/>La regénération du token a échoué.<br/>Le token current expire dans moins de trois(3) jours.";
                @$this->alerteSMS('madiop@numherit.com', 'Madiop GUEYE', $messages);
                @$this->alerteSMS('papa.ngom@numherit.com', 'Papa NGOM', $messages);
                @$this->alerteSMS('alioubalde@numherit.com', 'Aliou BALDE', $messages);
            }
        }
        curl_close($ch);
    }


    /************************* send SMS *************************/
    public function sendSMS($sender, $destinataire, $message){

        //echo $sender = 'Paositra'; die;
        if($destinataire[0] == '+'){
            $destinataire = substr($destinataire, 1);
        }
        else if($destinataire[0] == '0' && $destinataire[1] == '0'){
            $destinataire = substr($destinataire, 2);
        }
        $operateur = substr($destinataire, 3, 2);

        try {

            $destinataire = str_replace(' ', '', $destinataire);
            $query_rq_service = "SELECT * FROM operateur WHERE statut=1";
            $service = $this->pdo->prepare($query_rq_service);
            $service->execute();
            $row_rq_service = $service->fetchObject();
            $to_day = date('Y-m-d');
            $expire = date('Y-m-d', strtotime($to_day . ' + 3 days'));
            if ($expire >= $row_rq_service->date_fin) {
                $this->tokenSMSOrange();
            }
            if ((int)$row_rq_service->rowid === 1) {

                $destinataire = '+' . $destinataire;
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B221000000000/requests',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{"outboundSMSMessageRequest":{"address":"tel:' . $destinataire . '","outboundSMSTextMessage":{"message":"' . $message . '"},"senderAddress":"tel:+221000000000","senderName":"' . $sender . '"}}',
                    CURLOPT_HTTPHEADER => array(
                        'accept: application/json',
                        'authorization: Bearer ' . $row_rq_service->token,
                        'content-type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    $messages = "Madagascar<br/>Erreur WS Envoi SMS Orange: " . $err . "</b>.<br/>Tel: ".$destinataire."<br/>Merci de faire le necessairee (Urgence).";
                    @$this->alerteSMS('madiop@numherit.com', 'Madiop GUEYE', $messages);
                    @$this->alerteSMS('papa.ngom@numherit.com', 'Papa NGOM', $messages);
                    @$this->alerteSMS('alioubalde@numherit.com', 'Aliou BALDE', $messages);
                    return -1;
                } else {
                    $json = json_decode($response);
                    //var_dump($json); die;
                    if (!array_key_exists('outboundSMSMessageRequest', $json) && array_key_exists('code', $json) && (int)$json->code === 42) {
                        $this->tokenSMSOrange();

                        return -1;
                    } else if (!array_key_exists('outboundSMSMessageRequest', $json) && array_key_exists('code', $json) && (int)$json->code === 41) {
                        $messages = "Madagascar<br/>Le nombre de SMS restant dans le compte est arrive a epuisement.: <b>0 sms</b>.<br/>Merci de recharger le compte (Urgence).";
                        @$this->alerteSMS('madiop@numherit.com', 'Madiop GUEYE', $messages);
                        @$this->alerteSMS('papa.ngom@numherit.com', 'Papa NGOM', $messages);
                        @$this->alerteSMS('alioubalde@numherit.com', 'Aliou BALDE', $messages);
                        return -1;
                    } else if (!array_key_exists('outboundSMSMessageRequest', $json)) {
                        $messages = "Madagascar<br/>Erreur WS Envoi SMS Orange: " . json_encode($json) . "</b>.<br/>Tel: ".$destinataire."<br/>Merci de faire le necessairee (Urgence).";
                        @$this->alerteSMS('madiop@numherit.com', 'Madiop GUEYE', $messages);
                        @$this->alerteSMS('papa.ngom@numherit.com', 'Papa NGOM', $messages);
                        @$this->alerteSMS('alioubalde@numherit.com', 'Aliou BALDE', $messages);
                        return -1;
                    }
                    else{
                        $nb_sms_restant = $this->soldeSMSOrange($row_rq_service->token);
                        if (($nb_sms_restant <= 500 && $nb_sms_restant % 10 === 0) || $nb_sms_restant <= 100) {
                            $messages = "Madagascar<br/>Le nombre de SMS restant dans le compte est faible: <b>" . $nb_sms_restant . " sms</b>.<br/>Merci de recharger le compte (Urgence).";
                            @$this->alerteSMS('madiop@numherit.com', 'Madiop GUEYE', $messages);
                            @$this->alerteSMS('papa.ngom@numherit.com', 'Papa NGOM', $messages);
                            @$this->alerteSMS('alioubalde@numherit.com', 'Aliou BALDE', $messages);
                        }
                        return 1;
                    }
                }


            } else if ((int)$row_rq_service->rowid === 2) {
                $token = base64_encode('jula-login:jula1986');
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.infobip.com/sms/1/text/single",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{ \"from\":\" " . $sender . "\", \"to\":\"" . $destinataire . "\", \"text\":\"" . $message . "\" }",
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "authorization: Basic " . $token . "",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                if ($err) {
                    return "cURL Error #:" . $err;
                } else {
                    return $response;
                }
            } else if ((int)$row_rq_service->rowid === 3) {
                $destinataire = '+' . $destinataire;
                //$destinataire = '+221774246535';
                $url = 'https://api.primotexto.com/v2/notification/messages/send';
                $curl = curl_init($url);
                /*if (isSet(baseManager::$CURLOPT_PROXY)) {
                    curl_setopt($curl, CURLOPT_PROXY, baseManager::$CURLOPT_PROXY);
                }*/

                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'X-Primotexto-ApiKey: fd772fc697e680b76a07a71e9cd58209',
                ));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"number\":\"$destinataire\",\"message\":\"$message\",\"sender\":\"$sender\",\"campaignName\":\"Code de confirmation\",\"category\":\"codeConfirmation\"}");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($curl);
                //echo("$result\n");
                curl_close($curl);
                return $result;
            } else if ((int)$row_rq_service->rowid === 4) {
                $params = array(

                    'access_token' => 'Jhc5qS0eCRx5s8JEhMe5a39Bht5YDVPqHUsoiZ9O',          //sms api access token
                    'to' => '+' . $destinataire,         //destination number
                    'from' => 'Paositra',                //sender name has to be active
                    'message' => $message,    //message content
                );

                if ($params['access_token'] && $params['to'] && $params['message'] && $params['from']) {
                    $date = '?' . http_build_query($params);
                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://api.smsapi.com/sms.do'.$date);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");


                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        // echo 'Error:' . curl_error($ch);
                        return curl_errno($ch);
                    }

                    curl_close ($ch);
                    return $result;
                    /* $file = fopen('https://api.smsapi.com/sms.do' . $date, 'r');
                     $result = fread($file, 1024);
                     fclose($file);
                     return $result;*/
                }

            } else {
                $token = base64_encode('jula-login:jula1986');
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.infobip.com/sms/1/text/single",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{ \"from\":\" " . $sender . "\", \"to\":\"" . $destinataire . "\", \"text\":\"" . $message . "\" }",
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "authorization: Basic " . $token . "",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                if ($err) {
                    return "cURL Error #:" . $err;
                } else {
                    return $response;
                }
            }
        } catch (Exception $e) {
            return $e;
        }

    }




}