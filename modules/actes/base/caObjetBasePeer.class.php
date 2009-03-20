<?php

/*
 * caObjetBasePeer
 * tout ce qui tient de la manipulation d'objets de types caObjetBase
 * typiquement methodes statiques 
 * 
 *
 */

 abstract class caObjetBasePeer {
 	
 	const DATABASE_ID = 'default';
	const TABLE_NAME = 'default';
	const PRIMARY_KEY = 'default';
	const TYPE_PRIMARY_KEY = 'int'; //int ou string
	
	/*
	 * getFromPK renvoie un objet identifié par sa valeur de clé primaire
	 * @param int|string pkval
	 * @return caObjetBase
	 */
	static function getFromPK($pkval) 
	{
		//rajout des quotes si texte
		if(caObjetBasePeer::TYPE_PRIMARY_KEY == 'string')
			$pkval = "'".addslashes(stripslashes($pkval))."'";
			
		$tabObj = caObjetBasePeer::getFromCW(' WHERE '.caObjetBasePeer::PRIMARY_KEY." = $pkval ");
		if(sizeof($tabObj) == 0)
			return null ;
		return $tabObj[0];
	}
	
	abstract static function getDatabasebId() ;
	abstract static function getTableNameId() ;
	abstract static function getTableNameId() ;
	
	
	/*
	 * getFromCW renvoie une liste d'objets selon la clause where passée dans la requete
	 * @param string "clause where" exemple: "WHERE toto='jambon' "
	 * @param bool $bypassSqlCrossScriptDetection mettre à true si zapper la protection anti sql injection
	 * @return array un tableau d'objets
	 */
	static function getFromCW($cw,$bypassSqlInjectionDetection='') 
	{
		if(preg('/DELETE/',$cw) || preg('/UPDATE/',$cw) || preg('/SELECT/',$cw) || preg('/GRANT/',$cw) ) {
			return caEnvironnementBase::addException('Tentative de sql injection détectée');
		}
		$requete = caObjetBasePeer::getRequete(caObjetBasePeer::DATABASE_ID,caObjetBasePeer::TABLE_NAME);
		$res = $requete -> getGen($cw,'tab');
		$tabReturn = array() ;
		foreach($res as $datarow)
		{
			$tabReturn[] = caObjetBasePeer::fill($datarow);
		}
		return $tabReturn ;
	}
	
	/*
	 *fill , donné un tableau de data, remplit un objet basique et le renvoie 
	 *@param array
	 *@return caObjetBase
	 */
	static function fill($dataTab) 
	{
		return caObjetBasePeer($dataTab);
	}
	
	
	
	/*
	 * renvoie un objet requete donné id local connexion
	 * @param string idconnexion
	 * @param string tablename
	 * @param array tableau de data
	 * @return XhamRequete
	 */
	static function getRequete($idConnexion,$tablename,$data='')
	{
		$tabInfoReq = caEnvironnementBase::getInfoConnexion($idConnexion) ;
		$requete = new XhamRequete( $tabInfoReq['nomdb'], $tablename, $data='',$tabInfoReq['host'], $tabInfoReq['user'],$tabInfoReq['pass'] ) ;

	}
	
	/*
	 * doSave sauve l'objet en base
	 * @param objet à sauver
	 * 
	 */
	static function doSave($object)
	{
		$requete = caObjetBasePeer::getRequete(caObjetBasePeer::DATABASE_ID,caObjetBasePeer::TABLE_NAME,$object->getData());
		
		if ( ! $object->isInbase() )
		{
			$sql = $requete->addRecord();
			$object->set(caObjetBasePeer::PRIMARY_KEY,$sql['cur_id']); 
		}
		else
		{
			$sql = $requete->addRecord();
		}
		
	}
	
	/*
	 * construit la cw qui identifie un objet par sa clé primaire
	 */

	static function getPkcw($object) {
		return ' '.caObjetBasePeer::PRIMARY_KEY.' = '.$object->get(caObjetBasePeer::PRIMARY_KEY) .' ';
	}
 
 }