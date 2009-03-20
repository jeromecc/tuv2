<?php

// Titre  : Classe Alertes
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 23 janvier 2008

// Description : 
// Gestion des alertes du terminal des urgences.

class clAlertes {

  	// Attributs de la classe.
	private $af ;

  	// Constructeur.
  	function __construct ( ) {
 		$date = new clDate ( ) ;
 		$this->af .= "<div style=\"padding:20px 40px 40px 40px;\"><h4>Alertes automatiques</h4>" ;
 		if ( ( $date->getHours ( ) == "7" OR $date->getHours ( ) == "15" ) AND $date->getMinutes ( ) == 0 ) {
 			$this->launchAES ( ) ;
 			$this->af .= "Lancement des alertes AES : automatique à 7h et 15h." ;
 		} else {
 			$this->af .= "Lancement des alertes AES : automatique à 7h et 15h." ;
 			//$this->launchAES ( ) ;	
 		}
 		$this->af .= "<div/>" ;
  	}

	function launchAES ( ) {
		global $options ;
		
		$mails = '' ;
		$codes = '' ;
		$contenu = '' ;
		
		$config[type] = "MySQL" ;
		$config[host] = MYSQL_HOST ;
		$config[login] = MYSQL_USER ;
		$config[password] = MYSQL_PASS ;
		$config[db] = BDD;
		
		$date = new clDate () ;
		$date_naissance = new clDate ();
		$date_admission = new clDate ();
		//$aujourdhui =getdate();
		//$date ->getHours ( )
		
		$date1 = $date->getDate("Y-m-d");     // aujourdhui J
		
		$date->addDays ( "-1" ) ;
		$date2=$date->getDate ("Y-m-d" ) ;  // date J-1
		
		$heure = $date->getDate("H:i:00");
		
		$date->addHours ( "-8" ) ;
		$hour1 = $date->getDate("H:i:00") ;
		$date->addHours ( "8" ) ;
		
		$date->addHours ( "-16" ) ;
		$hour2 = $date->getDate("H:i:00") ;
		
		$requete = new clResultQuery ;
		
		$listeGen = new clListesGenerales ( "recup" ) ;
		$listeMails = $listeGen -> getListeItems ( "Mails Alertes AES", "1", '', '', "1" ) ;
		$listeCodesR = $listeGen -> getListeItems ( "Alertes AES - Code Recours", "1", '', '', "1" ) ;
		$listeCodesD = $listeGen -> getListeItems ( "Alertes AES - Code Diagnostics", "1", '', '', "1" ) ;
		
		while ( list ( $key, $val ) = each ( $listeMails ) ) {
		  if ( $mails ) $virgule = ',' ; else $virgule = '' ;
		  if ( $val != "--" ) 
		    $mails .= $virgule.$val ;
		}
		//print "Mails : $mails" ;
		while ( list ( $key, $val ) = each ( $listeCodesR ) ) {
		  if ( $codes ) $or = ' OR ' ; else $or = '' ;
		  if ( $val != "--" ) 
		    $codes .= $or."recours_code='".$val."'" ;
		}
		while ( list ( $key, $val ) = each ( $listeCodesD ) ) {
		  if ( $codes ) $or = ' OR ' ; else $or = '' ;
		    if ( $val != "--" )
		      $codes .= $or."diagnostic_code='".$val."'" ;
		}
				
		if ( $heure >= "15:00:00" ) {
		  $req2= "SELECT * FROM patients_sortis WHERE ($codes) AND dt_sortie BETWEEN '$date1 $hour1' AND '$date1 $heure'" ;
		  $res1 = $requete -> Execute ( "Query", $req2, $config) ;
		} else {
		  $req2 = "SELECT * FROM patients_sortis WHERE ($codes) AND dt_sortie BETWEEN '$date2 $hour2' AND '$date1 $heure'" ;
		  $res1 = $requete -> Execute ( "Query", $req2, $config) ;
		} ;
		
		
		$contenu1 = $contenu1."\n" ;
		if ( $res1[idpatient] > 0 ) {
		  $contenu1 = "<table align=\"center\" border =1>
			       <tr bgcolor=\"Silver\">
			       <th colspan=\"5\">Informations AES (Patients sortis)</th>
				</tr>
				<tr bgcolor=\"Silver\">
				<th>Nom</th>
				<th>Prénom</th>
				<th>Né(e) le</th>
				<th>Admission le</th>
				<th>Médecin Urg.</th>
				</tr>";
		    for ( $i = 0 ; isset ( $res1[idpatient][$i] ) ; ++$i ) {
		      // Affectation de resultats dans le tableau entrants
		      $date_naissance -> setDate ( $res1[dt_naissance][$i] ) ;
		      $date_admission -> setDate ( $res1[dt_admission][$i] ) ;
		      
		      $contenu1 .="<tr>"; 
		      $contenu1 .="<td>".$res1[nom][$i]."</td>";
		      $contenu1 .="<td>".$res1[prenom][$i]."</td>";
		      $contenu1 .="<td>".$date_naissance->getDate('d-m-Y H:i:s')."</td>";
		      $contenu1 .="<td>".$date_admission->getDate('d-m-Y H:i:s')."</td>";
		      $contenu1 .="<td>Dr ".$res1[medecin_urgences][$i]."</td>";
		
		      $contenu1 .="</tr>";
		      //Ecriture du log
		      
		      $text_log1 .= $text_log1."\n" ;
		      $text_log1 .= $res1[nom][$i].";" ;
		      $text_log1 .= $res1[prenom][$i].";" ;
		      $text_log1 .= $res1[dt_naissance][$i].";" ;
		      $text_log1 .= $res1[dt_admission][$i].";" ;
		      $text_log1 .= $res1[medecin_urgences][$i].";" ;
		      $text_log1 .= " patients sortis " ;
		      $fp=fopen( "fichier_log.txt","a" ) ;
		      fputs ( $fp, $text_log1 ) ;
		      
		    }
		    $contenu1 .= "</table>" ;
		    eko ( $contenu1 ) ;   
		} else {
		  $verif2 = 1 ;
		  eko ( "Aucun enregistrement correspondant à la requête de patients sortis." ) ;
		  $contenu1 .= "Aucun enregistrement correspondant à la requête de patients sortis." ;
		}
		
		// Paramètre de mail
		$reply = Erreurs_MailApp ;
		$comment = "<hr><i>Ce message a été envoyé automatiquement par un serveur !</i> " ;
		$contenu2 = $contenu ;
		$contenu2 .= $contenu1 ;
		$contenu2 .= $comment ;
		//$contenu2 .= "<br>req1 : $req<br>" ;
		//$contenu2 .= "req2 : $req2<br>" ;
		$from = Erreurs_MailApp ;
		//$dest_mail = "dborel@ch-hyeres.fr" ;
		$dest_mail = $mails ;
		
		eko ( "\nContenu : $contenu2\n" ) ;
		
		$content = "Content-Type: text/html; charset=\"iso-8859-1\"" ;
		$head = "From: $from\n$content\n" ;
		$obj = "Alerte AES" ;
		
		if ( $verif2 ) {
			$dest_mail = "developpeurs@ch-hyeres.fr" ;
			$obj = "Alerte AES (RAS)" ;
		} else 	mail ( $dest_mail, $obj, $contenu2, $head ) ;
		
		eko ( $comment ) ;
	}

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>
