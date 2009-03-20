<?php

// Titre  : Classe EnvoiARH
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 06 Juin 2007

// Description : 
// Envoi vers le serveur de veille arh.

class clEnvoiARH {

  	// Attributs de la classe.
		

  	// Constructeur.
  	function __construct ( ) {
 		$this->genEnvoi ( ) ;
  	}

	function genEnvoi ( ) {
		global $options ;
		global $errs ;
		
		//Attention, jour concerné = lendemain des données
		$dateJourConcerne = new clDate();
		$tsp = $dateJourConcerne->getTimestamp();
		
		
		define ( nomForm, "URG" ) ;
		define ( idActeur, $options->getOption ( 'RPU_IdActeur' ) ) ;
		define ( arRequis, $options->getOption ( 'RPU_AR_Actif' ) ) ;
		define ( cleDepot, $options->getOption ( 'RPU_CleActeur' ) ) ;
		define ( mail, $options->getOption ( 'RPU_AR_Mail' ) ) ;
		define ( cfg_expediteur_alerte, "terminal_urgences" ) ;
		define ( chemin, URLRPU.'arh/' ) ;
		define ( date_jour, date("Ymd",$tsp) ) ;
		define ( jour, substr(date_jour,6,2) ) ;
		define ( mois, substr(date_jour,4,2) ) ;
		define ( annee, substr(date_jour,0,4) ) ;
		define ( date_file, date("YmdHis") ) ;
		define ( date_envoi, date("d/m/Y à H:i:s") ) ;
		define ( date_event, date("d/m/Y", mktime(0, 0, 0,mois,jour-1,annee)) ) ;
		define ( from, date("Y-m-d H:i:s", mktime(0, 0, 0,mois,jour-1,annee)) ) ;
		define ( to, date("Y-m-d H:i:s", mktime(23, 59, 59,mois,jour-1,annee)) ) ;
		
		$req="select idpatient, uf , dt_naissance , type_destination
        from  patients_sortis
	    where manuel!=1 and dt_admission between '".from."' and '".to ."' and type_destination!='X' and valide>=1
		UNION
		select idpatient, uf , dt_naissance , type_destination
		from  patients_presents
		where manuel!=1 and dt_admission between '".from."' and '".to ."' and type_destination!='X' and valide>=1";

		$config[type] = "MySQL" ;
		$config[host] = MYSQL_HOST;
		$config[login] = MYSQL_USER ;
		$config[password] = MYSQL_PASS ;
		$config[db] = BDD ;
		
		$requete = new clResultQuery ;
		// On récupère le résultat de la requête sous la forme ResultQuery.
		$res = $requete -> Execute ( "requete", $req, $config ) ;
		eko($res[INDIC_SVC]);
		$age1=0;
		$age75=0;
		$NbHospit=0;
		$NbUHCD=0;
		$NbTransfert=0;

		$ufUHCD = $options -> getOption ( "numUFUHCD" ) ;
		//eko ( $res['INDIC_SVC'] ) ;
 		if ($res[INDIC_SVC][2]){
 			$NbPassages= $res[INDIC_SVC][2];                     	// On a le nombre de passages
    		while (list($key,$val) = each($res[dt_naissance])){
	        	$dateN = new clDate ( $val ) ;
				$dateA = new clDate ( from ) ;
				$duree = new clDuree ( ) ;
				$duree -> setValues ( $dateA -> getDifference ( $dateN ) ) ;
				$age=$duree -> getYears ( ) ;
				if ($age < 1){
	        		$age1++;										// Constitution du nb de passage < à 1 an
				}
				elseif( $age >75){
	        		$age75++;                                       // Constitution du nb de passage > 75 ans
				}
	        	// Hospitalisations
	        	if(($res[type_destination][$key] =="H") and ($res[uf][$key]!=$ufUHCD)){
	        		$NbHospit++;
	        	}
	        	// UHCD
	        	if(($res[uf][$key]==$ufUHCD)){
	        		$NbUHCD++;
	        	}
	        	 // Transferts
	        	if(($res[type_destination][$key] =="T")){
	        		$NbTransfert++;
	        	}
				//echo $val."--=> ".$res[type_destination][$key]." ".$age   ." ans <br>";
			}
			$balise_element="\n<entete>";
			$balise_element.="\n<idActeur>".idActeur."</idActeur>";
			$balise_element.="\n<cleActeur>".cleDepot."</cleActeur>";
			$balise_element.="\n<arRequis>".arRequis."</arRequis>";
			$balise_element.="\n<mail>".mail."</mail>";
			$balise_element.="\n</entete>";
	
			$balise_element.="\n<element>";
			$balise_element.="\n<nomForm>".nomForm."</nomForm>";
			$balise_element.="\n<date_event>".date_event."</date_event>";
			$balise_element.="\n<NbPassages>$NbPassages</NbPassages>";
			$balise_element.="\n<NbPassInf1An>$age1</NbPassInf1An>";
			$balise_element.="\n<NbPassageSup75Ans>$age75</NbPassageSup75Ans>";
			$balise_element.="\n<NbHospit>$NbHospit</NbHospit>";
			$balise_element.="\n<NbHospitUHCD>$NbUHCD</NbHospitUHCD>";
			$balise_element.="\n<NbTransfert>$NbTransfert</NbTransfert>";
			$balise_element.="\n</element>";
	
			$nom_fic=idActeur."_".date_file.".xml";
			$nom_fic_export=chemin.$nom_fic;
			$xml_data="<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>
			\n<result>".$balise_element."\n</result>";
	
	
		
			$fp=fopen($nom_fic_export,"w");
			if (!fwrite ($fp, $xml_data))
				eko ( "pb ecriture fichier xml" );
				fclose($fp);
			copy ( $nom_fic_export ,URLLOCAL.'rpu/logs/'.$nom_fic );	
			$affichage="<center><h2>Export des données Urgences - CH-Hyères</h2></center>
			<u>Date d'export:</u> le ".date_envoi."<br>
			<u>Contenu exporté :</u> $balise_element<br>
			<u>Fichier d'export :</u> $nom_fic_export";
			
			// Cryptage du fichier.
			$mailE = $options->getOption('RPU_Envoi_Mail') ;
			$gpg = new gnuPG();
			$gpg -> EncryptFile ( $mailE, $nom_fic_export ) ;
			if ( ! $gpg -> error ) 
				$this->message .= "<font color=\"green\">Le cryptage du fichier ($nom_fic_export.gpg) s'est bien déroulé.<br/></font>" ;
			else
				$this->message .= "<font color=\"red\">Le cryptage du fichier ($nom_fic_export.gpg) a échoué :".$gpg -> error."<br/></font>" ;
			
			if ( $options -> getOption ( 'RPU_TypeEnvoi' ) == 'mail' ) {
				// Envoi du fichier RPU.
				$contenuFic = fread ( fopen ( $nom_fic_export.'.gpg', "r" ), filesize ( $nom_fic_export.'.gpg' ) ) ;
				// eko ( $contenuFic ) ;
				$mail = new mime_mail ( ) ;
				$mail->to = $options->getOption ( "RPU_Envoi_Mail" ) ;
				//$mail->to = "dborel@ch-hyeres.fr" ;
				//eko ( $options->getOption ( "RPU_Envoi_Mail" ) ) ;
				$mail->subject = "Données urgences (".date_envoi.")" ; 
				$mail->body = "Données urgences (".date_envoi.")" ;    
				$mail->from = Erreurs_MailApp ;    
				$mail->attach ( $contenuFic, $nom_fic.'.gpg' ) ;
	 
				if ( $mail->sendXham ( ) ) $this->message .= "<font color=\"green\">L'envoi du fichier ($nom_fic_export.gpg) s'est bien déroulé.<br/></font>" ;
				else $this->message .= "<font color=\"red\">L'envoi du fichier ($nom_fic_export.gpg) a échoué.<br/></font>" ;
			} else {
                    rename ( $nom_fic_export, chemin.'ok/'.$nom_fic ) ;
					$this->message .= "Connexion au serveur FTP '".$options->getOption ( 'RPU_FTP_Host' ).':'.$options->getOption ( 'RPU_FTP_Port' )."' -> " ;
  					$con = ftp_connect ( $options->getOption ( 'RPU_FTP_Host' ) ) ;
  					if ( ! $con ) {
  						 $this->message .= "<font color='red'>KO</font><br/>" ;
  						$errs->addErreur ( 'RPU : Impossible de se connecter au serveur "'.$options->getOption ( 'RPU_FTP_Host' ).':'.$options->getOption ( 'RPU_FTP_Port' ).'"' ) ;
  					} else {
  						$this->message .= "<font color='green'>OK</font><br/>" ;
  						$this->message .= "Authentification au serveur FTP avec l'utilisateur '".$options->getOption('RPU_FTP_User')."' -> " ;
  						$log = ftp_login ( $con, $options->getOption ( 'RPU_FTP_User' ), $options->getOption ( 'RPU_FTP_Pass' ) ) ;
  						if ( ! $log ) {
  							$this->message .= "<font color='red'>KO</font><br/>" ;
  							$errs->addErreur ( 'RPU : Impossible de se connecter au serveur avec l\'utilisateur "'.$options->getOption ( 'RPU_FTP_User' ).'"' ) ;
  						} else {
  							$this->message .= "<font color='green'>OK</font><br/>" ;
	  						$r = opendir ( 'rpu/arh/' ) ;
  							while ( $fic = readdir ( $r ) ) {
  								if ( $fic != "." AND $fic != ".." AND $fic != "ok" AND $fic != "logs" AND $fic != 'arh' ) {		
			  						$this->message .= "Envoi du fichier '$fic' -> " ;
			  						$put = ftp_put ( $con, $fic, chemin.$fic, FTP_ASCII ) ;
			  						if ( ! $put ) {
			  							$this->message .= "<font color='red'>KO</font><br/>" ;
			  							$errs->addErreur ( 'RPU : Impossible d\'envoyer le fichier "'.$fic.'".' ) ;
			  						} else {
			  							$this->message .= "<font color='green'>OK</font><br/>" ;
			  							rename ( chemin.$fic, chemin.'ok/'.$fic ) ;
			  						}
  								}
  							}
  						}
					}
			}
			
			$this -> af = $affichage."<br/>".$this->message ;
 		} else $this->af .= "Aucun patient retourné par la requête : envoi impossible." ;

		
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>