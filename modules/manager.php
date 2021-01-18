<?php
	class manager{
		public $_table;
		public $_id;
		private $_db; // Instance de PDO

		protected $_listObject; // array
		protected $_nbreObject;
		
		public function __construct($db,$table)
		{
			$this->setDb($db);
			$this->setTable($table);
		}

		public function add($obj)
		{
			$this->_listObject[] = $obj;
			$this->_nbreObject++;

		}

		function insert($values) {
			try {
				$keys = array_keys($values);
				$fields = implode(", ", $keys);
				$valu = ":" . implode(", :", $keys);
				$insert = "INSERT INTO $this->_table ($fields) VALUES ($valu)";
				$query = $this->getDb()->prepare($insert);
				return $query->execute($values);
			} catch(PDOException $e) {
				return -1;
			}
		}

		function modifier($values,$idField,$idValue) {
			try {
				$keys = array_keys($values);
				$tableau =array();
				for($i=0; $i<count($keys);$i++){
					if($i==count($keys)-1)
						$tableau[$i]=$keys[$i].'=:'.$keys[$i];
					else
						$tableau[$i]=$keys[$i].'=:'.$keys[$i].',';
				}
				$fields = implode(" ", $tableau);
//				echo "<pre>";var_dump($values);exit;
				$update = "UPDATE  $this->_table SET $fields where $idField=$idValue";
				$query = $this->getDb()->prepare($update);
				return $query->execute($values);
			} catch(PDOException $e) {
				return -1;
			}
		}

		public function userExistByCol($email=null,$login=null)
		{
			$requete = null;
			if(!is_null($email))
			{
				$requete=$this->_db->query("SELECT  idUtilisateur  FROM UTILISATEURS where email = '".$email."'");
			}elseif(!is_null($login))
			{
				$requete=$this->_db->query("SELECT  idUtilisateur  FROM UTILISATEURS where login = '".$login."'");
			}
			$donnees = $requete->fetchAll(PDO::FETCH_ASSOC);
			return count($donnees) == 1 ? true : false ;
		}

		public function lister($fields=null)
		{
			if($fields==null)
			{
				$fields="*";
			}
			$requete=$this->_db->query('SELECT '.$fields.' FROM '.$this->_table);
			$donnees = $requete->fetchAll(PDO::FETCH_ASSOC);
			return $donnees;
		}
		public function listerAvecId($fields,$idField,$idValue)
		{
			if($fields=='')
			{
				$fields="*";
			}
			$requete=$this->_db->query("SELECT  $fields  FROM $this->_table where $idField = $idValue");
			$donnees = $requete->fetchAll(PDO::FETCH_ASSOC);
			return $donnees;
		}



		public function supprimer($fieldId,$idValue)
		{
			$result=$this->_db->exec("DELETE from $this->_table where $fieldId=$idValue");
			return $result;
		}



		public function setTable($table)
		{
			$this->_table=$table;
		}

		public function setDb($db)
		{
			$this->_db=$db;
		}

		/**
		 * @return mixed
		 */
		public function getTable()
		{
			return $this->_table;
		}

		/**
		 * @return mixed
		 */
		public function getDb()
		{
			return $this->_db;
		}


		/**
		 * @return mixed
		 */
		public function getListObject()
		{
			return $this->_listObject;
		}

		/**
		 * @param mixed $listObject
		 */
		public function setListObject($listObject)
		{
			$this->_listObject = $listObject;
		}

		/**
		 * @return mixed
		 */
		public function getNbreObject()
		{
			return $this->_nbreObject;
		}

		/**
		 * @param mixed $nbreObject
		 */
		public function setNbreObject($nbreObject)
		{
			$this->_nbreObject = $nbreObject;
		}


	}
?>