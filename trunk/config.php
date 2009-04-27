<?php

// Titre  : Fichier de configuration du terminal
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 05 Janvier 2005

// Description : 
// La connexion à la base de donnée, le chargement des classes,
// le chargement des fonctions sont définis ici. L'ouverture de 
// session se fait aussi à ce niveau.

// Ouverture de la session.
@session_start ( ) ;

ini_set('default_charset','iso-8859-1');


//ob_flush ( ) ; flush ( ) ;

// fonction permettant de récupérer le temps écoulé depuis l'époque UNIX ( 1 - 1 1970 )
function temps ( ) {
  $time = microtime();
  $tableau = explode(" ",$time);
  return ($tableau[1] + $tableau[0]);
}
$debTemps = temps ( ) ;

if ( ! isset ( $stopAffichage ) ) $stopAffichage = '' ;

// Importation.
include ( "define.php" ) ;
//include_once ( URLLOCAL."classes_ext/adodb-time.inc.php" ) ;


if(PROXY)
{
	require_once(URLLOCAL.'classes_ext/proxy.class.php');
	list($host,$port) = explode(':',PROXY);
	HttpProxyStream::$proxy_host = $host;
	HttpProxyStream::$proxy_port = $port;
	define('PROTO','proxy://');
} else {
	define('PROTO','http://');
}


$stopAffichage = '' ;

//affichage d'un tableau en récursif
function affTab( $tab ) {
  reset ( $tab ) ;
  //$contenu = "<table align=\"center\" border =1><tr bgcolor=\"Silver\"><th>Index</th><th>Valeur</th></tr>" ;
  $contenu = "<table>" ;
  while ( list ( $key, $val ) = each ( $tab) ) {
  	if(is_array($val))
  		$contenu .= "<tr><td><i>tab</i>[".$key."]</td><td>".affTab($val)."</td></tr>";
  	else
    	$contenu .= "<tr><td>[".$key."]</td><td>".$val."</td></tr>" ;
  }
  $contenu .= "</table>";
  return $contenu;
}	


// Echo mais en mieux
function eko ( $message ) {
  global $errs;
  if ( is_array ( $message ) ) {
    $message = "Affichage d'un tableau demandé<BR/>".affTab ( $message ) ;
  }
  if ( isset ( $errs ) )
    $errs->logThis ( $message ) ;
}

//	this is the sock mail function
function sock_mail ( $auth, $to, $subj, $body, $head, $from, $cc='' ) {
	global $options ;
	$lb        = "\r\n" ;
    $body_lb   = "\r\n" ;
    $loc_host  = "localhost" ;
    $smtp_acc  = $options->getOption('SMTP_User') ;
    $smtp_pass = $options->getOption('SMTP_Pass') ;
    $smtp_host = $options->getOption('SMTP_Host') ;
    $hdr = explode ( $lb, $head ) ;    	//header
    if ( $body ) {
    	$bdy = preg_replace ( "/^\./", "..", explode ( $body_lb, $body ) ) ; 
    }

    // build the array for the SMTP dialog. Line content is	array(command, success code, additonal error message)
    if ( $auth == 1 ) {
	    // SMTP authentication methode AUTH LOGIN, use extended HELO "EHLO"
        $smtp = array (
        	// call the server and tell the name of your local host
            array ( "EHLO ".$loc_host.$lb, "220,250", "HELO error: " ),
            // request to auth
            array ( "AUTH LOGIN".$lb, "334", "AUTH error:" ),
            // username
            array ( base64_encode($smtp_acc).$lb, "334", "AUTHENTIFICATION error : " ),
            // password
            array ( base64_encode($smtp_pass).$lb, "235", "AUTHENTIFICATION error : " ) ) ;
	} else {
		// no authentication, use standard HELO
        $smtp = array (
        	// call the server and tell the name of your local host
            array ( "HELO ".$loc_host.$lb, "220,250", "HELO error: " ) ) ;
	}
	
	// print $head ;
    // envelop
    $smtp[] = array ( "MAIL FROM: <".$from.">".$lb, "250", "MAIL FROM error:" ) ;
    $tos = explode( ',', $to ) ;
    // print affTab ( $tos ) ;
    $toss = '' ;
    for ( $i = 0 ; isset ( $tos[$i] ) ; $i++ ) {
    	$smtp[] = array ( "RCPT TO: <".$tos[$i].">".$lb, "250", "RCPT TO error: " ) ;
    } 
    //$smtp[] = array ( "RCPT CC: <".$cc.">".$lb, "250", "RCPT TO error: " ) ;
    // begin data
    $smtp[] = array ( "DATA".$lb, "354", "DATA error: " ) ;
    // header
    $smtp[] = array ( "Subject: ".$subj.$lb, "", "" ) ;
    if ( $to )
    	$smtp[] = array ( "To:".$to.$lb, "", "" ) ;
    //$smtp[] = array ( "Cc:dborel@ch-hyeres.fr".$lb, "", "" ) ;	
    // AF 15-12-06 - Missing protocol
    //$smtp[] = array("From:".$from.$lb,"","");
    foreach ( $hdr as $h ) { $smtp[] = array ( $h.$lb, "", "" ) ; }
	
    // end header, begin the body
    $smtp[] = array ( $lb, "", "" ) ;
    if ( $bdy ) { foreach ( $bdy as $b ) { $smtp[] = array ( $b.$body_lb, "", "" ) ; } }
    // end of message
    $smtp[] = array ( ".".$lb, "250", "DATA(end)error: " ) ;
    $smtp[] = array ( "QUIT".$lb, "221", "QUIT error: " ) ;
	
    // open socket
    $fp = @fsockopen ( $smtp_host, 25 ) ;
    if ( ! $fp ) eko  ( "<b>Error:</b> Cannot connect to ".$smtp_host."<br>" ) ;
    $banner = @fgets ( $fp, 1024 ) ;
    // perform the SMTP dialog with all lines of the list
    foreach ( $smtp as $req ) {
		$r = $req[0] ;
        // send request
        @fputs ( $fp, $req[0] ) ;
        // get available server messages and stop on errors
        if ( $req[1] ) {
        	while ( $result = @fgets ( $fp, 1024 ) ) { if ( substr ( $result, 3, 1 ) == " " ) { break ; } } ;
            if ( ! strstr ( $req[1], substr ( $result, 0, 3 ) ) )
				eko ( "$req[2].$result<br>" ) ;
        }
	}
    $result = @fgets ( $fp, 1024 ) ;
    // close socket
    @fclose ( $fp ) ;
    return 1 ;
} 

// Chargement automatique des classes.
function __autoload ( $classe ) {
  global $errs ;
  global $session ;
  global $relocate;
  if     ( file_exists ( $relocate.'classes_int/'.$classe.'.php' ) ) require_once ( $relocate.'classes_int/'.$classe.'.php' ) ;
  elseif ( file_exists ( $relocate.'classes_gen/'.$classe.'.php' ) ) require_once ( $relocate.'classes_gen/'.$classe.'.php') ;
  elseif ( file_exists ( $relocate.'classes_ext/'.$classe.'.php' ) ) require_once ( $relocate.'classes_ext/'.$classe.'.php') ;
  elseif ( file_exists ( $relocate.MODULE_CCAM.'classes_int/'.$classe.'.php' ) ) require_once ( $relocate.MODULE_CCAM.'classes_int/'.$classe.'.php') ;
}

?>
