<?php

/*
 * XAP
 * X-hopi Authentification Protocol
 * 2006 ecervetti@ch-hyeres.fr
 * 
 * necessite l'extension openssl pour php 
 * 
 */


//cette classe contient le noyau commun à la classe server et client
class XhopiAuth {
//copier coller ici le contenu du certificat au format pem
// donné par  openssl rsa -in xhopi.privkey -pubout -out xhopi.pubkey
//apres avoir genere la cle privee, cf plus loin
protected  $publicKey = 			 "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDACc/FapB6Qz1VbVkDb5uqZHF7
lo7K1AK2FeWVqpymCxzEa8HdzTHuVfw7zCZjFcEV9+QHlq7I0b31lWMsvycQDSk2
KCRfF/xbXaZJ5phvk3Yb3mOaEShRWw86cqjWLoxiTx/kFbecBC0n1mufrnxJxWWB
HSv7+XHRW+UdhkHsVwIDAQAB
-----END PUBLIC KEY-----";	

//a la construction, verifie que php a l'extension openssl
function __construct() {
	if(! function_exists("openssl_pkey_get_public"))
		die("php doit etre installé avec le module openssl");
}

//procedure qui hache les données login + clé challenge
static function hashChallengeProc($login,$challenge) {
	return md5(md5("fabien".$login."la".$challenge."fouine"));
}

//transforme une donnée b64 en une donnée passable par url
static function urlSafeEncode($s) {
	return  str_replace(array('+','/','='),array('-','_','.'),$s);
}

//decode une donnée b64 en donnée passable par url
static function urlSafeDecode($s) {
	return  str_replace(array('-','_','.'),array('+','/','='),$s);
}

}





class XhopiAuthClient extends XhopiAuth {


private $urlServer ;
public $lengthChallengeKey=100;


function __construct() {
if(! defined("URL_XAP_SERVER"))
	die("La variable d'environnement URL_XAP_SERVER doit être définie ");
$this->urlServer=URL_XAP_SERVER;	
}
  
//----------------------------------------
//---------- fonctions open ssl ----------
//----------------------------------------  	


//crypte avec la clé publique
function crypter($mess) {
	$res = "";
	if( openssl_get_publickey(($this->publicKey) )) {
			openssl_public_encrypt($mess,$res,$this->publicKey);
	} else {
		die("Erreur interne: clé publique non valide.");	
	}
 return(base64_encode($res));
}

//verifie une signature
function verifSign($mess,$sign) {
	$sign=base64_decode($sign);
	if( $idk = openssl_get_publickey($this->publicKey )) {
		$ok = openssl_verify($mess,$sign,$idk);
	} else {
		die("Erreur interne: clé publique non valide.");	
	}
 return $ok;
}

//----------------------------------------
//---------- fonctions xhopi--- ----------
//----------------------------------------


function validAuthAsk($idsession,$idtransaction){
 //generation d'un id de challenge
	$i=0;
	$s="";
	for($i=0;$i<$this->lengthChallengeKey;$i++) {
		$s.=chr(rand(0,255));	
	}
	$idc=base64_encode($s);
	$idcUrl = XhopiAuth::urlSafeEncode($idc);
	
 //envoi au serveur des arguments du challenge
 
 if(strpos ($this->urlServer,"?"))
	 $sep="&";
 else 
 	$sep="?";
 $url = $this->urlServer.$sep."xapcheck=$idsession&challenge=$idcUrl&transaction=$idtransaction";
 $getret = file_get_contents($url);
 if(! $getret)
 	die ("erreur d'acces au serveur xap à l'url: ".URL_XAP_SERVER);
 $res = array();
 ereg('^proof:([^;]*);(.*)$',$getret,$res);
 $proof = $res[1];
 $infos = $res[2];
 $proof=$proof;
 $res = $this->verifSign($this->hashChallengeProc($idsession,$idc),$proof);
 if(! $res) {
 	//print $getret;
 	return false;	
 }
 //si la signature est valide, on recompose les infos recues en tableau d'informations
 rtrim($infos,";");
 $tab = explode(";",$infos);
 $tab2 = array();
 foreach($tab as $val) {
	if(!$val) continue;
 	$ttmp = explode(':',$val);
 	$tab2[$ttmp[0]]=$ttmp[1];
 }
 //generation du tableau d'informations
 //print "<br/>infos reçues:".$infos."<br/>";	
 return $tab2;
}



 
}


/*
//production du mechanisme d'authenification (appeller avec iamclient=y dans l'url)
if ($_GET['iamclient']) {
	//partie client
	$client = new XhopiAuthClient();
	$valid = $client->validAuthAsk("osef");
	print "<br />".($valid?"ok":"pabon");
} else {
	$server = new XhopiAuthServer();	
	$server->sendProofToClient($_GET['xapcheck'],$_GET['challenge']);
}
*/


/*
//--------signature-----------------
$server = new XhopiAuthServer();
$message = "sdvsd";
$signature = $server->signer($message);
print "<br/>signature reçue: ".$signature;
$client = new XhopiAuthClient();
$res = $client->verifSign($message,$signature);
if($res == 1)
	print "<br/>signature valide :)";
elseif ($res == 0)
	print "<br/>signature invalide :(";
else 
	print "<br/>Erreur interne de verification :|";
*/	
	
/*
//----cryptage decryptage---------------------------------
$message = "aller voir les baleines";
$client = new XhopiAuthClient();
$cri = $client->crypter($message);
$server = new XhopiAuthServer();
$decri = $server->decrypter($cri);
print "<br />message de base:$message";
print "<br />message decrypté:$decri";
*/
?>