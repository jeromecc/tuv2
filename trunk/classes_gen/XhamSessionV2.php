<?php
/*clSessionV2.php
 * Created on 5 juin 2006
 * Author : Emmanuel Cervetti ecervetti@ch-hyeres.fr
 * Version 0.1A
 */
 
 class XhamSessionV2 {
 //idsession , par defaut mainUser
 private $idsession;
 //cl�session : grand entier propre � chaque utilisateur et stock� � la fois en cookie et en session
 private $clesession;
 //instance xham cr�atrice
 private $xham;
 //instance user li�e � cette session
 private $user;
 //dur�e max d'une session xham avant expiration d'inactivit�
 private $maxSessionTime ;
 //Test si une connexion valide est en cours
 private $isValidConn;
 //infos debug
 public $debug;
 //code eventuel � afficher lors de la deconnection de fin de constructeur
	// 0 -> pas de mess
	// 1 -> le user vient de se deconnecter proprement
	// 2 -> la session du user est perim�e
	// 3 -> le user s'est connect� ailleurs et a d�connect� la session courante
 private $decoMessCode;
 
  // une session se construit � partir de l'instance de la classe xham et d'un identifiant unique $idsession de "session utilisateur"
  // par default c'est 'mainUser'
  //
  // toutes les infos relatives � une session utilisateur 
  // se stockent dans la variable $_SESSION[$idsession]

 function __construct($xhaminst,$idSess='mainUser12') {
 	//� passer en option
 	$this->xham=$xhaminst;
 	$this->maxSessionTime = $this->xham->getOption ( 'DureeSession' ) * 60 ;
 	$this->idsession = $idSess;
 }
 
function loadSession() {
 	$this->debug.="<br/>ouverture de la session ".$this->idsession;
 	//demande de connexion ?
 	 if($this->isAskConnection() ) {
 	 	$this->debug.="<br/>demande de connexion recue";
    	$this->connection();
    }
    //a t-on une connexion valide en cours ?
 	$this->debug.="<br/>Le user a �t� cr��";
    if((! $this->isActiveConnection()) || $this->isAskDisconnection() ) {
    	if((! $this->isActiveConnection()))
    		$this->debug.="<br/>pas de connexion active d�tect�e";
    	if($this->isAskDisconnection()) {
    		$this->debug.="<br/>demande de d�connexion recue";
    		$this->decoMessCode = 1;
    	}
    	$this->disconnect();
    }
	//doit-on recharger la connexion ?
    if($this->isActiveConnection())
    	$this->reloadConnection();	
}
 
 
 
function connection( ) {
  	$this->user = $this->newUser ( );	
  	// print("<br/><br/><br/><br/>entree dans connexion");
  	if( $this->user->connect() ) {
  		$this->debug.="<br/>ok pour connexion";
  		$this->isValidConn=true;
  		$this->clesession=rand(1,999999999);
  		$this->setInfo('user',$this->user);
  		$this->setInfo('clesession',$this->clesession);
  		setcookie('clesession_'.$this->idsession, $this->clesession, (time() + $this->maxSessionTime),'/');
  		return true;
  	} else {
  		$this->decoMessCode = 4;
  		$this->isValidConn=false;
  	}
 }
 
  // Renvoi un objet utilisateur en fonction du contexte...
function newUser ( ) {
	// S'il y a une cl� de session valide alors qu'on cr�e un nouvel
	// utilisateur, alors on historise ses statistiques.
	$this->debug.="<br/>instanciation d'un nouvel user...";
	if ( $this->isInfo ( "clesession" ) )
  		$this->xham->stats->delete ( $this->getInfo ( "clesession" ) ) ;
  	// Cr�ation d'un utilisateur de type "ADMIN"
	$user = new XhamUserAdmin ( $this->xham ) ;
	// S'il n'est pas reconnu, alors on cr�e un utilisateur
	// dans le bon type en fonction des pr�f�rences de l'appli.
	if ( ! $user -> connect ( ) ) {		
		if($this->xham->getOption('ModuleHopi') && $this->xham->getr('idsession') && $this->xham->getr('idtransaction') ){
			$XAPclient = new XhopiAuthClient();
 			$valid = $XAPclient->validAuthAsk($this->xham->getr('idsession'),$this->xham->getr('idtransaction'));
 			if($valid) {
 				//ok pour valider l'idsession 	
 				$this->xham->setr('login',$valid['login']);
 				$this->xham->setMode('manuel');
 			}
 		}
		switch ( $this->xham->getDefine ( "TYPEAUTH" ) ) {
			case 'MySQLInterne':
				$this->debug.="type mysql d�tect�";
				$user = new XhamUserSQL ( $this->xham ) ;
			break;
			case 'LDAP':
				$this->debug.="type LDAP d�tect�";
				$user = new XhamUserLdap ( $this->xham ) ;
			break;
			case 'specific':
				$this->debug.="type specifique d�tect�";
				 $user = new user ( $this->xham ) ;
				break;
			default:
				$this->debug.="type par defaut d�tect� (sql)";
				$user = new XhamUserSQL ( $this->xham ) ;
			break;
		}
	}
	return $user ;
} 
 
  
  // d�connexion de l'utilisateur courant
  	function disconnect() {
  	global $xham_idClic;
  	//suppresion de l'user
  	$this->debug.=" <br/>deconnection.....";
  	if(is_object($this->user)) {
  		$this->xham->stats->delete ( $this->getInfo ( "clesession" ) ) ;
  		$this->user->disconnect();
  	} else {
  		$this->debug.="<br/>pas de user � deco";
  		return ;
  	}
  	//suppression du cookie
  	setcookie('clesession_'.$this->idsession,"",time()-86400);
  	//suppression des variables de session propres � la session user
  	if(session_id() && isset($_SESSION[$this->idsession])) {
  		$this->debug.="";
  		//print "<br/><br/>suppression de _SESSION ".$this->idsession;		
  		unset($_SESSION[$this->idsession]);
  		//$xham_idClic;
  		}
  	//generation du popup si code
  	switch($this->decoMessCode) {
  	case 0:
  		break;
  	case 1:
  		$this->xham->pi->addPostIt("D�connexion","Vous vous �tes d�connect�.","reussite");
  		break;
  	case 2:
  		$this->xham->pi->addPostIt("D�connexion","Votre session a expir�. Vous devez vous reconnecter.","alerte");
  		break;
  	case 3:
  		$this->xham->pi->addPostIt("D�connexion","Vous avez �t� d�connect� car vous vous �tes connect� ailleurs.","erreur");
  		break;
  	case 4:
  		$this->xham->pi->addPostIt("Erreur","Erreur dans la saisie du login ou du mot de passe","erreur");
  		break;
  	}
  	
  	$this->isValidConn=false;
  	}
 
 
// teste si une connection valide est en cours
// protection de la s�curit� avec la comparaison entre
// une variable session et un cookie
// si c'est le cas, teste �galement si la dur�e
// d'inactivit� n'est pas m�rim�e
  	
function isActiveConnection() {
	global $xham;
	$this->debug.="<br/>entr�e dans le test de connection active";
	
	//evite de refaire le m�me test
	if(isset($this->isValidConn)) {
		$this->debug.="<br/>le test de connexion active a d�ja �t� pr�calcul�";
		return $this->isValidConn;
	}

	
	//si un user en cours, on le charge. m�me si il a une session perim�e
	//en effet il faut pouvoir le deconnecter proprement
	if($this->isInfo('user')) {
		$this->debug.="<br/>un user est present dans les donn�es sessions";
		$this->user = $this->getInfo('user');
		//si une deco suit, et que le user n'est pas invit�, alors preparation du message
		if($this->user->getType() != 'Echec' )
			$this->decoMessCode = 2;
		//if(! $xham->stats->isSameIpThanLast($this->user->getLogin()))
		//	$this->debug.="<br/>po la m�me ip";
		//else
		//	$this->debug.="<br/>meme ip que toute";
		//si un user est present, on verifie que c'est la m�me ip qu'au precedent
		//clic (si l'option et si on est pas en agir en tant que)
		if ( $xham->mode != 'manuel' &&  $xham->getOption("UniqLogin")  &&  ! $xham->stats->isSameIpThanLast($this->user->getLogin()) ) {
			$this->debug.="<br/>une connection sur une autre ip est d�tect�e. deconnection";
			$this->decoMessCode = 3;
			$this->isValidConn = false;
			return false;
		}
		
	} else {
		$this->debug.="<br/>pas de user present dans les donn�es sessions";
	}
	//calcul proprement dit
	if ( $this->isInfo('clesession')&& isset($_COOKIE['clesession_'.$this->idsession])  ) {
		if($this->isInfo('clesession') == $_COOKIE['clesession_'.$this->idsession]  ) {
			$this->isValidConn=true;
			$this->clesession = $this->getInfo('clesession');
			return true;
		}
		
	}
	$this->isValidConn=false;
	return false;
 }


// lorsque une connexion valide est reconnue,
// recharge les donn�es contenues dans $_SESSION pour les instancier
function reloadConnection() {
	 	if($this->idsession != 'A14') {
	//print "<br/><br/><br/><br/>".$this->debug; 		
	 	}
$this->debug.="<br/>mises � jour propres � chaque clic";	
//mise � jour avec report de la date d'expiration
setcookie('clesession_'.$this->idsession, $this->clesession, (time() + $this->maxSessionTime),'/');
//Rechargement	

	//print $this->debug ;

$this->user->reconnect();
}


// teste si une demande de connexion a �t� faite

function isAskConnection() {
	if($this->xham->getr('login') ||$this->xham->getr('atq_iduser') || $this->xham->getr('password') ) {
		return true;
	}
	if( $this->xham->getOption('ModuleHopi') && $this->xham->getr('idsession') && $this->xham->getr('idtransaction') )
		return true;
	return false;
}

// teste si une demande de d�connexion a �t� faite

function isAskDisconnection() {
	if($this->xham->getr('D�connexion')) {
		return true;
	}
	return false;
}

  // Retourne l'utilisateur actuel.
  function getUser ( ) {
  	if ( $this->isActiveConnection ( ) )
  		return $this->getInfo ( 'user' ) ;
  	else {
  		$this->user = new XhamUserGuest ( $this->xham ) ;
  		$this->user->connect ( ) ;
  		return $this->user ;
  	}
  }

// met une information comme variable de session pour l'user en cours
// cette variable sera effac�e � la d�connexion
 
 function setInfo($val,$var) {
 $_SESSION[$this->idsession][$val]=$var;
 }
 

 function isInfo($val) {
 if ( $this->idsession )
 return isset($_SESSION[$this->idsession][$val]);
 return false;
 }

function getInfo($val) {
	global $xham;
	global $xham;
	if ( isset ( $_SESSION[$this->idsession][$val] ) )
		return $_SESSION[$this->idsession][$val];
}

function delInfo($val) {
unset($_SESSION[$this->idsession][$val]);	
}
 
//----------------------------------------------
//             Fonctions statiques
//----------------------------------------------
static function listSessions(){
$l = array();
foreach($_COOKIE as $key => $value) {
	$reg = array();
	if( ereg("^clesession_(.*)$",$key,$reg))
	if(isset($_SESSION[$reg[1]]['user']) && is_object($_SESSION[$reg[1]]['user']))
		$l[]=$reg[1];
  }		
return $l;
}



   
}
 
?>