<?php

/*
Titre  : Classe FormX
Emmanuel Cervetti
Date   : Aout 2005

Description : 
Cette classe genere une instance de "Formulaire Etendu" à partir d'un fichier XML bien formé ( voir inclusion_guidage.xml pour exemple ultra commenté).
Cela permet de generer, charger, stocker, modifier des formulaires "actifs"
la variable statique FORMX_LOCATION doit être definie
1.3.1 28 bugs plus tard edition
-corrections de clFormX et clFormXtools en rapport avec la migration Montana

1.3.0 krakatoa edition
-mise à jour du fichier clFoRmXtOols.php nécessaire pour l'export de données formx via le terminal des urgences
1.2.9 hannibal Lecter edition (beta)
-implémentation de l'attribut onDelete qui appelle un script lorque le formulaire est effaçé
-validation de l'attribut 'optimize' cf 1.2.8
1.2.8 The day after edition
-implémentation de l'attribut expérimental 'optimize' , affecté à la balise 'ETAPE', qui à travers certaines heuristiques
de traitement, améliore considérablement la rapidité des gros formulaires. (x5 sur le formulaire de la main)
1.2.7 Tchernobil edition
-amélioration du type 'goto' de la balise ACTION, désormais on peut rêpeter un bloc d'étapes et également aller plus loin vers l'avant
1.2.6 Iwo Jima edition
-les balises RADIO et CHECK acceptent désormais l'attribut 'cols=n' , qui permettent de regrouper les items par n colonnes 
1.2.5 Titanic edition
-correction bug lorsque base différente de la base de l'appli
-ajout du type "goto" à la balise ACTION, il permet de rêpeter indéfiniment une étape
-fichierss modifiés: clFormx clFormxSession
1.2.4 World trade center edition
-ajout de l'attribut "bloc" dans la balise "ActiveItem".
Il permet de définir une zone de répétition de l'ensemble des items clonés
1.2.3 Raspoutine edition
-ajout de l'attribut hyperinfo qui permet d'ajouter une infobulle d'aide ou une iframe d'aide:
Il se place dans la balise ITEM
Cela peut être soit sur la forme d'une iframe, exemple: hyperinfo="url:http://www.mimun2.com/spock.jpg;width:286;height:388;"¤
Soit sous la simple forme d'une infobulle textuelle, exemple: hyperinfo="text:Ici un texte explicatif d'explication;"
1.2.2 Greentiger edition
-Possibilité de changer un libelle en cours d'edition du formulaire, via API
-Enrichissement de la methode API easyFormx
-Enrichissement de FormxSession pour besoins de ces maj
-debug utf8 dans affichage des dates de l'historique d'un item 
1.2.1 Roselyne Bachelot Edition
-Correction de bug concernant le nom de l'auteur lors de l'affichage de l'historique d'un item
-Ajout de l'attribut 'closeOnLoad' qui cloture un formulaire en l'ouvrant (utile pour formulaires en lecture seule)
1.2.0 Tsunami edition
Ajout de la nouvelle balise <OnChangeRefresh> qui permet à un item de mettre à jour d'autres items
quand il est modifié
1.1.9 Pompeï edition
Debug critique de la methode easyFormx qui travaillait sur le droit global formx au lieu du droit spécifique
du formulaire quand il était spécifié
1.1.8 Ravaillac editon
spécialisation de la methode getAllValuesFromFormx()
1.1.7 Enola Gay Edition
L'attribut priority qui définit l'ordre d'apparition des formulaires marche désormais de manière décroissante
cad le formulaire de priorité 1 sera affiché avant le forulaire de priorité 10
Fichier modifié: clFoRmXtOoLs.php
1.1.6 Hiroshima Edition
résolution du bug #borel.1, dans un item de type TAB les colonnes peuvent à nouveau contenir des choix multiples différents 
1.1.5
résolution du bug #derock.1 , les listes multiples ont de nouveau l'indication "choix multiple = [ctrl] " 
1.1.4
La balise <Expediteur> à l'interieur d'une balise <ACTION> est désormais optionnelle,
conformément à la documentation, et ne génère plus d'erreurs si elle est absente.
1.1.3
Modification du comportement de la balise CHECK: si pas de données post reçues,
et pas de critère conditionel défini on réinitialise la valeur à vide.
1.1.2
Debug du stockage des variables globales à la validation d'un formulaire  ayant l'attribut closeAfterValid 

*/	

	

class  clFoRmX {

  //--- Attributs de la classe.
  // Contient l'affichage généré par la classe.
  protected $af ;
  // contient les javascripts générés dynamiquement par la classe lors de l'affichage 
  protected $js = '' ;
  // objet principal XML DOM
  public $XMLDOM ;
  // buffer SimpleXML
  public $XMLCore ;

  
  //les valeurs possibles de l'attribut type de la balise ITEM
  private $types;
  //le prefixe de l'id des champs des formulaires HTML
  public $prefix;
  //longueur de caracteres max des champs affichables
  private $lngchmp;
  //valeur du texte invitant à choisir dans un select (se deduit
  private $invselect;
  //classes necessaires pour la manipulation des listes XHAM
  public $listeCom;
  public $listeGen;
  //valeur par defaut des du nombre de champs select multiples affiches
  private $multiselectsize;
  //nombre de ligne d'un champ texte par defaut
  private $defrows;
  //identifiant unique de l'instance du formX
  private $idInstance;
  //variable pour savoir si un calendrier est généré
  private $ya_un_cal;
  //identifiant de sujet
  public $ids;
  //type de formulaire
  private $idformx;
  //est-ce que qqn a cliqué sur quelque chose avant l'instanciation
  private $isuseraction;
  //utilitaire de parité pour affichage de lignes
  private $tmp_parite;
  //pour affichage de fusions, utilitaire
  private $current_fusion;
  //utilitaire marqueur de non affichage
  private $rien;
  //marqueur de finitude de formualire, utilitaure
  private $justClosed ;
  //objet sujet du formulaire (optionel)
  //doit contenir la methode get et être instanciée par une classe heritée
  public $subject;
  //accesseur du formulaire
  public $accessor;
  //createur du formulaire
  public $author; 
  //Destination des uplads de fichier, à spécialiser par la classe héritée
  protected $destinationUploads;
 //Droit général sur le formulaire, à spécialiser par la classe héritée
  protected $droit;
  //lors de la non validation d'une étape, premier item trouvé comme non rempli (simpleXML) 
  public $lastNonValidItem;
  //boolean : indique au generateur d'imression si on est en mode impression ou pas
  private $modeImpression;
  //date de derniere modification, affecté par methode loadInstance
  private $dt_modif;
  //libelle du formulaire
  public $libelle;
  //si bouton de fermeture des formulaires autorisé (pas le cas quand generation des iframes)
  public $isWindowClose;
  //nom du groupe d'appartenance du formulaire (1er trouvé dans le code XML)
  //le voir comme le nom du classeur de formulaires
  public $groupeClassement;
  
  //id de l'application qui a créé le formulaire
  public $idApplication;
  
  private $useCache;
  
  //marqueur de non affichage du formulaire
  private $ImustDisapear ;
  
  /*
  Constructeur de la classe. se construit avec un identifiant de sujet (ids)
  eventuellement les options, séparés par un espace
  NO_POST_THREAT : ne traite pas les données post pour mettre à jour un formulaire
  
  */
//$fin = $this->temps();
//    $total = $fin - $debut;
//    $total = substr($total,0,8);
//    if ( DEBUGSQL ) eko ( "Temps d'exécution de la requête : $total ($requete)" ) ;
   //
  //  
   // return ();
  //Debug
  
  function debug($log){
  	//eko('L'.clFoRmXtOoLs::upCodeInfo().$log.'<br/>');
  	if(defined('FORMX_DEBUG') && FORMX_DEBUG) {
  		print 'L'.clFoRmXtOoLs::upCodeInfo().' '.$this->debugTemps().' '.$log."\n\r".'<br/>';	ob_flush() ; flush() ; }
  	if(defined('FORMX_DEBUG_EKO') && FORMX_DEBUG_EKO)
  		eko($log.'<br/>');
  }
  
  
  function debugTemps() {
  	global $temps_debug_formx;
  	$time = microtime();
  	$tableau = explode(" ",$time);
  	$temps = $tableau[1] + $tableau[0];
  	if( ! $temps_debug_formx )
  		$temps_debug_formx = $temps;
  	return substr($temps - $temps_debug_formx,0,4);
  }
  
  

  
  // Construction.------------------------------
  function __construct ($ids='',$options='') { 	
  	$this->construire($ids,$options);
  
	}
  
  function construire($ids,$options='') {
	global $errs;	//necessaire ???
	global $formxSession; //necessaire pour acces via les helpers (fonctions autonomes )

  //inititalisation des variables globales

//FIXME ce qui suit devrait etre remplacé par des variables d'environnement;
  
  
  $this->debug("entrée dans le constructeur. arguments ids=$ids options=$options");

 $this->debug("initialisation de l'objet liste");
  $this->setTypes();
  $this->setPrefix();
  $this->setLngchmp();
  $this->setInvselect();
  $this->setListes();
  $this->setMultiselectsize();
  $this->setDefrows();
  $this->instanciedsOptions=$options;
 
 $this->ImustDisapear = false ;

  $this->debug("chargement de l'objet session");
  //vars d'environnement
  if( isset($formxSession) && is_object($formxSession) )
  {
	$this->session = $formxSession ;
  } else if ( isset( $this->session ) && is_object($this->session) ) {
	$formxSession = $this->session ;
  } else { 
	$formxSession = clFoRmXtOoLs::getCurrentFormxSession(); 
	$this->session = $formxSession ;
  }

	$this->droit = $this->session->droit ;
 
 $this->fix_comp_1_1_5();
   
  //$this->setIdInstance($idInstance);
  $this->setIDS($ids);
  	$this->debug('Chargement des variables globales...');
  	if($this->ids && $this->session->tableVariables ) {
		if($this->loadGlobvars())
			$this->debug('ok');
  	} else {
		$this->debug('Pas d\'ids ou de table variables... abandon');
  	}
	if(isset($_GET['fxstrpost'])) {
		$this->debug('trouvé données dans le _GET à transmettre au _POST');
		$_POST = $_POST + clFoRmXtOoLs::string_to_array($_GET['fxstrpost']);
			$a="";
		   foreach($_POST as $key=>$value) {
   			$a .= $key."=".$value." ; ";
   				} 
   			$this->debug('données POST actuelles: '.$a);
	}	

  if(  ereg("MODE_IFRAME",$options) ) 	$this->isWindowClose=false; 
  	else $this->isWindowClose=true;
  //Si pas de traitement des données post demandé
  
  $this->debug("La prochaine ligne sera 'entrée dans TraiterPost' sauf si option NO_POST_THREAT spécifiée dans le deuxieme argument du constructeur");
 
  if( ! ereg("NO_POST_THREAT",$options) )   $this->TraiterPost();
   	$this->debug('Sortie du constructeur');
 

 
  }
  
  
  
  
  //instanciation des variables privées lors de la construction
   function setTypes() {$this->types=array('TXT','LONGTXT','CAL','SLIDER','LISTE','LISTEDYN','RO','RADIO','CHECK','LISTEN','TAB','FILE'); }
   function setPrefix() {$this->prefix='FoRmX_'; }
   //2 fonctions suivantes: astuces  pour ques les champs select aient la même longueur

//FIXME fonction obsolete, doit etre REMPLACÉE
   function setLngchmp($l=44) {$this->lngchmp=$l; }
//FIXME fonction obsolete et débile, doit etre REMPLACÉE
   function setInvselect($mess="--choisir--") {
   	if (! isset($this->lngchmp)) {
		$this->invselect="choisir";
		return ;
		}
	$this->invselect=$mess;
   	}
	//instanciation des classes pour les listes XHAM
function setListes() {
 global $xham;
 if(defined("VERSIONXHAM") && VERSIONXHAM == "2" ) {
 		$this->listeGen = new XhamGestionListesGenerales( $xham);
 } else {
	if (class_exists('clListes') && class_exists('clListesGenerales')) {
  		$this->listeCom = new clListes ( "Recours", "recup" ) ;
  		$this->listeGen = new clListesGenerales ( "recup" ) ; 
	}
 }	
}
	
   function setMultiselectsize($a = 4) {$this->multiselectsize = $a ;}
   function setDefrows($a = 2) {$this->defrows = $a ;}
   function setIdInstance($a='') {$this->idInstance=$a;}
   function setIDS($ids='') {
   	$this->ids=$ids;
	}
   
   //utilitaire pour compatibilité < 1.1.5
   function printDefineProblem($a) {
   	if(! defined('FX_'.$a)) {
   				if(defined($a)) 
   					define('FX_'.$a,constant($a));
   				else
   					$this->addErreur("La variable d'environnement ".'FX_'.$a." N'est pas définie.");
   				}
   }
   
   function fix_comp_1_1_5() {
   	/*
    $this->printDefineProblem('URL');$this->printDefineProblem('URLCACHE');$this->printDefineProblem('URLCACHEWEB');$this->printDefineProblem('URLIMGLOGO');$this->printDefineProblem('URLIMGEDI');
    $this->printDefineProblem('URLIMGRIEN');$this->printDefineProblem('URLIMGCLO');$this->printDefineProblem('URLDOCS');$this->printDefineProblem('URLIMGANNMINI');$this->printDefineProblem('URLIMGVAL');
    $this->printDefineProblem('URLIMGPREV');$this->printDefineProblem('URLIMGNEXT');$this->printDefineProblem('URLLOCAL');
    */
   }
   
   function getIdInstance() {return $this->idInstance;}
   function getPriority() {
   	if ($this->XMLDOM->getElementsByTagName('FORMX')->item(0)->hasAttribute('priority') ) {
   	//eko($this->XMLDOM->getElementsByTagName('FORMX')->item(0)->getAttribute('priority'));
   		return $this->XMLDOM->getElementsByTagName('FORMX')->item(0)->getAttribute('priority') ; }
   	else
   		return 0 ;
   	}
   function getXML() {return utf8_decode(addslashes($this->XMLDOM->saveXML())); }
   function getIDS() {return $this->ids;}
   
   

   
   /*renvoie un tableau avec les identifiants d'instance -> titre concernant un ids donné
   status : I: init / F : fini / E : en cours
   */
  function ListFromIds($status='') {
  	return clFoRmXtOoLs::ListFromIds($this->ids,$status);
    }
  

function getRootDom()
  {
	if(! is_object($this->XMLDOM->documentElement))
		throw new Exception("Acces in getRootDom without any loaded instance.");
  	return $this->XMLDOM->documentElement ;
  }


   /*charge un squelette de formulaire dans le buffer XML de la classe 
  typiquement , loadForm($fichier)
	*/
  function loadForm($ressource) {
  	if(! ereg("xml",$ressource))
  		$ressource.=".xml";
  	//on crée l'objet DOM
	$this->XMLDOM = new DomDocument ();
	if (! $this->XMLDOM->load($this->session->xmlLocation.$ressource)) {
		$this->addErreur("Impossible de charger le fichier $ressource");
		return '';
		}

	//on va verifier que la balise <val/> existe pour chaque <item/> sinon, on le crée

	foreach( formxTools::domSearch($this->getRootDom(),'ITEM')  as $item )
	{
		$val='';
		if ( ! formxTools::isTag($item,'Val')) 
		{
			if ( formxTools::isTag($item,'From') )
				$this->makeBalVal($item,'Val',$this->getValueFrom( formxTools::getTagValue($item,'From') )); //reinject /  creation
			else
				$this->makeBalVal($item,'Val',''); //reinject /  creation
		}
	}
	
	//on crée la balise qui marque l'application qui a créé le formulaire	
	$this->makeBalVal($this->XMLDOM->documentElement,'IdApplication',$this->session->idApplication); //reinject /  creation
		
	
	
	$etapes = $this->XMLDOM->documentElement->getElementsByTagName('ETAPE');	//liste de nodes	
	//on numerote les etapes
	$i = 0;
	foreach($etapes as $etape) {
		//par defaut on optimise
		  
		if( ! $etape->hasAttribute('optimize') ||  in_array($etape->getAttribute('optimize'),array('n','N','non') ) || $etape->getAttribute('optimize')=='' )
		
			$etape->setAttribute('optimize','y');	
		$i++;
		$etape->setAttribute('no_etape',$i);
		}
	//on reporte le nombre d'etapes
	$this->XMLDOM->documentElement->setAttribute('nb_etapes',$i);
		
	//on le copie en objet EasyXML, plus facile à utiliser en lecture seule
	$this->updtXML();
	
	//on génère les balises conditionnelles explicitement lorsqu'elles sont déclarées implicitement (ShowItemsOnVal)
	$this->makeExplicitConds();
	
	
	//stockage des variables privées
	$this->idformx = $this->XMLCore['id'];
	if((string) $this->XMLCore['access']) $this->session->droit = (string) $this->XMLCore['access'];
	return 'padaireur';
}

/* objets XML : En cas de modification de l'objet DOM, il faut mettre à jour l'objet simpleXML
	*/
   function updtXML() {
   	unset($this->XMLCore);
   $this->XMLCore = simplexml_import_dom($this->XMLDOM);
   $xtest = $this->XMLCore;
}

 //génère les balises conditionnelles explicitement lorsqu'elles sont déclarées implicitement (ShowItemsOnVal)
 //ajoute également l'attribut printHidden pour que l'item s'affiche caché ( si le bloc item n'est pas généré par le serveur, le javascript ne peut pas le faire réapparaitre )
  function makeExplicitConds() {
	foreach( $this->XMLDOM->getElementsByTagname('ShowItemsOnVal') as $EleShowItemsOnVal ) {
		$idImplicit = $EleShowItemsOnVal->parentNode->getAttribute('id');
		$listeIds = explode('|',$this->getValueFrom( $EleShowItemsOnVal->getElementsByTagname('ListIdItems')->item(0)->nodeValue));
		foreach($listeIds as $idItem) {
			$eleItem = $this->getDomItemFromId($idItem) ;
			$eleItem->setAttribute('printHidden','y');
			
			$eleCond = $this->XMLDOM->createElement('Cond');
			$eleCond->setAttribute('type','in');
			if( ! $eleItem->hasAttribute('opt') ) {
				$eleCond->setAttribute('oblig','y');
			 	$eleItem->setAttribute('opt','y');
			}
			$eleArg1 = $this->XMLDOM->createElement('Arg1');
			$eleArg1->nodeValue = $EleShowItemsOnVal->getElementsByTagname('OnVal')->item(0)->nodeValue ;
			$eleCond->appendChild($eleArg1);
			
			$eleArg2 = $this->XMLDOM->createElement('Arg2');
			$eleArg2->nodeValue = "formVar:$idImplicit" ;
			$eleCond->appendChild($eleArg2);
			
			//si pas de condition existante, on la place simplement dans l'item
			$listeoldConds = $eleItem->getElementsByTagname('Cond');
			if( $listeoldConds->length == 0 ) {
				$eleItem->appendChild($eleCond);
			//si une condition existe déjà, on crée un OU entre la nouvelle condition et l'ancienne
			} else {
                
				//refactoriser ici le code qui "change" le nodeType d'un neud
				$oldCond = $listeoldConds->item(0);
                $op = 'or' ;
                if( $oldCond->hasAttribute('prerequite') )
                {
                    $op = 'and' ;
                }
                else
                {
                    $op = 'or' ;
                }
				$eleNewCond = $this->XMLDOM->createElement('Cond');
				$eleNewCond->setAttribute('type',$op);
				if($eleCond->getAttribute('oblig') == 'y') $eleNewCond->setAttribute('oblig','y');
				
				$suparg1 = $this->XMLDOM->createElement('Arg1');
				$suparg1->appendChild($eleArg1);
				$suparg1->appendChild($eleArg2);
				$suparg1->setAttribute('type',$eleCond->getAttribute('type'));
				
				$suparg2 = $this->XMLDOM->createElement('Arg2');
				$suparg2->appendChild($oldCond->getElementsByTagname('Arg1')->item(0) );
				$suparg2->appendChild($oldCond->getElementsByTagname('Arg2')->item(0) );
				$suparg2->setAttribute('type',$oldCond->getAttribute('type'));
				
				$eleNewCond->appendChild($suparg1);
				$eleNewCond->appendChild($suparg2);
				
				$eleItem->appendChild($eleNewCond);
				$eleItem->removeChild($oldCond);

				
				
				//eko('<xmp>'.$this->XMLDOM->saveXML($eleItem).'</xmp>');
				
			}
			
		}
		
	}
  	//eko('<xmp>'.$this->XMLDOM->saveXML().'</xmp>');
  }
   
    
   
   
  /*   charge une instance de formulaire, de la base de données récipient vers le buffer XML  */
  function loadInstance($id_instance) {
  
 

  $req = new clResultQuery ;
  $param['table']=$this->session->tableInstances;
  $param['cw']="WHERE id_instance = '$id_instance'";
  $res = $req -> Execute ( "Fichier", "FX_getGen", $param, "ResultQuery" ) ;
   
   if ($res['INDIC_SVC'][2] == 1 ) {
   	//si l'ids chargé n'est pas la même, rechargement du contructeur
   	if($this->ids != $res['ids'][0]) {
   		$this->debug('Note: la classe a été instanciée avec l\'ids \''.$this->ids."' et doit editer un formulaire dont l'ids est ".$res['ids'][0]);
   			$options = $this->instanciedsOptions ;
   			$this->__construct($res['ids'][0],"$options NO_POST_THREAT");
   		}
   	$this->dt_modif = $res['dt_modif'][0];
   	$this->libelle = $res['libelle'][0];
   	$this->author = $res['author'][0];
   	if(isset($res['id_application']))
   		$this->session->idApplication = $res['id_application'][0];
  	//on crée l'objet DOM
	$this->XMLDOM = new DomDocument ();
	//echo('<xmp>'.utf8_encode($res["data"][0]).'</xmp>');
	$a = $this->XMLDOM->loadXML(formxTools::decodeFromBdd(utf8_encode($res["data"][0])));
	if (! $a) { 
		$this->addErreur("Les données XML de l'instance ".$id_instance." sont corrompues.","1");
		return false;	
	}

	$this->getRootDom()->getElementsByTagName('ETAPE');	//liste de nodes

	//die('ok');

	$this->debug("chargement de l'instance ".$id_instance);
	$this->makeBalVal($this->XMLDOM->documentElement,"STATUS",$res["status"][0]);
	//eko("val bal status:".$this->XMLDOM->getElementsByTagName('STATUS')->item(0)->nodeValue);
	$this->debug('<xmp>'.utf8_encode($res["data"][0]).'</xmp>');
	//on le copie en objet EasyXML, plus facile à utiliser en lecture seule
	$this->updtXML();
	$this->updtXML();
	//print $id_instance ;
	$this->idformx = $this->XMLDOM->getElementsByTagName('FORMX')->item(0)->getAttribute('id');
	if(is_object($this->XMLDOM->documentElement->getElementsByTagName('Groupe')->item(0)))
		$this->groupeClassement = $this->XMLDOM->documentElement->getElementsByTagName('Groupe')->item(0)->nodeValue;
	$this->setIdInstance($id_instance);
	//si formulaire défini en lecture seule
	if($this->XMLDOM->getElementsByTagName('FORMX')->item(0)->hasAttribute('closeOnLoad')) {
		$this->close();
		//si le formulaire doit disparaitre ou pas
		if( $state = $this->getAndCloseState() ) {
			formxTools::setDomState($this,$state);
  			$this->saveInstance();
   		}
	}
	return true;
	} else {
		$this->addErreur("Impossible de charger l'instance '$id_instance' dans la table ".$param['table']." ".$res['INDIC_SVC'][2],"1");
		return false;
	}
  }
    
  function cleanInstance() {
  	 $this->justClosed = false ;
  }  
   
  /*initialise l'instance d'un formulaire 
  ids : identifiant de sujet (99 fois sur 100 l'idu)
  elle va être stockée dans la base de donnée récipient (voir constructeur) et aura un champ unique : id_instance   */
  function initInstance($ids='',$idformx='',$titre='') {
  	global $formxSession ;
  	if(! $ids) $ids = $this->ids ;
	if(! $idformx) $idformx = $this->idformx ;
	if(is_object($this->XMLDOM) && !isset( $this->XMLDOM->documentElement ))
		 throw new Exception ( "Le fichier XML ne s'est pas correctement chargé ou n'a pas été chargé." );
	if(! $titre) $titre = $this->supprMP( utf8_decode($this->XMLDOM->documentElement->getElementsByTagName('Libelle')->item(0)->nodeValue) );
	$vals = array();
	$vals['ids']=$ids;
	$vals['dt_creation']=date("Y-m-d H:i:s");
	$vals['dt_modif']=date("Y-m-d H:i:s");
	//$vals['idformx']=$this->XMLCore[id];
	$vals['etape']='FoRmX_init';
	$vals['status']='I';
	$vals['libelle']=$titre;
	$vals['idformx']=$idformx;
	$vals['author']=$formxSession->getUser();
	$vals['data']=utf8_decode($this->XMLDOM->saveXML());
	//Test si la colonne idApplication existe
	
	$requete = formxSession::getInstance()->getObjRequete($vals) ;
	$infos = $requete->descTable();
	if (array_key_exists ('id_application',$infos)) {
		$vals['id_application']=$this->session->idApplication;
		$requete = formxSession::getInstance()->getObjRequete($vals) ;
	}

	//si l'attribut uniq est présent, c'est qu'on ne peut avoit qu'une seule instance de ce type
	//de formulaire pour un ids donné
	if((string) $this->XMLDOM->documentElement->getAttribute("uniq"))
		$requete->delRecord (" ids = '$ids' and idformx = '$idformx' ") ;
	
	//<---------------
	
  	$resu = $requete->addRecord () ;

	//recuperation de l'identifiant d'instance attribué
	$this->idInstance = $resu['cur_id'];
	
	//kill the zombie
	$this->makeBalVal($this->XMLDOM->documentElement,"STATUS",'I');
	$this->killTheZombie();
	return $resu['cur_id'];
	
  }
  

  
  
  function rmInstance($id_instance_pre) {
  $formxSession = $this->session ;
  //eko("je traite $id_instance_pre");
  $reg=array();
  if ( ereg("^(.*)_CONFIRM",$id_instance_pre,$reg)) {
  	//eko('syntaxe ok');
  	$idinstance=$reg[1];
	$confirm='yes';
   } else {
   	$idinstance = $id_instance_pre;
   	}
   	$this->loadInstance($idinstance);

  //test du droit défini dans le fichier xml si il est défini	
  if ($this->XMLCore['access'] ) {
    	$droit = $formxSession->getDroit(utf8_decode((string) $this->XMLCore['access']),'d') ;
   } else { //sinon on va chercher celui par defaut
   	$droit = $formxSession->getDroit($this->session->droit,'d') ;
	}
  
  	
  //si pas les droits pour effacer
  if(! $droit ) {
  	$mod = new ModeliXe ( "FX_message.mxt" ) ;
  	$mod -> SetModeliXe ( ) ;
  	$mod -> MxText('titre', 'Erreur');
  	$mod -> MxText('message', 'Vous n\'avez pas les droits nÃ©cessaires <br/>pour effacer le formulaire <b>'.utf8_encode($this->libelle).'</b>.');
	$mod->MxHidden('hidden2','navi='.$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2)));
  	$this->af .= $mod -> MxWrite ( "1" ) ;
  	unset($_POST['FormX_ext_goto_']);
  	unset($this->idInstance) ;
	return 1;
	}
  //si la confirmation  d'effacement a été validée	
  if(isset($confirm) && $confirm && ($_POST['valider_popup'] || $_POST['valider_popup_x']) ) {
  	$this->rmInstanceForce($idinstance);
  	unset($this->idInstance) ;
  	unset($_POST['FormX_ext_goto_']);
	return 1;
    	}
  //si la demande de confirmation n'a pas été validée
  if(isset($confirm) && $confirm) {
  	unset($_POST['FormX_ext_goto_']);
  	unset($this->idInstance) ;
  	 return 1;
  }
	
   //sinon, demande de confirmation
  $mod = new ModeliXe ( "FX_message.mxt" ) ;
  $mod -> SetModeliXe ( ) ;
  $mod -> MxText('titre', 'Confirmation');
  //var_dump($this->XMLCore);
  $mod -> MxText('message', "Voulez-vous vraiment effacer <br/>le formulaire " .(string) $this->XMLCore['id'].'?');
  $mod->MxHidden('hidden',"FormX_ext_goto_=RM".$idinstance.'_CONFIRM');
  if( (string)$formxSession->getNavi(3))
  	$mod->MxHidden('hidden2','navi='.$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2),$formxSession->getNavi(3)));
  else
  	$mod->MxHidden('hidden2','navi='.$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2)));
  $this->af .= $mod -> MxWrite ( "1" ) ;
  unset($this->idInstance) ;
  //unset($_POST['FormX_ext_goto_']);
  return 1;
  }
  
 function rmInstanceForce($idinstance='') {
  if(!$idinstance) $idinstance = $this->idInstance ;
  if($idinstance == $this->idInstance &&  $this->XMLDOM->getElementsByTagName('FORMX')->item(0)->hasAttribute('onDelete')) {
  	$callProc = $this->XMLDOM->getElementsByTagName('FORMX')->item(0)->getAttribute('onDelete');
  	$this->callFunc($callProc);
  }
   formxTools::simpleRemoveInstance($idinstance);
 }
  
  
  /*  Genere un popup contenant la version pdf du formulaire
  affFoRmX() doit être généré avant*/
  function genPdf() {
  //vidage du cache
  $mapoub = new clPoubelle();
  $mapoub ->setRepertoire ($this->session->urlCache) ;
  $mapoub ->purgerRepertoire(3600);
  
  $buffer = $this->getAffichage () ;
  $mod = new ModeliXe ( "FX_Edition.mxt" ) ;
  	$mod -> SetModeliXe ( ) ;
  	$mod -> MxAttribut('FoRmX_css_mail', $this->session->url.'css/FoRmX_mail.css');
	$mod -> MxAttribut('FoRmX_css', $this->session->url.'css/FoRmX.css');
	$mod -> MxAttribut('piclogo', $this->session->urlImglogo);
  	$mod -> MxText('contenu', $buffer);
	$buffer = $mod -> MxWrite ( "1" ) ;
  
  $fic_html = date('y-m-j-h-i-s-').rand(1,1000);
  $fic_pdf = $fic_html . '.pdf';
  $fic_html.='.html';
  if($fp = fopen($this->session->urlCache.$fic_html,"a" ) ){
		fputs($fp,$buffer);
		fclose($fp);
		}
		
//appel par URL externet à HTML2PS - rendu excellent mais tres long
  $buffer_pdf = file_get_contents(HTML2PS_LOCATION.'html2ps.php?URL='.FX_URLCACHEWEB.$fic_html.'&pixels=800&scalepoints=1&renderimages=1&media=A4&cssmedia=screen&leftmargin=10&rightmargin=15&topmargin=7&bottommargin=7&pageborder=1&encoding=&ps2pdf=1&output=0&compress=0');
  if($fp = fopen(FX_URLCACHE.$fic_pdf,"a") ){
		fputs($fp,$buffer_pdf);
		fclose($fp);
		}
   $this->af .= "<SCRIPT LANGUAGE=\"JavaScript\">window.open('".FX_URLCACHEWEB.$fic_pdf."','_blank','toolbar=0, location=0, directories=0, status=0, scrollbars=0, resizable=0, copyhistory=0, menuBar=0');</SCRIPT>";
   } 
  
   /*  Genere un popup contenant la version html du formulaire et lance le module
   d'impression du Navigateur
  affFoRmX() doit être généré avant*/
  function genPrint($buffer) {
  
  $this->debug("entrée dans genPrint()");
  //vidage du cache
  $mapoub = new clPoubelle($this->session->urlCache);
  $mapoub ->purgerRepertoire(1);
  if(! $buffer)
  	$buffer = $this->getAffichage ('print') ;
  else
  	$buffer = $this->miseEnPage('print',$buffer);
  $mod = new ModeliXe ( "FX_Edition.mxt" ) ;
  	$mod -> SetModeliXe ('print') ;
	//si css particulier
	if ($this->XMLCore['cssprint'] || $this->XMLCore['css']) {
		//le css est déja dans le buffer
	} else {
		$style='';
  		$mod -> MxAttribut('FoRmX_css_mail', $this->session->url.'css/FoRmX_print.css');
		$mod -> MxAttribut('FoRmX_css', $this->session->url.'css/FoRmX.css');
		}
	$mod -> MxAttribut('piclogo', $this->session->urlImglogo);
  	$mod -> MxText('contenu', $buffer);
	$buffer = $mod -> MxWrite ( "1" ) ;
	
   //on vire les images d'impression
  $conv = array($this->session->urlImgEdi => $this->session->urlImgRien, $this->session->urlImgClo => $this->session->urlImgRien);
  $buffer2 = strtr(& $buffer ,& $conv) ;
	
  $fic_html = date('y-m-j-h-i-s-').rand(1,1000);
  $fic_pdf = $fic_html . '.pdf';
  $fic_html.='.html';
  if($fp = fopen($this->session->urlCache.$fic_html,"a" ) ){
		fputs($fp,$buffer2);
		fclose($fp);
		}
   unset($buffer,$buffer2);
  $this->af .= "<SCRIPT LANGUAGE=\"JavaScript\">window.open('".$this->session->urlCacheWeb.$fic_html."','_blank','toolbar=0, location=0, directories=0,width=800, status=0, scrollbars=0, resizable=0, copyhistory=0, menuBar=0' );</SCRIPT>";
  } 
  
  
   
   /*   Affiche le formulaire, avec saisie   */
  function affFoRmX($store=false) {  
  //pour commodité d'ecriture
    $formxSession = $this->session ;
    $xml = & $this->XMLCore ;
    $this->debug("Entrée dans affFoRmX(), générateur de l'affichage.");
    if (  $xml['access'] &&  ! $formxSession->getDroit(utf8_decode( (string) $xml['access']),'r' ) ) return '';
    
    //instanciation modelixe  
    $mod = new ModeliXe ( "FX_squeletteFoRmX.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    //recuperation des balises descriptives
    $htmlLibelle = $xml->Libelle[0] ;
    if( $xml->Logo[0] )
    $htmlLibelle = '<img alt="logo" src="'.$xml->Logo[0].'" />'.$htmlLibelle ;
    $mod -> MxText ( "titre",$htmlLibelle);

    if ($xml->Objet[0]) 
    	$mod -> MxText ( "objet.libobj",$xml->Objet[0]); 
    else
    	$mod -> MxBloc("objet","delete");
    if ($xml->Explication[0]) {
    	$mod -> MxText ( "explication.explication",$xml->Explication[0]); 
	} else {
	$mod -> MxBloc("explication","delete");
	}
	
	 

    //boutton de fermeture
    if($this->isWindowClose)
    	$mod->MxFormField("windowClose","image",$this->prefix."close","on","value='on'  src=\"".FX_URLIMGCLO."\"");
    if( ! (string) $xml['dontPrintPrinter'] )
    	$mod->MxFormField("windowPrint","image",$this->prefix."print","on","value='on'  src=\"".FX_URLIMGEDI."\"");


    
    
    //puis on parcourt les balises ETAPE jusqu'on en trouve une non achevée
    
    foreach ($xml->ETAPE as $etape) {
    	if ( ( ! $etape['access'] ) || (  $formxSession->getDroit(utf8_decode((string) $etape['access']),'r' ))) 
		$this->printEtape($mod,$etape);
	if ($etape['etat'] != 'fini') break ;
        }
        
     //puis on parcourt les balises fusion 
 	//On parcourt les balises FUSION
    foreach ($xml->FUSION as $fusion) {
    	//eko("Entre fusion");
    	$libelle_default = (string) $fusion->Libelle[0] ; 
    	if ( ( ! $fusion['access'] ) || (  $formxSession->getDroit(utf8_decode((string) $fusion['access']),'r' ))) 
		$fusion['etat']='fini';
		//$libelle = (string) $fusion->Libelle ;
		$vars = array();
		foreach ($fusion->Recup as $recup) {
			$vars[] = (string) $recup['var'] ;
			}
		//debug comportement bizarre de simpleXml
		if(count($vars)==1 & ! $vars[0])
			$vars = array() ;
		
		$varsFusions =$this->getAllValuesFromFormx($fusion['id_formx'],$vars,"","moreinfos") ;

		$i = 0;
		$nb = $varsFusions['INDIC_SVC'][2];
		//Transfo du tableau
		$varsFusions2 = array();
		$tab_oef= array();
		foreach($varsFusions as $key => $value) {
			if(! ereg("^infosmore_(.*)",$key,$tab_oef))
			for ($i=0;$i<$nb;$i++) {
				$varsFusions2[$nb-$i-1][$key]=$varsFusions[$key][$i];
				$varsFusions2[$nb-$i-1]["infos_creation"]=$varsFusions["infosmore_dt_creation"][$i]." ".$varsFusions["infosmore_auteur"][$i];
				}
    		}

		for ($i=0;$i<$nb;$i++) {
			//eko($libelle_default);
			$this->current_fusion = $varsFusions2[$i];
			
			//eko($this->current_fusion);
			
			if(! $libelle_default)
				$fusion->Libelle[0] = (string) $this->getValueFrom("fusion:".$fusion->Libelle['var']);
			else
				$fusion->Libelle[0] = (string) $libelle_default." ".$this->current_fusion["infos_creation"];

			$this->printEtape($mod,$fusion);
			$fusion->Libelle[0] = $libelle_default ;
			unset($this->current_fusion);
		}
     }
        
        
        
        
        
        
	//on indique en caché la référence de formulaire pour traitemenent ulterieur des données POST	
    $mod->MxHidden('hidden1',$this->prefix."INSTANCE=".$this->idInstance);
    $mod->MxHidden('hidden2','navi='.$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2),$formxSession->getNavi(3)));
    if( ! $this->ya_un_cal ) $mod -> MxBloc ( "JavaCAL", "delete" ) ;
    
    //javascript dynamique global au formulaire
    $mod -> MxText ( "js",$this->js);
    $af = $mod -> MxWrite ( "1" ) ;
    if($store) $this->af .= $af;
	//tout affichage peut impliquer une mise à jour en base
	//( 1er affichage charge les valeurs nom prenoms)
	//il y a eu un changement d'adresse...
	
	//2009/01 : je commente la ligne suivante qui me semble obsolete, à recetter.    
   // $this->saveInstance();
    return $af; 
  }
  
// fonction qui empeche erreurs notice poour variable non instanciée $_POST
function regPost($vars)
    {
	   	foreach ($vars as $value) {
    	if (! isset($_POST[$value])) $_POST[$value] ='';
	   	}
    }
  
  
  /*traite les postData pour mettre à jour l'instance du formulaire*/
  function TraiterPost() {
  
  global $formxSession;
  global $errs;
  
  
  $this->debug('entrée dans TraiterPost');
  
  /*d'abord analyse des données POST pour savoir si l'on a demandé l'affichage d'une instance de formulaire en particulier 
   Si c'est le cas , on remplit l'affichage de la future fenetre popup et on s'en va
   */
   //Regularisation des données post
   $this->regPost(array('FormX_ext_goto_','FoRmX_step_next_x','FoRmX_step_prev_x','FoRmX_step_cancel_x','FoRmX_selCancel_x','FoRmX_step_next_x','FoRmX_step_prev_x','FoRmX_print_x','FoRmX_close_x','FoRmX_selValid_x','FormX_ext_goto_'));
  	
    if($_POST['FormX_ext_goto_']  ) {
    $this->debug('trouvé Commande $_POST[\'FormX_ext_goto_\'] ='.$_POST['FormX_ext_goto_']);
	$this->isuseraction=true;
   
   	//si on va vers la selection d'un nouveau type de formulaire
	if($_POST['FormX_ext_goto_']=='new') {
		$this->af .= $this->genMenuSelection();
		return;
		}
	//si on va vers une suppression d'instance
	$reg=array();	
	if ( ereg("^RM(.*)$",$_POST['FormX_ext_goto_'],$reg) ) {
		//eko("demande de virer le form");
		$this->rmInstance($reg[1]);
		return;
		}

	//si on va vers une réouverture d'instance cloturée
	if ( ereg("^ED(.*)$",$_POST['FormX_ext_goto_'],$reg) ) {
		$this->loadInstance($reg[1]);
		$this->unclose();
		$this->af .=$this->affFoRmX();
		return;
		}
	
	//si on vient de la popup de confirmation de validation totale forcée du formulaire
	if ( ereg("^FRCACH(.*)$",$_POST['FormX_ext_goto_'],$reg) ) {
		//eko("demande de virer le form");
		$id_instance = $reg[1];
		//si clic sur 'ok'
		$_POST['FormX_ext_goto_']= $id_instance;
		if ( $_POST['valider_popup_x'] ) {
			$_POST[$this->prefix.'step_next_x']='ok';
		} else {
			//on ne detecte pas la finitude du formulaire
			$_POST['pavalider']='y';
			}
		$goOn  = 'y';
		$validForce = 'y';
		}
	
	//sinon, chargement d'instance donné
	if ( ! isset($goOn) ||  ! $goOn) {
		$this->loadInstance($_POST['FormX_ext_goto_']);
		if(! isset($validForce) || ! $validForce)
			$this->detectHeresie();
	    //si cette instance a l'attribut onCloseRedirForm et qu'elle est fermée
		$xml =  $this->XMLDOM ;
        if ( $xml->documentElement->hasAttribute('onCloseAndReadCallForm') ) {
   		 if( formxTools::getDomState($this) == 'F' ) {
   			$redirform = $xml->documentElement->getAttribute('onCloseAndReadCallForm') ;
   			$this->loadForm($redirform.".xml") ;
   			$id_instance = $this->initInstance($this->ids);
   			$this->loadInstance($id_instance);
   		  }
    	} 
		$this->af .=$this->affFoRmX();
		return;
		}
	} else {
		$this->debug('Pas trouvé de commande $_POST[\'FormX_ext_goto_\']');
    }
   
	//si fermeture de la fenêtre de sélection de formulaire à ajoutter
	 if($_POST[$this->prefix.'selCancel_x']  )
		$this->isuseraction=true;
			
    //si un demande de nouveau formulaire à instancier pour le sujet
   if($_POST[$this->prefix.'selValid_x']  ) {
	$this->isuseraction=true;
    //pour chaque instance demandée on en crée une
   if(is_array($_POST[$this->prefix."chooseNew"]))
    foreach($_POST[$this->prefix."chooseNew"] as $ajout) {
   	 //chargement du squelette
	 if($this->loadForm($ajout.".xml")){
  		//initialisation de l'instance
		$id_instance = $this->initInstance($this->ids);
		$priority = $this->getPriority();
		clFoRmXtOoLs::addFormToLoad($this->prefix,$id_instance,$priority);
		}
   	 }
	//chargement de la derniere instance demandée
	$instance2load = clFoRmXtOoLs::isFormToLoad($this->prefix);
	if($instance2load) {
		clFoRmXtOoLs::delFormToLoad($this->prefix,$instance2load);
		$this->loadInstance($instance2load);
		$id_instance = $instance2load ;
		$this->af .=$this->affFoRmX();
	}
	return '';
   }
  //recherche un formulaire présent dans les données POST
  //un formulaire etait déja affiché précédement
  foreach ($_POST as $cle => $valeur) {
  	//eko("TEST du couple $cle $valeur");
	//on verifie que la clé du POST a bien été générée par FoRmX
  	if ( ereg("^".$this->prefix."INSTANCE$",$cle) ) {
	$this->isuseraction=true;
	$id_instance=$valeur;
	$this->debug("trouve un post d'instance présente: $valeur");
	break;
	}
   }
   
   //su aucune clé trouvée, rien à
   if(! isset($id_instance)) $id_instance="";    
   if( ! $id_instance )  {
   		$this->debug("Pas trouvé d'instance dans les données post: $valeur"); 
   		return ;
   		}
     
   //si pour cette instance, on a demandé la fermeture: rien
   if( $_POST[$this->prefix.'close_x']) {
   	clFoRmXtOoLs::cleanFormToLoad($this->prefix);
	$this->isuseraction=true;
	return;
	}
   //on charge l'instance en question depuis la BD
   $this->loadInstance($id_instance);
   $xml =  $this->XMLDOM ;
 
   //si pour cette instance, on a demandé l'impression:
   if( $_POST[$this->prefix.'print_x']) {
	$this->isuseraction=true;
	$this->modeImpression = true;
	$this->genPrint($this->affFoRmX());
	$this->modeImpression = false;
	$this->af .= $this->affFoRmX();
	return 1;
   	}
   //traitement DOM----------------------------------------------------
   //fonctions DOM
      
   $etapes = $xml->documentElement->getElementsByTagName('ETAPE');	//liste de nodes
    
   //on s'arrête à la dernière étape non validée
   $notTheFirst = '';
   $isNotFini = false ;
   $etape_memo_prec='';
   foreach ($etapes as  $etape) { //on parcours les nodes
   	if(isset($etape_current) )
		$notTheLast = 'oui';
	else
		if ($etape->getAttribute('etat') != 'fini') {
			$isNotFini = true ; 
			$etape_current = $etape ;
			$etape_prec = $etape_memo_prec;
			}
	$etape_memo_prec = $etape ;
	if(! isset($etape_current) )
		$notTheFirst = 'oui';
	}
   $etape =(isset($etape_current)?$etape_current:'') ;
	
if( $_POST[$this->prefix.'step_prev_x'] && $notTheFirst) {
	$this->debug("retour en arriere détecté et appliqué");
	$etape = $etape_prec ;
	$etape->setAttribute('etat', 'en_cours' );
	}

//FIXME : c'est moche ça là...
//popup de validation du formulaire total

if( $_POST[$this->prefix.'step_next_x'] && empty($notTheLast) && empty($validForce)) {
	unset($_POST[$this->prefix.'step_next_x']);
	$mod = new ModeliXe ( "FX_message.mxt" ) ;
  	$mod -> SetModeliXe ( ) ;
  	$mod -> MxText('titre', 'Confirmation');
  	$mod -> MxText('message', "Voulez-vous vraiment forcer <br/>l'achevement du formulaire ?");
  	$mod->MxHidden('hidden',"FormX_ext_goto_=FRCACH".$id_instance);
  	$mod->MxHidden('hidden2','navi='.$formxSession->genNavi($formxSession->getNavi(0),$formxSession->getNavi(1),$formxSession->getNavi(2),$formxSession->getNavi(3)));
  	$this->af .= $mod -> MxWrite ( "1" ) ;
	//on ne detecte pas la finitude du formulaire
	$_POST['pavalider']='y';
	}
   
   //si le champ cancel n' pas été validé on enregistre les changements
   if(! $_POST[$this->prefix.'step_cancel_x'] && ! $_POST[$this->prefix.'step_prev_x'] && $isNotFini) {
	   	$this->debug("Mise à jour des Items par les données post demandée"); 
	   	
	   	//on parcours les champs ITEM et on remplit les champs en validant le type de données
	   	$items = $etape->getElementsByTagName('ITEM');
	   	$this->debug("Parcours des items de l'etape");
		foreach ($items as  $item) {
	   		$this->debug("traitement de ".$item->getAttribute('id'));
	   		$this->affectPost2XML($item);
		}
		
		//on applique les opérations liés aux changements précédents
		$varGlobalsContainer = formxTools::globalsLoad($this->getIDS());
		foreach ($items as  $item) {
			$this->traiterPostPost($item);
		}
		$this->getGlobvarsContainer()->save();
		
		//clonage ou manipulation dynamique d'un champ demandée 
		$activIs = $etape->getElementsByTagName('ActiveItem');
	   	foreach ($activIs as  $activI) {
	 		if ( $this->testCondDOM($activI) ) {
				$this->cloneItem($activI->getAttribute('idItem'),$etape,$activI);
				}
	   		}
		//Si le formulaire était initialisé, il passe en status "en cours"
		if( formxTools::getDomState($this) == 'I' )
		{
			formxTools::setDomState ($this , 'E') ;
		}
		
		//on teste si l'étape est validée, si oui on met à jour, on applique les actions, et on cherche si le form est fini .
		//seulement si le post nest pas defini
		if($_POST['pavalider']!='y' && $etape) {
				//si pas les droits au moins W
				
		//TODO le deplacer avant ça
		$droitetape=$this->session->droit;
		if($this->XMLCore['access']) $droitetape = utf8_decode((string) $this->XMLCore['access']);
		if($etape->getAttribute('access')) $droitetape = utf8_decode($etape->getAttribute('access')) ;
		
		if(! $formxSession->getDroit($droitetape,'w'))
			$this->debug("validation de l'etape refusée car pas W sur le droit '$droit'");
		if($etape->getAttribute('dontvalid'))	
			$this->debug("validation de l'etape refusée car présence de l'attribut dontvalid");	
	   	if ( ( ! $etape->getAttribute('dontvalid')) && $formxSession->getDroit($droitetape,'w') && ( $this->testEtape($etape) || $_POST[$this->prefix.'step_next_x'] )) {
			$etape-> setAttribute('etat','fini') ;
			$this->applyActions($etape);
			if($this->formIsFini()) {
				clFoRmXtOoLs::delFormToLoad($this->prefix,$this->idInstance); //supprime du pipeline des multiformulaires
				$this->justClosed = true ;
				if ( $this->XMLCore['disappear'] ) {
					//si le form doit disparaitre apres finition
					$this->rmInstanceForce();
					return 1;
				} elseif ( $this->XMLCore['phantom'] ) {
					formxTools::setDomState($this,'H');
				} else {
					formxTools::setDomState($this,$this->traiterFini());
					}
				}
		} else {
		
		}
		} else {
		//NON VALIDATION FORCEE
		}
		

		//$this->saveInstance();
   } else {
   	$this->debug("Pas de mise à jour des Items par les données post nécéssaire");
   }
  
   
   /*on a plus qu'à l'afficher*/
   //--------------------------------------------FIN TRAITEMENT DOM
   //rechagement de simpleXML apres modifs via DOM

   	if( $this->mustICloseAfterValid() && $this->justClosed )
    {
		$this->affFoRmX(); //TODO : lorsque plus aucune regle métier dans affFoRmX, virer la ligne
    } else
		$this->af .=$this->affFoRmX();	
		
	$this->saveInstance();
	
   if($this->justClosed && $id2load =clFoRmXtOoLs::isFormToLoad($this->prefix) ) {
   		//on gruge sur les données post pour réouvrir le formulaire suivant
   		$this->debug("Au moins un autre formulaire est dans la pipeline: le $id2load . On l'ouvre");
   		$_POST['FormX_ext_goto_'] = $id2load ;
   		$this->TraiterPost();
   }	
   //apres car à l'affichage on peut se rendre compte de certaines choses et vouloir sauvegarder
   return 1;
  }
  
  
   //doit-je me fermer tout seul apres une validation ?
   function mustICloseAfterValid()
   {
   		if( $this->getRootDom()->getAttribute('closeAfterValid') || $this->ImustDisapear )
   			return true ;
   			
   		return false ;
   			
   }
  
  
  /*sauve l'instance en BDD*/
  function saveInstance() {
  	global $errs ;
  //eko( $errs->whereAmI() )	;
  $vals = array();
  $vals['data']=utf8_decode(addslashes($this->XMLDOM->saveXML()));
  $vals['status'] = formxTools::getDomState($this);
  $vals['dt_modif'] = $this->dt_modif ;  
  $vals['libelle'] = $this->supprMP( utf8_decode( formxTools::getTagValue($this->XMLDOM->documentElement,'Libelle') ));
  if(( $vals['status'] ) != 'F' || $this->justClosed ) $vals['dt_modif']=date("Y-m-d H:i:s");
  $this->debug("SAUVEGARDE de l'instance ".$this->idInstance." dans la table ".$this->session->tableInstances." avec le status ".$vals['status']);
  $requete = $this->session->getObjRequete($vals);
  $resu = $requete->updRecord (" id_instance = '".$this->idInstance."'  ") ;
 }
  
  //definir dans la classe héritée
  function traiterFini() {
  return 'F';
  }
  
  /*regarde si le form est fini*/
  function formIsFini() {
  $etapes = $this->XMLDOM->documentElement->getElementsByTagName('ETAPE');
  foreach ($etapes as  $etape) { //on parcours les nodes
	//eko("on regarde l'etape".$etape->Libelle[0]);
    	if ($etape->getAttribute('etat') != 'fini') {
		//eko('etape po finie : '.$etape->getAttribute('id'));
		return '' ;
		}
        }
  return 'oki';
  }

  /*cloture un formulaire*/
function close() {
  $etapes = $this->XMLDOM->documentElement->getElementsByTagName('ETAPE');
  foreach ($etapes as  $etape)
  { //on parcours les nodes
	//eko("on regarde l'etape".$etape->Libelle[0]);
        $etape->setAttribute('etat','fini') ;
   }
   clFoRmXtOoLs::delFormToLoad($this->prefix,$this->idInstance); //supprime du pipeline des multiformulaires
   if ( $this->XMLCore['disappear'] )
   {
    //si le form doit disparaitre apres finition
        $this->rmInstanceForce();
        return 1;
    } elseif ( $this->XMLCore['phantom'] ) {
        formxTools::setDomState($this,'H');
    } else {
        formxTools::setDomState($this,$this->traiterFini());
    }
  //formxTools::setDomState($this,$etat);
  $this->saveInstance();
  $this->justClosed = true ;
  return 'oki';
  }
  
//cloture une etape sans appliquer les actions
function closeEtape($objDomEtape) {
	$objDomEtape-> setAttribute('etat','fini') ;	
}

  /*decloture un formulaire*/
function  unclose() {
  //test si pas fini, fait rien
   if (in_array(formxTools::getDomState($this),array('E','I')))
	return '';
   $etapes = $this->XMLDOM->documentElement->getElementsByTagName('ETAPE');	//liste de nodes
   foreach ($etapes as  $etape) {} //on parcours les nodes pour ariver à la dernière etape
   $etape->setAttribute('etat','');
   formxTools::setDomState($this,'E');
   $this->saveInstance();
}


//retourne l'etat que doit avoir le formulaire en base apres avoir appliqué les
//dernieres actions qui doivent avoir eu lieu
function getAndCloseState() {
 if ( $this->XMLCore['disappear'] ) {
	//si le form doit disparaitre apres finition
	$this->rmInstanceForce();
	return false;
 } elseif ( $this->XMLCore['phantom'] ) {
	return 'H' ;
 } else {
	return  $this->traiterFini();
 }
}	

  /*Applique les actions d'une étape validée*/
  function applyActions($etape) {
  
  global $formxSession;
  $actions = $etape->getElementsByTagName('ACTION'); 
  foreach($actions as $action) {
  	//on teste la condition si yen a une
	$condition = $action->getElementsByTagName('Cond')->item(0);

	if($condition) {
		if ( ! $this->testCondDOM($action) ) {
			$action->setAttribute('cond','false');
			continue ;
			}
		$action->setAttribute('cond','true');
		}
		
	switch($action->getAttribute('type')) {
	//creation d'une autre instance de formulaire
	case 'formxproc':
		$this->callFunc($action->getAttribute('id_formxproc'));
		break;
	case 'goto': //goto revient à dupliquer une partie du code xml situé entre les étapes etape_goto et etape_courante
		$currentEtapeId = $etape->getAttribute('id');
		$gotoEtapeId	= $action->getAttribute('step');
		$options	= $action->getAttribute('options');
		$etapes = $this->XMLDOM->getElementsByTagName('ETAPE') ;
		$objBas = $etape ;
		$trouveCurrent = false;
		$trouveGoto = false ;
		foreach( $etapes as $etapeTraitee ) { 		//parcours des étapes
			if ($etapeTraitee->getAttribute('id') == $gotoEtapeId) { //etape goto
					$trouveGoto = true ;
					if( $trouveCurrent == false ) {//On est arrivé à l'etape goto qui est avant l'etape courante
							$newEtape = $this->cloneEtape($gotoEtapeId,$objBas->getAttribute('id'),$options);
							$objBas = $newEtape ;
							if ($currentEtapeId == $gotoEtapeId) //si l'etape goto est egalement l'etape courante, sortie
								break ;
					} else { // On est arrivé à l'etape goto apres avoir trouvé l'etape courante, fin du traitement 
							break ;
					}
			} else if ($etapeTraitee->getAttribute('id') == $currentEtapeId) { //etape action
					$trouveCurrent = true ;
					if ( $trouveGoto && ! $etapeTraitee->hasAttribute('IamAclone')	) {
						$this->cloneEtape($currentEtapeId,$objBas->getAttribute('id'),$options); // clonage de l'etape courante puis fin du traitement
						break ;
					} elseif ($trouveGoto) {
						break ;
					}
			} else { //etape quelconque
					if ( ! $trouveCurrent && ! $trouveGoto ) //sortie de la boucle si l'etape en cours de traitement n'est pas concernée
						continue ;
					elseif ( ! $trouveCurrent &&  $trouveGoto && ! $etapeTraitee->hasAttribute('IamAclone')) {//cas ou il faut cloner l'etape traitée apres l'etape courrante
						$newEtape = $this->cloneEtape($etapeTraitee->getAttribute('id'),$objBas->getAttribute('id'),$options);
						$objBas = $newEtape ;
					} elseif ( $trouveCurrent &&  ! $trouveGoto ) { //cas ou il faut sauter l'étape
						$this->closeEtape($etapeTraitee);
					}
			}
		}
		break ;
	case 'trigger':
		$newInstance = new clFoRmX($this->ids,'NO_POST_THREAT');
		if ( $newInstance->loadForm($action->getAttribute('id_formx').'.xml') ) {
			$valeurs = $action->getElementsByTagName('Affect');
			foreach ($valeurs as $valeur) {
				$newInstance->setFormVar($valeur->getAttribute('id_var'),utf8_decode(getValueFrom($valeur->nodeValue) ),'no_simplexml_update');
				}
		
			$newInstance->initInstance();
			}
		
			
		unset($newInstance);
		break;
	//envoi de mail
	case 'mail':
		$sujet=utf8_decode($action->getElementsByTagName('Libelle')->item(0)->nodeValue);
		$content_type="Content-Type: text/html; charset=\"iso-8859-1\"";
		if (is_object($action->getElementsByTagName('Expediteur')->item(0)))
			$from = $this->getValueFrom($action->getElementsByTagName('Expediteur')->item(0)->nodeValue);
		else
			$from='diffusion-system@FoRmX.ch-hyeres.fr';
		$head="From :$from\n".$content_type."\n";
		$message=utf8_decode($this->convMP($action->getElementsByTagName('Message')->item(0)->nodeValue));
		
		if($action->getAttribute('joindreForm')) {
			$htmlform=utf8_decode($this->convMP($this->affFoRmX()));
			$filename="css/FoRmX_mail.css";
			$handle=fopen($filename,'r');
			$style=fread($handle,filesize($filename));
			fclose($handle);
			$filename="css/FoRmX.css";
			$handle=fopen($filename,'r');
			$style.=fread($handle,filesize($filename));
			fclose($handle);
			$htmlMessage="<html><body><style>$style</style><br/>$message<br/><br/>$htmlform</body></html>";		
		} else {
			$htmlMessage="<html><body><br/>$message<br/></body></html>";		
			}
		foreach ($action->getElementsByTagName('Destinataire') as $dest ) {
			$strDesti = $this->getValueFrom($dest->nodeValue,true);
			mail($strDesti,$sujet,$htmlMessage,$head);
			}
		break;
	case 'editdoc':
		//vidage du cache
		$mapoub = new clPoubelle();
  		$mapoub ->setRepertoire (FX_URLCACHE) ;
  		$mapoub ->purgerRepertoire(3600);
		$i = 0;
		if (isset($output)) unset($output);
		foreach ($action->getElementsByTagName('Document') as $docu ) {
			//mail($dest->nodeValue,$sujet,$htmlMessage,$head);
			if (isset($param)) unset($param);
			if($docu->getAttribute('nom_doc')) 
				$param[cw] = "WHERE nom='".utf8_decode($docu->getAttribute('nom_doc'))."' ORDER BY VERSION DESC LIMIT 0,1" ;
			if($docu->getAttribute('id_doc')) $param[cw] = "WHERE 				iddocument='".utf8_decode($docu->getAttribute('id_doc'))."'" ;
			$req = new clResultQuery ;
			$ras = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
			$ok='';
			if($ras['INDIC_SVC'][2]>=1) {
				$docs = new clDocuments ( "impr" ,'DocumentsTegeria') ;
      				$date = new clDate ( ) ;		
				$rep = $date->getYear()."/".$date->getMonthNumber()."/";
				
				$output[$i] = $date->getTimestamp()."-".$this->ids."-".$ras['iddocument'][0].".pdf" ;
				$dataa[idpatient] = $this->ids;
				$dataa[iddocument] = $ras['iddocument'][0] ;
				$dataa[nomedition] = $ras[nom][0] ;
				$dataa[urledition] = $rep.$output[0] ;
				$dataa[iduser] = $formxSession->getUser ( ) ;
				$dataa[date] = $date->getDatetime ( ) ;
				$sel[$i] = $dataa[iddocument] ;
				$requetee = new clRequete ( BDD, DOCSEDITES, $dataa ) ;
				$requetee->addRecord ( ) ;
				unset($dataa);
				$rep = $date->getYear()."/".$date->getMonthNumber()."/";
				$ok='y';
				} else {
				eko($ras[INDIC_SVC]);
				$this->addErreur("le document type ".$docu->getAttribute('nom_doc')." n'a pas été trouvé");
				break ;
				}
			$i++;
			}
		if(!$ok) break;
		$rep = $date->getYear()."/".$date->getMonthNumber()."/";
      		$buff_pdf = $docs -> genDoc ( $sel, $this->ids, $output, FX_URLDOCS.$rep ) ;
      		//popup de l'ouverture du doc	
      		$this->af .= "<SCRIPT LANGUAGE=\"JavaScript\">window.open('".FX_URLCACHEWEB.$buff_pdf."','_blank','toolbar=0, location=0, directories=0, status=0, scrollbars=0, resizable=0, copyhistory=0, menuBar=0');</SCRIPT>";
		break;
	
	default:
		break;
		}
    	}
  }
  
  //genere la chaine logique qui va représenter les items à chercher
  //va chercher l'etape SXML equivalente
  function genLogicStringItems($etape,&$etapeSX) {
  	   $id_etape=$etape->getAttribute('id');
  //rechagement de simpleXML apres modifs via DOM
  //$this->updtXML();
  //on retrouve la bonne balise etape
  foreach($this->XMLCore->ETAPE as $etapeSX) {
  	if ($etapeSX['id'] == $id_etape)
  	break ;
  	}
	
   //si la chaîne de texte ogligatoire n'est pas définie alors on en construit une qui rend toutes les valeurs obligatoires sauf celle spécifiée optionelle
   if ( isset($etapeSX->ChampsOblig) ) {
   	$chainedetest = $etape->getElementsByTagName('ChampsOblig')->item(0)->nodeValue ;
	} else {
	//valeurs facultatives
	$tabFac = array();
	$reg=array();
	if ( isset($etapeSX->ChampsFacult) ) {
		ereg("logic:(.*)",$etapeSX->ChampsFacult,$reg);
		$tabFac = explode('|', $reg[1]);
		}
	
	$chainedetest = 'logic:';
	$nb_items = 0;
	foreach($etapeSX->ITEM as $item) { 
		if(((! in_array($item['id'],$tabFac)) && ( $item['opt'] != 'y') ) || ($item['oblig'] == 'y') ) {
			$chainedetest .= $item['id'].'#';
			$nb_items ++;
			}
		}
	if ($nb_items > 0)  {
		$chainedetest = rtrim($chainedetest,'#');
		}
	}
  	return $chainedetest;
  }
  
  //regarde si un item est obligatoire
  function isItemOblig($iditem) {
  $pere = $this->XMLDOM ;
  $ok = false;
  foreach ( $pere->getElementsByTagName('ETAPE') as $etape ) {
  	foreach ($etape->getElementsByTagName('ITEM')  as $item ){
  		if ($item->getAttribute('id') == $iditem ) {
  			$ok=true;
			break;
  			}
  	}
  if($ok) {
  	break;
  }
  }
  if(!$ok)
  	return false;
  $etapeSX='';
  $chainedetest = $this->genLogicStringItems($etape,$etapeSX);
  $reg = array();
  $tabtmp = array();
  $val =  ereg("logic:(.*)",$chainedetest,$reg);
  if($reg[1] == '') return false;
   $tabtmp = explode('#',$reg[1]);
  foreach ($tabtmp as $elements_et) {
  	$tabtmp2 = explode('|',$elements_et);
  	foreach ($tabtmp2 as $element_ou) {
  		if($element_ou == $iditem) {
  			return true;	
  		}
  	}
  }

return false;
}
  
  
  
  function testEtape($etape) {
	
	$this->debug("Entrée dans testEtape");
	$etapeSX = '';
	$chainedetest = $this->genLogicStringItems($etape,$etapeSX);
	
	//remise à vide de la variable contenantr un eventuel item non validé
	unset($this->lastNonValidItem);
  //valeurs nulles	
   $nullvals = array('','#','-Autres-','-Autre-');
  //on parcours les "et" puis les "ou" pour que les elements à definir soient bien rentrés
  $cond = 'ok';
  
  $reg = array();
  $val =  ereg("logic:(.*)",$chainedetest,$reg);
   if($reg[1] == '') return 'ok';
   //eko("je traite la chaine: ".$etape->getElementsByTagName('ChampsOblig')->item(0)->nodeValue);
   $tabtmp = explode('#',$reg[1]);
   foreach ($tabtmp as $elements_et) {
	$tabtmp2 = explode('|',$elements_et);
	$cond2 = '';
	foreach ($tabtmp2 as $element_ou) {
		$litem = $this->getSXitembyId($etapeSX,$element_ou) ;
		$cur_var = $litem->Val[0] ;
		/*if ( ereg('^Â¤.*',$cur_var) ) {
			eko("$element_ou de valeur $cur_var est reconnue comme bien is Null");
			} else {
			eko("$element_ou de valeur $cur_var pas reconnue comme is Null");
			}
		*/
		//petit traitement de l'info dans le cas d'un tableau
			if($litem['type'] == 'TAB') $cur_var = $this->testTab($litem);
		//pour le slider , un bug javascript renvoie nul à place de zéro, donc on peut pas tester
			if($litem['type'] == 'SLIDER') {
			if( ! $cur_var ) 
				$cur_var='1';	
			}
		
   		if ( (! in_array($cur_var,$nullvals) && !ereg('^Â¤.*',$cur_var) ) || $cond2 ) {$cond2 = 'ok' ;} else { 
 			$this->debug("L'etape n'a pas été validée car l' item obligatoire '".$element_ou.'\' dont le libelle est \''.$this->getSXitembyId($etapeSX,$element_ou)->Libelle.'\' est considéré comme nul avec la valeur \''.($this->getSXitembyId($etapeSX,$element_ou)->Val)."'") ;
 			$this->lastNonValidItem = $litem ;
			$cond2 = '' ;
			//if( isset($this->getSXitembyId($etapeSX,$element_ou)->Val) ) eko('labalis lexist : <xmp>'.$this->XMLDOM->saveXML().'</xmp>');
			
			}
   		}
	if ( $cond2 ) {$cond = 'ok' ;} else {$cond = '' ; break ;}
  }
  return $cond2;
  }
  
  /*tester la complétude d'un tableau*/
  function testTab($litem) {
  $val = (string) $litem->Val ;
  $tab = explode('|',$val);
    foreach($tab as $val) {
  	if( $val == '' ) return '';
  	}
  return 'oki';  
  }

  /*obtenir, à l'interieur d'une etape en objet simpleXML, l'item d'id $id*/
  function getSXitembyId($etape,$id){
  	foreach ($etape->ITEM as $item) {
	if ( $item['id'] == $id)  return $item ;
	}
  }
  
  //met à à jour un attribut dans la valeur SXML et DOM-XML, à partir de l'item SXLK
  function makeSXMLItemAttribute($item,$var,$val) {
  
  $id=$item['id'];
  $pere = $this->XMLDOM ;
  foreach ( $pere->getElementsByTagName('ITEM') as $itemDOM ) {
  	if ($itemDOM->getAttribute('id') == $id ) {
		$ok='y';
		break;
		}
  	}
   if(! $ok) $this->addErreur("item $id non trouvé");
   $item[$var]=$val;
   $itemDOM->setAttribute($var,$val);
    }
    
   function getDomItemFromId($id,$pere='') {
   	if ( ! $pere ) $pere = $this->XMLDOM ;
   	$ok=false;
   	foreach ( $pere->getElementsByTagName('ITEM') as $itemDOM ) {
  	if ($itemDOM->getAttribute('id') == $id ) {
		$ok=true;
		break;
		}
  	}
   	if($ok) return $itemDOM;
   	throw new exception("Pas d'item trouvé avec id='$id' dans l'etape ");
   	return false;
   }
  
     function getDomEtapeFromId($id) {
   	$pere = $this->XMLDOM ;
   	$ok=false;
   	foreach ( $pere->getElementsByTagName('ETAPE') as $itemDOM ) {
  	if ($itemDOM->getAttribute('id') == $id ) {
		$ok=true;
		break;
		}
  	}
   	if($ok) return $itemDOM;
   	return false;
   }
      
  /*Clone un Item d'id 'id' dans l'objet pere spécifié
   *  c'est la balise ActiveItem qui est le déclencheur
   * de clonage d'un Item. Les clones sont créés avec le suffixe
   * _NewIntancex
   * si un element DOM est specfifé pour $frere; alors
   * l'item sera créé juste avant
  */
  //$this->cloneItem($activI->getAttribute('idItem'),$etape,$activI);
  function cloneItem($id,$pere,$frere='') {
   if (!$pere ) $pere = $this->XMLDOM ;
  
  foreach ( $pere->getElementsByTagName('ITEM') as $item ) {
  	if ($item->getAttribute('id') == $id ) {
		break;
		}
  	}
  $nb=$item->getAttribute('nombre_de_clones');
  if(!$nb) $nb = 0;
  $nb ++ ;
  $item->setAttribute('nombre_de_clones',$nb);
  $newnode = $item->cloneNode(true);
  $newnode->setAttribute('id',$id.'_NewIntance'.$nb);
  if($frere) {
  	//si attribut bloc, recherche du premier du bloc
  	if ( $frere->hasAttribute('bloc') ) {
  		foreach ( $pere->getElementsByTagName('ActiveItem') as $cousin ) {
  			if ($cousin->getAttribute('bloc') == $frere->getAttribute('bloc') )
				break;
  		}
  		$pere->insertBefore($newnode,$cousin);
  	} else {
  		$pere->insertBefore($newnode,$frere);	
  	}
  } else {
  	$pere->appendChild($newnode);	
  }
  
}
  
  //Renvoie un tableau contenant les valeurs de l'item et de ses clones
  function getCloneValues($id) {
  	$pere = $this->XMLDOM ;
  	$reg=array();
  	$vals=array();
  	foreach ( $pere->getElementsByTagName('ITEM') as $item ) {
  	if ($item->getAttribute('id') == $id || ereg("^".$id.'_NewIntance'.".*",$item->getAttribute('id'),$reg)) {
  		$vals[$item->getAttribute('id')]=$item->getElementsByTagName('Val')->item(0)->nodeValue;
		}
  	}
  return $vals;	
  }
  
  
    //clone l'etape id apres l'etape afterid, remet les val à vide éventuellement
  function cloneEtape($id,$afterid,$options="") {
  	if($id == '')
  		return false;
	$pere = $this->XMLDOM->getElementsByTagName("FORMX")->item(0) ;
	//print $pere->saveXML()
	//positionnement des items  	
	foreach ( $pere->getElementsByTagName('ETAPE') as $item )   {
		if ( isset ($obEtapeAft) && ! isset($obEtapeBef) )	$obEtapeBef = $item ;
		if ($item->getAttribute('id') == $id ) 				$obEtapeOri = $item ;
		if ($item->getAttribute('id') == $afterid ) 		$obEtapeAft = $item ;
	}
	//incrementation de l'etape souche
	if( $obEtapeOri->hasAttribute('nombre_de_clones')) {
		$nb=$obEtapeOri->getAttribute('nombre_de_clones');
		$nb++;
		$obEtapeOri->setAttribute('nombre_de_clones',$nb);
	} else {
		$nb = 1 ;
		$obEtapeOri->setAttribute('nombre_de_clones',$nb);
	}
	//creation du clone
	$newnode = $obEtapeOri->cloneNode(true);
	$newnode->setAttribute('id',$obEtapeOri->getAttribute('id')."_$nb");
	//renommage de tous items en  "_$nb"
	$convIdItems = array();
	foreach ( $newnode->getElementsByTagName('ITEM') as $item )   {
		$current_id = $item->getAttribute('id');
		$item->setAttribute('id',$current_id."_$nb");
		$convIdItems[$current_id] = $current_id."_$nb" ;
	}
	//renommage de toutes les balises de type valeur si l'id est relatif à l'étape
	$listeBaliseTypeValeur = array();
	foreach ( $newnode->getElementsByTagName('From') as $item )   $listeBaliseTypeValeur[] = $item ;
	foreach ( $newnode->getElementsByTagName('Arg1') as $item )   $listeBaliseTypeValeur[] = $item ;
	foreach ( $newnode->getElementsByTagName('Arg2') as $item )   $listeBaliseTypeValeur[] = $item ;
	foreach ( $listeBaliseTypeValeur as $item )   {
		$arr = array();
		if (ereg('^formVar:(.*)$',$item->nodeValue,$arr)) { 
			$id = $arr[1] ;
			//si id dans le bloc, renommage
			if ( isset($convIdItems[$id])) {
				$id = $convIdItems[$id] ;
				$item->nodeValue = "formVar:$id";	
			}
		}
	}
		
	//on remet tous les items à une valeur vide si le champ options contient reset
	if(ereg($options,'reset')) {
		foreach ( $newnode->getElementsByTagName('ITEM') as $item )
			$item->getElementsByTagName('Val')->item(0)->nodeValue = "";
		$newnode->setAttribute('etat','');
	}
	$newnode->setAttribute('IamAclone','y');
	//renommage du libelle en ...(n)
	$newnode->getElementsByTagName('Libelle')->item(0)->nodevalue = $newnode->getElementsByTagName('Libelle')->item(0)->nodevalue." ($nb)";

	//placage du clone
	if( isset($obEtapeBef) ) {
  		$pere->insertBefore($newnode,$obEtapeBef);	
  	} else {
  		$pere->appendChild($newnode);	
  	}
  	
  	//$xml = $this->XMLDOM->saveXML();
	//eko("fin duplication etape <xmp>$xml</xmp>");
  	
  	return $newnode;
  }
  
  
  
  /*Verifie qu'une balise de nom $bal existe dans un item,la crée éventuellement, et lui donne la valeur $val
   * 
  */
  function makeBalVal($item,$bal='Val',$val,$xmlobj='') 
 {
 	if(! $xmlobj) $xmlobj = $this->XMLDOM ;
 	formxTools::createTagValue($item, $bal, $val, $xmlobj);
 	return ;
  
  
  //On regarde si la balise  existe
  $hasValBal = '';
  //eko("je set ".$item->getAttribute("id")." à ".$val);
  foreach ($item->childNodes as $child) {
	if ( $child->nodeName == $bal) {
		$hasValBal = 'y' ;
		break;
		} 
	}
	//si elle existe pas on la crée
	if (! $hasValBal) {
		$BalVal=$xmlobj->createElement($bal);
		$item->appendChild($BalVal);
		}
	//suppression  des caracteres interdits	
	$conv = array('§' => '');
	$val =  strtr($val , $conv) ;

	//on regle le pb du caractere &
	$val = preg_replace ("#&(?!(amp|lt|gt);)#U","&amp;",$val);

   	//On donne la bonne valeur à la balise
	$item->getElementsByTagName($bal)->item(0)->nodeValue = utf8_encode($val) ;

	//eko("nouvelle valeur: ".$item->getElementsByTagName($bal)->item(0)->nodeValue);
 }
    
 //teste si dans les données post des infos sont présentes pour remplir cet item
 function isPostValueForItem(/*objet DOM*/  $item) {
	switch($item->getAttribute('type')) {
  		case 'LISTEN':
			if(isset($_POST[$this->prefix.$item->getAttribute('id').'_LISTEN_0']))
				return true ;
			return false ;
  		case 'TAB':
  			if ( isset($_POST[$this->prefix.$item->getAttribute('id')."_0_0"]) )
  				return true ;
  			return false;
   		case 'LISTEDYN':
   			if ( isset($_POST[$this->prefix.$item->getAttribute('id').'_new']) || isset($_POST[$this->prefix.$item->getAttribute('id')]) )
   				return true;
   			return false;
		case 'FILE':
			//TODO : tester
			return true ;
		case 'CHECK' :
			if ( isset($_POST[$this->prefix.$item->getAttribute('id')]) )
   				return true;
			//si pas conditionel, alors on considere qu'il est toujours rempli
			if(! is_object($item->getElementsByTagName('Cond')->item(0)))
				return true;
			return false ;
		default :
   			if ( isset($_POST[$this->prefix.$item->getAttribute('id')]) )
   				return true;
   			return false;
   }
 }
    
    
  /*verifie la validité d'un champ de formulaire avant de le transformer et de l'affecter dans l'objet XML*/
  function affectPost2XML($item) {  
  
  global $formxSession;
  //Certains items ne peuvent être instanciés
  if(in_array($item->getAttribute('type'),array('CLOSER')))
  	return ; 
  
  $reg = array();
  //eko("affectation de la variable ".$this->prefix.$item->getAttribute('id')."  à  ".$_POST[$this->prefix.$item->getAttribute('id')]);
  //eko("je traite le post ".$this->prefix.$item->getAttribute('id'));
	$this->debug("Entrée dans affectPost2XML,"."affectation de la variable ".$this->prefix.$item->getAttribute('id')."  à  la valeur ".clFoRmXtOoLs::getPost($this->prefix.$item->getAttribute('id')));

  //si pas de données post pour cet item, mais que sa valeur n'est pas nulle, surtout on ne
  //touche à rien, on risquerait d'écraser les données
  //ex: balises conditionelles
  //isPostValueForItem se charge des cas particuliers (ex CHECK )
  
  //f ( ! $this->isPostValueForItem($item) && (string) $item->getElementsByTagName('Val')->item(0)->nodeValue ) {
  if ( ! $this->isPostValueForItem($item) ) {
  			$this->debug("Aucune donnée POST n'est associée à cet item,  Sortie de la methode");
  			return;
  		}



	//si pas les droits au moins W
	$droit=$this->session->droit;
	if($this->XMLCore['access']) $droit = utf8_decode((string) $this->XMLCore['access']);
	if($item->getAttribute('access')) $droit = utf8_decode($item->getAttribute('access')) ;
	if(! $formxSession->getDroit($droit,'w')){
		$this->debug("acces refusé en écriture. cet item est associé au droit '$droit'");
		 return;
	}
  if( is_object($item->getElementsByTagName('Val')->item(0)) )
 	 $val_old_value = $item->getElementsByTagName('Val')->item(0)->nodeValue;
  else
  	 $val_old_value = "" ;
  	 
  switch($item->getAttribute('type')) {
  case 'LISTEN':
//  	echo(afftab($_POST));
  	$i = 0;
	$val='';
  	while ($_POST[$this->prefix.$item->getAttribute('id').'_LISTEN_'.$i]) {
		$val .= stripslashes($_POST[$this->prefix.$item->getAttribute('id').'_LISTEN_'.$i]).'|';
		$i++;
		}
  	$val = rtrim($val,'|')  ;
	$this->makeBalVal($item,'Val',$val);
  	break;
  case 'TAB':
	ereg("list:(.*)",$item->getElementsByTagName('Rows')->item(0)->nodeValue,$reg);
	$rows = explode('|',$reg[1]);
	ereg("list:(.*)",$item->getElementsByTagName('Cols')->item(0)->nodeValue,$reg);
	$cols = explode('|',$reg[1]);
	$nb_cols=0;
	foreach($cols as $col) $nb_cols++ ;
	
	$x=0;
	$y=0;
	$tab=array();
   	foreach($rows as $row) {
		foreach($cols as $col) {
			$tab[$y*$nb_cols+$x]=$_POST[$this->prefix.$item->getAttribute('id')."_$x"."_$y"];
			$x++;
			}
		$x=0;
		$y++;
		}
	$tab = implode('|',$tab);
	$this->makeBalVal($item,'Val',$tab);
  	break;
   case 'LISTEDYN':
   	//SI DEMANDE on va inserer dans la liste XHAM la valeur demandée
	$nouveau=stripslashes($_POST[$this->prefix.$item->getAttribute('id').'_new']);
	if ($nouveau) {
		$param['liste_nom']= $item->getElementsByTagName('FromXHAMList')->item(0)->nodeValue;
		$param['item_nom']=$nouveau;
		$param['idapplication']=IDAPPLICATION;
		$req = new clResultQuery ;
    	//execution de la requete
		$res = $req -> Execute ( "Fichier", "FX_addXHAMListItem", $param, "ResultQuery" ) ;
		//eko($res['INDIC_SVC']);
		//nouvelle valeur
		$this->makeBalVal($item,'Val',$nouveau);
		break;
	}
	
   //insertOnList
   case 'LISTE':
   
   	if($item->hasAttribute('multiple')) {
   		if( ! isset($_POST[$this->prefix.$item->getAttribute('id')]) || ! $_POST[$this->prefix.$item->getAttribute('id')])
   			$this->makeBalVal($item,'Val','');
   		else
			$this->makeBalVal($item,'Val',@stripslashes(implode('|',$_POST[$this->prefix.$item->getAttribute('id')])));
		} else {
		$this->makeBalVal($item,'Val',stripslashes($_POST[$this->prefix.$item->getAttribute('id')]));
		}
	 break;
   case 'FILE':
   		if(! $item->hasAttribute('extension')) {
   			$this->addErreur("L'attribut extention n'est pas présent. Pour des raisons de sécurité la balise FILE est désactivée.");
   			break;
   		}
   		if( $item->hasAttribute('rename')) 
   			$rename =$item->getAttribute('rename');
   		else 
   			$rename='';
   		if( $item->hasAttribute('destination')) 
   			$destination =$item->getAttribute('destination');
   		else 
   			$destination = $this->destinationUploads;
   		if( $item->hasAttribute('maxfilesize'))
   			$maxfilesize=$item->getAttribute('maxfilesize');
   		else
   			$maxfilesize = $this->max_size_upload;
		$re=clFoRmXtOoLs::gestUpload($this->prefix.$item->getAttribute('id'),$destination,$rename,$maxfilesize,utf8_decode($item->getAttribute('extension')));
		if($re)
			$this->makeBalVal($item,'Val','ok');
		else
			$this->makeBalVal($item,'Val','¤ko');
		break;
   case 'RADIO':
   case 'CHECK':
   		if(!isset($_POST[$this->prefix.$item->getAttribute('id')])) {
   			$this->makeBalVal($item,'Val','');
   			break;
   		}
		if($_POST[$this->prefix.$item->getAttribute('id')]=='')
			$this->makeBalVal($item,'Val','');
		else {
			//debug horreur de php 5.1.3
			$dat = $_POST[$this->prefix.$item->getAttribute('id')];
			$this->makeBalVal($item,'Val',stripslashes(clFoRmXtOoLs::implode_r($dat)));
		}
		break;
	case 'CAL':
		$postval = stripslashes($_POST[$this->prefix.$item->getAttribute('id')]);
		$this->makeBalVal($item,'Val2',$postval);
		if($item->hasAttribute('formatStore') && $postval ) {
			$date = new clDate($postval);
			switch($item->getAttribute('formatStore')) {
			case 'timestamp':	
				$newval = $date->getTimestamp();
				break;
			case 'date':
				$newval = $date->getDate();
				break;
			case 'datetime':
			default:
				$newval = $date->getDatetime();
				break;	
			}
			$this->makeBalVal($item,'Val',$newval);
		} else {
			$this->makeBalVal($item,'Val',$postval);
		}
		break;
   	default :
    	$this->makeBalVal($item,'Val',formxTools::helper_formatDatatype($item,stripslashes($_POST[$this->prefix.$item->getAttribute('id')])));
	}
 $val_new_value = $item->getElementsByTagName('Val')->item(0)->nodeValue;
 
 //si la valeur de la balise a été modifiée et que d'autres éléments dynamiques dépendent de sa valeur,
 //on réinitialise leur valeurs pour  ne pas avoir d'incohérences
 if( ( $val_new_value != $val_old_value ) && is_object($item->getElementsByTagName('OnChangeRefresh')->item(0)) ) {
 	$strListIds = $this->getValueFrom($item->getElementsByTagName('OnChangeRefresh')->item(0)->nodeValue) ;
 	$arrListIds = explode('|',$strListIds);
 	foreach($arrListIds as $id) {
 		//print "<br />va chercher ".$id;
 		$this->makeBalVal($this->getDomItemFromId($id),'Val',"");
 		if(isset($_POST[$this->prefix.$id]))
 			unset($_POST[$this->prefix.$id]);
 	}

 }
}
  //traitement des items apres application des post
function traiterPostPost($item) {
  	
$reg=array(); 	
//si demande de création linkage dans colone dynamique dans base
 if($item->getAttribute('linkDBCol')) {
			if( ereg(".*field:([^;]*);.*",$item->getAttribute('linkDBCol'),$reg) ) $field =  $reg[1] ;
				else $field = $item->getAttribute('id');
			if( ereg(".*table:([^;]*);.*",$item->getAttribute('linkDBCol'),$reg) ) { $table =  $reg[1] ;	}
					else  { $table = $this->session->tableDyn ; }
			if( ereg(".*db:([^;]*);.*",$item->getAttribute('linkDBCol'),$reg) ) $db =  $reg[1] ;
				else $db = $this->session->baseInstances ;
			
			if( ereg(".*type:([^;]*);.*",$item->getAttribute('linkDBCol'),$reg) ) { $type =  $reg[1] ; }
			else {
				//sinon on regarde si il existe déja
					$requete=new clRequete ($db, $table);
					$desc = $requete->descTable();
					if( isset($desc[$field]))
						$type=$desc[$field][1]."(".$desc[$field][0].")";
					//sinon un par defaut
					else $type = "VARCHAR(32)";
			}	
			if( ereg(".*key:([^;]*);.*",$item->getAttribute('linkDBCol'),$reg) ) { 
					$key =  $reg[1] ;
					if(ereg("(.*):(.*)",$key,$reg)) { //key:pouet:pouet;
						$key = $reg[1];
						switch ($reg[2]) {
							case 'ids':
								$val = $this->ids; break;
							case 'idinstance':
								$val = $this->idInstance; break;
							default:
								$val = $this->getFormVar($reg[2]);break;
						}
					} else { // key:pouet;
						switch ($key) {
							case 'idinstance':
								$val = $this->idInstance; break;
							case 'ids':
							default:
								$val = $this->ids;break;
						}
					}
					$keycol = $key;
					$keyval= $val;
					$key = $key." = '$val'";
				}
				else { 
					$key = "ids='".$this->ids."'" ;
					$keycol = 'ids';
					$keyval=$this->ids;
				}


			$val=utf8_decode($item->getElementsByTagName('Val')->item(0)->nodeValue);
			//on coupe si chaine rentrée trop grande
			if(ereg("VARCHAR\((.*)\).*",$type,$reg)){
				$val=substr($val, 0, $reg[1]);
				}
			$data[$field]=$val;
			$data[$keycol]=$keyval;
			$requete=new clRequete ( $db, $table ) ;
			$requete->testAndMakeCol($field,$type);
			//rechargement de la classe pour traitement des donnes data avec la colonne cette fois
			$requete=new clRequete ( $db,$table,$data) ;
			$resu = $requete->uoiRecord ($key) ;
	
 	} 
 	
 	if($item->getAttribute('link')) { //si l'item est lié à une variable globale
		$this->getGlobvarsContainer()->set($item->getAttribute('link'), formxTools::domGetValueFxItem($item));
 	} 
 	
 	if($item->getElementsByTagname('Cond')->length > 0 ) { //si l'item a une condition d'affichage, on la met à jour
 		$this->majCondAffichage($item);
 	}



 	//si l'item est de type closer et que sa condition est vraie, on cloture le formulaire et on le ferme
    if($item->getAttribute('closer') &&   $this->testCondDOM($item) ) {
 		$this->ImustDisapear = true ;
 		$this->close();
 	}
 	
 	//marqueur que les infos sont fraiches pour l'affichage ( à charge de l'afficheur de les remettre à zero ensuite )
 	$item->setAttribute('isfresh','y');
  	
  }
  
  //met à jour la condition d'affichage sur un item et renvoie
  function majCondAffichage($item) {
  		$cond = $item->getElementsByTagname('Cond')->item(0);
  		$res = $this->testCondDOM($item);
  		if($res)
  			$item->setAttribute('lasttestcond','y');
  		else	
  			$item->setAttribute('lasttestcond','n');
  		if( $res && $cond->hasAttribute('oblig')) {
  			$item->setAttribute('oblig','y');
  			$item->removeAttribute('opt');
  		} else {
  			$item->setAttribute('opt','y');
  			$item->removeAttribute('oblig');
  		}
  		return $res ;
  }
  
  
  /*
  affiche la maquette d'un formulaire pour prévisualisation
  */
  function affMaquette() {
    //pour commodité d'ecriture
    $xml = & $this->XMLCore ;
    //instanciation modelixe  
    $mod = new ModeliXe ( "TG_maquetteFoRmX.mxt" ) ;
    $mod -> SetModeliXe ( ) ;

    //recuperation des balises descriptives
    $mod -> MxText ( "titre",$xml->Libelle[0]);
    $mod -> MxText ( "objet",$xml->Objet[0]);
    
    //puis on parcourt les balises ETAPE               
    foreach ($xml->ETAPE as $etape) {
    	$this->printEtape($mod,$etape);
        }
 
	
    return $mod -> MxWrite ( "1" ) ;
  }
  
  function printActions(& $mod, $etape) {
  //si pas d'actions pour cette étape
  if (!  isset($etape->ACTION) ) {
  	$mod -> Mxbloc( "etape.actions", "delete" );	
  } else { //sinon
  	foreach ($etape->ACTION as $action) {
  	$ya_une_action="";
	if($action['cond']=='false') continue;
	if($action['hide']=='y') continue;
	$ya_une_action="1";
	switch($action["type"]){
	case "mail":
		$descr = $action->Libelle[0];
    		$descr .= utf8_encode(": Envoi de courriel à ");
		foreach($action->Destinataire as $destinataire) $descr .= $destinataire.' ';
		break;
	case "trigger":
		$descr = utf8_encode("Création de l'action ");
		$descr .= $action["id_formx"];
		$decr .= " .";
		break;
	case "goto":
		$descr = utf8_encode("Répétition de l'étape ".$action['step']);
		break ;
	case "totem":
	case "formxproc":
		$descr = "appel du module ";
		$descr .= $action['id_formxproc'];
		break;
	case "note":
		$descr = "Note: ";
		$descr .= $action->Libelle[0];
		break;
	case "editdoc":
		$descr = "Impression du document ";
		foreach($action->Document as $docu) {
			$descr .= '"'.$docu['nom_doc'].'", ';
			}
		$descr=rtrim($descr);
		$descr=rtrim($descr,',');
		break;	
	default :
		$descr = "Action: ";
		}
		$mod->MxText ( "etape.actions.item.libelle",$descr);
		$mod -> MxBloc ( "etape.actions.item", "loop" ) ;
	}
     if ($ya_une_action) $mod -> MxBloc ( "etape.actions", "loop" ) ;
     else $mod -> Mxbloc( "etape.actions", "delete" );
     }	
  }
  
  /*Affiche le bloc etape*/
  function printEtape(& $mod, & $etape) {
  global $formxSession ;
  if ($etape['optimize'] == 'y' ) {
  	$this->useCache = true ;
  	$this->resetCacheValue();
  } else {
  	$this->useCache = false ;
  }
  $domEtape = $this->getDomEtapeFromId($etape['id']);
  $this->debug("Entrée dans printEtape() pour l'etape ".$etape->Libelle[0]);
   $mod -> MxText ( "etape.titre_etape",$etape->Libelle[0]);
   if ((! $this->XMLCore['dontPrintNavi']) && $this->XMLCore['nb_etapes'])
   	$mod -> MxText ( "etape.navi_etape",'('.$etape['no_etape'].'/'.$this->XMLCore['nb_etapes'].')');
       
   /* boutons de validation ou d'annulation de l'etape	*/
   	//si l'etape est achevée, on enleve le bloc dans le template et on affiche les actions prises
   if (( $etape['etat']) != "fini" ) {
   	foreach ($etape->ITEM as $item){
   		$optimize=$etape['optimize']?'y':'';
		$this->printItem($mod,$item,'RW',$domEtape,$optimize);
		}
	//si présence d'un item non validé 
		if(isset($this->lastNonValidItem) && $this->lastNonValidItem) {
		$mod->MxText("etape.infoNoValid.infoNoValid","<span style='color:red;'>".clFoRmXtOoLs::u8message("infoNoValid1")
				.$this->lastNonValidItem->Libelle[0]
				.clFoRmXtOoLs::u8message("infoNoValid2"))."</span>";	
		} else {
				$mod->MxBloc( "etape.infoNoValid", "delete" ) ;
		}
	
	//on affiche les bouttons et vire les actions
   	$mod->MxFormField("etape.valid_bouttons.etapeCancel","image",$this->prefix."step_cancel","on","value='on' src=\"".FX_URLIMGANNMINI."\"");
  	$mod->MxFormField("etape.valid_bouttons.etapeValid","image",$this->prefix."step_valid_".$etape['id'],"on","value='on' src=\"".FX_URLIMGVAL."\"");
	//on va chercher le droit general du formlaire
	if ($this->XMLCore['access'] ) {
    		$droit = utf8_decode((string) $this->XMLCore['access']);
   	} else { //sinon on va chercher celui par defaut
   		$droit = $this->session->droit ;
		}
	if ($formxSession->getDroit($droit,'m'))
		$mod->MxFormField("etape.valid_bouttons.etapePrev","image",$this->prefix."step_prev","<-","value='on' src=\"".FX_URLIMGPREV."\"");
	if ($formxSession->getDroit($droit,'a'))
	$mod->MxFormField("etape.valid_bouttons.etapeNext","image",$this->prefix."step_next","->","value='on' src=\"".FX_URLIMGNEXT."\"");
	
   	$mod -> MxBloc ( "etape.actions", "delete" ) ;
      	
} else {//sinon
	//affichage des items en lecture seule
	foreach ($etape->ITEM as $item){
		$optimize=$etape['optimize']?'y':'';
		$this->printItem($mod,$item,'RO',$domEtape,$optimize);
		$this->debug("Sorti de printItem");
		}
	$this->printActions($mod, $etape);
	//on vire les bouttons	
	$mod -> MxBloc ( "etape.valid_bouttons", "delete" ) ;
	}
     $mod -> MxBloc ( "etape", "loop" ) ;
     $this->useCache = false ;
}


/*Exexution d'un test d'une balise conditionelle en simpleXML*/
function testCond($cond){

	switch($cond['type']) {
	case 'equal':
		return ( $this->testCond($cond->Arg1) == $this->testCond($cond->Arg2) );
	case 'inf':
		return ( $this->testCond($cond->Arg1) < $this->testCond($cond->Arg2) );
	case 'inf_equal':
		return ( $this->testCond($cond->Arg1) <= $this->testCond($cond->Arg2) );
	case 'sup_equal':
		return ( $this->testCond($cond->Arg1) >= $this->testCond($cond->Arg2) );
	case 'sup':
		return ( $this->testCond($cond->Arg1) > $this->testCond($cond->Arg2) );
	case 'diff':
		return ( $this->testCond($cond->Arg1) != $this->testCond($cond->Arg2) );
	case 'or':
		return ( $this->testCond($cond->Arg1) || $this->testCond($cond->Arg2) );
	case 'and':
		return ( $this->testCond($cond->Arg1) && $this->testCond($cond->Arg2) );
	case 'not':
		return (! $this->testCond($cond->Arg1)) ;
	case 'in':
		return(in_array($this->testCond($cond->Arg1),explode('|',$this->testCond($cond->Arg2))));
	case 'function':
	  	return ( $this->callFunc($cond['namefunc']));
	default:
		return $this->getValueFrom((string) $cond);
	}
	
}

/*Exexution d'un test d'une balise conditionelle en DOM*/
/*attention, s'appelle un niveau AU DESSUS (item) */
function testCondDOM($cond){
	$xml = $this->XMLDOM->saveXML($cond);
	//eko("balise condition: <xmp>$xml</xmp>");
	return $this->testCond(simplexml_load_string($xml)->Cond);
}



/*analyse et renvoie selon le type de données le contenu d'une
balyse From */
function getValueFrom($dedans,$noErrorIfRaw="") {
	//eko($dedans);
	$this->debug("entree danss getValueFrom avec l\'argument $dedans");
	$reg = array();
	$regs = array();
	//eko("<xmp>".$dedans." </xmp>");
	
	//on sépare le sélecteur de l'argument
	ereg('^([^:]+):(.*)$',$dedans,$reg);
	$selecteur = $reg[1];
	//on regarde si le sélecteur contient un helper
	if(ereg('^([^\|]+)\|(.*)$',$selecteur,$regs)) {
		$helper=$regs[1];
		$selecteur=$regs[2];
	} else
		$helper='';
	switch($selecteur) {
	case 'const':
	case 'list':
		$valBrute = $reg[2];
		break ;
	case 'formVar':
		$valBrute = $this->getFormVar($reg[2]);
		break ;
	case 'var':
		$valBrute = $this->getVar($reg[2]);
		break ;
	case 'func':
		$valBrute = $this->callFunc($reg[2]);
		break ;	
	case 'fusion':
		$valBrute = utf8_encode( $this->current_fusion[$reg[2]] );
		break ;
	case 'object':
		$valBrute = utf8_encode($this->subject->get($reg[2]));
		break ;
	case 'subject':
		$srtOut = "" ;
		$strexec = "\$srtOut = utf8_encode(\$this->subject->".$reg[2]."); " ;
		//print $strexec ;
		eval($strexec);
		$valBrute = $srtOut;
		break ;			
	case 'accessor':
		$valBrute = utf8_encode($this->accessor->get($reg[2]));
		break ;
	case 'globalObject':
		$reg2 = array();
		ereg('^(.*)->(.*)$',$reg[2],$reg2);
		$srtOut = "" ;
		eval("\$srtOut = utf8_encode(\$GLOBALS['".$reg2[1]."']->".$reg2[2]."); ");
		$valBrute = $srtOut;
		break ;
	case 'class':
		$reg2 = array();
		ereg('^(.*)->(.*)$',$reg[2],$reg2);
		eval("\$myObj = new ".$reg2[1].";");
		eval("\$srtOut utf8_encode(\$myObj->".$reg2[2].");");
		$valBrute = $srtOut;
		break ;
	case 'option':
		$valBrute =  $this->session->getOption($reg[2]);
		break;
	case 'defined':
		$valBrute = utf8_encode(constant($reg[2]));
		break ;
	default :
		if($noErrorIfRaw)
			$valBrute = $dedans ;
		else
			$this->addErreur("FoRmX: <xmp>".$dedans." </xmp>:type inconnu dans la balise From");
		return '';
		}
		
		if($helper) {
			$funcname = $this->session->loadFunc($helper);
			$a = '';
			eval("\$a = $funcname(\$valBrute);");
			return $a;
		}
		return $valBrute ;
}






/*Cette fonction gère l'affichage d'un item XML du formulaire*/
function printItem(& $mod,$item,$acces='RW',$domEtape,$optimize='') {
   global $formxSession;
   global $compteurItem ;
   
	//eko($item['id']);
   
   //Certains items ne peuvent être affichés
	if(in_array($item['type'],array('CLOSER')))
		return ;
   
   //print '<br />'.$domEtape->getAttribute('no_etape')._.$item['id'] ;
   
   $hideItem = false ;
   
   //print "******************ACCESS $acces ********************";
   if($acces == 'RW' ) {
   	$compteurItem ++ ;
   	$this->debug("____________________ITEM $compteurItem _______________");
   }
   
	//eko($item[id]." = ".$item->Val[0]);
	$this->debug("Entrée dans printItem pour l'item ".$item['id']);
	$domItem = $this->getDomItemFromId($item['id'],$domEtape);

	//----------------------------------------
  //Gestion des conditions sur l'item et de leur implication sur l'affichage
	if($item->Cond['type'] && ! ( $acces=='RO' &&  $optimize ) ) { //cas normal, ou pas d'optmize sur RO (rare)
  		$this->debug("test de la condition activee pour l'item");
  		//test condition
  		if($item['isfresh']=='y') { //TraiterPostPost vient de le calculer
  			//eko("c'est frais");
  			$resCond =  $item['lasttestcond']=='y'?true:false;
  			$domItem->setAttribute('isfresh','');	
  		} else { //sinon on le recalcule
  			//eko("c'est pas frais");
  			$resCond =$this->majCondAffichage($domItem);
  			$domItem->setAttribute('isfresh','');	
  		}
  		$this->debug("Fin du calcul");
  		if (! $resCond) { //condition renvoie faux

			//on quitte l'affichage de l'item, sauf dans le cas où l'item est lié avec du DHTML, au quel cas il doit s'afficher caché
			if( ! $item['printHidden'])
				return ;
			else
				$hideItem = true ;
  		}
  	} elseif ($item->Cond['type'] && $acces =='RO' && $item['lasttestcond'] == 'n' ) {  //optimize est activé , lecture seule, et en cache on a la valeur non
		return ;	
  	}
  //--------------fin des conditions sur l'item
  
  $this->debug("calculs preliminaires");
	//si saut de l'item en mode non-impression
	if($this->modeImpression && (string) $item['dontPrint'])	
  		return ;

   //generation d'un id de champ de formulaire
   $id=$this->prefix . $item['id'];
  //ajout ou non de l'asterisque
   if($this->XMLCore['showOblig'] && $this->isItemOblig($item['id']) && !($this->modeImpression ) )
  		$showOblig = '<span style="color:red"> * </span>';
  else 
  		$showOblig = "";
	//si précisions supplémentaires nécéssaires
	if((string) $item['multiple']  && (string) $item['type'] == 'LISTE' ) {
		//eko('lplp');
		$precisions = '<span style="color:#AACC00;"><I> choix multiple = [ctrl]</I></span>';
	}
	else
		$precisions = "";
		
	if((string) $item['hyperinfo']  && !($this->modeImpression ) ) {
		$precisions .= clFoRmXtOoLs::getHyperLink((string) $item['hyperinfo']);
	}	
		
  		
  	 //recuperation du libelle mis en forme
   //attention, le libellé peut être redéfini plus loin
   //pour l'instant, faire une recherche de texte item->Libelle pour les retrouver
   if((string) $item->Libelle[0]) {
   	//$mod -> MxText ( "etape.item.libelle.libelles","yahourt bleu");
   	$mod -> MxText ( "etape.item.libelle.libelles",clFoRmXtOoLs::getStyledBal($item->Libelle).$showOblig.$precisions);
   }
   
   //affectation de l'id de la ligne
   $mod -> MxText ( "etape.item.idligne",'ligne_'.$item['id']);
   //cachageDeLaLigne
   if($hideItem) 
		$mod -> MxText ( "etape.item.optionsligne","style='display:none;'");
	else
		$mod -> MxText ( "etape.item.optionsligne","");

   //maintenant on va traiter suivant le type de la balise item (attribut)
   $type_item = $item['type'];
   //si l'etape est finie ou
   
   //si balise Readonly, alors texte en lecture seule
   //SAUF pour les items beneficiant d'un affichage spécial,
   //il faut les lister ici
   if ($acces == 'RO' && $type_item!='TAB' && $type_item!='SLIDER' ) $type_item = 'RO';
   
   //si pas le droit d'acces à ce champ
   if ($item['access'] && ! $formxSession->getDroit(utf8_decode((string) $item['access']),'r' )) $type_item = 'NORIGHT';
   
   if ( $item['readonly'] && in_array($item['readonly'] , array('y','yes','o','oui','Y','O','OUI','YES') ) && ! in_array($item['readonly'] , array('n','no','non','N','NON','NO') ) )  $type_item = 'RO';
   

   //si demande d'appel externe pour remplir le champ
	if($item->From[0] && ! (  $optimize  &&  $acces == 'RO' )) 
	{
   		if(isset($valXT)) unset($valXT);
			$valXT=  (string) $this->getValueFrom($item->From[0]);
			

		if ($this->current_fusion) 
		{
			$this->setFormVar($item['id'],utf8_decode($valXT),'no_simplexml_update no_sql_update');
			$item->Val[0] = $valXT ;
		//cas général
		} 
		else if ( (( ($valXT != '') && ! (string) $item->Val[0]  ) || $item['resync'] ) && ! (  $optimize  &&  $acces == 'RO' ) ) 
		{
			$item->Val[0] = $valXT ;
			$this->setFormVar($item['id'],utf8_decode($valXT),'no_simplexml_update no_sql_update');
		}

	}
	
	//si demande de saut de l'item quand il est nul
  	if ( (string) $item['dontPrintwhenNull'] and $acces == 'RO' and ( ! (string) $item->Val || in_array((string) $item->Val,$this->session->getNullValues()))) {
  		//eko(  (string) $item->Val);
  		//eko(  "<xmp>".$item->asXML()."</xmp>");
  		return ;
  	}	
	
	
   //Si la variable est liée à une variable globale d'ids
   //migré dans traitePostPost , pour recette, décommenter si problème
   //if($item['link']  && ! ($optimize && $acces == 'RO')) $this->setVar($item['link'],(string) utf8_decode( $item->Val )); 
	   
   //Si valeur persitante demandée
   if($item->ValPersist[0] )
		$item->Val[0] = (string) $item->ValPersist;

   if($item->FromPersist[0] ) {
   		$item->Val[0]= $this->getValueFrom((string) $item->FromPersist);
	}

   //si rechargement demandé
	if ($item['onChangeReload'] == 'y' ) {
		$options = "onchange=\"document.formx_form.pavalider.value='y' ;document.formx_form.submit() ;\"" ;
		$optionsradio = "onclick=\"document.formx_form.pavalider.value='y' ;document.formx_form.submit() ;\"" ;
	}

	//si rechargement demandé avec possibilité de validation
	if ($item['onChangeSubmit'] == 'y' ) {
		$options = "onchange=\"document.formx_form.submit() ;\"" ;
		$optionsradio = "onclick=\"document.formx_form.submit() ;\"" ;
	}
	
	//------------ affichage DHTML de certains items ---------------
	//Construction du javascript exécuté à chaque changement de valeur de l'item
	$nbItemsDhtmlKinked = 0 ;
	$js = '' ;
	//1) on cache tous ceux qui sont cités
	foreach( $item->ShowItemsOnVal as  $showItemOnVal) {
		if( ! (string) $showItemOnVal->OnVal )
			continue ;
		$liste = explode('|',$this->getValueFrom((string) $showItemOnVal->ListIdItems));
		foreach($liste as $idLinkedItem) {
			$js .= "   formxjs_disappears('$idLinkedItem') ; ";
		}
	}
	//2) on fait apparaitre ceux qui ont une valeur autorisée
	foreach( $item->ShowItemsOnVal as  $showItemOnVal) {
		if( ! (string) $showItemOnVal->OnVal )
			continue ;
		$nbItemsDhtmlKinked ++ ;
		$val = $this->getValueFrom((string) $showItemOnVal->OnVal);
		$liste = explode('|',$this->getValueFrom((string) $showItemOnVal->ListIdItems));
		foreach($liste as $idLinkedItem)
		{
			$js .= " if ( formxjs_getvalue('".$item['id']."') == '".addslashes($val)."'  || formxjs_invalue('".addslashes($val)."','".$item['id']."') ) { formxjs_appears('$idLinkedItem') ; }  ; ";
		}
	}
	if( $nbItemsDhtmlKinked > 0 ) {
		$options = "onchange=\"$js\" ";
		$optionsradio = "onclick=\"$js\" ";
	}
	//-------------------FIN DHTML conditionel items----------------------


   //on garde juste le bloc modelixe concerné
   $this->keepBloc(& $mod,"etape.item",$type_item);
      $this->tmp_parite = ($this->tmp_parite + 1) %2 ;
   if (($this->tmp_parite ) == 1 ) {
   	$mod -> Mxattribut ( "etape.item.style_gche","aa_gauche");
	$mod -> Mxattribut ( "etape.item.style_dte","aa_bb");
   } else {
	$mod -> Mxattribut ( "etape.item.style_gche","aa_gauche_pair");
	$mod -> Mxattribut ( "etape.item.style_dte","aa_bb_pair");
	}
   //si pas d'exlpication pour l'item, on vire le bloc explication
   if(isset($item->Explication) ) {
   	$mod-> MxText("etape.item.explication.expl",$item->Explication[0] );
	$mod-> MxText("etape.item.explication.lib",$item->Libelle[0].$showOblig.$precisions );
	$mod-> MxBloc("etape.item.libelle","delete");
	if (($this->tmp_parite ) == 1 ) {
   		$mod -> Mxattribut ( "etape.item.explication.style_gche","aa_gauche");
	} else {
		$mod -> Mxattribut (	 "etape.item.explication.style_gche","aa_gauche_pair");
		}
   } elseif ((string) $item['histo']) {
   		//recuperation de l'historisation des données précédentes
		$varsFusions =$this->getAllValuesFromFormx($this->idformx,(string) $item['id'],"","moreinfos") ;
		$i = 0;
		$nb = $varsFusions['INDIC_SVC'][2] ;
		$info = "";
		for ($i=0;$i<($nb-1);$i++) {
			if( ! (string) $varsFusions["infosmore_auteur"][$nb-$i-1] )
				$auteur = "inconnu" ;
			else
				$auteur = $varsFusions["infosmore_auteur"][$nb-$i-1] ;
			//$info.= "<br />".$varsFusions["infosmore_dt_creation"][$nb-$i-1].",&nbsp;".$auteur.":&nbsp;".utf8_encode($varsFusions[(string) $item['id']][$nb-$i-1]) ;
			$info.= "<br />".utf8_encode($varsFusions["infosmore_dt_creation"][$nb-$i-1]).",&nbsp;".$auteur.":&nbsp;".utf8_encode($varsFusions[(string) $item['id']][$nb-$i-1]) ;
			}
   		$mod-> MxText("etape.item.explication.lib",clFoRmXtOoLs::getStyledBal($item->Libelle).$showOblig.$precisions );
   		$mod-> MxText("etape.item.explication.expl",$info);
		$mod-> MxBloc("etape.item.libelle","delete");
   			if (($this->tmp_parite ) == 1 ) {
   				$mod -> Mxattribut ( "etape.item.explication.style_gche","aa_gauche");
			} else {
				$mod -> Mxattribut ( "etape.item.explication.style_gche","aa_gauche_pair");
			}
   } else {
	$mod-> MxBloc("etape.item.explication","delete");
	}
   $this->debug("debut bloc affichage");   	
   $reg = array();
   //distiguons suivant le type d'item
   switch( $type_item ) {
   //Si l'etape est finie et qu'on est en read only
   
   case 'NORIGHT':
		break;
		
   case 'RO':
   	if($item['type'] == 'HIDDEN') {
		$this->tmp_parite = ($this->tmp_parite + 1) %2 ;
		break ;
		}
   	$nullvals=array('#','');
	$txt = $item->Val[0];
	//si cal; et qu'une valeur d'affichage differe de valeur de stockage
	if($item['type'] == 'CAL' && (string) $item->Val2)
		$txt = $item->Val2[0];
	
	if(in_array($txt,$nullvals)) $txt = '' ;
	//application de styles
	if((string) $item['class'] )
		$txt = "<span class='".$item['class']."'>$txt</span>";
	if((string) $item['style'] ) {
		$txt = "<span style='".$item['style']."'>$txt</span>";
	}
	$mod -> MxText("etape.item.RO.minitxt",$txt );
   	break ;
   case 'RADIO':
   	$mXtype='radio';
   case 'CHECK':
   	if($item['cols'])
   		$nbcols = (int) $item['cols'] ;
   	else
   		$nbcols = 1 ;
   	if(! isset($mXtype)) $mXtype='checkbox';
   	//Cas de liste figée
   	if (isset($item->FromList)) {
		$val = ereg("list:(.*)",$item->FromList[0],$reg);
		$tabtmp = explode('|',$reg[1]);
		}
	//cas de liste XHAM
	if (isset($item->FromXHAMList)) {	
		//recuperation de la liste HHAM
		$tabtmp= $this->listeGen -> getListeItems ( addslashes(utf8_decode($item->FromXHAMList[0])), "1", '', '', "" ) ;
		//creation de la liste
		unset($tabtmp[0]);
		if(count($tabtmp) < 1 ) {
			$this->listeGen -> addListeWithItem ( addslashes(utf8_decode($item->FromXHAMList[0]))) ;
			$tabtmp= $this->listeGen -> getListeItems ( addslashes(utf8_decode($item->FromXHAMList[0])), "1", '', '', "" ) ;
			}
		//on enlve une specificité XHAM
		unset($tabtmp[0]);
		$tabtmp2=array_map('utf8_encode',$tabtmp);
		unset($tabtmp);
		foreach ($tabtmp2 as $cle => $valeur) {
			$tabtmp[$valeur]=substr($valeur,0,$this->lngchmp);
			}
		unset($tabtmp2);
 		}
	
	//si demande de libelles à la place des valeurs
	if (isset($tablibs)) unset($tablibs);
	if(isset($item->FromListLibelles)) {
		$tablibs = ereg("list:(.*)",$item->FromListLibelles[0],$reg);
		$tablibs = explode('|',$reg[1]);
		}
			
	//on recupere les données XML
	$vals = explode('|',$item->Val[0]);	

	//affichage
	$i=-1;
	if(! isset($optionsradio)) $optionsradio='' ;
	foreach($tabtmp as $val) {
		$i++;
		if ( in_array($val,$vals) ) {$value='CHECKED';} else {$value='';}
		//$mod -> MxCheckerField("etape.item.$type_item.champ",$mXtype, $id.'[]', $val,$value,$optionsradio);
		if( $item['reloadOnlyCheckValues'] && ! in_array($val,explode('|',$item['reloadOnlyCheckValues'])))
			$currentOptionRadio = '' ;
		else
			$currentOptionRadio = $optionsradio ;
		$mod -> MxCheckerField("etape.item.$type_item.ligne.col.champ",$mXtype, $id.'[]', $val,$value,$currentOptionRadio);
		if(!isset($tablibs)) $tablibs="";
		if(! $tablibs) {
			$mod -> MxText("etape.item.$type_item.ligne.col.champ_aff",$val);
		} else {
			$mod -> MxText("etape.item.$type_item.ligne.col.champ_aff",$tablibs[$i]);
		}
		//$mod -> MxText("etape.item.$type_item.ligne.col.champ_aff",$tablibs[$i]);
		$mod -> MxBloc("etape.item.".$type_item.".ligne.col",'loop');	
		if( ($i % $nbcols) == ($nbcols - 1) )
			$mod -> MxBloc("etape.item.".$type_item.".ligne",'loop');
		//$mod -> MxBloc("etape.item.".$type_item,'loop');
		}
	//si sortie de la boucle avant validation du bloc de la derniere ligne
	if( ($i % $nbcols) != ($nbcols - 1) )
		  $mod -> MxBloc("etape.item.".$type_item.".ligne",'loop'); 
   	unset($mXtype);
   	unset($tabtmp);
   	break;
    case 'LISTEN':	
    	//pour communiquer avec la fonction appellée
    	$this->ListenVals = explode('|',$item->Val[0]);
    	
    	//recuperer le tableau des listes
    	if (isset($item->FromFuncListN)) {	
		//recuperation de la liste HHAM
		$tablist= $this->callFunc($item->FromFuncListN[0]);
		}
	//combien on a de 
	$nb_listes = count($tablist);
	for($i=0;$i<$nb_listes;$i++) {
		if( ! is_array( $tablist[$i]))
			$tablist[$i] = array();
		$mod -> MxSelect("etape.item.LISTEN.miniitem.select1",$id."_LISTEN_$i" , $this->ListenVals[$i], $tablist[$i] ,$this->invselect, '', "class=\"select_mono\""." onchange=\"document.formx_form.pavalider.value='y' ;document.formx_form.submit() ;\"");
		
		
		//$mod->Mxattribut("actions.frem.code","document.FoRmXcase.FormX_ext_goto_.value = 'RM".$data[id_instance][$i]."';document.FoRmXcase.submit();");
		
	 	$mod -> MxBloc ( "etape.item.LISTEN.miniitem", "loop" ) ;  
		}
	break ;
	
    case 'LISTEDYN':
   //peu de difference avec LISTE, c'est pour ça qu'on ne le fait pas sortir
   //et qu'il va emprunter le case LISTE
	   //generation du formfield d'ajout de champ
   	$size = $this->lngchmp -20 ;
   	if(!isset($options)) $options='';
   	$mod -> MxAttribut('etape.item.LISTEDYN.img',FX_URLIMGRET);
	$mod -> MxFormField("etape.item.LISTEDYN.newEntry",'text', $id."_new",'',"class=\"text2\" size=\"$size\" $options");
   case 'LISTE':
	$this->debug("entrée dans le cas type='LISTE'");
	$tabtmp=$this->getListes($item);
	$val = $item->Val[0];
	//si c'est un select multiple
	if(isset($multiple)) unset($multiple);
	$invselect=$this->invselect;
	if ( isset($item['multiple']) ) {
		//précisions sur le libelle
		$invselect='';
		//traitement suivant valeur de l'attribut
		if ( $item['multiple'] == 'yes' || $item['multiple']=='y') {
			$multiple = (int) $this->multiselectsize;
		} elseif ($item['multiple'] == 'all') {
			$multiple = sizeof($tabtmp) ;
		} else {
			$multiple = (int) $item['multiple'] ;
				}
		//on repasse en array pour que le form prenne bien la valeur par defaut
		$val = explode('|',$item->Val[0]);
		}
	//instanciation du modelisque
	if(!isset($options)) $options='';
	if(!isset($multiple)) $multiple='';
	if (! is_array($tabtmp)) {
		$tabtmp = array();
		$this->addErreur("Attention ! le 4eme argument de MxSelect n' est pas un tableau!! assumé par tableau vide");
	}
	$mod -> MxSelect("etape.item.$type_item.select1",$id , $val, $tabtmp ,$invselect, $multiple, "class=\"select_mono\" $options");
	unset($tabtmp);
	unset($tablist);
	break;
   case 'TXT':

   		$mod -> MxFormField("etape.item.TXT.textsimple",'text', $id,$item->Val[0],"class=\"text1\" size=\"".($this->lngchmp + 2)."\"");
		break;
	case 'SLIDER':
		$mod -> MxAttribut("etape.item.SLIDER.idsliderinput",$id);
		$mod -> MxAttribut("etape.item.SLIDER.idslidername",$id);
		$mod->MxText('etape.item.SLIDER.slidervalue',$item->Val[0]);
		$mod -> MxAttribut("etape.item.SLIDER.idslider",$id.'_container');
		$mod->MxText('etape.item.SLIDER.varsliderid',$id);
		$mod->MxText('etape.item.SLIDER.idslider2',$id.'_container');
		$mod->MxText('etape.item.SLIDER.idsliderinput2',$id);
		$mod->MxText('etape.item.SLIDER.minslider',$item['min']);
		$mod->MxText('etape.item.SLIDER.maxslider',$item['max']);
		$mod->MxText('etape.item.SLIDER.slider_labelg',$item->LibelleGauche[0]);
		$mod->MxText('etape.item.SLIDER.slider_labeld',$item->LibelleDroite[0]);
		if( $acces == 'RO' )
			$mod->MxText('etape.item.SLIDER.isreadonly','true');
		else
			$mod->MxText('etape.item.SLIDER.isreadonly','false');
		break;
   case 'NONE':
		break;		
   case 'TAB':	
   		if( $acces == 'RO' ) $option = 'readonly';
   		ereg( "list:(.*)",$item->Rows[0],$reg );
		$rows = explode('|',$reg[1]);
   		ereg( "list:(.*)",$item->Cols[0],$reg );
		$cols = explode('|',$reg[1]);
		$tabval = explode('|',(string) $item->Val);
		$mod -> MxBloc ( "etape.item.TAB.titre", "loop" ) ;
		$nb_cols=0;
		foreach($cols as $col) {
			$nb_cols++;
			$mod -> MxText("etape.item.TAB.titre.libel",$col);
			$mod -> MxBloc ( "etape.item.TAB.titre", "loop" ) ;
			}
		$x=0;
		$y=0;
		$oki='';
   		foreach($rows as $row) {
			$mod -> MxText("etape.item.TAB.ligne.lib",$row);
			foreach($cols as $col) {
				$oki = '';
				foreach($item->Col as $colsp) {
				if( $colsp['lib']== (string) $col) {
					if($colsp['type'] == 'LISTE' && $option != 'readonly') {
						$oki=1;/*
						ereg("list:(.*)",(string) $colsp->FromList,$reg);
						$tabtmp = explode('|',$reg[1]);
						unset($tab2);
						$tab2['']='';
						foreach ( $tabtmp as $cle=>$val ) $tab2[$val]=$val;
						unset($tabtmp);*/
						$tabtmp = $this->getListes($item->Col);
						//FIXME : traitement liste comme les autres : ici ca differe
						$tabtmp = array(""=>"") + $tabtmp ;
 						$mod -> MxSelect("etape.item.TAB.ligne.colonne.case_select.lacase",$id."_$x"."_$y" ,$tabval[$y*$nb_cols+$x], $tabtmp ,'', '', "class=\"case\" $option");
						//$mod -> MxFormField("etape.item.TAB.ligne.colonne.case_simple.lacase",'text', $id."_$x"."_$y",$tabval[$y*$nb_cols+$x],"class=\"case\" $option");
						$mod -> MxBloc ( "etape.item.TAB.ligne.colonne.case_simple", "delete" ) ;
						$mod -> MxBloc ( "etape.item.TAB.ligne.colonne", "loop" ) ;
						$x++;
						}
					}
				}
				if($oki) continue ;
				$mod -> MxFormField("etape.item.TAB.ligne.colonne.case_simple.lacase",'text', $id."_$x"."_$y",$tabval[$y*$nb_cols+$x],"class=\"case\" $option");
				$mod -> MxBloc ( "etape.item.TAB.ligne.colonne.case_select", "delete" ) ;
				$mod -> MxBloc ( "etape.item.TAB.ligne.colonne", "loop" ) ;
				$x++;
			}
		$x=0;
		$y++;
		$mod -> MxBloc ( "etape.item.TAB.ligne", "loop" ) ;
		}
   
   	break;
  case 'HIDDEN':
  	//eko("OO");
   	$this->tmp_parite = ($this->tmp_parite + 1) %2 ;
  	$this->setFormVar($item['id'],(string) $item->Val,'no_simplexml_update');
  	//return ;
	break;
	
   case 'LONGTXT':
	//si argument longueur de ligne
	if (isset($item['rows'])) {
		$rows = $item['rows'];
 		} else { //Sinon valeur par defaut
		$rows = $this->defrows;
	}
		$cols=$this->lngchmp-1;
		$mod -> MxFormField("etape.item.LONGTXT.textlong","textarea",$id,$item->Val[0],"class=\"formx_longtext\" cols=\"$cols\"  rows=\"$rows\"");
		break;
		
	case 'FILE':
		switch((string) $item->Val){
			case 'ok':
				$mod -> MxText("etape.item.FILE.indication",utf8_encode("<span style='color:green'>Fichier correctement transféré</span>"));
				break;
			case utf8_encode('¤ko'):
				$mod -> MxText("etape.item.FILE.indication",utf8_encode("<span style='color:red'>Problème lors de l'envoi du fichier</span>"));
				break;
			default:
				$mod -> MxText("etape.item.FILE.indication",utf8_encode("Choisissez un fichier à envoyer"));
			
		}
		
		
		$mod -> MxAttribut("etape.item.FILE.name",$id);
		$mod -> MxAttribut("etape.item.FILE.value",1000 * $this->max_size_upload);
		break;
   case 'CAL':
   //val1 un contient la donnée brute, val2 la donnée d'affichage
   //c'est au niveau de affctPost qu'on passe de 2 à 1 par appel à la classe date 
   	$this->ya_un_cal = '1';
   	//eko("val XML du cal :<xmp>".$item->asXML()."</xmp>");
	//format date ou datetime ?
	if($item['format'] == 'datetime') {
		$format = '%Y-%m-%d %H:%M:%S';
	} else if (ereg('[YmdHMS]+',$item['format'])) {
		$format = clFoRmXtOoLs::formatDatePhp2FormatCalJs($item['format']) ;
   	} else {
		$format = '%d-%m-%Y';
	}
	//1ere clause pour fonctionnement de la balise From
	if($item->Val2[0]=='' && $item->Val[0]!='') {
		$dv=$item->Val[0];
	} else {
		$dv=$item->Val2[0];	
	}
	//si on a demandé d'afficher la date courante par defaut et que la date n'a pas encore été instanciée:
	
	if(($item['default'] == 'today') && (! $item->Val2[0] ) ) {
		$dv=date(strtr($format ,array('%' => '', 'M' => 'i'))) ;
	}
	
	$mod -> MxFormField("etape.item.CAL.textCal","text",$id,$dv,"class=\"text2\"  id=\"$id\" size=\"20\" readonly");
	$mod -> MxAttribut('etape.item.CAL.img',FX_URLIMGCAL);
	
	$mod -> MxText ( "etape.item.CAL.id",$id);
	//def d'une id pour l'appel du calendrier javascript
	$mod -> MxAttribut("etape.item.CAL.idcal",$id.'_trigger');
	//le calendrier javascript necessite d'être defini juste apres le formulaire.
	$mod -> MxText ( "JavaCAL.javcalid",$id);
	//$mod -> MxText ( "JavaCAL.javcalidtrigger",$id.'_trigger');
	//$mod -> MxText ( "JavaCAL.javcalform",$format);
	$mod -> MxBloc ( "JavaCAL", "loop" ) ;
	break;
   } 
   $this->debug("fin bloc affichage");
   //eko("item fini ".$item[id]);
   $mod -> MxHidden ( "hidden_listen", "pavalider=n")  ;
   $mod -> MxBloc ( "etape.item", "loop" ) ;   
}

//pour savoir si qqn a cliqué sur qqchose avant l'instanciation de la classe
function isUserAction() {
	return $this->isuseraction ;
	}




//retourne le droit global de l'utilisateur en ce qui concerne ce formulaire
function getFormMainDroit() { 
 $xml =  $this->XMLCore ;
 $this->debug("Entrée dans affFoRmX(), générateur de l'affichage.");
  if (  $xml['access'] )
  	return utf8_decode( (string) $xml['access']) ;
  else
  	return $this->session->droit ;  
}

//optient une liste
function getListes($item) {
$reg = array();
if (isset($item->FromList)) {
		//gen du tableau à partir de la liste
		$val = ereg("list:(.*)",$item->FromList[0],$reg);
		$tabtmp = explode('|',$reg[1]);
		foreach ($tabtmp as $val) {
			$tablist[$val]=$val;
			}
 		} 
	
	//cas de liste retournée par une fonction	
	if (isset($item->FromFuncList)) {	
		//recuperation de la liste HHAM
		$tablist= $this->callFunc($item->FromFuncList[0]);
		//on enlve une specificité XHAM
		$tablist=array_map('utf8_encode',$tablist);
 		}
	
	//cas de liste XHAM
	if (isset($item->FromXHAMList)) {	
		//recuperation de la liste HHAM
		$this->debug("Recupération de la liste XHAM ".$item->FromXHAMList[0]);
		$tablist= $this->listeGen -> getListeItems ( utf8_decode($item->FromXHAMList[0]), "1") ;
		if(count($tablist) < 1 ) {
			$this->debug("Pas de liste XHAM trouvée... création");
			$this->listeGen -> addListeWithItem ( addslashes(utf8_decode($item->FromXHAMList[0]))) ;
			$tablist= $this->listeGen -> getListeItems (utf8_decode($item->FromXHAMList[0]), "1" ) ;
			}
		//on enlve une specificité XHAM
		$tablist=array_map('utf8_encode',$tablist);
		$tabtmp = array() ;
		foreach($tablist as $key => $val) {	
			$tabtmp[$val]=$val;
 		}
 		$tablist = $tabtmp ;
	}
	unset($tabtmp);
/*
	//FIXME utlilité de ce qui suit ??????
	//traitement des champs montrés pour pas qu'ils depassent la longueur max
	$nb = 0;
	foreach ($tablist as $cle => $valeur) {
		$tabtmp[$valeur]=substr($valeur,0,$this->lngchmp);
		$nb ++ ;
		}
*/
	//si libelles différents:
	if(isset($tablibs)) unset($tablibs);
	if(isset($item->FromListLibelles)) {
		$tablibs = ereg("list:(.*)",$item->FromListLibelles[0],$reg);
		$tablibs = explode('|',$reg[1]);
		$i=-1;
		foreach($tabtmp as $key=>$value){
			$i++;
			$tabtmp[$key]=$tablibs[$i];
			}
	} else {
		$tabtmp = $tablist;
	}
	
	return $tabtmp;

}

//verifie que le formx n'est pas "mort vivant", si c'est le cas : le cloture
function killTheZombie()
{
	$this->detectHeresie();
}

//detetction comportement anormal
 function detectHeresie()
 {

	$etapes = $this->getRootDom()->getElementsByTagName('ETAPE');	//liste de nodes
   
   //on s'arrête à la dernière étape non validée
	$derniereEtapeNonFinie = null ;
	foreach ($etapes as  $etape)
	{
		if ($etape->getAttribute('etat') != 'fini')
		{
			$derniereEtapeNonFinie = $etape ;
		}
	}

	//detection heresie non finitude
	$oldstate = formxTools::getDomState($this);
	if(! $derniereEtapeNonFinie && $oldstate != 'F' && $oldstate != 'H'  )
	{
		$state = $this->getAndCloseState() ;
   		formxTools::setDomState($this,$state);
		$this->saveInstance();
   }
}

//OBSOLETTE, popup de choix de selection
function genMenuSelection() {
	return clFoRmXtOoLs::genMenuSelection($this->prefix,$this->ids);
}



/*Appelle une fonction FoRmX située dans le repertoire formx/functions/  */
function callFunc($funcname) 
{
	$this->debug("appel de la fonction $funcname via le script ".FORMX_LOCATION.'/functions/'.$funcname.'.php');
	$funcname = $this->session->loadFunc($funcname);
	if(! $funcname)
		throw new exception("fonction $funcname non présente");
	$a = '';
	eval("\$a = $funcname(\$this);");
	return $a;
}




/*va chercher une variable globale FX concernant un ids*/
function getVar($nom) {
	return $this->varGlobalContener->get($nom);
}

//Recupere l'identifiant du type de formulaire
function getIdFormx() {
 return $this->idformx;	
}

/*Quand l'ids est connu et est passé par setIds, cette fonction va charger les variables globales qui concernent cette ids*/
function loadGlobvars() {
  $this->varGlobalContener = formxTools::globalsLoad($this->getIDS());
}

public function getGlobvarsContainer()
{
	return $this->varGlobalContener ;
}


// insere un changement de variable globale en BD
function setVars() {
	$this->varGlobalContener->save();
}


/*affecte une variable globale FX concernant un ids*/
//LORSQUE JE MIGRERAI cette fonction, bien penser qu'on enleve la sauvegarde dynamique et qu'il faut que formx le gere
function setVar($nom,$val) 
{
	$this->varGlobalContener->set($nom,$val,true);
	}
	

/*acces à une variable locale au formulaire*/
function getFormVar($nom) {
  if ($this->useCache) {
  	$resTmp = $this->isCacheValue($nom);
  	if ($resTmp) return $resTmp ;
  }
   foreach (  formxTools::domSearch($this->getRootDom(),'ITEM') as $item) {
   	if ( $item->getAttribute('id') == $nom) { 
   		try 
   		{
   			$val = formxTools::getValueDomItem($item, 'Val') ;
   			$this->setCacheValue($nom,$val);
			return $val ;
   		} catch( Exception $e) {
   			$this->session->addErreur(" l'item formx $nom n'a pas de sous balise Val");
   			return '' ;
   		}
		
		
   		}
   	}
return '';
}

function isCacheValue($nom) {
	//eko($this->cacheValue);
 if ( ! isset($this->cacheValue)){
	$this->cacheValue = array () ;
	return false ;	
 } else if ( isset ($this->cacheValue[$nom])) {
 	$this->debug("========acces à un form par le cache");
 	return $this->cacheValue[$nom] ;
 } else {
 	return false ;	
 }
}

function setCacheValue($nom,$val) {
	 if ( ! isset($this->cacheValue))
		$this->cacheValue = array ();
	$this->cacheValue[(string) $nom] = $val ;
}

function resetCacheValue() {
	$this->cacheValue = array ();
}

/*enregistre une variable de formulaire*/
function setFormVar($nom,$val,$option='') {
	global $errs;
	//$errs->whereAmI();
	//eko("setFormVar appellé pour $nom = $val option= $option");
  $items = $this->XMLDOM->getElementsByTagName('ITEM');
   foreach ($items as $item) {
   	if ( $item->getAttribute('id') == $nom) { 
		//eko("trouve la valise");
		$this->makeBalVal($item,'Val',$val);
		$this->setCacheValue($nom,$val);
		//$item->getElementsByTagName('Val')->item(0)->nodeValue = utf8_encode($val);
		//eko("maintenant il a la valeur ".$item->getElementsByTagName('Val')->item(0)->nodeValue );
		$reg= array();
		if(! ereg('no_simplexml_update',$option,$reg) ) 	$this->updtXML() ;
		if(! ereg('no_sql_update',$option,$reg) ) $this->saveInstance();
//		eko('labalis lexist : <xmp>'.$this->XMLDOM->saveXML().'</xmp>');
		return 'oki';
   		}
   	}
return '';

}


//renvoie tous les items du formulaire
function getAllItems() {
	$items = $this->XMLDOM->getElementsByTagName('ITEM');
	$res = array();
   	foreach ($items as $item) {
   		$res[]=$item->getAttribute('id');
   	}
   	return $res;
}

//renvoie un simple tableau avec les id /val du formulaire ( validé uniquement pour les formulaires clotures
//renvoie tous les items du formulaire
function getTabAllItemsValues($options='') {
	$noNominativeData = false ;
	if($options)
	{
		if(isset($options['noNominativeData']) && $options['noNominativeData'])  $noNominativeData = true ;
	}
	$res = array() ;
    $res['ids'] = $this->getIDS() ;
    $tabFirstCols = array() ;
    if(isset($options['firstColsFunc']))
    {
        if(isset($options['firstColsFuncArgField']))
            $arg = $this->getFormVar($options['firstColsFuncArgField']) ;
        else
        {
            $arg = $this->getIDS() ;
        }
		try
		{
			eval ("\$tabFirstCols = ".$options['firstColsFunc']."('$arg') ; ");
		} catch (Exception $e)  {
			return array() ;
		}
    }
    
   	foreach (formxTools::domSearch($this->getRootDom(), 'ITEM') as $item)
	{
		if( ! $noNominativeData || ! $item->hasAttribute('nominativeData') )
			$res[$item->getAttribute('id')]=formxTools::getValueDomItem($item);
   	}
   	return $tabFirstCols + $res;
}



//donné un ids precis, renvoie un TABLEAU comportant toutes les valeurs dans toutes les types d'instances spécifiées trouvées dans la base
//EXEMPLE: getAllValuesFromFormx('urgences3',array("ch1","ch2","ch3"))
//options :"moreinfos" renvoie des metadonnées supplémentaires ( heure de modif, auteur...)
// 			"idinstance" indique que le premier attribut n'est pas un idformx mais idinstance
function getAllValuesFromFormx($idformx,$values='',$ids='',$options="") 
{
  if(! $ids) $ids=$this->ids;
  if($values)
  if(! is_array($values)) $values = array($values);
  $array_oef = array();
  if(ereg("idinstance",$options)) {
  	$res['id_instance'][0]=$idformx;
  	$nb = 1 ;
  } else {
  	$req = new clResultQuery ;
  	$param = array();
  	$param['table']=$this->session->tableInstances;
  	$param['idformx']=$idformx;
  	$param['ids']=$ids;
  	$res = $req -> Execute ( "Fichier", "FX_getValuesInstance", $param, "ResultQuery" ) ;
  	$nb = $res['INDIC_SVC'][2];
  	if ( $nb == 0 ) return array("INDIC_SVC" => array( 2 => 0 ) );
  }
    $ret = array();
  	for($i=0;$i<$nb;$i++) {
 		$newInstance = new clFoRmX($this->ids,'NO_POST_THREAT');
  		$r = $newInstance->loadInstance($res['id_instance'][$i]);
  		if ( ! $r ) return array("INDIC_SVC" => array( 2 => 0 ) ) ;
  		if(! $values ) { $values = $newInstance->getAllItems();
  			//eko("ttval");
  		} else {
  			//eko($values);
  		}


		foreach($values as $val) {
			if(! isset($ret[$val])) $ret[$val] = array();
			$ret[$val][$i] = utf8_decode($newInstance->getFormVar($val));
		}
		if(ereg("moreinfos",$options,$array_oef)) {
			$ret["infosmore_auteur"][$i]=$res['author'][$i];
			$date_creation = new clDate($res['dt_creation'][$i]);
			$ret["infosmore_dt_creation"][$i]=$date_creation->getDateTextFull();
		}
	}
	unset($newInstance);
  	$ret['INDIC_SVC'][2]=$nb;
	return $ret;
}
  
  
  
  //renvoie tout les identifiants de formulaires d'un type de form trouvé pour un idu précis
  function getAllFormx($idformx,$ids='') {
  if(! $ids) $ids=$this->ids;
  $req = new clResultQuery ;
  $param['table']=$this->session->tableInstances;
  $param['idformx']=$idformx;
  $param['ids']=$ids;
  $res = $req -> Execute ( "Fichier", "FX_getIdInstance", $param, "ResultQuery" ) ;
  $nb = $res['INDIC_SVC'][2];
  if ( $nb == 0 ) return array();
  $ret = array();
  for($i=0;$i<=$nb;$i++) {
 	$ret[$i] = $res['id_instance'];
	}
  return $res;
  }
  //renvoie tous les identifiants de formulaire pour l'idu déja instancié et eventuellemen rajoutte une clause where
  function getAllFormxCw($cw=" 1=1 ") {
    $req = new clResultQuery ;
  $param['table']=$this->session->tableInstances;
  $param['cw']=$cw;
  $param['ids']=$this->ids;
  $res = $req -> Execute ( "Fichier", "FX_getInstancesFromIds", $param, "ResultQuery" ) ;
  return $res;
  }  
  

// teste si un formulaire est présent dans les données posts 
// utile pour savoir si à l'exterieur d'une classe appelante,
// quelque chose est affiché à l'écran ou pas
function isFormPresent() {
	//print affTab($_POST);
	$this->regPost(array('FormX_ext_goto_','FoRmX_INSTANCE','FoRmX_chooseNew'));
	if ( $_POST['FormX_ext_goto_'] || $_POST['FoRmX_INSTANCE'] || $_POST['FoRmX_chooseNew'] ) {
		if($this->idInstance) return $this->idInstance ;
		return "fx_inconnu"; 
	}
	return false;
}



  /*utilitaire pour modelix, palliant une chianceté du code qui implique de supprimer manuellement tous les sous blocs inutilisés dans une boucle */
  function keepBloc(& $mod,$bloc,$typegarder) {
  foreach ($this->types as $type) {
  if ($type !=$typegarder) $mod -> Mxbloc( $bloc.".".$type, "delete" );
	}
  }
  
  //fonction de fonctionnement facile pour être appellée en une ligne par une classe externe
   function easyFormx($comm='',$opt='') {
  	global $formxSession;
  	global $pi;
  	$user = $formxSession->getUser();
  	//si formulaire présent dans les données post, on ne fait rien d'autre que l'afficher
  	if ( $this->isFormPresent())
  		return $this->getAffichage();
  	//sinon on distingue suivant commande
  	switch ($comm) {
		case 'new':
			//l'option contient le nom du fichier XML
			$this->loadForm($opt);
			//test si droit en A
			if( ! $this->getDroit($this->getFormMainDroit(),'w'))
				return "Vous n'avez pas le droit d'accéder à cette page. (user: $user) (".$this->getFormMainDroit().",w)<br />Peut-être devez-vous vous ré-authentifier ?";
  			$id_instance = $this->initInstance();
  			$this->loadInstance($id_instance);
  			$this->genAffichage();
  			return $this->getAffichage();
		case 'edit':
			$this->loadInstance($opt);
			if( ! $this->getDroit($this->getFormMainDroit(),'r'))
				return "Vous n'avez pas le droit d'accéder à cette page. (user: $user) (".$this->getFormMainDroit().",r)<br />Peut-être devez-vous vous ré-authentifier ?";
			//l'option contient l'id d'instance du formulaire   	
    		$this->genAffichage();	
			return $this->getAffichage();
		case 'modif':
			$this->loadInstance($opt);
			if( ! $this->getDroit($this->getFormMainDroit(),'m'))
				return "Vous n'avez pas le droit d'accéder à cette page. (user: $user) (".$this->getFormMainDroit().",m)<br />Peut-être devez-vous vous ré-authentifier ?";
			//comme edit, mais décloture le formulaire si il est fini
			$this->unclose();
			$this->genAffichage();	
			return $this->getAffichage();
		case 'list':
			//l'option contient la clause where à passer à la requete
			//sinon tout ce qui concerne l'ids est renvoyé
			//sous forme d'un résultat résultquery
			return $this->getAllFormxCw($opt);
		case 'delete':
			//l'option contient l'id d'instance à effacer
			$this->loadInstance($opt);
			//test sur les droits
			if ($this->XMLCore['access'] ) {
    			$droit = utf8_decode((string) $this->XMLCore['access']);
   			} else { //sinon on va chercher celui par defaut
   				$droit = $this->session->droit ;
			}
			if($formxSession->getDroit($droit,'d'))
				$this->rmInstanceForce();
			//else
			//	print "pas les droits";
			break;
		case 'askDelete':
		    if ( ! isset($_SESSION['formx_askDelInstance_step'])) {
		    	$_POST['FormX_ext_goto_'] = "RM$opt";
		    	$_SESSION['formx_askDelInstance_step']=true;
		    	$this->TraiterPost();
		    } else {
		    	unset($_SESSION['formx_askDelInstance_step']);
		    	if( isset( $_POST['valider_popup']) || isset($_POST['valider_popup_x'] )) 
		    		return "Formulaire effacé." ;
		    	else
		    		return "Effacement annulé.";
		    }
			$this->genAffichage();
  			return $this->getAffichage();
			break ;
		case 'print':			
			$this->loadInstance($opt);
			if( ! $this->getDroit($this->getFormMainDroit(),'r'))
				return "Vous n'avez pas le droit d'accéder à cette page. (user: $user) (".$this->getFormMainDroit().",r)<br />Peut-être devez-vous vous ré-authentifier ?";
			//l'option contient l'id d'instance du formulaire   	
    		//$this->genAffichage();	
			$this->isuseraction=true;
			$this->modeImpression = true;
			$this->genPrint($this->affFoRmX());
			return $this->af ;
			break;
		default:
			return '';
	}  	
  }
  
  
  //TODO tester si cette fonction est encore utilisée
  //bug ? l'UTF-8 ne s'affiche pas correctement, on convertit à la main.
  function utf8tohtml(& $chaine) { 
  $conv = array('Ã©' => 'é', 'Ã¨' => 'è' , 'Ã'=> 'à' ,'Ã§'=>'ç','Ã¹'=>'ù');
  return strtr(& $chaine ,& $conv) ; 
  }
  
  //convertir un les symboles de mise en page
  function convMP ( $chaine ) {
  $conv = array('-*-' => '<br/>' , '-isNull-' => '' , '-**' => '<' , '**-' => '>' );
  $conv['/*and*/']='&';
  return strtr( $chaine , $conv) ; 
  }
  
  //suppr symboles mise en page
  function supprMP ( $chaine ) {
  	$res = array();
  while ( ereg("^(.*)(-\*\*.*\*\*-)(.*)$",$chaine,$res)) {
  	$chaine = $res[1].$res[3];
  	}
	$chaine = str_replace(chr(0xA7),'',$chaine); //on enleve '§'
	return $chaine ;
  }
  
  //ajoute du js à l'affichage
  function addJs($jscode) {
  	$this->js .= $jscode ;
  	//eko($this->js);
  }
  
  
  function genAffichage() {
  	$this->affFoRmX(true);
  }
  
  // Retourne l'affichage généré par la classe.
  function miseEnPage ($mode='',$aff) {
  global $tool;
  $this->debug("entrée dans getAffichage en mode ".$mode);
  if ( $this->rien ) return "RIEN" ;
    if(function_exists('genJavaHideIE') ) {
    	if($aff)
	$aff .= genJavaHideIE('pave_spe_00');
	}
	$style ="";
	
	//si css particulier
	if ( $this->XMLCore['css'] && ( $mode=='' || ($mode =='print' && ! $this->XMLCore['cssprint']))) {
		$this->debug("attribut css appliqué chargement de ".$this->XMLCore['css']);
		$filename=$this->XMLCore['css'];
		$handle=fopen(FX_URLLOCAL.$filename,'r');
		$style=fread($handle,filesize($filename));
		fclose($handle);
		$style="<style>$style</style>";
			}
	if ( $this->XMLCore['cssprint'] && $mode=='print') {
		$this->debug("attribut cssprint appliqué chargement de ".$this->XMLCore['cssprint']);
		$filename=$this->XMLCore['cssprint'];
		$handle=fopen(FX_URLLOCAL.$filename,'r');
		$style=fread($handle,filesize($filename));
		fclose($handle);
		$style="<style>$style</style>";
			}
    return  $style.utf8_decode($this->convMP ($aff)) ;
  }

function getAffichage($mode='') {
	return $this->miseEnPage($mode,$this->af);
}


function getAuthor() {
	return $this->author;
}

 /*OBSOLETE : Genere la case d'infos*/
   function genCase($infobulle) {
   	return clFoRmXtOoLs::genCase($infobulle) ;
   }	


 /*Fonctions devant être spécialisés dans la classe héritée
  * 
  * */ 
  function getOption($opt) {
  	
  	global $options;
  	$this->debug("Entrée et sortie du getOption interface natif");
	return $options->getOption($opt);
	}
	
  function getDroit($opt,$c="r") {
  	global $formxSession;
  	$this->debug("Entrée et sortie du getDroit interface natif");
  	return $this->session->getDroit($opt,$c);
  } 
  
  function addErreur($err) {
  	global $formxSession;
  	$this->session->addErreur($err);
  	$this->debug($err);
  }
  
}


?>
