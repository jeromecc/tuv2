<?php

/*
 * caManipulatorBase
 * Classe manipulant des objets
 * les requete, les charge , etc... 
 * pas utilisée directement : passer par les classes Peer
 */

 abstract class caManipulatorBase {
 	
	/*
	 * constructeur
	 * @param string db id local
	 * @param string tablename
	 * @param string primary key
	 * @param type primary key
	 * @param nom de la classe à manipuler
	 */
	function __construct($dbid,$table_name,$primary_key,$type_pk,$className)
	{
		$this->databaseId = $dbid ;
		$this->tableName = $table_name ;
		$this->primaryKey = $primary_key ;
		$this->typePk = $type_pk ;
		$this->className = $className ;
	}
	
	/*
	 * methode statique retournant une instance
	 * 
	 */
	static function getInstance($dbid,$table_name,$primary_key,$type_pk,$className)
	{
		$obj = new caManipulator($dbid,$table_name,$primary_key,$type_pk,$className);
		return $obj ;
	}
	
	/*
	 * getFromPK renvoie un objet identifié par sa valeur de clé primaire
	 * @param int|string pkval
	 * @return caObjetBase
	 */
	public function getFromPK($pkval) 
	{
		//rajout des quotes si texte
		if($this->typePk == 'string')
			$pkval = "'".addslashes(stripslashes($pkval))."'";
			
		$tabObj = $this->getFromCW(' WHERE '.$this->primaryKey." = $pkval ");
		if(sizeof($tabObj) == 0)
			return null ;
		return $tabObj[0];
	}
	
	
	
	/*
	 * getFromCW renvoie une liste d'objets selon la clause where passée dans la requete
	 * @param string "clause where" exemple: "WHERE toto='jambon' "
	 * @param bool $bypassSqlCrossScriptDetection mettre à true si zapper la protection anti sql injection
	 * @return array un tableau d'objets
	 */
	public function getFromCW($cw,$bypassSqlInjectionDetection='') 
	{
		if(preg('/DELETE/',$cw) || preg('/UPDATE/',$cw) || preg('/SELECT/',$cw) || preg('/GRANT/',$cw) ) {
			return caEnvironnement::addException('Tentative de sql injection détectée');
		}
		$requete = caObjetBase::getRequete(caObjetBasePeer::DATABASE_ID,caObjetBasePeer::TABLE_NAME);
		$res = $requete -> getGen($cw,'tab');
		$tabReturn = array() ;
		foreach($res as $datarow)
		{
			$tabReturn[] = $this->fill($datarow);
		}
		return $tabReturn ;
	}
	
	/*
	 *fill , donné un tableau de data, remplit un objet basique et le renvoie 
	 *@param array
	 *@return caObjetBase
	 */
	protected function fill($dataTab) 
	{
		$obj = '' ;
		eval("\$obj = new ".$this->className."(\$dataTab);") ;
		return $obj ;
	}
	
	
	
	/*
	 * renvoie un objet requete donné id local connexion
	 * @param string idconnexion
	 * @param string tablename
	 * @param array tableau de data
	 * @return XhamRequete
	 */
	protected static function getRequete($idConnexion,$tablename,$data='')
	{
		$tabInfoReq = caEnvironnement::getInfoConnexion($idConnexion) ;
		$requete = new XhamRequete( $tabInfoReq['nomdb'], $tablename, $data='',$tabInfoReq['host'], $tabInfoReq['user'],$tabInfoReq['pass'] ) ;
		return $requete ;
	}
	
	/*
	 * doSave sauve l'objet en base
	 * @param objet à sauver
	 * 
	 */
	protected static function doSave($object)
	{
		$requete = caManipulator::getRequete($this->databaseId,$this->tableName,$object->getData());
		
		if ( ! $object->isInbase() )
		{
			$sql = $requete->addRecord();
			$object->set($this->primaryKey,$sql['cur_id']); 
			$object->inBase(true);
		}
		else
		{
			$sql = $requete->updRecord($this->getPkcw($object));
		}
		
	}
	
	/*
	 * construit la cw qui identifie un objet par sa clé primaire
	 */

	protected function getPkcw($object) {
		return ' '.$this->primaryKey.' = '.$object->get($this->primaryKey) .' ';
	}
 
 }