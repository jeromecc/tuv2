<?php

// Titre  : Classe Hyeres
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 07 Décembre 2006

// Description : 
// Affichage d'un lien vers le dossier médical & cyberlab.

class clHyeres {

  	// Constructeur.
  	function __construct ( ) {
  		$this->genAffichage ( ) ;
  	}

	// Génération de l'affichage.
	function genAffichage ( ) {
		global $stopAffichage ;
		global $session ;
		$stopAffichage = 1 ;
		
		// GENERATION DU LIEN VERS DOME.
		
		$af .= '<a target="_blank" href="http://bigfoot.ch-hyeres.fr/xham/dome/'.URLNAVI.$session->genNavi ( 'Informations', 'Fiche Patient', 'setPatient', $_REQUEST['idu'] ).'" ><img src="images/dossiermedical.gif" style="border: 0px;" alt="DM" /></a>' ;
		
		$af .= '<div style="height: 13px;"></div>' ;
		
		// GENERATION DU LIEN VERS CYBERLAB.
		
		// Recherche des infos de session
   		$uid=$session->getUid();
  		//$uid="araoult";
  		//Password requis par Cyberlab
  		$password="cyberlab";
  		// Gestion du navigateur utilisé et de la résolution
  		$resolution=800;
  		(strstr ($_SERVER["HTTP_USER_AGENT"], "MSIE"))?$browser="":$browser="&browser=Netscape";
  		// Gestion de l'IDU du patient en cours
  		$IDU=$_REQUEST['idu'] ;
  		// Url de retour
  		//$url_retour="http://".$_SERVER['SERVER_NAME']."/blank.php";
    	$url_retour="http://".$_SERVER['SERVER_NAME']."/xham/terminal_urgences/";
    	// Url de cyberlab
    	$urlcyberlab="http://bionet.ch-hyeres.fr/cyberlab/servlet/be.mips.cyberlab.web.APIEntry?LoginName=$uid&Password=$password&Class=Patient&Object=$IDU&Method=ViewOrders&OnClose=$url_retour&screenResolution=$resolution".$browser;
		//$urlcyberlab="http://bionet.ch-hyeres.fr/cyberlab/servlet/be.mips.cyberlab.web.APIEntry?Class=Login&loginName=$uid&password=$password&OnClose=$url_retour&screenResolution=$resolution".$browser;
		$af .= '<a target="_blank" href="'.$urlcyberlab.'" ><img src="images/cyberlab.gif" style="border: 0px;" alt="CBL" /></a>' ;
		
		/*
		Class=Patient
            &Method=CreateOrder
            &LoginName=<loginName>
            &Password=<password>
            &Object=<object>
            &OnClose=<onClose>
            &Request=<request>
            &Browser=<browser>
            &ScreenResolution=<screenResolution>
            &Application=<application>
            &Issuer=<issuer>
            &Location=<location>
            &Visit=<visit>
		*/		
		$urlcyberlab="http://bionet.ch-hyeres.fr/cyberlab/servlet/be.mips.cyberlab.web.APIEntry?Class=Patient&Method=CreateOrder&LoginName=$uid&Password=$password&Object=$IDU&OnClose=$url_retour&Request=<request>$browser&ScreenResolution=$resolution&Application=TUV2" ;

            		//"Method=CreateOrder&LoginName=$uid&Password=$password&Class=Patient&Object=$IDU&Method=ViewOrders&OnClose=$url_retour&screenResolution=$resolution".$browser;
		$af .= '<div style="height: 13px;"></div>' ;
		$af .= '<a target="_blank" href="'.$urlcyberlab.'" ><img src="images/cyberlab2.gif" style="border: 0px;" alt="CBL" /></a>' ;
		
		
		$af .= '<div style="height: 13px;"></div>' ;
		$af .= '<a target="_blank" href="http://bigfoot.ch-hyeres.fr/xham/blopera/'.URLNAVI.$session->genNavi ( "Reservation", "", "etape3", "setPatient", $_REQUEST['ilp'] ).'" ><img src="images/blopera.gif" style="border: 0px;" alt="BLO" /></a>' ;
		
		
		print $af ;				
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
