<?php

/*
 * caObjetBase : particule élémentaire d'abstraction objet
 */
 
 abstract class  caObjetBase{
 	
	protected $tabData = array() ;
	
	/*
	 * constructeur
	 * @param array ensemble des valeurs
	 */
	public function __construct($data)
	{
		$this->tabData = $data ;	
	}
	 /*
	  * set affecte une valeur à une propriété de l'objet
	  * @param string nom de la colonne
	  * @param valeur
	  */
 	public function set($var,$val) 
 	{
 		$this->tabData[$var]=$val;
 	}

	 /*
	  * get renvoie la valeur d'une propriété de l'objet
	  * @param string nom de la colonne
	  * @return valeur
	  */
 	public function get($var) 
 	{
 		return $this->tabData[$var] ;
 	}
 	
 	//Renvoie la tableau data (lecture seule)
 	public function getData() {
 		return $this->tabData ;
 	}
 	
 	/*
 	 * regarde si l'objet est dans la table ou ou pas ( pas encore inséré )
 	 * @return bool
 	 */
 	public function isInbase() {
 		return $this->isInBase();	
 	}
 	
 	/*
 	 * signale si l'objet est dans une table ou pas
 	 * @param bool 
 	 */
 	public function inBase($val) {
 		$this->isInBase = $val ;
 	}
 	 	
 	/*
 	 * save
 	 * enregistre l'objet en base
 	 */
 	public function save() 
 	{
 		caObjetPeerBase::save($this);
 	}
 	
 }
