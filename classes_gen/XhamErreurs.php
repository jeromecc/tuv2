<?php
/*
 * Created on 7 juin 2006
 * By Damien Borel <dborel@ch-hyeres.fr>
 * 
 */
 
 // Description : 
// Cette classe génère un rapport des erreurs rencontrées durant la génération 
// d'une page. Ce rapport est ensuite envoyé en fonction des options définies 
// en amont dans le terminal.


restore_error_handler ( ) ;


class XhamErreurs {

  // Déclaration des attributs de la classe.
  private $liste ;
  private $indice ;
  private $maxErreurs ;
  //booleen d'appartenance à un bloc sans erreurs
  public $inCatch;
  //force l'affichage de la console eko
  public $forceEko;
  // Constructeur : initialisation du nombre d'erreurs à zéro.
  function __construct ( ) {
    $this->indice = 0 ;
    $this ->forceEko = false;
    $this->inCatch = false;
    $this->rand = rand(0,100000);
    if(defined('DEBUG_NIVEAURECU'))
    	$this->niveaurecu = DEBUG_NIVEAURECU ;
    else
    	$this->niveaurecu = 4 ;
  }

  // On ajoute une erreur à la liste. Si l'erreur est bloquante, alors le 
  // deuxième argument doit être renseigné pour envoyer le mail de rapport
  // directement après l'enregistrement de l'erreur.
  function addErreur ( $text, $exit='' ) {
    global $xham ;
    //si on est dans le bloc catch
    if($this->inCatch) return;
    $text = "<div style='color:orange;'>".$this->whereAmI('l',$this->niveaurecu,1)."</div>"."<div style='color:#9B12F1;'>".$text.'</div>';
		if (defined("PRINT_ERRORS"))if (PRINT_ERRORS) { print $text; ob_flush();flush(); }
    if ( $xham ) {
      $this->maxErreurs ++ ;
      //if($this->maxErreurs > 10) { die() ; }
      $this -> liste[$this->indice] = $text ;
      $this -> indice++ ;
      if ( $xham -> getDroit ( "Configuration", "a" ) )
	$this->logThis( $text ) ;
      if ( $exit ) {
	$this -> sendMail ( '1' ) ;
	//	if ( $xham -> getDroit ( "Configuration", "a" ) ) {
	  $this->indice = "" ;
	  $this->liste = "" ;
	  if ( $xham -> getDroit ( "Configuration", "a" ) ) $droits = '<droit>1</droit>' ; else $droits = '' ;
	  die ( "<root>$droits<text><![CDATA[<center>Une erreur est survenue, le problème a été transmis à l'équipe opérationnelle.</center>]]></text>" ) ;
	  //}
      }
    }
  }


  // Efface la dernière erreur...
  function delErreur ( ) {
    if ( $this->indice > 0 )
      $this->indice-- ;
  }
  
 //crée un bloc invincible qui ne connaît pas l'erreur 
function startCatch(){
  	$this->inCatch = true;
  	}
function stopCatch(){
  	$this->inCatch = false;
  	}
 
  // Fonction appelée en fin de page. Après toutes les étapes de génération.
  function end ( ) {
    if ( $this->indice ) $this->sendMail ( ) ;
  }
		 
  
  // Fonction appelée en fin de page. Après toutes les étapes de génération.
  function __destruct ( ) {
  	//debug("appel destructeur ".$this->rand);
  	global $secDead ;
  	if (isset($this->iamDead) OR isset($secDead))
  		return ;
  	
  	//debug("validation destructeur");
  	$secDead = true ;
  	$this->iamDead = true ;
    if ( $this->indice ) $this->sendMail ( ) ;
    //print 
    if ( defined ( 'SENDEKOAJAX' ) AND SENDEKOAJAX AND isset($_POST['ajax']) ) $this->logPrintAJAX();
    //$this->logPrintV2();
  }
  
  function eldestructor() {
  	$this->__destruct();
  }

  // Gestion et envoi du mail de rapport des erreurs.
  function sendMail ( $type='' ) {
    // On envoie seulement si l'option d'envoi est activée
    if ( Erreurs_Actif ) {
      global $xham ;
      $date = new clDate ( ) ;
      // En fonction de la dernière erreur rencontrée, on fabrique
      // le sujet du message.
      if ( $type ) $subject = Erreurs_Bloquante ;
      else $subject = Erreurs_Normale ;

      // Préparation des informations du mail.
      $entete  = "<html><head><title>$subject</title><body>" ;
      $fin     = "</ul></body></html>" ;
      // print affTab ( get_object_vars ( $xham ) ) ;
      // Entête explicatif.
      if(method_exists($xham->user,'getIdentite'))
      	$message = "L'utilisateur <i>".($xham?$xham->user->getIdentite ( ):"Inconnu")."</i>, lors de l'exécution de la page " ;
      else
      	$message = "L'utilisateur <i>erreur cougnac</i>, lors de l'exécution de la page " ;
      $message .= ($xham?$xham->getNaviFull():"Chemin introuvable")." le ".$date->getDateTextFull() ;
      if ( $this->indice > 1 ) $message .= ", a provoqué les erreurs suivantes : <br/><ul>" ;
      else $message .= ", a provoqué l'erreur suivante : <br/><ul>" ;
      // Liste des erreurs.
      for ( $i = 0 ; isset ( $this->liste[$i] ) ; $i++ )
	$message .= "<li>".$this->liste[$i]."</li>" ;
      $message .= "</ul><br /><a href=\"http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?navi=".($_POST['navi']?$_POST['navi']:$_GET['navi'])."\">Lien vers la page d'erreur</a>" ;
      $headers  = "MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\n";
      $headers .= "To: ".Erreurs_Mail."\nFrom: ".Erreurs_NomApp."<".Erreurs_MailApp.">\n";
      //$headers .= "Bcc: dborel@ch-hyeres.fr\r\n" ;
      // Envoi du mail.
//      print $message;
	 if(is_object($xham->user) AND method_exists($xham->user,"getLogin"))
	 	$userErreur = $xham->user->getLogin() ;
	 else
	 	$userErreur = "[impossible d'acceder à la methode getLogin]";
      mail ( '', $subject." (".$userErreur.")", $entete.$message.$fin, $headers ) ;
      //print "$entete.$message.$fin" ;
    }
  }

 //va chercher quel fichier et à quelle ligne on était deux piles plus haut. Simplifie le nom du fic
 function upCodeInfo()
 {
	$infoDebug = debug_backtrace();
	$infofic = $infoDebug[2]["file"] ;
	/*Virer les lignes suivantes pour affichage total du nom du fic*/
	$Tfic = explode ( "/",$infofic );
	$infofic = array_pop($Tfic);
	/*-----------*/
	return " ".$infofic." L".$infoDebug[2]["line"]." ";
 }
    
function whereAmI($mode='',$nivorecu=666,$debut=0) {
	$i=$debut;
	$j=0;
	$infoDebug = debug_backtrace();
	$info = '';
	while(isset($infoDebug[$i])) {
		$i++;
		if(isset($infoDebug[$i]["file"]) && $infoDebug[$i]["file"]) {
			$info .= $infoDebug[$i]["file"]." L".$infoDebug[$i]["line"].'<br/>';
			$j++;
		}
		if($j>=$nivorecu)
			break;
		}
	if($mode=='')
		$this->addErreur("Debug whereAmI:". $info);
	else
		return  $info;
	}


  //Logue ce qui lui est demandé dans 
  //ajoute des infos:date, ligne, fichier
  
    function logThis($content,$force=false)
	{
			//generation du message
	//inverse l'ordre des lignes !
	$rep = date("G:i:s");
	$rep.=$this->upCodeInfo();
	$rep .= "> ";
	if($force)
		$this ->forceEko = true;
	if(defined("EKORECURS") && EKORECURS) 
		$rep="<div style='color:orange;'>".$this->whereAmI('2',EKORECURS,1)."</div>";

	if( ! isset($this->messages))
		$this->messages = "";
	$this->messages = $rep.$content."<BR/>".$this->messages ;
	}
	

	
	//appelle l'affichage de la console de log
	function logPrint()
	{
	//necessaire pour obtention de la navig
	global $xham ;
	if ( ! isset( $this->messages) || ! $this->messages ) {return "" ;}
	
	$droit = $xham -> getDroit ( "Configuration", "a" ) ;
	if (defined("PRINT_ERRORS"))if (PRINT_ERRORS) $droit = true;
	if($this ->forceEko) $droit=true;
	if ( ! $droit ) {return "" ;}
	
	if (defined("DISABLE_EKO") && DISABLE_EKO ) return '';
	
	//instanciation de la template grace à la classe modeliXe
	$mod = new ModeliXe ( "ConsoleLogs.mxt" ) ;
	$mod -> SetModeliXe ( ) ;

	// Préparation du titre, des images, des urls...
	$mod -> MxText ( "contenu", $this->messages ) ;
	//$mod -> MxHidden ( "cache", "navi=".$xham->getNaviFull() ) ;
	//$mod -> MxHidden ( "vider","vider=oui");
	return $mod -> MxWrite ( "1" ) ;
	}
	
	//appelle l'affichage de la console de log
	function logPrintV2($droit=0)
	{
	//necessaire pour obtention de la navig
	global $xham ;
	if ( ! isset( $this->messages) || ! $this->messages ) { 
		return "" ;
	}
	
	if ( ! $droit ) $droit = $xham -> getDroit ( "Configuration", "a" ) ;
	if (defined("PRINT_ERRORS"))if (PRINT_ERRORS) $droit = true;
	if($this ->forceEko) $droit=true;
	if ( ! $droit ) {return "" ;}
	
	// Préparation du titre, des images, des urls...
	
	return $this->messages ;
	}
	
	//appelle l'affichage de la console de log
	function logPrintAJAX()
	{
		//necessaire pour obtention de la navig
		global $xham ;
		if ( ! isset( $this->messages) || ! $this->messages ) { 
			if ( $_POST['ajax'] OR $_GET['ajax'] ) print "<erreursxham><![CDATA[]]></erreursxham></root>" ;
		} elseif ($_POST['ajax'] OR $_GET['ajax']) print "<erreursxham><![CDATA[".$this->messages."]]></erreursxham></root>" ;
	}
	
	function logTxt($mess,$fic='log.txt') {
	if($fp = fopen($fic,"a")){
		fputs($fp, "\n");
		$rep = date("G:i:s");
		$rep.=$this->upCodeInfo();
		$rep .= ">";
		fputs($fp, $rep.$mess);
		fclose($fp);
		}
	}
}
	
	// Configure le niveau de rapport d'erreur pour ce script
error_reporting(E_ALL) ;

// Gestionnaire d'erreurs

function myErrorHandler($errno, $errstr, $errfile, $errline, $jambon )
{
  global $xham ;
  if(! is_object($xham) || ! is_object($xham->errs)) {
  	$errs = new XhamErreurs;
  } else {
  	$errs=$xham->errs;	
  }
  
  //si dans bloc sans erreurs
  if($errs->inCatch)
  	return ;
  switch ($errno) {
  case E_PARSE:
    $errs->addErreur ( "parse error" ) ;
    $errs->end ( ) ;
    break;
  case E_ERROR:
    $errs->addErreur ( "<b>PHP ERREUR</b> [$errno] $errstrbr />Erreur fatale à la ligne $errline dans le fichier $errfile" ) ;
    break;
  case E_WARNING:
    $errs->addErreur ( "<b>PHP ALERTE</b> [$errno] $errstr in file $errfile at line $errline<br />\n" ) ;
    break;
  case E_NOTICE:
    if ( ! defined ( "NONOTICE" ) OR ! NONOTICE )
    if(function_exists('eko'))
      eko ( "<b>NOTICE</b> [$errno] $errstr in file $errfile at line $errline<br />\n" ) ;
      else
      	print "<b>NOTICE</b> [$errno] $errstr in file $errfile at line $errline<br />\n";

      if ( defined('PRINT_ERRORS') && PRINT_ERRORS )
      {
      	print "<b>NOTICE</b> [$errno] $errstr in file $errfile at line $errline<br />\n" ; /* if( ob_get_contents() ) ob_flush() ;  flush() ; */
      }
      	
    break;
  case 2048:
  	//$errs->addErreur ("Non-static method should not be called statically (Avertissement non bloquant que vous n'auriez pas si DB testait les releases de php en dev avant de les mettre en prod.)") ;
  	$errs->addErreur ( "Type d'erreur inconnu : [$errno] $errstr in file $errfile at line $errline<br />\n" ) ;
  	break;  
  default:
    $errs->addErreur ( "Type d'erreur inconnu : [$errno] $errstr in file $errfile at line $errline<br />\n" ) ;
    break;
  }
}

// Configuration du gestionnaire d'erreurs

$old_error_handler = set_error_handler("myErrorHandler");
	





?>
