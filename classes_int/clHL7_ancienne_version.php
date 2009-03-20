<?php

// Titre  : Classe HL7
// Auteur : François Derock <fderock@ch-hyeres.fr>
//          Damien Borel    <dborel@ch-hyeres.fr>
// Date   : 09 Novembre 2007

// Description : 
// Intégration des fichiers HL7 version 2.3.1.

class clHL7 {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;

  // Constructeur.
  function __construct ( ) {
    $this->initHL7 ( ) ;
		$this->runImport ( ) ;
  }

	// Initialisation des colonnes HPRIM.
	function initHL7 ( ) {
    global $options ;
		$this->filtresUF = $options -> getOption ( 'FiltreHprimUF' ) ;
		$this->tabUF = explode ( '|', $this->filtresUF ) ;
	}

	// Renvoi vrai si l'UF passée en paramètre est valide par
	// rapport au filtre des UF (option FiltreHprimUF).
	function validUF ( $uf ) {
    if ( $this->filtresUF ) {
		  for ( $i = 0 ; isset ( $this->tabUF[$i] ) ; $i++ ) {
				if ( $this->tabUF[$i] == $uf ) return 1 ;
			}
		} else return 1 ;
	}

  // Lancement de l'import automatique.
  function runImport ( ) {
    
    global $errs ;
		$mod       = new ModeliXe ( "Importation.mxt" ) ;
    $mod      -> SetModeliXe ( ) ;
    $this     -> news1 = 0 ;
    $this     -> mods1 = 0 ;
    $this     -> errs1 = 0 ;
    // Lancement des imports en provenance de la BAL MySQL.
    $this     -> runImportHL7 ( ) ;
    // Nombre d'entrées de la BAL MySQL
    $mod      -> MxText ( "titre", "Import depuis le répertoire 'hprim/'" ) ;
    $mod      -> MxText ( "total1", $this->news1 + $this->mods1 + $this->errs1 ) ;
    $mod      -> MxText ( "news1", $this->news1 ) ;
    $mod      -> MxText ( "modif1", $this->mods1 ) ;
    $mod      -> MxText ( "errs1", $this->errs1 ) ;
    // Récupération du code HTML généré.
    $this->af .= $mod -> MxWrite ( "1" ) ;
    // Pour débugage des imports automatiques (un mail est envoyé à chaque import...).
    // $errs -> addErreur ( "Résultats de l'import automatique : <br>".$this->af ) ;
  }


  // Lance l'import des données.
  function runImportHL7 ( ) {
  // On parcourt le répertoire 'hprim' à la recherche des nouveaux fichiers HPRIM. 
  
  $r = opendir ( "hprim" ) ;
  while ( $fic = readdir ( $r ) ) {
    if ( $fic != "." AND $fic != ".." AND $fic != "ok" AND $fic != "ko" ) {
      if ( stristr ( $fic, ".HL7" ) or stristr ( $fic, ".hl7" ) ) {
		    $tmp = explode ( ".", $fic ) ;
		    //if ( file_exists ( "hprim/".$tmp[0].'.OK' ) OR file_exists ( "hprim/".$tmp[0].'.ok' ) ) {
          $this   -> readHL7 ( "hprim/$fic" ) ;
			    $date    = new clDate ( ) ;
			    $tmpDate = $date -> getTimestamp ( ) ;
				  // Les 4 lignes suivantes sont à commenter si on veut tester l'intégration de fichiers
				  // HL7 sans les déplacer dans le répertoire ok/ après traitement (en cas de test donc)			    
			    //rename ( "hprim/$fic", "hprim/ok/".$tmpDate.$fic ) ;
			    //if ( file_exists ( "hprim/".$tmp[0].'.OK' ) )
			    	//rename ( "hprim/".$tmp[0].".OK", "hprim/ok/".$tmpDate.$tmp[0].".OK" ) ;
			    //elseif ( file_exists ( "hprim/".$tmp[0].'.ok' ) )
			    	//rename ( "hprim/".$tmp[0].".ok", "hprim/ok/".$tmpDate.$tmp[0].".ok" ) ;
			  //}
      }
    }
  }
  }

	// Lecture d'un fichier HL7 passé en paramètre.
	function readHL7 ( $file ) {
    global $options ;
		if ( file_exists ( $file ) ) {
      $req        = new clResultQuery ;
			$content    = file_get_contents( $file ) ;
			//Eclate une chaîne par expression rationnelle
      $tableau    = preg_split ( "/\n/", $content ) ;
      //eko($tableau);
      //Retourne un tableau avec les résultats de la recherche
			$infos      = preg_grep ( "/^MSH/", $tableau ) ;
			$evenements = preg_grep ( "/^EVN\|/", $tableau ) ;
      $patients   = preg_grep ( "/^PID\|/", $tableau ) ;
			$unite      = preg_grep ( "/^ZFU\|/", $tableau ) ;
			//eko ( $infos ) ;
			//eko ( $evenements );
      //eko ( $patients );
			//eko ( $unite );
			$patients   = array_values ( $patients ) ;
			$evenements = array_values ( $evenements ) ;
			$unite      = array_values ( $unite ) ;

			//eko ( $patients );
			//eko ( $evenements );
			//eko ( $unite);
			// Récupération des informations sur les séparateurs et caractères spéciaux utilisés.
      
      $sep1 = $infos[0][3] ;
			$sep2 = $infos[0][4] ;
			$sep3 = $infos[0][6] ;
			$rep  = $infos[0][5] ;
			$desp = $infos[0][7] ;
			//eko("SEPERATEUR");
      //eko($infos[0][3]);
			//eko($infos[0][4]);
			//eko($infos[0][6]);
			//eko($infos[0][5]);
			//eko($infos[0][7]);
			//eko("SEPERATEUR");
			// Parcours des différents patients contenus dans le fichier HPR/
			for ( $i = 0 ; isset ( $patients[$i] ) ; $i++ ) {
        
        $patient        = explode ( $sep1, $patients[$i] ) ;
        //eko ( $patient ) ;
				
        // Récupération de l'identifiant du patient (IPP)
        $iden           = explode ( $sep2, $patient[3] ) ;
        //eko($iden);
				$data['idu']    = $iden[0] ;
				$data['ilp']    = $iden[0] ;
				
				// Récupération du numéro de dossier (ou séjour) 
				$sej            = explode ( $sep2, $patient[18] ) ;
        $data['nsej']   = $sej[0] ;
				
				$unit           = explode ( $sep1, $unite[$i] ) ;
				//eko($unit);
        $data['uf']     = $unit[5];
        eko($data['uf']);
				
        // Etat civil du patient.
				$civ            = explode ( $sep2, $patient[5] ) ;
				$data['nom']    = $civ[0] ;
				$data['prenom'] = $civ[1] ;
				
        // Sexe
        $data['sexe']   = $patient[8] ;
				
        // Calcul de la date de naissance du patient.
				$d                    = $patient[7] ;
				$date                 = substr($d,0,4).'-'.substr($d,4,2).'-'.substr($d,6,2).' 00:00:00' ;
				$data['dt_naissance'] = $date ;
				
        // Adresse
				$adr                   = explode ( $sep2, $patient[11] ) ;
				$data['adresse_libre'] = $adr[0].' '.$adr[1] ;
				$data['adresse_cp']    = $adr[4] ;
				$data['adresse_ville'] = $adr[2] ;
				
        // Téléphone
				$data['telephone']     = $patient[13] ;
				
        // Médecin
				//$meds = explode ( $rep, $patient[13] ) ;
				//$infoMed = explode ( $sep2, $meds[0] ) ;
				//$civMed = explode ( $sep3, $infoMed[1] ) ;
				
				// if ( $civMed[0] != '\\' ) $data['medecin_traitant'] = $civMed[4].' '.$civMed[0].' '.$civMed[1] ;
				// else $data['medecin_traitant'] = '' ;
				
        // Statut
				//$sta = $patient[24] ;
				
        // Date d'admission.
        $evenement = explode ( $sep1, $evenements[$i] ) ;
				$d         = $evenement[6];
				//eko($d);
				$date      = substr($d,0,4).'-'.substr($d,4,2).'-'.substr($d,6,2).' '.substr($d,8,2).':'.substr($d,10,2).':00' ;
				//eko($date);
        if ( $d )
					$data['dt_admission'] = $date ;
				// Informations fixes supplémentaires.
				// $data['prevenir'] = '' ;
				$data['mode_admission'] = '' ;	
				$data['iduser']         = "IMPORT" ;	
				$data['manuel']         = 0 ;
				
        if ( $data['nsej'] ) {
					
          $param2['table'] = PPRESENTS ;
					$param2['cw']    = "WHERE nsej='".$data['nsej']."'" ;
					$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
					//eko($ras);
					
          // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
					$param3['table'] = PSORTIS ;
					$param3['cw']    = "WHERE nsej='".$data['nsej']."'" ;
					$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;
					//eko($rus);
				}

        // Si l'uf est valide
				if ( $this->validUF ( $data['uf'] ) ) {
				  //eko("uf valide");
					if ( $ras['INDIC_SVC'][2] ) {
						$this->majPatientHL7 ( $data, PPRESENTS ) ;
					} elseif ( $rus['INDIC_SVC'][2] ) {
						$this->majPatientHL7 ( $data, PSORTIS ) ;
					}
					else
						$this->addPatientHL7 ( $data, PPRESENTS ) ;
				} 
        // Si l'uf n'est pas valide
        else {
            //eko("uf non valide");   	
            // On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
						//eko($ras);
						
            // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;
						//eko($rus);
						
            if ( $ras['INDIC_SVC'][2] ) {
              $this->majPatientHL7 ( $data, PPRESENTS, $data['ilp'] ) ;
            }
            elseif ( $rus['INDIC_SVC'][2] ) {
              $this->majPatientHL7 ( $data, PSORTIS, $data['ilp'] ) ;
            }
                    	
        } 

	//eko($data);
  }
}
}
	
	// Ajout d'un patient dans une des tables du terminal (présents ou sortis).
	function addPatientHL7 ( $data, $table ) {
    global $errs ;
		// Appel de la classe Requete.
		$requete = new clRequete ( BDD, $table, $data ) ;
		// Exécution de la requete.
		$requete->addRecord ( ) ;
		// eko ( "On ajoute un nouveau patient (nsej=$nsej)" ) ;
		$this -> news1++ ;
	}

	// Mise à jour des informations d'un patient dans une des tables du terminal (présents ou sortis).
	function majPatientHL7 ( $data, $table, $ilp='' ) {
		global $errs ;
		// eko ( $data ) ;
		// Appel de la classe Requete.
		$requete = new clRequete ( BDD, $table, $data ) ;
		// Exécution de la requete.
		//if ( ! $data['nsej'] ) unset ( $data['nsej'] ) ; 
		
		unset ( $data['uf'] ) ; 
		if ( $ilp )	{
			unset ( $data['nsej'] ) ;
			unset ( $data['dt_admission'] ) ;
			$res = $requete->updRecord ( "ilp='".$data['ilp']."'" ) ;		
		} elseif ( $data['nsej'] ) $res = $requete->updRecord ( "nsej='".$data['nsej']."'" ) ;
		if ( $ilp ) {
			//$errs->addErreur ( afftab($res) ) ;
		}
		//eko ( $data ) ;
		//eko ( $res ) ;
		$this -> mods1++ ;
	}

	// Lancement du mécanisme de fusion de deux patients.
	function launchFusion ( $data ) {
		eko ( "Lancement de la fusion des patients ".$data['nsej']." et ".$data['fusion'] ) ;
		$info['idu'] = $data['idu'] ;
		$info['ilp'] = $data['ilp'] ;
		$info['nom'] = $data['nom'] ;
		$info['prenom'] = $data['prenom'] ;
		$info['sexe'] = $data['sexe'] ;
		$info['dt_naissance'] = $data['dt_naissance'] ;
		$info['adresse_libre'] = $data['adresse_libre'] ;
		$info['adresse_cp'] = $data['adresse_cp'] ;
		$info['adresse_ville'] = $data['adresse_ville'] ;
		$info['telephone'] = $data['telephone'] ;
		$info['medecin_traitant'] = $data['medecin_traitant'] ;
		// Appel de la classe Requete.
		$requete = new clRequete ( BDD, PPRESENTS, $info ) ;
		// Exécution de la requete.
		$res = $requete->updRecord ( "idu='".$data['fusion']."'" ) ;
		// eko ( $res ) ;
		// Appel de la classe Requete.
		$requete = new clRequete ( BDD, PSORTIS, $info ) ;
		// Exécution de la requete.
		$res = $requete->updRecord ( "idu='".$data['fusion']."'" ) ;
		// eko ( $res ) ;
		// Recherche du dernier séjour de ce patient dans les présents
		$req = new clResultQuery ( ) ;
		$param2['table'] = PPRESENTS ;
		$param2['cw'] = "WHERE idu='".$data['idu']."' ORDER BY dt_admission DESC" ;
		$pres = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
		// eko ( $pres['INDIC_SVC'] ) ;
		// Recherche du dernier séjour de ce patient dans les sortis
		$param2['table'] = PSORTIS ;
		$param2['cw'] = "WHERE idu='".$data['idu']."' ORDER BY dt_admission DESC" ;
		$sort = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
		// eko ( $sort['INDIC_SVC'] ) ;
	}

	// Renvoie l'affichage généré par la classe.
	function getAffichage ( ) {
		
	return $this->af ;	
	
	}
	
}

?>
