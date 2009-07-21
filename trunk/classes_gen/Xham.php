<?php
/*clXham.php
 * Created on 5 juin 2006
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Author : Damien Borel <dborel@ch-hyeres.fr>
 * Version 0.1A
 */

//override_function('print', '$a', 'echo "%%%%%%%% <b>Fatal error :</b>INCORRECT DATA FORMAT in <b>SOURICEAU</b> in clFoRmX.php at line 443.".$a;');


if(isset($relocate)) 
	include ( $relocate."classes_gen/XhamFunctions.php") ;
else
	include ( "classes_gen/XhamFunctions.php") ;

	
class Xham {
	// Les différents attributs...
	public $options ;
	public $logs ;
	public $pi ;
	public $session ;
	public $user ;
	public $errs ;
	public $date ;
	public $menu ;
	public $prefs ;
	public $tool ;
	public $requete ;
	public $navi ;
	public $query ;
	public $debTemps ;
	public $mode ; //rien pour normal, 'manuel' pour agir en tant que
	private $requestdata ; //contient les post, get, et permet de modifier leur acces
    public $nbRequetes ;
    public $tpRequetes ;
	private $noLog ;
	public $idUser ;
	public $opttab;
	public $stopAffichage;
	public $idClic;
	public $droits ;
	public $cache ;
	
	// Constructeur de la classe.
	//$options est un tableau d'options
	//options: 	dontCalcNavig=true  -> ne calcule pas l'affichage avec init
	//			navi=menu			->instancie xham avec ce menu precis à affichier
	function __construct ( $idUser="" ,$options="") {
		global $debug;
		global $xham_idClic; 
		//$debug .= affTab($_SESSION);
		if( $idUser )
			$this->mode = 'manuel';
		$this->idUser = ($idUser?$idUser:"A".IDAPPLICATION) ;
 		// Des choses à faire peut-être ?
 		if($this->getr('idsess')) $this->idUser = $this->getr('idsess') ;
 		if($options) 
 			$this->opttab = $options ;
 		else
 			$this->opttab = array() ;
 		$this->initCounts ( ) ;
 		if(isset($_POST['ajax']) || isset($_GET['ajax']))
 			$this->stopAffichage=true;
 	}

	// Initialisation de variables compteurs.
	function initCounts ( ) {
		if(! defined("DEFAULT_TIMEZONE"))
			define ( "DEFAULT_TIMEZONE", "CET" ) ;
 		if ( function_exists ( "date_default_timezone_set" ) )
 			date_default_timezone_set ( DEFAULT_TIMEZONE ) ;
		$this->debTemps = XhamTools::temps ( ) ;	
		$this->nbRequetes = 0 ;
		$this->tpRequetes = 0 ;
	}

	// Initialisation des différents objets.  
 	function init ( $noLog='0' ) {
 		global $xhamerreurs;
		// Appel de la classe Erreurs.
		if (is_object($xhamerreurs))
			$this->errs = $xhamerreurs ;
		else
			$this->errs = new XhamErreurs ( ) ;
	    // Récupération de la navigation.	    
	    if( isset($this->opttab['navi'])) 
	    	$this->setNavi   ( $this->opttab['navi'] ) ;
	    else 
	    	$this->setNavi   ( ) ;
		// Objet XhamQuery.
		$this->query   = new XhamQuery ( ) ;
		// Objet Requete.
		$this->requete = new XhamRequete ( '', '', '', '', '', '' ) ;
		// Appel de la classe Options.
		$this->options = new XhamOptions ( $this ) ;
		// Appel de la classe Logs.
		$this->logs    = new XhamLogs ( $this ) ;
		// En fonction de la variable noLog, on log ou non cette action.
		if ( $noLog ) $this->noLog ( ) ;
		// Appel de la classe PostIt.
		$this->pi      = new XhamPostIt ( ) ;
		// Ajout des statistiques.
		$this->stats   = new XhamStats ( $this, $noLog ) ;
		// Appel de la classe session V2.
		$this->session = new XhamSessionV2 ( $this, $this->idUser ) ;
		$this->session->loadSession();
		// Récupération de l'utilisateur.
		$this->user = $this->session->getUser ( ) ;
		// Récupération du tableau brut de l'utilisateur.
		$this->informations = $this->user->getInformations ( ) ;
		// Récupération des droits de l'utilisateurs.
		$this->setDroits ( ) ;
		//plus de droits si la methode suivante est definie dans la classe utilisateur
		if( method_exists($this->user,'setMoreXhamDroits'))
			$this->user->setMoreXhamDroits();
		// Récupération de la date du jour.
		$this->date    = new clDate ( ) ;
		// Fabrication du menu.
		$this->menu    = new XhamMenu ( ) ;
		// Mise à jour des stats.
		$this->stats->setStats ( ) ;
		// Appel des préférences.
		$this->prefs   = new XhamPreferences ( $this ) ;
		// Classe avec fonctions aides diverses.
		$this->tool    = new XhamTools ( ) ;
		// Fabrication de la page.
		if( ! isset($this->opttab['dontCalcNavig'])) 
			$this->navigation = new XhamNavigation ( $this ) ;
		
 	}
 	
	// ******************************************************************************************* //
  	// ***************************************** Divers ****************************************** //
  	// ******************************************************************************************* //
 	
 	// Retourne une valeur d'une variable transmises en GET ou POST.
 	function getr($var) {
 		
 		if(isset ( $this->requestdata[$var] ) )
 			return $this->requestdata[$var];
		if ( isset ( $_POST[$var] ) )
 			return 	$_POST[$var] ;
 		if ( isset ( $_GET[$var] ) )
 			return 	$_GET[$var] ;
		else
			return false ;
 	}
 	
 	// Retourne une valeur d'une variable transmises en GET ou POST en traitant la chaine avec utf8_decode
 	function getrd($var) {
 		
 		if(isset ( $this->requestdata[$var] ) )
 			return $this->requestdata[$var];
		if ( isset ( $_POST[$var] ) )
 			return 	utf8_decode($_POST[$var]) ;
 		if ( isset ( $_GET[$var] ) )
 			return 	utf8_decode($_GET[$var]) ;
		else
			return false ;
 	}
 	
 	function setr($var,$val) {
 		if( ! $this->requestdata )
 			$this->requestdata = array();
 		$this->requestdata[$var]=$val; 
 		}
 	
 	// Retourne le contenu d'un define.
 	function getDefine ( $name ) {
 		$tmp = '' ;
 		if ( defined ( $name ) ) { eval ('$tmp = '.$name." ;" ) ; return $tmp ; }
 		else return false ;
 	}
 	
  	// Retourne la valeur d'une option.
 	function getOption ( $opt ) {
 		return $this->options->getOption ( $opt ) ;
 	}
 	
 	// Retourne l'affichage des menus.
 	function getMenu ( ) {
 		//return "<b>Fatal error :</b> INCORRECT DATA FORMAT <b>DROSOPHILE</b> in clFoRmX.php at line 1337<br/>".$this->menu->getAffichage ( ) ;
 		return $this->menu->getAffichage ( ) ;
 	}
 	
 	// Retourne l'affichage de la page.
 	function getAffichage ( ) {
 		return $this->navigation->getAffichage ( ) ;
 	}
 	
 	// Ajoute une erreur XHAM.
 	function addErreur ( $text, $exit='' ) {
 		$this->errs->addErreur ( $text, $exit) ;
 	}

	// Exécute une requête XhamQuery.
	function Execute ( $type_entree, $requete, $param, $type_sortie='ResultQuery' ) {
		return $this->query->Execute ( $type_entree, $requete, $param, $type_sortie ) ;
	}
	
	function setMode($s) { $this->mode = $s ; }
	function getMode() { return $this->mode ; }
	
	// Retourne la liste des items.
  	// $nomListe : Nom de la liste à récupérer.
  	// $opt : Met une case vide en début de tableau si vrai.
  	// $code : Rempli le tableau avec le code de l'item au lieu de son nom si vrai.
  	// TODO : A virer de XHAM... Mais où ?
  	function getListeItems ( $nomListe, $opt='', $code='' ) {
     	$this->options-> checkOptionListe ( $nomListe ) ;
    	// Préparation du type de classement pour la requête.
    	switch ( $this->getOption ( $nomListe ) ) {
    		case 'Manuel': $order = "ORDER BY rang" ; break ;
    		case 'Alphabétique': $order = "ORDER BY nomitem" ; break ;
    		case 'Alphabétique inversé': $order = "ORDER BY nomitem DESC" ; break ;
    		default : $order = "ORDER BY nomitem" ; break ;
    	}
    	$param['cw'] = "WHERE nomliste='".addslashes($nomListe)."' $order" ;
    	$res = $this -> Execute ( "Fichier", "getListesItems", $param, "ResultQuery" ) ;
    	//print affTab ( $res ) ;
    	if ( $opt ) $tab[] = SELECTLISTE ;
    	// Fabrication du tableau.
    	for ( $i = 0 ; isset ( $res['iditem'][$i] ) ; $i++ )
      		if ( $code ) $tab[$res['libre'][$i]] = $res['nomitem'][$i] ;
      		else $tab[$res['nomitem'][$i]] = $res['nomitem'][$i] ;
    	// Retourne le tableau au format attendu par modelixe.
    	return $tab ;
  	} 	
	
	// ******************************************************************************************* //
  	// *************************************** XhamCache ***************************************** //
  	// ******************************************************************************************* //
	
	// Ajoute une variable en cache.
	function addCache ( $nom, $var ) {
		$this->cache[$nom] = $var ;
	}
	
	// Retourne la valeur de la variable cache.
	// Si $i, $j et $k existent alors, on les utilisent comme les indices d'un tableau.
	function getCache ( $nom, $i='%CACHEXHAM%', $j='%CACHEXHAM%', $k='%CACHEXHAM%' ) {
		if ( $i != '%CACHEXHAM%' ) {
			if ( $j != '%CACHEXHAM%' ) {
				if ( $k != '%CACHEXHAM%' ) { 
					if ( isset ( $this->cache[$nom][$i][$j][$k] ) ) return $this->cache[$nom][$i][$j][$k] ; }
				elseif ( isset ( $this->cache[$nom][$i][$j] ) ) return $this->cache[$nom][$i][$j] ;
			} elseif ( isset ( $this->cache[$nom][$i] ) ) return $this->cache[$nom][$i] ;
		} elseif ( isset ( $this->cache[$nom] ) ) return $this->cache[$nom] ;
	}
	
	// Retourne vrai si cet élément existe dans le cache.
	function isInCache ( $nom ) {
		if ( isset ( $this->cache[$nom] ) ) return true ;
		else return false ;
	}
	
	// ******************************************************************************************* //
  	// *************************************** XhamLogs ****************************************** //
  	// ******************************************************************************************* //
	
	// Désactive les logs.
	function noLog ( ) {
		$this->logs->noLog ( ) ;
	}
	
  	// Change le log supplémentaire.
  	function setLogSup ( $logSup='' ) {
    	$this->logs->setLogSup ( $logSup ) ;
  	}
	
	// Destructeur
  	function __destruct ( ) {

  	}
	
	//si, si, hombre hombre
	function eldestructor() {
		if ( ! $this->logs->IsNoLog ( ) ) {
      		$this -> logs -> addLog ( "navi", $this->getNaviFull ( ), $this->logs->logSup ) ;
    	}
	}
	
	// ******************************************************************************************* //
  	// ************************************** XhamSession **************************************** //
  	// ******************************************************************************************* //

	// Retourne une informations de l'utilisateur.
	function getInfo ( $info ) {
		return $this->session->getInfo ( $info ) ;
	}

 	// Renvoie vrai si l'utilisateur a accès à la page avec le droit demandé.
  	function getDroit ( $page, $type='r' ) {
  		//if(isset($this->droits))
  			//print afftab($this->droits) ;	
    	if ( isset ( $this->informations['superadmin'] ) AND $this->informations['superadmin'] ) return 1 ; 
    	else return ( ( isset ( $this->droits[$page] ) ? $this->droits[$page][$type] : '' ) ) ; 
  	}
 	
 	// Récupération des droits de l'utilisateur connecté.
  	function setDroits ( ) {
  		//print $this->user->getIdGroupes ( );
    	$droits = new XhamDroits ( $this, $this->user->getIdGroupes ( ) ) ;
// 		eko( $this->user->getIdGroupes ( ));
    	$this->droits = $droits->getDroits ( ) ;
    	//print affTab($this->droits);
  	}
 	
	// ******************************************************************************************* //
  	// ************************************** XhamRequete **************************************** //
  	// ******************************************************************************************* //
 	
 	// Création d'une instance de la classe XhamRequete
 	function NewRequete ( $db, $table, $data='', $h='', $u='', $p='' ) {
 		$this->requete = new XhamRequete ( $db, $table, $data, $h, $u, $p ) ;
 	}
 	
 	// Lancement de l'ajout d'un enregistrement.
 	function addRecord ( ) {
 		return $this->requete->addRecord ( ) ;
 	}
 	
 	// Lancement de la mise à jour d'un enregistrement.
 	function updRecord ( $arg='' ) {
 		return $this->requete->updRecord ( $arg ) ;
 	}	
 	
 	// Lancement de la suppression d'un enregistrement.
 	function delRecord ( $arg='' ) {
 		return $this->requete->delRecord ( $arg ) ;
 	}	
 	
	// Lancement du vidage d'une table.
	function truncateTable ( ) {
		return $this->requete->truncateTable ( ) ;
	} 
	
	// Lancement de la maj ou création.
	function uoiRecord ( $arg='' ) {
		return $this->requete->uoiRecord ( $arg ) ;
	}
	
	// ******************************************************************************************* //
  	// ************************************** Navigation ***************************************** //
  	// ******************************************************************************************* //
  	
  	// Permet de se situer dans la navigation...
  	function getNavi ( $lvl ) {
  		if ( isset ( $this->navi[$lvl] ) ) return $this->navi[$lvl] ; 
  	}
  	
  	// Renvoie la navigation complète pour débugage.
  	function getNaviFull ( ) { 
  		$navi = '' ; 
  		for ( $i = 0 ; isset ( $this->navi[$i] ) ; $i++ ) 
  			$navi .= $this->navi[$i]."|" ; 
  		$ret = rtrim($navi,'|') ;
  		if($this->getr('idsess'))
  			 $ret.="&idsess=".$this->getr('idsess');
  		return $ret; 
  	}
  	
  	// Retourne la navigation actuelle.
  	function genNaviFull ( ) { 
  		//agir en tant que version un
  		if ( isset ( $_POST['idSessionATQ'] ) ) $sess = $_POST['idSessionATQ'] ;
    	if ( isset ( $_GET['idSessionATQ'] ) ) $sess = $_GET['idSessionATQ'] ;
    	if ( isset ( $sess ) ) $lienSessionATQ = "&idSessionATQ=".$sess ;
		//pas de menu
    	if ( isset ( $_POST['noMenu'] ) ) $nomenu = $_POST['noMenu'] ;
    	if ( isset ( $_GET['noMenu'] ) ) $nomenu = $_GET['noMenu'] ;
    	if ( isset ( $nomenu ) ) $lienNoMenu = "&noMenu=".$nomenu ;
    	$ret = $this->navifull.(isset($lienSessionATQ)?$lienSessionATQ:'').(isset($lienNoMenu)?$lienNoMenu:'') ;
    	//Agir en tant que v2
    	if($this->getr('idsess')) {
    		//ne pas mettre &amp; ici, sinon ça fait foirer le passage de idsess dans une requete ajax
    		$ret.="&idsess=".$this->getr('idsess');
    	}
 		return $ret;   	 
  	}
  	
  

  	
  	//initialisation manelle de la navigation avec calcul du cryptage navi 'à la volée'
  	//attend argument formé par men1|men2|men3 NON CRYPTE
  	function genAndSetNavi($naviforce) {
  		$navi = $this->genNavi($naviforce);
  		$this->setNavi($navi);
  	}
  	
  	
  	// Initialisation de la navigation.
  	function setNavi ( $naviforce='') {
  		
    	// Elle est transmise soit dans une variable de type GET (lien),
    	// soit dans une variable de type POST (formulaire).
    	// ajout manu: soit dans la variable get 'clean'
    	if ( ENCODERURL ) $navi = 'QWNjdWVpbA=='; else $navi = 'Accueil' ;
    	if ( isset($_GET['navi']) && $_GET['navi'] ) $navi = $_GET['navi'] ;
    	if ( isset($_POST['navi']) && $_POST['navi'] ) $navi = $_POST['navi'] ;
  		if ( isset($_GET['navi']) && ereg('http://',$_GET['navi']) ) $navi = 'Accueil' ; // antispam
  		
    	$this->navifull = $navi ;
    	if ( isset($_REQUEST['Déconnexion']) && $_REQUEST['Déconnexion'] ) { $this->navifull = '' ; $navi = '' ; }
    	if ( $naviforce ) {
      		$navi = $naviforce;
      		$this->navifull = $navi ;
    	}
   		// print "<br/><br/>on est dans setnavi. navifull=".$this->navifull.'<br/>';
    	// Si la navigation a bien été transmise, alors on récupère les différents
    	// niveaux dans un tableau.
    	if ( ENCODERURL ) { 
      		// NOTE : un petit str_replace histoire que base64_encode('FABIENLAFOUINE') soit un peu partout dans les URL.
      		if ( $navi ) { $this->navi = explode ( "|", base64_decode ( str_replace ( "RkFCSUVOTEFGT1VJTkU", "+", $navi )))   ;
      		} else { $this->navi[0] = "Accueil" ; } 
    	} else { 
      		if ( $navi ) { 
      			$this->navi = explode ( "|", $navi ) ; 
      			// Sinon, on initialise le premier niveau à la page d'accueil du terminal.
     		 } else { 
     		 	$this->navi[0] = "Accueil" ; 
     		 } 
    	}
    	$_SESSION['XHAM_veryOldNavi']=(isset($_SESSION['XHAM_oldNavi'])?$_SESSION['XHAM_oldNavi']:'');    
    	$_SESSION['XHAM_oldNavi']=(isset($_SESSION['XHAM_Navi'])?$_SESSION['XHAM_Navi']:'');
    	$_SESSION['XHAM_Navi']=$this->navi;
  	}
  	
  	// Même que ça fait un truc cool il parait !
  	//ok tu veux des commentaires ?
  	//declaration et nom de la methode
  	function unsetOldNavi ( ) {
  		//affecte la valeur de la variable $_SESSION['XHAM_oldNavi'] à la variable $_SESSION['XHAM_Navi'
    	$_SESSION['XHAM_Navi']=$_SESSION['XHAM_oldNavi'];
    	//affecte la valeur de la variable $_SESSION['XHAM_veryOldNavi'] à la variable $_SESSION['XHAM_oldNavi'
    	$_SESSION['XHAM_oldNavi']=$_SESSION['XHAM_veryOldNavi'];
    	//accolade de fermeture de la methode
  	}

	// Redéfinit à la volée.
  	function setMiniNavi ( $lvl, $valeur ) {
    	$this->navi[$lvl] = $valeur ;
  	}

  	// Génération de la valeur de la variable navigation à transmettre.
  	function genNavi ( ) {
    	// Récupération du nombre d'arguments de la fonction.
    	$n = func_num_args ( ) ;
    	// Pour chaque argument, on le concatène au précédent avec le séparateur |.
    	for ( $i = 0 ; $i < $n ; $i++ ) {
      		if ( isset ( $lien ) ) $lien .= "|".func_get_arg ( $i ) ;
      		else $lien = func_get_arg ( $i ) ;
    	}
    	// Si aucun lien n'est défini, on renvoie vers la page d'accueil.
    	if ( ! isset ( $lien ) ) $lien = "Accueil" ;
    	if ( isset ( $_POST['idSessionATQ'] ) ) $sess = $_POST['idSessionATQ'] ;
    	if ( isset ( $_GET['idSessionATQ'] ) ) $sess = $_GET['idSessionATQ'] ;
    	if ( isset ( $sess ) ) $lienSessionATQ = "&idSessionATQ=".$sess ;
    	else $lienSessionATQ = "" ;
    	if ( isset ( $_POST['noMenu'] ) ) $nomenu = $_POST['noMenu'] ;
    	if ( isset ( $_GET['noMenu'] ) ) $nomenu = $_GET['noMenu'] ;
    	if ( isset ( $nomenu ) ) $lienNoMenu = "&noMenu=".$nomenu ;
    	else $lienNoMenu = '' ;
    	// On renvoie la chaîne ainsi construite. (Et on remplace les '+' par le résultat 
    	// de base64_encode ( "FABIENLAFOUINE" ) : vrai mais peu probable dans une url...)
    	if ( ENCODERURL ) $ret =  str_replace ( "+", "RkFCSUVOTEFGT1VJTkU", base64_encode ( $lien ) ).$lienSessionATQ.$lienNoMenu ;
    	else  $ret = $lien.$lienSessionATQ.$lienNoMenu ;
      	//Agir en tant que v2
    	if($this->getr('idsess')) {
    		//ne pas mettre &amp; ici, sinon ça fait foirer le passage de idsess dans une requete ajax
    		$ret.="&idsess=".$this->getr('idsess');
    	}
 		return $ret;   	 
  	}	
  	

  	// On garde la navigation dans le menu XHAM (les deux premiers niveau de la variable navi)
  	function genNaviPlus ( ) {
    	// Récupération du nombre d'arguments de la fonction.
    	$n = func_num_args ( ) ;
    	// Pour chaque argument, on le concatène au précédent avec le séparateur |.
    	$lien = $this->getNavi(0).'|'.$this->getNavi(1) ;
    	for ( $i = 0 ; $i < $n ; $i++ ) {
      		if ( isset ( $lien ) ) $lien .= "|".func_get_arg ( $i ) ;
      		else $lien = func_get_arg ( $i ) ;
    	}
    	// Si aucun lien n'est défini, on renvoie vers la page d'accueil.
    	if ( ! isset ( $lien ) ) $lien = "Accueil" ;
    	if ( isset ( $_POST['idSessionATQ'] ) ) $sess = $_POST['idSessionATQ'] ;
    	if ( isset ( $_GET['idSessionATQ'] ) ) $sess = $_GET['idSessionATQ'] ;
    	if ( isset ( $sess ) ) $lienSessionATQ = "&idSessionATQ=".$sess ;
    	else $lienSessionATQ = "" ;
    	if ( isset ( $_POST['noMenu'] ) ) $nomenu = $_POST['noMenu'] ;
    	if ( isset ( $_GET['noMenu'] ) ) $nomenu = $_GET['noMenu'] ;
    	if ( isset ( $nomenu ) ) $lienNoMenu = "&noMenu=".$nomenu ;
    	else $lienNoMenu = '' ;
    	// On renvoie la chaîne ainsi construite. (Et on remplace les '+' par le résultat 
    	// de base64_encode ( "FABIENLAFOUINE" ) : vrai mais peu probable dans une url...)
    	if ( ENCODERURL ) $ret =  str_replace ( "+", "RkFCSUVOTEFGT1VJTkU", base64_encode ( $lien ) ).$lienSessionATQ.$lienNoMenu ;
    	else  $ret = $lien.$lienSessionATQ.$lienNoMenu ;
      	//Agir en tant que v2
    	if($this->getr('idsess')) {
    		//ne pas mettre &amp; ici, sinon ça fait foirer le passage de idsess dans une requete ajax
    		$ret.="&idsess=".$this->getr('idsess');
    	}
 		return $ret;   	 
  	}	
  	
  	// récupération de l'ancienne variable de Navigation
  	function getOldNaviFull() {
  		return implode('|',$_SESSION['XHAM_oldNavi']);
  	}
  	function getOldNavi() {
  		return $_SESSION['XHAM_oldNavi'];
  	}
  

  	function crypt ( $val ) {
    	$cle = CRYPTKEY ;
    	$lenCle = strlen ( $cle ) ;
    	$lenVal = strlen ( $val ) ;
    	$crypt = '';
    	for ( $i = 0 ; $i < $lenVal ; $i++ ) {
      		$crypt .= substr ( $val, $i, 1 ) ^ substr ( $cle, $i % $lenCle, 1 ) ;
    	}
    	return $val ;
  	}
  	
  	
} 
 

 
?>