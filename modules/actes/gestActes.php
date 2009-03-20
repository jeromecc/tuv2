<?php
/*
 * Created on 13 oct. 08
 *

 * 
 * gestActes 
 * 
 * Interface pour XHAM du module collecteur d'actes.
 * 
 * Seule cette classe a le droit de manipuler les options et les methodes xham
	
 * 
 * 
 */
 
 //
 /* xhamException fonction qui servira d'appel pour les exceptions du collecteur d'actes dans l'environnment xham
  * @param $message ;
  * @param $type ;
  */
 function xhamException($message,$type='') {
 	global $xham;
 	$xham->addErreur($message);
 }
 
 class gestActes 
 {
 	private $af ='';
 	/*
 	 *  instancie la classe xham : génere l'environnement et appelle le controleur Actes
 	 * @param string $opt l'argument xham envoyé 
 	 */
 	public function __construct($opt='')
 	{
 		global $xham ;
		
		//instanciation de l'environnement
		gestActes::setEnvironnement();
		
		$fileClassSuffixes = '.class.php';
 		//inclusion des définition des classes mères
 		$motherDirectory = ( realpath(dirname(__FILE__).'/base/')) ;
 		include_once($motherDirectory.'caObjetBase'		.$fileClassSuffixes);
 		include_once($motherDirectory.'caObjetBasePeer'	.$fileClassSuffixes);
 		include_once($motherDirectory.'caEnvironnement'	.$fileClassSuffixes);
 		
 		//dans quel répertoire aller chercher les classes héritées ?
 		$suffixDirectory =  $xham->getOption('actes_site') ? upperCase($xham->getOption('actes_site')) : 'Default' ;  
 		
 		//inclusion des définition des classes filles
 		$dautherDirectory = ( realpath(dirname(__FILE__)."/ca$suffixDirectory/")) ;
 		include_once($motherDirectory.'toto'	.$fileClassSuffixes);
 		
 		//appel du controleur
 		$this->af = caController::executeAction($xham->getNavi(0),$xham->getNavi(1));
 		
 	}
 	/*
 	 * setEnvironnement Instancie l'objet environnement avec les options xham
 	 */
 	public static function setEnvironnement() 
 	{
 		global $xham ;
 		$listeOptions = array('patate','poireau');
 		foreach($listeOptions as $option) caEnvironnement::setOption($option,$xham->getOption($option));
 		//definition des connexions aux bases
 		caEnvironnementBase::setInfoConnexion('ccam','mysql',MYSQL_HOST,MYSQL_USER,MYSQL_PASS,CCAM_BDD);
 		caEnvironnementBase::setInfoConnexion('terminal','mysql',MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BDD);
 		caEnvironnementBase::setInfoConnexion('xham','mysql',MYSQL_HOST,MYSQL_USER,MYSQL_PASS,BASEXHAM);
 		
 		//definion de la fonction appellant les exceptions
 		caEnvironnementBase::setExceptionFunc('xhamException');
 	}
 	/*
 	 * récupération de l'affichage
 	 * @return string HTML ou XML code
 	 */
 	public function getAffichage() 
 	{
 		return $this->af();	
 	}
 	
 }
 
 
?>
