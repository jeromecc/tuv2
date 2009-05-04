<?php

// Titre  : Classe HL7
// Auteur : François Derock <fderock@ch-hyeres.fr>
//          Damien Borel    <dborel@ch-hyeres.fr>
// Date   : 09 Novembre 2007

// Description : 
// Intégration des fichiers HL7 version 2.3.1.

class clHL7 {
  // params dans modules/importation.php
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
						
						
						rename ( "hprim/$fic", "hprim/ok/".$tmpDate.$fic ) ;
						
						if ( file_exists ( "hprim/".$tmp[0].'.OK' ) )
						   	rename ( "hprim/".$tmp[0].".OK", "hprim/ok/".$tmpDate.$tmp[0].".OK" ) ;
						elseif ( file_exists ( "hprim/".$tmp[0].'.ok' ) )
						   	rename ( "hprim/".$tmp[0].".ok", "hprim/ok/".$tmpDate.$tmp[0].".ok" ) ;
						
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
	      $tableau    = preg_split ( "/\n/", $content ) ;
	      // Pour gérer les CRLF(10+13)
	      if ( count ($tableau) <= 1 ) {
	      	$content = nl2br ( $content ) ;
	      	$tableau = preg_split ( "/<br \/>\r/", $content ) ;
	      }
	      
	      //Retourne un tableau avec les résultats de la recherche
				
	    $infos      = preg_grep ( "/^MSH/",   $tableau ) ;
			$evenements = preg_grep ( "/^EVN\|/", $tableau ) ;
			//00: EVN
	    //06: Date d'admission

		// On vérifie que le message est bien du type A04 ou A08.	     
	    $meinf = explode (  '|', $infos[0] ) ;
	    $mess = explode ( '^', $meinf[8] ) ;
	    if ( $mess[1] != 'A04' AND $mess[1] != 'A08' ) {
	    	$this->errs1++ ;
	    	return 0 ;
	    }
	    
	    $patients   = preg_grep ( "/^PID\|/", $tableau ) ;
	    //00: PID
	    //03: IDU[0] ILP[0]
	    //05: Nom[0] Prenom[1]
	    //11: Adresse
	    //13: Telephone
	    //18: Numero de sejour[0]
		$unite      = preg_grep ( "/^ZFU\|/", $tableau ) ;
		//00: ZFU
	    //05: Uf[5]
				
	    $medecin    = preg_grep ( "/^PV1\|/", $tableau ) ;
	    //00: PV1
	    //08: Medecin Traitant nom[1] prenom[2]
	      
			$prevenir   = preg_grep ( "/^NK1\|/", $tableau ) ;
			//00: NK1
	      	//02: Nom[0] Prenom[1]
			//03: Lien de parente
			//04: Adresse
			//05: Telephone
				
			/*$parente[FTH] ="Père";
			$parente[MTH] ="Mère";
			$parente[SPO] ="Epoux";
			$parente[SPO] ="Epouse";
			$parente[CHD] ="Fils";
			$parente[CHD] ="Fille";
			$parente[BRO] ="Frère";
			$parente[SIS] ="Soeur";
			$parente[GRP] ="Grand-père";
			$parente[GRP] ="Grand-mère";
			$parente[PAR] ="Oncle";
			$parente[PAR] ="Tante";
			$parente[PAR] ="Cousin";
			$parente[PAR] ="Cousine";
			$parente[OTH] ="Autre";
			$parente[DOM] ="Concubin";
			$parente[DOM] ="Concubine";
			$parente[EXF] ="Beau Frère";
			$parente[EXF] ="Belle Soeur";
			$parente[PAR] ="Nièce";
			$parente[PAR] ="Neveu";
			$parente[PAR] ="Petit-fils";
			$parente[PAR] ="Petite-fille";
			$parente[GRD] ="Tuteur";
			$parente[GRD] ="Tutrice";
			$parente[EXF] ="Beau-père";
			$parente[EXF] ="Belle-mère";
			$parente[FND] ="Ami";
			$parente[FND] ="Amie";
			$parente[GRD] ="Tuteur CH";
			$parente[GRD] ="Tuteur autre";
			$parente[PAR] ="Parents";
			$parente[SCH] ="Gendre";
			$parente[SCH] ="Belle-fille";*/
			
			
				
	      	//eko ( $infos ) ;
	      	//eko ( $evenements );
	      	//eko ( $patients );
	      	//eko ( $unite );
	      	//eko ( $medecin );
	      	//eko ($prevenir);
	      
				
	    	$patients   = array_values ( $patients ) ;
			$evenements = array_values ( $evenements ) ;
			$unite      = array_values ( $unite ) ;
			$medecin    = array_values ( $medecin );
			$prevenir   = array_values ( $prevenir );
	
			//eko ( $patients );
			//eko ( $evenements );
			//eko ( $unite);
			//eko($prevenir);
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
	        	//eko($data['uf']);
					
	        	// Etat civil du patient.
				$civ            = explode ( $sep2, $patient[5] ) ;
				$data['nom']    = $civ[0] ;
				$data['prenom'] = $civ[1] ;
				if ( $patient[9] ) $data['nom'] = $patient[9] ;
					
	        	// Sexe
	        	$data['sexe']   = $patient[8] ;
	        	
					
	        	// Calcul de la date de naissance du patient.
				$d                    = $patient[7] ;
				$date                 = substr($d,0,4).'-'.substr($d,4,2).'-'.substr($d,6,2).' 00:00:00' ;
				$data['dt_naissance'] = $date ;
					
	        	// Adresse
				$adr                   = explode ( $sep2, $patient[11] ) ;
				//Modif Emmanuel Cervetti, nettoyage des caracteres indésirables
				$data['adresse_libre'] =  ereg_replace('[^[:alnum:]\., \-]*','',$adr[0].' '.$adr[1]) ;
				$data['adresse_cp']    = $adr[4] ;
				$data['adresse_ville'] = $adr[2] ;
					
	        	// Téléphone
	        	//Modif Emmanuel Cervetti: on ne garde que les chiffres les espaces et les .
				$data['telephone']  = ereg_replace('[^[:digit:]\. ]*','',$patient[13]);
					
		        // Date d'admission.
	        	$evenement = explode ( $sep1, $evenements[$i] ) ;
				$d         = $evenement[6];
                if ( !$d ) $d = $unit[6] ;
                if ( !$d ) $d = $evenement[2];
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
				
	    		// Récuperation du medecin traitant
				$medecin                  = explode ( $sep1, $medecin[$i] ) ;
				$med                      = explode ( $sep2, $medecin[8] ) ;
	    		$data['medecin_traitant'] = $med[1]." ".$med[2];
	    		
	    
		        // Personne à prevenir
		        $prevenir           = explode ( $sep1, $prevenir[$i] ) ;
		        $nomprevenir        = explode ( $sep2, $prevenir[2] ) ;
		        $adresseprevenir    = explode ( $sep2, $prevenir[4] ) ;
		        $teleprevenir       = explode ( $sep2, $prevenir[5] ) ;
		        
		        $data['prevenir'] = $nomprevenir[0]." ".$nomprevenir[1];
	    
	    		if ( $adresseprevenir[0]!= "" or $adresseprevenir[1] != "" or $adresseprevenir[4] != "" or $adresseprevenir[2] != "" )
	      			$data['prevenir'] .= " (".$adresseprevenir[0]." ".$adresseprevenir[1]." ".$adresseprevenir[4]." ".$adresseprevenir[2].")";
	    
	    		if ( $teleprevenir[0] != "" )
	      			$data['prevenir'] .= " Tél:".$teleprevenir[0];

            	if ( $data['nsej'] ) {
	          		if ( ! defined ( "DOUBLESERVICE" ) OR ( $data['uf'] != UF2  AND $data['uf'] != UF3 AND $data['uf'] != UF4 AND $data['uf'] != UF5 ) ) {
	          			
	          			$param2['table'] = PPRESENTS ;
			  			$param2['cw']    = "WHERE nsej='".$data['nsej']."'" ;
			  			$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD ) ;
	          			// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE nsej='".$data['nsej']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD ) ;
	          		} elseif ( $data['uf'] == UF2 ) {
	          			$param2['table'] = PPRESENTS ;
			  			$param2['cw']    = "WHERE nsej='".$data['nsej']."'" ;
			  			$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD2 ) ;
	          			// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE nsej='".$data['nsej']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD2 ) ;
	          		} elseif ( $data['uf'] == UF3 ) {
	          			$param2['table'] = PPRESENTS ;
			  			$param2['cw']    = "WHERE nsej='".$data['nsej']."'" ;
			  			$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD3 ) ;
	          			// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE nsej='".$data['nsej']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD3 ) ;
	          		} elseif ( $data['uf'] == UF4 ) {
	          			$param2['table'] = PPRESENTS ;
			  			$param2['cw']    = "WHERE nsej='".$data['nsej']."'" ;
			  			$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD4 ) ;
	          			// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE nsej='".$data['nsej']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD4 ) ;
	          		} elseif ( $data['uf'] == UF5 ) {
	          			$param2['table'] = PPRESENTS ;
			  			$param2['cw']    = "WHERE nsej='".$data['nsej']."'" ;
			  			$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD5 ) ;
	          			// On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE nsej='".$data['nsej']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD5 ) ;
	          		} 
				}
	
	
	        	// Si l'uf est valide
				if ( $this->validUF ( $data['uf'] ) ) {
					if ( ! defined ( "DOUBLESERVICE" ) OR ( $data['uf'] != UF2  AND $data['uf'] != UF3 AND $data['uf'] != UF4 AND $data['uf'] != UF5 ) ) {
						if ( $ras['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PPRESENTS, '', BDD ) ;
						} elseif ( $rus['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PSORTIS, '', BDD ) ;
						} else $this->addPatientHL7 ( $data, PPRESENTS, BDD ) ;
					} elseif ( $data['uf'] == UF2 ) {
						if ( $ras['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PPRESENTS, '', BDD2 ) ;
						} elseif ( $rus['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PSORTIS, '', BDD2 ) ;
						} else $this->addPatientHL7 ( $data, PPRESENTS, BDD2 ) ;
					} elseif ( $data['uf'] == UF3 ) {
						if ( $ras['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PPRESENTS, '', BDD3 ) ;
						} elseif ( $rus['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PSORTIS, '', BDD3 ) ;
						} else $this->addPatientHL7 ( $data, PPRESENTS, BDD3 ) ;
					} elseif ( $data['uf'] == UF4 ) {
						if ( $ras['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PPRESENTS, '', BDD4 ) ;
						} elseif ( $rus['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PSORTIS, '', BDD4 ) ;
						} else $this->addPatientHL7 ( $data, PPRESENTS, BDD4 ) ;
					} elseif ( $data['uf'] == UF5 ) {
						if ( $ras['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PPRESENTS, '', BDD5 ) ;
						} elseif ( $rus['INDIC_SVC'][2] ) {
							$this->majPatientHL7 ( $data, PSORTIS, '', BDD5 ) ;
						} else $this->addPatientHL7 ( $data, PPRESENTS, BDD5 ) ;
					}
				} 
	        	// Si l'uf n'est pas valide
	        	else {
	            	if ( ! defined ( "DOUBLESERVICE" ) OR ( $data['uf'] != UF2  AND $data['uf'] != UF3 AND $data['uf'] != UF4 AND $data['uf'] != UF5 ) ) {
		            	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD ) ;
			            // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD ) ;
						if ( $ras['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PPRESENTS, $data['ilp'], BDD ) ;
	            		} elseif ( $rus['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PSORTIS, $data['ilp'], BDD ) ;
	            		}
	            	} elseif ( $data['uf'] == UF2 ) {
		            	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD2 ) ;
			            // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD2 ) ;
						if ( $ras['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PPRESENTS, $data['ilp'], BDD2 ) ;
	            		} elseif ( $rus['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PSORTIS, $data['ilp'], BDD2 ) ;
	            		}            		
	            	} elseif ( $data['uf'] == UF3 ) {
		            	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD3 ) ;
			            // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD3 ) ;
						if ( $ras['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PPRESENTS, $data['ilp'], BDD3 ) ;
	            		} elseif ( $rus['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PSORTIS, $data['ilp'], BDD3 ) ;
	            		}            		
	            	} elseif ( $data['uf'] == UF4 ) {
		            	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD4 ) ;
			            // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD4 ) ;
						if ( $ras['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PPRESENTS, $data['ilp'], BDD4 ) ;
	            		} elseif ( $rus['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PSORTIS, $data['ilp'], BDD4 ) ;
	            		}            		
	            	} elseif ( $data['uf'] == UF5 ) {
		            	// On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
						$param2['table'] = PPRESENTS ;
						$param2['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$ras             = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery", BDD5 ) ;
			            // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
						$param3['table'] = PSORTIS ;
						$param3['cw']    = "WHERE ilp='".$data['ilp']."'" ;
						$rus             = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery", BDD5 ) ;
						if ( $ras['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PPRESENTS, $data['ilp'], BDD5 ) ;
	            		} elseif ( $rus['INDIC_SVC'][2] ) {
	              			$this->majPatientHL7 ( $data, PSORTIS, $data['ilp'], BDD5 ) ;
	            		}            		
	            	}
	        	}
	  		}
		}
	}
	
	// Ajout d'un patient dans une des tables du terminal (présents ou sortis).
	function addPatientHL7 ( $data, $table, $base=BDD ) {
    	global $errs ;
	  	
	  	// Calcul de la durée depuis lequel le patient est admis.
      	$d1 = new clDate ( ) ;
     	$d2 = new clDate ( $data['dt_admission'] ) ;
      	$duree = new clDuree ( $d1 -> getDifference ( $d2 ) ) ;
      	$duree -> invertNegatif ( ) ;

		// Appel de la classe Requete.
		$requete = new clRequete ( $base, $table, $data ) ;
		// Exécution de la requete.
		$res = $requete->addRecord ( ) ;
		// eko ( "On ajoute un nouveau patient (nsej=$nsej)" ) ;
		
		// Si le patient est admis depuis plus de 30 minutes, alors il est placé dans la table des sortis
		if ( $duree -> getMinutes ( ) > 30 ) {
			$pat = new clPatient ( $res['cur_id'], '', $base ) ;
			$pat -> sortirPatient ( 'simple' ) ;
		}
		$this -> news1++ ;
	}

	// Mise à jour des informations d'un patient dans une des tables du terminal (présents ou sortis).
	function majPatientHL7 ( $data, $table, $ilp='', $base=BDD ) {
		global $errs ;
	
		unset ( $data['uf'] ) ; 
		unset ( $data['prevenir']) ;
		unset ( $data['medecin_traitant']) ;
		unset ( $data['mode_admission']) ;
		if ( $ilp )	{
			unset ( $data['nsej'] ) ;
			unset ( $data['dt_admission'] ) ;
			$requete = new clRequete ( $base, $table, $data ) ;
			$res = $requete->updRecord ( "ilp='".$data['ilp']."'" ) ;		
		} elseif ( $data['nsej'] ) {
			$requete = new clRequete ( $base, $table, $data ) ;
			$res = $requete->updRecord ( "nsej='".$data['nsej']."'" ) ;
		}
		if ( $ilp ) {
			//$errs->addErreur ( afftab($res) ) ;
		}
		//eko ( $data ) ;
		//eko ( $res ) ;
		$this -> mods1++ ;
	}

	// Renvoie l'affichage généré par la classe.
	function getAffichage ( ) {
		return $this->af ;	
	}
	
}

?>
