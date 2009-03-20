<?php

/*
 * exemple d'objet peer type
 */

class caExemplePeer implements caInterfacePeer 
{
	const CONNECTION_ID = 'test' ;
	const TABLE_NAME = 'test';
	const PRIMARY_KEY = 'id' ;
	const TYPE_PRIMARY_KEY = 'int' ;
	const OBJECT_CLASS_NAME = 'caExemple' ;

	private static function getManipulator() 
	{
		caManipulator::getInstance(self::CONNECTION_ID,self::TABLE_NAME,self::PRIMARY_KEY,self::TYPE_PRIMARY_KEY,self::OBJECT_CLASS_NAME);	
	}
		
	static function save($obj)
	{
		self::getManipulator()->doSave($obj);
	}
	
	 static function getFromPK($pkval)
	 {
	 	return self::getManipulator()->getFromPK($pkval);
	 }
	  
	static function getFromCW($cw)
	 {
	 	return self::getManipulator()->getFromCW($cw);
	 }
	
}