<?php

// Titre  : Gestion des appels contextuels depuis une application XHAM
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 24 Mars 2008

class XhamBluePig {

	private $af ;

	function __construct ( $idu='', $ilp='', $sej='', $nuf='' ) {
		// Récupération des options en fonction de la version de XHAM.
		global $options ;
		global $droits ;
		global $session ;
		global $xham ;
		if ( ! is_object ( $xham ) ) {
			$this->options = $options ;
			$this->droits  = $session ;
			$this->user    = $session ;
			$this->navi    = $session ;
		} else { 
			$this->options = $xham ;
			$this->droits  = $xham ;
			$this->user    = $xham->user ;
			$this->navi    = $xham ;
		}
		
		$this->idu = $idu ;
		$this->ilp = $ilp ;
		$this->sej = $sej ;
		$this->uf  = $nuf ;
	}

	function genBarre ( ) {
		$mod = new ModeliXe ( "BarreAppelContextuel.html" ) ;
		$mod -> SetModeliXe ( ) ;
		$app[] = $this->genDome ( ) ;
		$app[] = $this->genCyberlab ( ) ;
		$app[] = $this->genCyberlabPrescription ( ) ;
		$app[] = $this->genBlopera ( ) ;
		$app[] = $this->genCoraRecueil ( ) ;
		$app[] = $this->genCoraExterne ( ) ;
		$app[] = $this->genAxigate ( ) ;
		$app[] = $this->genAxigateLabo ( ) ;
		$app[] = $this->genSriLabo ( ) ;
		$app[] = $this->genClinicom ( ) ;
		$app[] = $this->genMedis ( ) ;
		$app[] = $this->genMedisDocs ( ) ;
		$app[] = $this->genMedisLabo ( ) ;
		$app[] = $this->genBlueMedi ( ) ;
		$app[] = $this->genCarpentrasExcel ( ) ;
        $app[] = $this->genPacs ( ) ;
		$app[] = $this->genDxCare ( ) ;

		for ( $i = 0 ; $i < count($app) ; $i++ ) {
			if ( $app[$i] ) {
				$mod -> MxText ( "appels.lienAppel", $app[$i] ) ;
				$mod -> MxBloc ( "appels", "loop" ) ;
			}
		}

		$this->af .= $mod -> MxWrite ( "1" ) ;
	}
	

	function genBlueMedi ( $img='images/bluemedi.gif', $text='' ) {
    	$pref = "BlueMedi" ;
    	if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$url = $this->options->getOption ( $pref."_URL" ) ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de BlueMedi" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}


	function genClinicom ( $img='images/clinicom.gif', $text='' ) {
		$pref = "Clinicom" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerClinicom'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getUid ( ) ;
    			$pass = $this->options->getOption ( $pref."_Pass" ) ;
				$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				//eko ( $this->user->getPassword ( ) ) ;
				// Open ( $type, $idu, $idpass, $iduf, $idmedecin, $mode, $pass ) {
				$result = $dll -> Open ( "clinicom", $this->idu, $this->sej, $this->uf, $user, "MOZAIC", $this->user->getPassword ( ) ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerClinicom.html") ;
			} 
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Clinicom" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Clinicom" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerClinicom=1" '.$inf.'>'.$lien.'</a>' ;
	 }
	}

    function genPacs ( $img='images/pacs.gif', $text='' ) {
		$pref = "Pacs" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerPacs'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
                if ( ! $user ) $user = $this->user->getUid ( ) ;
    			//$pass = $this->options->getOption ( $pref."_Pass" ) ;
				//$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				//eko ( $this->user->getPassword ( ) ) ;
				// Open ( $type, $idu, $idpass, $iduf, $idmedecin, $mode, $pass ) {
				//$result = $dll -> Open ( "clinicom", $this->idu, $this->sej, $this->uf, $user, "MOZAIC", $this->user->getPassword ( ) ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerPacs.html") ;
                $act = '<script type="text/javascript">java.lang.Runtime.getRuntime().exec("xeyes");</script>' ;

			}
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Pacs" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement du Pacs" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerPacs=1" '.$inf.'>'.$lien.'</a>'.$act ;
	 }
	}
	
	function genMedis ( $img='images/medis.gif', $text='' ) {
		$pref = "Medis" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerMedis'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getUid ( ) ;
    			$pass = $this->options->getOption ( $pref."_Pass" ) ;
				$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				$result = $dll -> Open ( "medis", $this->idu, $this->sej, $this->uf, $user, $this->options->getOption ( 'Medis_URL'), ($pass?$pass:$this->user->getPassword ( )) ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerMedis.html") ;
			} 
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Medis" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Medis : dossier du patient" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerMedis=1" '.$inf.'>'.$lien.'</a>' ;
	 }
	}
	
	function genMedisDocs ( $img='images/medisdocs.gif', $text='' ) {
		$pref = "MedisDocs" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerMedisDocs'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getUid ( ) ;
    			$pass = $this->options->getOption ( $pref."_Pass" ) ;
				$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				$result = $dll -> Open ( "medisdocs", $this->idu, $this->sej, $this->uf, $user,  $this->options->getOption ( 'MedisDocs_URL'), ($pass?$pass:$this->user->getPassword ( )) ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerMedis.html") ;
			} 
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="MedisDocs" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Medis : documents du patient" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerMedisDocs=1" '.$inf.'>'.$lien.'</a>' ;
	 }
	}
	
	function genMedisLabo ( $img='images/medislabo.gif', $text='' ) {
		$pref = "MedisLabo" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerMedisLabo'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getUid ( ) ;
    			$pass = $this->options->getOption ( $pref."_Pass" ) ;
				$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				$result = $dll -> Open ( "medislabo", $this->idu, $this->sej, $this->uf, $user,  $this->options->getOption ( 'MedisLabo_URL'), ($pass?$pass:$this->user->getPassword ( )) ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerMedis.html") ;
			} 
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="MedisLabo" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Medis : résultats de laboratoire" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerMedisLabo=1" '.$inf.'>'.$lien.'</a>' ;
	 }
	}

    function genDxCare ( $img='images/cyberlab.gif', $text='' ) {
		$pref = "DxCare" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getMail () ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
		if ( ! $pass ) $pass = $this->user->getPassword ( ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
    		$url = $urls."?type=portail&menu=F&Mat=$user&password=$pass&NDA=".$this->idu."&NIP=".$this->sej ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de DxCare" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}

	function genCyberlab ( $img='images/cyberlab.gif', $text='' ) {
		$pref = "Cyberlab" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getUid ( ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
		if ( ! $pass ) $pass = $this->user->getPassword ( ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
    		$resolution=800;
  			(strstr ($_SERVER["HTTP_USER_AGENT"], "MSIE"))?$browser="":$browser="&browser=Netscape";
  			$url_retour="http://".$_SERVER['SERVER_NAME']."/xham/terminal_urgences/";
			$url = $urls."?LoginName=$user&Password=$pass&Class=Patient&Object=".$this->idu."&Method=ViewOrders&OnClose=$url_retour&screenResolution=$resolution".$browser;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Cyberlab : résultats" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}
	
	function genCyberlabPrescription ( $img='images/cyberlab2.gif', $text='' ) {
		$pref = "CyberlabPrescription" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		if ( ! $user ) $user = $this->user->getUid ( ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
			if ( ! $pass ) $pass = $this->user->getPassword ( ) ;
			$nomclient = $this->options->getOption ( $pref."_NomAppliCliente" ); 
			if ( ! $nomclient ) $nomclient = 'TUV2';
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
    		$resolution=800;
  			(strstr ($_SERVER["HTTP_USER_AGENT"], "MSIE"))?$browser="":$browser="&browser=Netscape";
  			$url_retour="http://".$_SERVER['SERVER_NAME']."/xham/terminal_urgences/";
			$url = $urls."?Class=Patient&Method=CreateOrder&LoginName=$user&Password=$pass&Object=".$this->idu."&OnClose=$url_retour&Request=<request>$browser&ScreenResolution=$resolution&Application=$nomclient" ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Cyberlab : prescription" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}
	
	function genDome ( $img='images/dossiermedical.gif', $text='' ) {
		$pref = "Dome" ;
		//if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
		if ( $this->options->getOption ( $pref."_Actif" ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
			$url = $urls.URLNAVI.$this->navi->genNavi ( 'Informations', 'Fiche Patient', 'setPatient', $this->idu ) ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Dome" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Dome (dossier médical v3)" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	} 
	
	function genBlopera ( $img='images/blopera.gif', $text='' ) {
		$pref = "Blopera" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
			$url = $urls.URLNAVI.$this->navi->genNavi ( "Reservation", "", "etape3", "setPatient", $this->ilp ) ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Blopera" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Blopera : réservation d'une salle pour le patient" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}
	
	function genCoraRecueil ( $img='images/cora.gif', $text='' ) {
		$pref = "CoraRecueil" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerCoraRecueil'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
    			if ( ! $user ) $user = $this->user->getUid ( ) ;
				$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				$result = $dll -> Open ( "cora", $this->idu, $this->sej, $this->uf, $user, "R", '' ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerCora.html") ;
			} 
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Cora recueil" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Cora Receuil" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerCoraRecueil=1" '.$inf.'>'.$lien.'</a>' ;
		}
	}
	
	function genCoraExterne ( $img='images/coraexterne.gif', $text='' ) {
		$pref = "CoraExterne" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
			if ( $_GET['appelerCoraExterne'] ) {
				$user = $this->options->getOption ( $pref."_User" ) ;
    			if ( ! $user ) $user = $this->user->getUid ( ) ;
				$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
				$result = $dll -> Open ( "cora", $this->idu, $this->sej, $this->uf, $user, "E", '' ) ;
				global $fenetreBloquante ;
				$fenetreBloquante = XhamTools::genFenetreBloquante("fenetreFermerCora.html") ;
			} 
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Cora externe" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Cora Externe" ) ;
			return '<a href="'.URLNAVI.$this->navi->genNaviFull().'&appelerCoraExterne=1" '.$inf.'>'.$lien.'</a>' ;
		}		
	}
	
	function genAxigate ( $img='images/axigate.gif', $text='' ) {
		$pref = "Axigate" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
			$url = $urls."?LOGIN_REQUEST=$user&LOGIN_REQUEST_PASSWORD=$pass&UID=PATIENT\$".$this->idu."&TEMPLATE=patientFolder.axlml" ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Axigate" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}
	
	function genAxigateLabo ( $img='images/axigatelabo.gif', $text='' ) {
		$pref = "AxigateLabo" ;
		if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
			$url = $urls."?login=$user&pwd=$pass&numperm=".$this->idu ;
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Labo" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}
	
	function genSriLabo ( $img='images/srilabo.gif', $text='' ) {
    	$pref = "SriLabo" ;
    	if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$user = $this->options->getOption ( $pref."_User" ) ;
    		$pass = $this->options->getOption ( $pref."_Pass" ) ;
    		$urls = $this->options->getOption ( $pref."_URL" ) ;
    		$url  = $urls."?logincode=".$user."&password=".$pass."&ipp=".sprintf ( '%09d', $this->ilp )."&hauteur=screenheight&largeur=screenwidth";
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Labo" />' ;
			$inf = XhamTools::genInfoBulle ( "Lancement de Cyberlab : résultats de laboratoire" ) ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
	}
	
	function genAgirh ( $img='images/agirh.gif', $text='' ) {
   		$dll = new cRPC ( $_SERVER[REMOTE_ADDR], $this->options->getOption('BPS_PORT'), $this->options->getOption ( 'BPS_TIMEOUT' ) );
		$result = $dll -> Open ( "agirh" ) ;
	}
	
	function genCarpentrasExcel( $img='images/excel_carpentras.gif', $text='' ) {
		global $patient;
		
    $lien = '<img src="'.$img.'" style="border: 0px;" alt="excel" />' ;
		if ( $this->options->getOption ('carpentras_fichepatient_lcexcel') AND $this->droits->getDroit ( "Presents_Prescription" ) ) {
			return '<a target="_blank" href="excel.php?idpatient='.$patient->getID().'" >'.$lien.'</a>' ;
		}
		/*
		$pref = "CarpentrasExcel" ;
    	if ( $this->options->getOption ( $pref."_Actif" ) AND ( ! $this->options->getOption ( $pref."_Droit" ) OR $this->droits->getDroit ( $this->options->getOption ( $pref."_Droit" ) ) ) ) {
    		$url  = $urls."?logincode=".$user."&password=".$pass."&ipp=".sprintf ( '%09d', $this->ilp )."&hauteur=screenheight&largeur=screenwidth";
			if ( $text ) $lien = $text ;
			else $lien = '<img src="'.$img.'" style="border: 0px;" alt="Excel" />' ;
			$inf = '' ;
			return '<a target="_blank" href="'.$url.'" '.$inf.'>'.$lien.'</a>' ;
    	}
    	*/
	}
	
		
	function getAffichage ( ) {
		global $uniqBP ;
		if ( isset ( $uniqBP ) AND $uniqBP == '1' ) { 
			// On n'ajoute pas le JS, c'est déjà fait.
		} else {
			// Ajout du javascript
		}
		return $this->af ;
	}

}

/*
 * Requête utile pour créer un nouveau lot d'options...
 INSERT INTO `options` ( `idapplication`, `categorie`, `libelle`, `description`, `type`, `choix`, `valeur`, `administrateur`) VALUES
( 1, 'Appels contextuels', '_Actif', 'Le lien externe vers  est activé.', 'bool', '', '', 1),
( 1, 'Appels contextuels', '_User', 'L''utilisateur pour se connecter à .', 'text', '', '', 1),
( 1, 'Appels contextuels', '_Pass', 'Le mot de passe utilisé pour se connecter à .', 'text', '', '', 1),
( 1, 'Appels contextuels', '_Droit', 'Le droit nécessaire pour voir le lien .', 'text', '', '', 1),
( 1, 'Appels contextuels', '_URL', 'URL d''appel à .', 'text', '', '', 1);
 */

?>
