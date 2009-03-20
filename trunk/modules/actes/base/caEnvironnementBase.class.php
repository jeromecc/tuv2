<?php
/*
 * caEnvironnement est une classe statique
 * la consultation d'une option dans le code Actes ne se fait plus par $xham->getOption mais par
 * caEnvironnement::getOption();
 */
abstract class caEnvironnementBase {
	
	const OPTIONS = array()  ;
	const CONNEXIONS = array()  ;
	const EXCEPTIONFUNC = 'caEnvironnementBase::defaultExceptionManager'  ;
	
	/*
	 * getOption renvoie la valeur d'une option
	 * @param string id de l'option
	 * @param string valeur si l'option n'existe pas
	 * @return string valeur de l'option
	 */
    static public function getOption($nameOpt,$valDefault='') 
    {
    	if( isset( caEnvironnementBase::$OPTIONS[$nameOpt]))
    		return caEnvironnementBase::$OPTIONS[$nameOpt];
    	return 	$valDefault ;
    }
    
 	/*
	 * getOption avvecte la valeur d'une option
	 * @param string id de l'option
	 * @param string valeur 
	 * 
	 */   
    static public function setOption($name,$val) 
    {
    	caEnvironnementBase::$OPTIONS[$name] = $val ;
    }
    
    
    /*
     * getInfoConnexion renvoie un tableau décrivant une connexion, donné une id de connexion,
     * avec : type host nomdb user pass comme indice
     * @param string id de la connexion
     * @return array
     */
     static public function getInfoConnexion($id) 
     {
     	return caEnvironnementBase::$CONNEXIONS[$id] ;
     }
     
     /*
      * setInfoConnexion enregistre une connexion à une base de données dans l'environnement
      * @param string identifiant local au code
      * @param string type , mysql uniquement
      * @param string hote
      * @param string user
      * @param string pass
      * @param string nomdb
      */
     static public function setInfoConnexion($id,$type,$hote,$user,$pass,$nomdb)
     {
     	caEnvironnementBase::$CONNEXIONS[$id] = array (
     		'type'	=> $type ,
     		'host'	=> $hote ,
     		'nomdb'	=> $nomdb ,
     		'user'	=> $user ,
     		'pass'	=> $pass ,
     	
     	);
     } 
     
     /*
      * setExceptionFunc définit le nom de la fonction qui définit une exception
      * @param string $nomFonction
      */
       static public function setExceptionFunc($nomFonction)
       {
       		caEnvironnementBase::EXCEPTIONFUNC = $nomFonction ;
       }
     
     /*
      * addException jette une exception
      * @param string $text
      * @param string $type
      */
     static public function addException($text,$type='') 
     {
     	eval ( caEnvironnementBase::EXCEPTIONFUNC."($text,$type);") ;
     }
     
     /* 
      *  defaultExceptionManager , gestionnaire d'exceptions par default
      * @param string $text
      * @param string $type
      */
      static public function defaultExceptionManager($text,$type='') 
      {
      		die($text);
      }
}
?>