<?php

// Titre  : Classe Hprim
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 10 Mai 2006

// Description : 
// Intégration des fichiers HPRIM.

class clHprim {

  	// Attributs de la classe.
  	// Contient l'affichage généré par la classe.
  	private $af ;

  	// Constructeur.
  	function __construct ( ) {
  		$this->initHPRIM ( ) ;
		$this->runImport ( ) ;
  	}

	// Initialisation des colonnes HPRIM.
	function initHPRIM ( ) {
		global $options ;
		$this->filtresUF = $options -> getOption ( 'FiltreHprimUF' ) ;
		$this->tabUF = explode ( '|', $this->filtresUF ) ;
		// Première ligne des fichiers HPR 
		// Exemple : H|^~&\|H104627.HPR||888888888^SERVEUR XML||ADM|||LABO-KODAC^MEDINFO|LS3||H2.1^C|200604251046
		// 00 : H
		// 01 : ???
		// 02 : Nom du fichier
		// 03 : --
		// 04 : ??? / Provenance
		// 05 : --
		// 06 : ???
		// 07 : --
		// 08 : --
		// 09 : ??? / ???
		// 10 : ???
		// 11 : --
		// 12 : Contenu fichier / ???
		// 13 : Date
		//
		// Ligne(s) patient(s) des fichiers HPR
		// Exemple : P|5|94000078^^||06007065|HAMZI^MOHAMED^^^M^||19361012|M||LE GD MURIER N°2^AV ST CASSIEN^LE MUY^^83490^||
		// 120494818226|13^\^|14|15|16|17|18|19|20|21|22|23200603140000~200603140000|24OP|25^^SORTIE|26|27|28|29|30|31|
		// 32200603140000|
		// 00 : P
		// 01 : Ce champ détermine le rang du segment, de 1 pour le premier patient à n pour le énième.
		// 02 : ILP / Fusion / FU
		// 03 : Identifiant de l'exécutant.
		// 04 : NSEJ
		// 05 : NOM / PRENOM / PRENOM2 / ALIAS / TITRE / DIPLOME
		// 06 : NOM JF
		// 07 : NAISSANCE (AAAAMMJJ)
		// 08 : SEXE (M/F/I)
		// 09 : Race (Interdit)
		// 10 : ADR1 / ADR2 / VILLE / Libellé département / CP / pays
		// 11 : CTS...
		// 12 : TEL : plusieurs numéros possibles, séparés par le caractère répétiteur
		// 13 : NU Médecin / ( Nom / Prenom / Prenom2 / Alias / Civilité / Diplome ) / Type code : Plusieurs méd. possibles
		// 14 : Traitement local 1
		// 15 : Traitement local 2
		// 16 : Taille
		// 17 : Poids
		// 18 : Diagnostic : Code 1 / libelle 1 / nomenclature 1 / code 2 / libelle 2 / nomenclature 2
		// 19 : Traitement
		// 20 : Régime
		// 21 : Commentaire 1
		// 22 : Commentaire 2
		// 23 : Date d?admission ou de mouvement, éventuellement suivie de la date de sortie séparée par un répétiteur
		// 24 : Statut : OP (sortie) IP (entrée) ER (urgence) PA (pré-admission) MP (mouvement interne ou modification)
		// 25 : Localisation : Lit / Chambre / unité de soin / + 5 champs libre
		// 26 : Classification du diagnostic : Idem item 18
		// 27 : Religion (Interdit)
		// 28 : Situation maritale : M / D / S (célibataire) / W (veuf) / A (séparé) / U (inconnu)
		// 29 : Précaution (???)
		// 30 : Langue
		// 31 : Statut confidentialité
		// 32 : Date dernière modification (AAAAMMDDHHMMSS)
		//
		// Ligne de fin de fichier HPR
		// Exemple : L|1|||
		// 00 : L
		// 01 : Rang (1)
		// 02 : non utilisé
		// 03 : Nombre de segments
		// 04 : Nombre de segments du message
		// 05 : Numéro de lot
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
		$mod = new ModeliXe ( "Importation.mxt" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$this->news1 = 0 ;
    	$this->mods1 = 0 ;
    	$this->errs1 = 0 ;
    	// Lancement des imports en provenance de la BAL MySQL.
    	$this->runImportHPRIM ( ) ;
    	// Nombre d'entrées de la BAL MySQL
    	$mod -> MxText ( "titre", "Import depuis le répertoire 'hprim/'" ) ;
    	$mod -> MxText ( "total1", $this->news1 + $this->mods1 + $this->errs1 ) ;
    	$mod -> MxText ( "news1", $this->news1 ) ;
    	$mod -> MxText ( "modif1", $this->mods1 ) ;
    	$mod -> MxText ( "errs1", $this->errs1 ) ;
    	// Récupération du code HTML généré.
    	$this->af .= $mod -> MxWrite ( "1" ) ;
    	// Pour débugage des imports automatiques (un mail est envoyé à chaque import...).
    	// $errs -> addErreur ( "Résultats de l'import automatique : <br>".$this->af ) ;
  	}


  	// Lance l'import des données.
  	function runImportHPRIM ( ) {
  		// On parcourt le répertoire 'hprim' à la recherche des nouveaux fichiers HPRIM. 
  		$r = opendir ( "hprim" ) ;
  		while ( $fic = readdir ( $r ) ) {
      	  if ( $fic != "." AND $fic != ".." AND $fic != "ok" AND $fic != "ko" ) {
      		if ( stristr ( $fic, ".HPR" ) or stristr ( $fic, ".hpr" ) ) {
			  $tmp = explode ( ".", $fic ) ;
			  // eko ( "Je suis ici : $fic et le fichier associé est :"."hprim/".$tmp[0].'.OK' ) ;
			  if ( file_exists ( "hprim/".$tmp[0].'.OK' ) OR file_exists ( "hprim/".$tmp[0].'.ok' ) ) {
			    $this->readHPRIM ( "hprim/$fic" ) ;
			    $date = new clDate ( ) ;
			    $tmpDate = $date -> getTimestamp ( ) ;

			    rename ( "hprim/$fic", "hprim/ok/".$tmpDate.$fic ) ;
			    if ( file_exists ( "hprim/".$tmp[0].'.OK' ) )
			    	rename ( "hprim/".$tmp[0].".OK", "hprim/ok/".$tmpDate.$tmp[0].".OK" ) ;
			    elseif ( file_exists ( "hprim/".$tmp[0].'.ok' ) )
			    	rename ( "hprim/".$tmp[0].".ok", "hprim/ok/".$tmpDate.$tmp[0].".ok" ) ;
			  }
      		}
      	  }
    	}
  	}

	// Lecture d'un fichier HPRIM 2.1 passé en paramètre.
	function readHPRIM ( $file ) {
		global $options ;
		if ( file_exists ( $file ) ) {
			$req = new clResultQuery ;
			$content  = file_get_contents( $file ) ;
			$tableau  = preg_split ( "/\n/", $content ) ;
			$infos    = preg_grep ( "/^H/", $tableau ) ;
			$patients = preg_grep ( "/^[AP]\|/", $tableau ) ;
			// eko ( $tableau ) ;
			$patients = array_values ( $patients ) ;
			// Récupération des informations sur les séparateurs et caractères spéciaux utilisés.
			$sep1 = $infos[0][1] ;
			$sep2 = $infos[0][2] ;
			$sep3 = $infos[0][4] ;
			$rep  = $infos[0][3] ;
			$desp = $infos[0][5] ;
			$tabufgen1 = explode ( '|', $infos[0] ) ;
			$tabufgen2 = explode ( '~', $tabufgen1[4] ) ;
			$ufgen = $tabufgen2[0] ;
			
			// Parcours des différents patients contenus dans le fichier HPR/
			for ( $i = 0 ; isset ( $patients[$i] ) ; $i++ ) {
				$patient = explode ( $sep1, $patients[$i] ) ;
				if ( count ( $patient ) < 34 AND $patient[0] == 'P' ) {
					$patient2 = explode ( $sep1, $patients[$i+1] ) ;
					if ( $patient2[0] == 'A' ) {
						//eko ( "On fusionne" ) ;
						//eko ( $patient ) ;
						//eko ( $patient2 ) ;
						unset ( $patient[count($patient)-1] ) ;
						unset ( $patient2[0] ) ;
						$final = array_merge($patient,$patient2) ;
						//eko ( $final ) ;
						$patient = $final ;
						$i++ ;
					}
				}
				// eko ( $patient ) ;
				// Récupération du numéro de dossier (ou séjour)
				if ( $patient[4] ) {
					$tabNsej = explode ( $sep2, $patient[4] ) ;
					$data['nsej'] = $tabNsej[0] ;
				}
				// Récupération de l'identifiant du patient (IPP)
				$id = explode ( $sep2, $patient[2] ) ;
				$data['idu'] = $id[0] ;
				$data['ilp'] = $id[0] ;
				if ( $id[1] ) $data['fusion'] = $id[1] ;
				else $data['fusion'] = '' ;
				// Localisation du patient
				$loc = explode ( $sep2, $patient[25] ) ;
				$data['uf'] = ($loc[2]?$loc[2]:($loc[3]?$loc[3]:($loc[4]?$loc[4]:($loc[1]?$loc[1]:($loc[0]?$loc[0]:$ufgen))))) ;
				// Etat civil du patient.
				$civ = explode ( $sep2, $patient[5] ) ;
				$data['nom'] = $civ[0] ;
				$data['prenom'] = $civ[1] ;
				$data['sexe'] = $patient[8] ;
				// Calcul de la date de naissance du patient.
				$d = $patient[7] ;
				$date = substr($d,0,4).'-'.substr($d,4,2).'-'.substr($d,6,2).' 00:00:00' ;
				$data['dt_naissance'] = $date ;
				// Adresse
				$adr = explode ( $sep2, $patient[10] ) ;
				$data['adresse_libre'] = $adr[0].' '.$adr[1] ;
				$data['adresse_cp'] = $adr[4] ;
				$data['adresse_ville'] = $adr[2] ;
				// Téléphone
				$data['telephone'] = $patient[12] ;
				// Médecin
				$meds = explode ( $rep, $patient[13] ) ;
				$infoMed = explode ( $sep2, $meds[0] ) ;
				$civMed = explode ( $sep3, $infoMed[1] ) ;
				// if ( $civMed[0] != '\\' ) $data['medecin_traitant'] = $civMed[4].' '.$civMed[0].' '.$civMed[1] ;
				// else $data['medecin_traitant'] = '' ;
				// Statut
				$sta = $patient[24] ;
				// Date d'admission.
				$d = $patient[23] ;
				$date = substr($d,0,4).'-'.substr($d,4,2).'-'.substr($d,6,2).' '.substr($d,8,2).':'.substr($d,10,2).':00' ;
				if ( $d )
					$data['dt_admission'] = $date ;
				// Informations fixes supplémentaires.
				// $data['prevenir'] = '' ;
				$data['mode_admission'] = '' ;	
				$data['iduser'] = "IMPORT" ;	
				$data['manuel'] = 0 ;
				// eko ( $data ) ;
				if ( $data['fusion'] ) $this->launchFusion ( $data ) ;
				// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
				if ( $data['nsej'] ) {
					$param2['table'] = PPRESENTS ;
					$param2['cw'] = "WHERE nsej='".$data['nsej']."'" ;
					$ras = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
					// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
					$param3['table'] = PSORTIS ;
					$param3['cw'] = "WHERE nsej='".$data['nsej']."'" ;
					$rus = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;
				}
				unset ( $data['fusion'] ) ;
				if ( $this->validUF ( $data['uf'] ) ) {
					if ( $ras['INDIC_SVC'][2] ) {
						$this->majPatientHPRIM ( $data, PPRESENTS ) ;
					} elseif ( $rus['INDIC_SVC'][2] ) {
						$this->majPatientHPRIM ( $data, PSORTIS ) ;
					} elseif ( $sta == 'ER' OR ! $options->getOption ( 'FiltreHprimStatut' ) ) {
						eko ( $patient ) ;
						$this->addPatientHPRIM ( $data, PPRESENTS ) ;
					}
				} else {
                	unset ( $data['dt_admission'] ) ;
                    unset ( $data['mode_admission'] ) ;
                    unset ( $data['telephone'] ) ;
                    unset ( $data['uf'] ) ;
                    if ( $sta == 'MI' ) {
                    	unset ( $data['nsej'] ) ;
                    	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw'] = "WHERE ilp='".$data['ilp']."'" ;
						$ras = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
						// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw'] = "WHERE ilp='".$data['ilp']."'" ;
						$rus = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;
						if ( $ras['INDIC_SVC'][2] ) {
                    		$this->majPatientHPRIM ( $data, PPRESENTS, $data['ilp'] ) ;
                    	} elseif ( $rus['INDIC_SVC'][2] ) {
                        	$this->majPatientHPRIM ( $data, PSORTIS, $data['ilp'] ) ;
                    	}
                    	
                    } else {
                    	if ( $ras['INDIC_SVC'][2] ) {
                    		$this->majPatientHPRIM ( $data, PPRESENTS ) ;
                    	} elseif ( $rus['INDIC_SVC'][2] ) {
                    	    $this->majPatientHPRIM ( $data, PSORTIS ) ;
                    	}
                    }
               }
			}
		}
	}
	
	// Ajout d'un patient dans une des tables du terminal (présents ou sortis).
	function addPatientHPRIM ( $data, $table ) {
		global $errs ;
		// Appel de la classe Requete.
		$requete = new clRequete ( BDD, $table, $data ) ;
		// Exécution de la requete.
		$requete->addRecord ( ) ;
		// eko ( "On ajoute un nouveau patient (nsej=$nsej)" ) ;
		$this -> news1++ ;
	}

	// Mise à jour des informations d'un patient dans une des tables du terminal (présents ou sortis).
	function majPatientHPRIM ( $data, $table, $ilp='' ) {
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
		/*
		// On récupère le plus récent.
		if ( $pres['INDIC_SVC'][2] AND $sort['INDIC_SVC'][2] ) {
			$dPres = new clDate ( $pres['dt_admission'][0] ) ;
			$dSort = new clDate ( $sort['dt_admission'][0] ) ;
			if ( $dPres -> getTimestamp ( ) > $dSort -> getTimestamp ( ) ) {
				$idpatient = $pres['idpatient'][0] ;
				$table = PPRESENTS ;
			} else {
				$idpatient = $sort['idpatient'][0] ;
				$table = PSORTIS ;
			}
		} else if ( $pres['INDIC_SVC'][2] AND ! $sort['INDIC_SVC'][2] ) {
			$idpatient = $pres['idpatient'][0] ;
			$table = PPRESENTS ;
		} else if ( ! $pres['INDIC_SVC'][2] AND $sort['INDIC_SVC'][2] ) {
			$idpatient = $sort['idpatient'][0] ;
			$table = PSORTIS ;
		} else {
			$idpatient = '' ;
		}
		if ( $idpatient ) {
			// On met à jour ce séjour avec les informations suivantes.
			if ( $data['nsej'] AND $data['nsej'] != '0000000' )
				$infoLastSejour['nsej'] = $data['nsej'] ;
			$infoLastSejour['uf'] = $data['uf'] ;
			if ( $data['dt_admission'] != '0000-00-00 00:00:00' )
				$infoLastSejour['dt_admission'] = $data['dt_admission'] ;
			// Appel de la classe Requete.
			$requete = new clRequete ( BDD, $table, $infoLastSejour ) ;
			// Exécution de la requete.
			$res = $requete->updRecord ( "idpatient='".$idpatient."'" ) ;
			eko ( $res ) ;
		}
		*/
	}

	// Renvoie l'affichage généré par la classe.
	function getAffichage ( ) {
		
	return $this->af ;	
	
	}
	
}

?>