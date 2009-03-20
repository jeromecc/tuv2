<?php


// Titre  : Classe Erreurs
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 14 Janvier 2005

// Description : 
// Cette classe génère un rapport des erreurs rencontrées durant la génération 
// d'une page. Ce rapport est ensuite envoyé en fonction des options définies 
// en amont dans le terminal.



restore_error_handler ( ) ;

// Configure le niveau de rapport d'erreur pour ce script
//error_reporting(E_ALL) ;

// Gestionnaire d'erreurs
function myErrorHandlerXhamV1($errno, $errstr, $errfile, $errline, $jambon )
{
  global $errs ;
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
  	$errs->whereAmI();
    $errs->addErreur ( "<b>PHP ALERTE</b> [$errno] $errstr in file $errfile at line $errline<br />\n" ) ;
    break;
  case E_NOTICE:
    if ( ! defined ( "NONOTICE" ) OR ! NONOTICE )
      eko ( "<b>NOTICE</b> [$errno] $errstr in file $errfile at line $errline<br />\n" ) ;
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

$old_error_handler = set_error_handler("myErrorHandlerXhamV1");


class clErreurs {

  // Déclaration des attributs de la classe.
  private $liste ;
  private $indice ;
  public $messages ;
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
    if(defined('DEBUG_NIVEAURECU'))
    	$this->niveaurecu = DEBUG_NIVEAURECU ;
    else
    	$this->niveaurecu = 4 ;
  }

  // On ajoute une erreur à la liste. Si l'erreur est bloquante, alors le 
  // deuxième argument doit être renseigné pour envoyer le mail de rapport
  // directement après l'enregistrement de l'erreur.
  function addErreur ( $text, $exit='' ) {
    global $session ;
    //si on est dans le bloc catch
    if($this->inCatch) return;
    $text = "<div style='color:orange;'>".$this->whereAmI('l',$this->niveaurecu,1)."</div>"."<div style='color:#9B12F1;'>".$text.'</div>';
		if (defined("PRINT_ERRORS"))if (PRINT_ERRORS) print $text;
    if ( $session ) {
      $this->maxErreurs ++ ;
      //if($this->maxErreurs > 10) { die() ; }
      $this -> liste[$this->indice] = $text ;
      $this -> indice++ ;
      if ( $session -> getDroit ( "Configuration", "a" ) )
	$this->logThis( $text ) ;
      if ( $exit ) {
	$this -> sendMail ( '1' ) ;
	//	if ( $session -> getDroit ( "Configuration", "a" ) ) {
	  $this->indice = "" ;
	  $this->liste = "" ;
	  die ( "<center>Une erreur est survenue, le problème a été transmis à l'équipe opérationnelle.</center>" ) ;
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
  function destruct ( ) {
    if ( $this->indice ) $this->sendMail ( ) ;
  }

  // Gestion et envoi du mail de rapport des erreurs.
  function sendMail ( $type='' ) {
    // On envoie seulement si l'option d'envoi est activée
    if ( Erreurs_Actif ) {
      global $session ;
      global $options ;
      $date = new clDate ( ) ;
      // En fonction de la dernière erreur rencontrée, on fabrique
      // le sujet du message.
      if ( $type ) $subject = Erreurs_Bloquante ;
      else $subject = Erreurs_Normale ;

      // Préparation des informations du mail.
      $entete  = "<html><head><title>$subject</title><body>" ;
      $fin     = "</ul></body></html>" ;
      // Entête explicatif.
      $user = ($session?$session->getUser ( ):"Inconnu");
      $uid = $session->getUid() ;
      if($uid=='fderock')  { $uid = 'fderock' ; $user = "François DEROCK" ;}
      if($uid=='dborel')  { $uid = 'dborel' ; $user = "Damien BOREL" ;}
      if($uid=='ecervetti')  { $uid = 'ecervetti' ; $user = "Emanuel CERVETTI" ;}
      $message = "L'utilisateur <i>".$user."</i>, lors de l'exécution de la page " ;
      $message .= ($session?$session->getNaviFull():"Chemin introuvable")." le ".$date->getDateTextFull() ;
      if ( $this->indice > 1 ) $message .= ", a provoqué les erreurs suivantes : <br/><ul>" ;
      else $message .= ", a provoqué l'erreur suivante : <br/><ul>" ;
      // Liste des erreurs.
      for ( $i = 0 ; isset ( $this->liste[$i] ) ; $i++ )
	    $message .= "<li>".$this->liste[$i]."</li>" ;
      $message .= "</ul><br /><a href=\"http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?navi=".($_POST['navi']?$_POST['navi']:$_GET['navi'])."\">Lien vers la page d'erreur</a>" ;
      $headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
      if ( $options->getOption('SMTP_BCC') ) 
      	$headers .= "cc: ".$options->getOption('SMTP_BCC')."\r\n" ;
      // Envoi du mail.
      if ( $options->getOption ( 'SMTP_Type' ) == 'autreAvecAuth' ) $auth = 1 ; else $auth = 0 ;
      if ( $options->getOption ( 'SMTP_Type' ) == 'localhost' OR ! $options->getOption ( 'SMTP_Type' ) ) {
      	$headers .= "To: ".Erreurs_Mail."\r\nFrom: ".Erreurs_NomApp."<".Erreurs_MailApp.">\r\n";
      	// print ( $headers ) ;
      	mail ( '', $subject." (".$session->getUid().")", $entete.$message.$fin, $headers ) ;
      } else {
      	$headers .= "From: ".Erreurs_NomApp."<".Erreurs_MailApp.">\r\n";
      	// print ( $headers ) ;
      	sock_mail ( $auth, Erreurs_Mail, $subject." (".$uid.")", $entete.$message.$fin, $headers, Erreurs_MailApp ) ;
      }
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
	
	$this->messages = $rep.$content."<BR/>".$this->messages ;
	}
	
	//appelle l'affichage de la console de log
	function logPrint()
	{
	//necessaire pour obtention de la navig
	global $session ;
	
	if ( ! $this->messages ) {return "" ;}
	
	$droit = $session -> getDroit ( "Configuration", "a" ) ;
	if (defined("PRINT_ERRORS"))if (PRINT_ERRORS) $droit = true;
	if($this ->forceEko) $droit=true;
	if ( ! $droit ) {return "" ;}
	
	//instanciation de la template grace à la classe modeliXe
	$mod = new ModeliXe ( "ConsoleLogs.mxt" ) ;
	$mod -> SetModeliXe ( ) ;

	// Préparation du titre, des images, des urls...
	$mod -> MxText ( "contenu", $this->messages ) ;
	$mod -> MxHidden ( "cache", "navi=".$session->getNaviFull() ) ;
	$mod -> MxHidden ( "vider","vider=oui");
	return $mod -> MxWrite ( "1" ) ;
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

?>
