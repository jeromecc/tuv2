<?php

// Titre  : Classe ListesPatients
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 03 Mars 2005

// Description : 
// Gestion de l'affichage des différentes listes de patients :
// Patients présents, patients sortis...

/* Intégration du module de cotation des actes
	Auteur : Christophe Boulay
	Date : 11 avril 2005
	
	Dans genFiche, invocation de la classe clCotationCCAM qui attend en entrée le tableau
	$paramCCAM qui contient les informations liées au patient et aux intervenants, déjà 
	disponibles dans la fiche patient et qui seront indispensables à la cotation CCAM
*/

class clFichePatient {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Type d'environnement (Presents, Sortis, UHCD...).
  private $type ;
  // Table sur laquelle les opérations seront réalisées.
  private $table ;
  // Identifiant du patient.
  private $idpatient ;
  //formulaires associés
  private $formulaires ;
  ////////////////////////// CCAM /////////////////////////////
  // Pour le module cotationCCAM
  private $paramCCAM=array();
  //\\\\\\\\\\\\\\\\\\\\\\\\ CCAM \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

  // Constructeur.
  function __construct ( $type, $table, $idpatient, $export='' ) {
    global $session ;
    global $options ;
    global $patient ;
    global $listeMedCCAM ;
    global $idpat ;
    $idpat = $idpatient ;

    $this->isIMHBusy = false ;
    
    if ( $_REQUEST['FormX_to_open_'] ) {
  		$_REQUEST['FoRmX_chooseNew'][] = $_REQUEST['FormX_to_open_'] ;
  		$_POST['FoRmX_chooseNew'][] = $_REQUEST['FormX_to_open_'] ;
  		$_GET['FoRmX_chooseNew'][] = $_REQUEST['FormX_to_open_'] ;
  		unset ( $_REQUEST['FormX_to_open_'] ) ;
  	}
    //eko ( $_POST ) ;
    // Initialisation des attributs.
    $this->type = $type ;
    $this->export = $export ;
    if ( $type == "UHCD" ) $this->type = "Presents" ;
    if ( $type == "Pédiatrie" ) $this->type = "Presents" ;
    $this->table = $table ;
    $this->idpatient = $idpatient ;
    $listeMedCCAM = new clCCAMListesComplexes ( "ListeMédecins" ) ;
    //$listeBilan = new clListes ( "ListesBilans" ) ;
    //eko ( $listeBilan -> getListeItems ( 'Bilan Standard', 1, 0, '*AUCUNITEM*', 1 ) ) ; 
    // En fonction de l'état actuel (sortie en cours, entrée, visualisation...), on initialise
    // l'attribut patient avec l'instanciation d'un objet de la classe clPatient.
    if ( ( $_POST['AnnulerSortie'] OR $_POST['AnnulerSortie_x'] ) AND $session -> getDroit ( $this->type."_EtatCivil", "d" ) ) {
      $this->patient = new clPatient ( $this->idpatient, "Sortis" ) ;
      $session->setLogSup ( "Annulation de la sortie" ) ;
    } elseif ( ( $_POST['Valider'] OR $_POST['Valider_x'] OR $_POST['ValiderMaintenant'] ) AND $session -> getNavi ( 3 ) == "modDateSortie" AND $session -> getDroit ( $this->type."_EtatCivil", "d" ) ) {
      $this->patient = new clPatient ( $this->idpatient, "Presents" ) ;
      if ( $_POST['Valider'] == "Forcer" OR $_POST['Valider_x'] == "Forcer" ) {
      	$session->setLogSup ( "Sortie forcée" ) ;
      	// eko ( "coucou" ) ;
      	$_REQUEST['sendMessage3'] = "Mails Sortie forcée" ; 
      } else $session->setLogSup ( "Sortie" ) ;
    } else {
      $this->patient   = new clPatient ( $idpatient, $this->type ) ;
    }
    // On change l'état de la case 'etatUHCD' si c'est demandé.
    if ( $session->getNavi ( 3 ) == 'changerEtatUHCD' ) {
    	$this->patient->setAttribut ( 'EtatUHCD', '' ) ;
    	if ( $this->patient->getUF ( ) == $options->getOption ( "numUFUHCD" ) ) {
    		$date = new clDate ( ) ;
    		$data['idpatient'] = $this->patient->getID ( ) ;
			$data['idu'] = $this->patient->getIDU ( ) ;
			$data['ilp'] = $this->patient->getILP ( ) ;
			$data['nsej'] = $this->patient->getNSej ( ) ;
			$data['uf'] = $options->getOption ( 'numUFexec' ) ;
			$data['nom'] = $this->patient->getNom ( ) ;
			$data['prenom'] = $this->patient->getPrenom ( ) ;
			$data['dest_attendue'] = $this->patient->getDestinationAttendue ( ) ;
			$data['type'] = 'UHCD' ;
			$data['date'] = $date->getDatetime ( ) ;
			$data['action'] = 'Annulation du passage en UF UHCD' ;
			$data['iduser'] = $session->getUid ( ) ;
			$requete = new clRequete ( BDD, 'bal', $data ) ;
      		$requete->addRecord ( ) ;
    		$this->patient->setAttribut ( 'UF', $options->getOption ( 'numUFexec' ) ) ;
    		$session->setLogSup ( 'Annulation du passage en UF UHCD' ) ;
    	} else $session->setLogSup ( 'Annulation de l\'état UHCD' ) ;
    	global $stopAffichage ;
    	$stopAffichage = 1 ;
    	header ( 'Location:?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)) ) ;
    	
    }    	
    // Impression
    if ( $session->getNavi ( 3 ) == "impressionEtiquettes" ) {
    	$eti = new clEtiquettes ( $options -> getOption ( "ModuleEtiquettes" ), $this->patient ) ;
    }
    $this->defineParamCCAM ( ) ;
    $patient = $this->patient ;
    // Génération de la fiche patient.
    if ( $this->patient->getIDU ( ) AND ! $export ) {
      $this->genFiche ( ) ;

	  //si enquetes
	 $enquetes = clTuFormxTrigger::getWatcher($this->patient);
	 $enquetes->launchTriggers();
	 if( $this->isIHMDispo() ) 	$this->af .= $enquetes->getHtml();

	} else
      $this->af .= "Le patient spécifié est introuvable dans cette liste." ;


	 


  //$this->Netforce ( );
  //$this->Netforce2 ( );
	
  }
  
  // Script qui permet d'ecrire dans MBTV2 les ATU depuis le 01-01-2008 jusqu'a .....
  // (en fonction de leur idu).
  // Fonction de secour
  // Utilisé par Damien Borel ou Derock François
  function Netforce2 ( ) {
 
  	global $session;
  	global $options;
  
    if ( strcmp($session->getNom( ),"ADMIN") == 0 || strcmp($session->getNom( ),"BOREL") == 0  || strcmp($session->getNom( ),"DEROCK") == 0 ) {
      eko("NETFORCE2");
      
      // On prend tous les patients depuis le 01 Janvier 2008
      $requete         = new clResultQuery();
      $listePatient    = $requete->Execute("Fichier","CCAM_Netforce",array(),"ResultQuery");
      
      //eko($listePatient);
      //$requete       = new clResultQuery;
      //unset($paramRq);
      //$paramRq["cw"] = "libelleActe='ATU'";
      //$codeNGAPATU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  
      for ( $i = 0 ; $i < $listePatient["INDIC_SVC"][2]; $i++ ) {
      //for ( $i = 0 ; $i < 2 ; $i++ ) {
        
        $idEvent = $listePatient["idEvent"][$i];
        
        // On prend tous les patients entree depuis le 01 Janvier 2008
        $requete          = new clResultQuery();
        $tab["idpatient"] = $idEvent;
        $listePatient2    = $requete->Execute("Fichier","CCAM_Netforce2",$tab,"ResultQuery");
          
        $date_entree      = $listePatient2["dt_admission"][0];
        //eko($date_entree);
        list($dateActe,$heureActe) = explode(" ",$date_entree);
        list($annee,$mois,$jour)   = explode("-",$dateActe);
        //eko($annee);
        if ( $annee == "2008"  ) {
          //eko($heureCalcule);
          //eko($idu_du_patient_en_question);
          $this->patient = new clPatient ( $idEvent, "Sortis" ) ;
          //eko (  $this->patient->getNom() );
          $this->defineParamCCAM ( ) ;
          //eko($this->paramCCAM);
        
          /*$dateEvent   = $this->paramCCAM["dateEvent"];
          $dtFinInterv = $this->paramCCAM["dtFinInterv"];
          $clDateDeb   = new clDate($dateEvent);
          $clDateFin   = new clDate($dtFinInterv);
      
          if ( strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure médiane")==0 ) {
            $nbHeures     = $clDateFin->getDifference($clDateDeb)/3600;
            $dateMediane  = $clDateDeb->addHours($nbHeures/2);
            $dateMediane  = $clDateDeb->getDate("Y-m-d H:i:s");
            $heureCalcule = $dateMediane;
            //eko($dateEvent);
            //eko($dtFinInterv);
            //eko($heureCalcule);
            }
          elseif (strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure d'admission")==0) {
            $clDateAdm    = new clDate ( $this->dtAdmission );
            $heureCalcule = $clDateAdm->getDate("Y-m-d H:i:s");
            }
          elseif (strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure d'examen")==0) {
            $clDateAdm    = new clDate ( $this->dateEvent );
            $heureCalcule = $clDateAdm->getDate("Y-m-d H:i:s");
            }
          elseif (strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure de sorti")==0) {
            $clDateAdm    = $clDateFin;
            $heureCalcule = $clDateAdm->getDate("Y-m-d H:i:s");
            }
          //eko($heureCalcule);
          list($dateActe,$heureActe)=explode(" ",$heureCalcule);
          list($annee,$mois,$jour)=explode("-",$dateActe);
        
          if ( $annee == "2008" ) {
          
            $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
          //eko($maxIdentifiant);
          // On prepare la nouvelle ligne à inserer dans la table ccam_actes_domaine
          unset($paramRq);
          $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
          $paramRq["idEvent"]             =   $this->paramCCAM["idEvent"];
          $paramRq["dateEvent"]           =   date("Y-m-d H:i:")."00";
          $paramRq["idDomaine"]           =   CCAM_IDDOMAINE;
          $paramRq["dtFinInterv"]         =   $this->paramCCAM["dtFinInterv"];
          $paramRq["idu"]                 =   $this->paramCCAM["idu"];
          $paramRq["ipp"]                 =   $this->paramCCAM["ipp"];
          $paramRq["nomu"]                =   $this->paramCCAM["nomu"];
		      $paramRq["pren"]                =   $this->paramCCAM["pren"];
		      $paramRq["sexe"]                =   $this->paramCCAM["sexe"];
		      $paramRq["dtnai"]               =   $this->paramCCAM["dtnai"];
          $paramRq["dateDemande"]         =   $heureCalcule;
          $paramRq["typeAdm"]             =   $this->paramCCAM["typeAdm"];
		      $paramRq["lieuInterv"]          =   $this->paramCCAM["lieuInterv"];
		      $paramRq["numUFdem"]            =   $this->paramCCAM["numUFdem"];	
		      $paramRq["numSejour"]           =   $this->paramCCAM["nsej"];
          $paramRq["type"]                =   "ACTE";
          $paramRq["Urgence"]             =   "O"; 
          
          if ( $this->paramCCAM["matriculeIntervenant"] == "" )
            $this->paramCCAM["matriculeIntervenant"] =  $options->getOption('codeAdeliChefService'); 

          $paramRq["matriculeIntervenant"]=   $this->paramCCAM["matriculeIntervenant"]; 
          
          $paramRq["nomIntervenant"]      =   $this->paramCCAM["nomIntervenant"];
          $paramRq["numUFexec"]           =   $this->paramCCAM["numUFdem"];
          $paramRq["codeActe"]            =   $codeNGAPATU["idActe"][0];
          $paramRq["libelleActe"]         =   "ATU";
          $paramRq["cotationNGAP"]        =	  "ATU 1";
          $paramRq["codeActivite4"]       =   "";
          $paramRq["modificateurs"]       =   "";
          $paramRq["categorie"]           =   "";
          $paramRq["extensionDoc"]        =   "";
          $paramRq["validDefinitive"]     =   "";
          $paramRq["quantite"]            =   "1";
          $paramRq["periodicite"]         =   "aucune";
          $paramRq["lesionMultiple"]      =   "Non";
          $paramRq["envoi_facturation"]   =   1;
          $paramRq["envoi_nomIntervenant"]        =   $this->paramCCAM["nomIntervenant"];
          $paramRq["envoi_matriculeIntervenant"]  =   $this->paramCCAM["matriculeIntervenant"];
      
          //eko($paramRq);

          // Insertion de la nouvelle ligne
          $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
          $requete -> addRecord();
      */
          $cot = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;

          unset($paramRq);
          $paramRq["cw"] = "idEvent=".$listePatient["idEvent"][$i]." and libelleActe='ATU'";
          $requete       = new clResultQuery;
          $res           = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");

          //eko($res);
      
          if ( $options->getOption('EnvoiATURegleFacturation') ) {
            
            if ( $this->patient->getTypeDestination() == "H" || $this->patient->ifInUHCD ( ) == 1 )
              $rien = 0;
            else {
              $type="creation";
              $cot->contenuBAL_Netforce($res,$type);
            }
          } //if ( $options->getOption('EnvoiATURegleFacturation') )
          else {
            $type="creation";
            $cot->contenuBAL_Netforce($res,$type);
          }
          } //if ( $annee == "2008"  )
        }
      eko("NETFORCE2");
    } // for ( $i = 0 ; $i < $listePatient["INDIC_SVC"][2]; $i++ )
  }
  
  // Script qui permet d'ecrire dans MBTV2 suite à une sortie des patients en crash
  // (en fonction de leur idu).
  // Fonction de secour
  // Utilisé par Damien Borel ou Derock François
  function Netforce ( ) {
 
  	global $session;
  	global $fusion;
  
    if ( strcmp($session->getNom( ),"BOREL") == 0  || strcmp($session->getNom( ),"DEROCK") == 0 ) {
      //$list = array ('95306','95316');
      eko ( "NETFORCE - DEB" ) ;
      $req = new clResultQuery ;
      $param['table'] = PSORTIS ;
      $param['cw'] = "WHERE dt_sortie LIKE '2008-06-28%' OR dt_sortie LIKE '2008-06-29%'" ;
      $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      for ( $i = 0 ; isset ( $res['idu'][$i] ) ; $i++ ) {
      //while ( list(, $idu_du_patient_en_question) = each ($list) ){
        eko($res['idu'][$i]) ;
        $this->patient   = new clPatient ( $res['idu'][$i], "Sortis" ) ;
        $this->defineParamCCAM ( ) ;
        //eko($this->paramCCAM);
        $fusion = 0;
        $cot = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
        $cot -> writeBALSorti ( ) ;
      }
      eko ( "NETFORCE - FIN") ;
    }
  }

	// Modification des patients manuels.
  	function modPatient ( ) {
 		global $session ;
 		
 		if ( $_POST['validerModPat'] == "Modifier" ) {
 			$session->setLogSup ( 'Modification de l\'identité du patient "'.$this->patient->getNom().' '.$this->patient->getPrenom().' ('.$this->patient->getID().')"' ) ;
 			//eko ( "Modification effectuée." ) ;
 			if ( $_POST['ipp'] ) 		$this->patient->setAttribut ( 'IPP', 		$_POST['ipp'] ) ;
 			if ( $_POST['nsej'] ) 		$this->patient->setAttribut ( 'NSej', 		$_POST['nsej'] ) ;
 			if ( $_POST['sexe'] ) 		$this->patient->setAttribut ( 'Sexe', 		$_POST['sexe'] ) ;
 			if ( $_POST['nom'] ) 		$this->patient->setAttribut ( 'Nom', 		$_POST['nom'] ) ;
 			if ( $_POST['prenom'] ) 	$this->patient->setAttribut ( 'Prenom', 	$_POST['prenom'] ) ;
 			$date = new clDate ( $_POST['naissance'] ) ;
 			if ( $_POST['naissance'] ) 	$this->patient->setAttribut ( 'Naissance', 	$date->getDatetime () ) ;
 			if ( $_POST['adresse'] ) 	$this->patient->setAttribut ( 'Adresse', 	$_POST['adresse'] ) ;
 			if ( $_POST['cp'] ) 		$this->patient->setAttribut ( 'CP', 		$_POST['cp'] ) ;
 			if ( $_POST['ville'] ) 		$this->patient->setAttribut ( 'Ville', 		$_POST['ville'] ) ;
 			if ( $_POST['tel'] ) 		$this->patient->setAttribut ( 'Tél', 		$_POST['tel'] ) ;
 			$this->patient   = new clPatient ( $this->patient->getID(), $this->type ) ;
 		} elseif ( $_POST['validerModPat'] != "Annuler" ) {
 			// Chargement du template ModeliXe.
			$mod = new ModeliXe ( "modPatient.html" ) ;
			$mod -> SetModeliXe ( ) ;
			$dateN = new clDate ( $this->patient->getDateNaissance() ) ;
			$mod ->  MxFormField ( "ipp", 'text', 'ipp', $this->patient->getILP()," class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "nsej", 'text', 'nsej', $this->patient->getNSej()," class=\"formPatient\"" ) ;
			//$mod ->  MxFormField ( "sexe", 'text', 'sexe', $this->patient->getSexe()," class=\"formPatient\"" ) ;
			$data['M'] = "Homme" ; $data['F'] = "Femme" ; $data['I'] = "Indéterminé" ;
			$mod -> MxSelect ( 'listeSexes', 'sexe', $this->patient->getSexe(), $data ) ;
			$mod ->  MxFormField ( "nom", 'text', 'nom', $this->patient->getNom()," class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "prenom", 'text', 'prenom', $this->patient->getPrenom()," class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "naissance", 'text', 'naissance', $dateN->getDate('d/m/Y'),"id=\"naissance\" class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "adresse", 'text', 'adresse', $this->patient->getAdresse()," class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "cp", 'text', 'cp', $this->patient->getCodePostal()," class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "ville", 'text', 'ville', $this->patient->getVille()," class=\"formPatient\"" ) ;
			$mod ->  MxFormField ( "tel", 'text', 'tel', $this->patient->getTel()," class=\"formPatient\"" ) ;
						
   			// Récupération du code HTML généré.  
   			$mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), $session->getNavi(3) ) ) ;
			return $mod -> MxWrite ( "1" ) ;
 		}
   	}

  function defineParamCCAM ( ) {
    $date = new clDate ( $this->patient->getDateNaissance ( ) ) ;
    $this->paramCCAM[nomu]                 = $this->patient->getNom ( ) ;
    $this->paramCCAM[pren]                 = $this->patient->getPrenom ( ) ;
    $this->paramCCAM[sexe]                 = $this->patient->getSexe ( ) ;
    $this->paramCCAM[dtnai]                = $date->getDate ( "Y-m-d" ) ;
    $this->paramCCAM[idEvent]              = $this->patient->getID ( ) ;
    $this->paramCCAM[dateAdmission]        = $this->patient->getDateAdmission ( ) ;
    $this->paramCCAM[dateEvent]            = $this->patient->getDateExamen ( ) ;
    $this->paramCCAM[dtFinInterv]          = $this->patient->getDateSortie ( ) ;
    $this->paramCCAM[idu]                  = $this->patient->getIDU ( ) ;
    $this->paramCCAM[ipp]                  = $this->patient->getILP ( ) ;
    $this->paramCCAM[nsej]                 = $this->patient->getNSej ( ) ;
    $this->paramCCAM[typeAdm]              = $this->patient->getTypeAdmission ( ) ;
    $this->paramCCAM[lieuInterv]           = $this->patient->getSalle ( ) ;
    $this->paramCCAM[matriculeIntervenant] = $this->patient->getMatriculeMedecin ( ) ;
    $this->paramCCAM[typeIntervenant]      = $this->patient->getTypeMedecin ( ) ;
    $this->paramCCAM[numUFdem]             = $this->patient->getUF ( ) ;
    $this->paramCCAM[typeListe]            = $this->type ;
    $this->paramCCAM[nomIntervenant]       = $this->patient->getMedecin ( ) ;
    $this->paramCCAM[nomIDE]               = $this->patient->getIDE ( ) ;
    $this->paramCCAM[matriculeIDE]         = $this->patient->getMatriculeIDE ( ) ;
    $this->paramCCAM[manuel]               = $this->patient->getManuel ( ) ;
    $this->paramCCAM[uf]				   = $this->patient->getUF ( ) ;
    $this->paramCCAM[etatUHCD]			   = $this->patient->getEtatUHCD ( ) ;
    $this->paramCCAM[typeDest]			   = $this->patient->getTypeDestination ( ) ;
    $this->paramUHCD[dateUHCD]			   = $this->patient->getDateUHCD ( ) ;
    
    
    // Ajout de la destination souhaitée
    $this->paramCCAM[dest_attendue] = $this->patient->getDestinationAttendue ( ) ;
  }

  // On affiche les différents blocs de la fiche.
  function genFiche ( ) {
    global $options ;
    global $session ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "FichePatient.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    //    eko ( "ça fonctionne" ) ;
    // Modification identité
    if ( $session->getNavi ( 3 ) == "modPatient" ) {
    	$modP = $this->modPatient ( ) ;
    }
    // Appel
    if ( ( $_REQUEST['act_print'] OR $_REQUEST['act_print_x'] ) AND $_REQUEST['Formulaire2print'] ) {
    	// eko ( "Impression du formulaire blablabla" ) ;
    	$t = new clDemandeBons ( $this->patient->getIDU ( ), $this->patient->getNsej ( ) ) ;
    }


    //eko ( $_REQUEST ) ;
    // Initialisation des différents éléments du template.
    if ( $session->getDroit ( $this->type."_EtatCivil", "r" ) ) $mod -> MxText ( "etatcivil", $modP.$this->EtatCivil ( ) ) ;
    if ( $session->getDroit ( $this->type."_Transfert", "r" ) ) $mod -> MxText ( "transfert", $this->Transfert ( ) ) ;
    if ( $options->getOption ( "ModuleBMR" ) )
      $mod -> MxText ( "bmr", $this->VerifBMR ( ) ) ;
    if ( $session->getDroit ( $this->type."_Informations", "r" ) ) $mod -> MxText ( "informations", $this->Informations ( ) ) ;

	 if ( ( $options->getOption ( "ModuleHistorique" ) == "ProvenanceHistorique" || $options->getOption ( "ProvenanceHistorique" ) == "as400" ) && $session->getDroit ( $this->type."_Historique", "r" ) ) {
      //eko('pipoun');
      $mod -> MxText ( "historique", $this->Historique ( ) ) ;
      $mod -> MxText ( "historiquedocs", "" ) ;
    } else if ( $options->getOption ( "ModuleHistorique" ) == "Oracle" && $session->getDroit ( $this->type."_Historique", "r" ) ) {
      $mod -> MxText ( "historique", $this->Historique ( ) ) ;
      $mod -> MxText ( "historiquedocs", $this->HistoriqueDocs ( ) ) ;
    } elseif ( $options->getOption ( "ModuleHistorique" ) == "Urgences" && $session->getDroit ( $this->type."_Historique", "r" ) ) {
      $mod -> MxText ( "historique", $this->HistoriqueUrgences ( ) ) ;
      $mod -> MxText ( "historiquedocs", "" ) ;
    } elseif ( $options->getOption ( "ModuleHistorique" ) == "OracleHisto" && $session->getDroit ( $this->type."_Historique", "r" ) ) {
      $mod -> MxText ( "historique", $this->Historique ( ) ) ;
      $mod -> MxText ( "historiquedocs", "" ) ;

    } else {
      $mod -> MxText ( "historique", "" ) ;
      $mod -> MxText ( "historiquedocs", "" ) ;
    }
	

    //$mod -> MxText ( "historique", XhamTools::genFenetreBloquante ( 'test.html' ) ) ;
    if ( $session->getDroit ( $this->type."_Messages", "r" ) ) $mod -> MxText ( "messages", $this->Messages ( ) ) ;
    if ( $options->getOption('ModuleDocuments') AND $session->getDroit ( $this->type."_Documents", "r" ) ) $mod -> MxText ( "documents", $this->Documents ( ) ) ;
    
    if ( $options->getOption ( "ActivationAppels" ) ) {
    	$ac = new XhamBluePig ( $this->patient->getIDU ( ),$this->patient->getILP ( ),$this->patient->getNsej ( ),$this->patient->getUF ( ) ) ;
    	$ac -> genBarre ( ) ;
    	$mod -> MxText ( "appelsContextuels", $ac->getAffichage ( ) ) ;
    }




































    ////////////////////////// CCAM /////////////////////////////
    // Invocation du module de cotation des actes CCAM
    /*
    if (($_POST['DetailDiagsActes_x'] or $session->getNavi(3)=="DetailDiagsActes") AND $options->getOption('CCAMExterne')) {
    	$bloq = XhamTools::genFenetreBloquante ( "CCAM_SaisieExterne.mxt" ) ;
    } else $bloq = '' ;
    */
    $bloq = '' ;
    $this->defineParamCCAM ( ) ;
    if ( $options -> getOption ( "Module_CCAM" ) and ($session -> getDroit ( "CCAM_ACTES_".$this->type,"r" ) or $session -> getDroit ( "CCAM_CONSULT_".$this->type,"r" )) and $this->patient->getDateExamen ( ) <> "0000-00-00 00:00:00" and $this->patient->getMatriculeMedecin ( ) ) {

      $cotationActes = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
      $mod -> MxText ( "cotationCCAM", $cotationActes -> cotationActes ( ).$bloq ) ;

    //tweak : cervetti
	//si sortie de ccam détectée, redirection vers une url saine qui ne se cumule pas le "ccam" en navi3 (bugogène )
	//je le fais ici pour éviter de modifier la classe cacam
	if( ( isset($_POST['lesion']) && isset($_POST['sortir_x'] ) ) || (isset($_POST['listeGauche'])   &&  isset($_POST['sortir_x']) ) ) {
		$saneNavi = $session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2)) ;
		header('Location: '.URLNAVI.$saneNavi);
		die ;
	}

    }    
    //////////////////////// FIN CCAM ///////////////////////////
    if ( $_POST['sendMessage'] ) { $_POST['type'] = "virus" ; $_POST['Envoyer'] =  1 ; $this->newMessage ( 'Mails Alerte Virus' ) ; }
    $mod -> MxText ( "blocnote", $this->mainCourante ( ) ) ;
    $pedi = $options -> getOption ( "FiltreSalleSup" ) ;
    //eko ( $pedi ) ;
    if ( $this->type != 'Sortis' AND ! ereg ( $pedi, $this->patient->getSalle ( ) ) )
    	$mod -> MxText ( "uhcd", $this->genUHCD ( ) ) ;
	$iframe = array ( '', '', '', '', '', '', '', '', '', '' ) ;
    for ( $i = 1 ; $i < 10 ; $i++ ) {
	    switch ( $options -> getOption ( "Iframe".$i."_Position" ) ) {
	    	case 'Av-H':
	    		$iframe[1] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Ap-H':
				$iframe[2] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Av-D':
	    		$iframe[3] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Ap-D':
	    		$iframe[4] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Av-A':
	    		$iframe[5] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Ap-A':
	    		$iframe[6] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Av-B':
	    		$iframe[7] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Ap-B':
	    		$iframe[8] .= $this->genIframe ( $i ) ;
	    	break;
	    	case 'Ap':
	    		$iframe[9] .= $this->genIframe ( $i ) ;
	    	break;
	    	default:
	    		$iframe[0] .= $this->genIframe ( $i ) ;
	    	break;
	    }
	    
    }
    $mod -> MxText ( "iframe1", $iframe[1] ) ;
    $mod -> MxText ( "iframe2", $iframe[2] ) ;
    $mod -> MxText ( "iframe3", $iframe[3] ) ;
    $mod -> MxText ( "iframe4", $iframe[4] ) ;
    $mod -> MxText ( "iframe5", $iframe[5] ) ;
    $mod -> MxText ( "iframe6", $iframe[6] ) ;
    $mod -> MxText ( "iframe7", $iframe[7] ) ;
    $mod -> MxText ( "iframe8", $iframe[8] ) ;
    $mod -> MxText ( "iframe9", $iframe[9] ) ;
    $mod -> MxText ( "iframe", $iframe[0] ) ;
    $mod -> MxText ( "formx", $this->genBlocFormx ( ) ) ;
    
    global $fenetreBloquante ;
    if ( $fenetreBloquante ) $mod -> MxText ( "fenetreBloquante", $fenetreBloquante ) ;
    
    // Récupération du code HTML généré.  
    $this->af .= $mod -> MxWrite ( "1" ) ;
  }
  
    // Iframe
  	function genIframe ( $i ) {
  		global $session ;
  		global $options ;
  		$af = '' ;
  		$typ = $options -> getOption ( 'Iframe'.$i.'_Position' ) ;
  		$sho = $options -> getOption ( 'Iframe'.$i.'_Show' ) ;
  		$url = $options -> getOption ( 'Iframe'.$i.'_URL' ) ;
  		$par = $options -> getOption ( 'Iframe'.$i.'_Par' ) ;
  		$wid = $options -> getOption ( 'Iframe'.$i.'_Lx' ) ;
  		$hei = $options -> getOption ( 'Iframe'.$i.'_Ly' ) ;
  		$pox = $options -> getOption ( 'Iframe'.$i.'_Px' ) ;
  		$poy = $options -> getOption ( 'Iframe'.$i.'_Py' ) ;
  		$idf = $options -> getOption ( 'Iframe'.$i.'_ID' ) ;
		// eko ( "$typ $sho $url $par $wid $hei $pox $poy $idf" ) ;
  		if ( $sho AND $url ) {
  			if ( $wid ) $width  = 'width: ' .$wid.'px;' ; else $width  = '' ;
  			if ( $hei ) $height = 'height: '.$hei.'px;' ; else $height = '' ;
  			if ( $pox ) $left   = 'left: '  .$pox.'px;' ; else $left   = '' ;
  			if ( $poy ) $top    = 'top: '   .$poy.'px;' ; else $top    = '' ;
  			if ( $idf ) $id     = 'id="'    .$idf.'"'   ; else $id     = '' ;
  			if ( $par ) {
  				// A.Falanga - 19-09-07 - Prise en charge du ? dans une URL déja existante
                $pos = strpos($url, '?');
                if ($pos==false) { $toadd='?'; } else { $toadd='&'; }
  				
  				$params = $toadd.'ilp='.$this->patient->getILP().'&idpatient='.$this->patient->getID()
  				.'&idu='.$this->patient->getIDU().'&nsej='.$this->patient->getNSej() ;
  			} else $params = '' ;
  			if ( $pox OR $poy ) $pos = 'position: absolute;' ; else $pos = '' ; 
  			if ( $typ != 'Libre' ) {
  				$pos = '' ;
  			} 
  			if ( $typ == 'Ap' AND ! $wid ) {
  				$width = "width: 100%;" ;
  			}
  			$af = '<iframe '.$id.' src="'.$url.$params.'" style="border: 1px solid #006699; background-color: #FFFF99; '.$pos.$width.$height.$left.$top.'"></iframe>' ;
  		}
  		return $af ;
  	}

	// Gestion des Soins continus
	function genSC ( ) {
		global $session ;
  		global $options ;
  		$ufExec = $options->getOption ( 'numUFexec' ) ;
		$ufUHCD = $options->getOption ( 'numUFUHCD' ) ;
		$ufSC   = $options->getOption ( 'numUFSC' ) ;
		$af = '' ;
  		if ( $options -> getOption ( "GestionSC" ) ) {
  			if ( $this->patient->getTISS ( ) >= $options->getOption ( "minTISSforSC" ) ) {
  				if ( ! $this->patient->isSoinsContinus ( ) ) {
    				$param['table'] = PPRESENTS ;
    				$param['cw'] = "WHERE uf='$ufSC'" ;
    				$req = new clResultQuery ;
    				$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ; 
    				eko ( $res['INDIC_SVC'] ) ;
  					if ( $res['INDIC_SVC'][2] < $options->getOption ( "maxSC" ) ) {
  						$session->setLogSup ( 'Passage en soins continus' ) ;
  						$this->addBAL ( 'sc', 'sc' ) ;
						$this->patient->setAttribut ( 'UF', $ufSC ) ;
						$this->patient->setAttribut ( 'EtatUHCD', 'nonSC' ) ;
						header ( 'Location:index.php?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)) ) ;
  					}
  				}
  			} else {
  				if ( $this->patient->isSoinsContinus ( ) ) {
  					$session->setLogSup ( 'Annulation du passage en soins continus' ) ;
  					$this->addBAL ( 'asc', 'asc' ) ;
					$this->patient->setAttribut ( 'UF', $ufExec ) ;
					$this->patient->setAttribut ( 'EtatUHCD', '' ) ;
					header ( 'Location:index.php?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)) ) ;
  				}
  			}
  		}
  		return $af ;
	}

	// Gestion de l'UHCD
	function genUHCD ( ) {
		global $options ;
		global $session ;
        global $logs ;
		
		// Appel du module de soins continus
		$this->genSC ( ) ;
		
		// Si le patient n'est pas en soins continus, alors on gère l'UHCD.
		if ( ! $this->patient->isSoinsContinus ( ) AND $options -> getOption ( 'GestionUHCD' ) ) {
		
			$ufExec       = $options->getOption ( 'numUFexec' ) ;
			$ufUHCD       = $options->getOption ( 'numUFUHCD' ) ;
			$ufUHCDrepere = $options->getOption ( 'numUFUHCDrepere' ) ;
			$salleUHCD    = $options -> getOption ( "FiltreSalleUHCD" ) ;
			$uf           = $options->getOption ( 'numUFexec' ) ;
			$etat         = $this->patient->getEtatUHCD ( ) ;
			$oldUF        = $this->patient->getUF ( ) ;
			
			
			if ( $options -> getOption ( 'GestionUHCDCode' ) == 'CCMU' ) 
				$codeG  = $this->patient->getCCMU ( ) ;
			else $codeG  = $this->patient->getCodeGravite ( ) ;
			
			// Gestion des changements de salle (UHCD et UHCD repéré)
			if ( $ufUHCDrepere AND $ufUHCDrepere != $ufUHCD ) {
				if ( $oldUF == $ufUHCDrepere AND ereg ( $salleUHCD, $this->patient->getSalle ( ) ) ) {
					if ( $_POST['dateUHCD'] == 'now' ) $date = new clDate ( ) ;
					else $date = new clDate ( $_POST['dateUHCD'] ) ;
					$dateC = $date -> getDatetime ( ) ;
					$this->addBAL ( '', 'uhcd' ) ;
					$this->patient->setAttribut ( 'DateUHCD', $dateC ) ;					
					$this->patient->setAttribut ( 'UF', $ufUHCD ) ;
					global $stopAffichage ;
					$stopAffichage = 1 ;
					header ( 'Location:index.php?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2))) ;
				} elseif ( $oldUF == $ufUHCD AND ! ereg ( $salleUHCD, $this->patient->getSalle ( ) ) ) {
					if ( $_POST['dateUHCD'] == 'now' ) $date = new clDate ( ) ;
					else $date = new clDate ( $_POST['dateUHCD'] ) ;
					$dateC = $date -> getDatetime ( ) ;
					$this->addBAL ( '', 'uhcdrepere' ) ;
					$this->patient->setAttribut ( 'DateUHCD', $dateC ) ;					
					$this->patient->setAttribut ( 'UF', $ufUHCDrepere ) ;
					global $stopAffichage ;
					$stopAffichage = 1 ;
					header ( 'Location:index.php?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2))) ;
				}
			}
			
			
			// Calcul du critère CCMU4 ou CCMU5
			if ( $codeG == 4 OR $codeG == 5 )
			$CCMU45 = 1 ; else $CCMU45 = 0 ;
			
	   if ( $rep == 'okCCMU45' ) {
                //eko ( "oldUF : $olfUF - ufUHCD : $ufUHCD - ufUHCDrepere : $ufUHCDrepere") ;
                if ( $oldUF != $ufUHCD AND $oldUF != $ufUHCDrepere ) {
                    if ( ! $ufUHCDrepere OR ereg ( $salleUHCD, $this->patient->getSalle ( ) ) ) {
                        $this->addBAL ( $rep, 'uhcd' ) ;
                        $logs -> addLog ( "uhcd", $session->getNaviFull ( ), "Passage automatique en UHCD (code > 3)" ) ;
                        $this->patient->setAttribut ( 'DateUHCD', $dateC ) ;
                        $this->patient->setAttribut ( 'UF', $ufUHCD ) ;
                    } else {
                        $this->addBAL ( $rep, 'uhcdrepere' ) ;
                        $logs -> addLog ( "uhcd", $session->getNaviFull ( ), "Passage automatique en UHCD repéré (code > 3)" ) ;
                        $this->patient->setAttribut ( 'DateUHCD', $dateC ) ;
                        $this->patient->setAttribut ( 'UF', $ufUHCDrepere ) ;
                    }
                }
                $this->patient->setAttribut ( 'EtatUHCD', $rep ) ;
                global $stopAffichage ;
                $stopAffichage = 1 ;
                header ( 'Location:index.php?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)).$sm ) ;
            }


			// Calcul du critère sur la durée
			$datead = new clDate ( $this->patient->getDateAdmission ( ) ) ;
		  	$dureead = new clDuree ( ) ;
		  	$duree = $dureead -> getDureeCourte ( $datead -> getDatetime ( ) ) ;
		  	$dureeMaxSansUHCD = 3600 * $options -> getOption ( "Présents UHCD" ) ;
		  	$dureeHeure = $options -> getOption ( "Présents UHCD" ) ;
			if ( $dureead -> getSeconds ( ) > $dureeMaxSansUHCD ) $duree = 1 ;
			else $duree = 0 ;
			
			// Calcul du critère sur le CCMU3
			if ( $codeG == 3 ) $CCMU3 = 1 ; else $CCMU3 = 0 ;
			
			// Vérification des critères directs.
			if ( $CCMU45 ) {
				$rep = 'okCCMU45' ;
				$uf = $ufUHCD ;
                $logs -> addLog ( "uhcd", $session->getNaviFull ( ), "Passage automatique en UHCD (code > 3)" ) ;
			} elseif ( $this->patient->getTypeDestination ( ) == 'T' ) {
				$rep = 'okTransfert' ;
				$uf = $ufUHCD ;
			} elseif ( $this->patient->getTypeDestination ( ) == 'D' ) {
				$rep = 'okDeces' ;
				$uf = $ufUHCD ;
			} else {
				if ( $duree ) {
					$rep = 'okDuree' ;
				} else {
					if ( $CCMU3 ) {
						$rep = 'okCCMU3' ;
					} else {
						$rep = 'noCCMU3' ;
						$uf = $ufExec ;
					}
				}
			}
			
			// Vérification de la réponse à la question sur les critères UHCD.
			if ( $rep == 'okDuree' ) {
				if ( $_POST['valider'] == 'Oui' ) {
					$session->setLogSup ( 'UHCD réponse : Oui' ) ;
                    $logs -> addLog ( "uhcd", $session->getNaviFull ( ), "UHCD réponse : Oui" ) ;
					$rep = 'okCriteres' ;
					$uf = $ufUHCD ;
				} elseif ( $_POST['valider'] == 'Non' ) {
					$session->setLogSup ( 'UHCD réponse : Non' ) ;
					$logs -> addLog ( "uhcd", $session->getNaviFull ( ), "UHCD réponse : Non" ) ;
					$rep = 'noCriteres' ;
					$uf = $ufExec ;
				} elseif ( $etat != 'okCriteres' AND $etat != 'noCriteres' ) {
					$session->setLogSup ( 'Question UHCD' ) ;
					// Chargement du template ModeliXe.
	    			$mod = new ModeliXe ( "ErreurUHCD.html" ) ;
                    $this->setIHMBusy();
	    			$mod -> SetModeliXe ( ) ;
	        		// Récupération du code HTML généré.  
	        		$mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
	    			return $mod -> MxWrite ( "1" ) ;
				} else return '' ;
			}

			// Vérification de la réponse à la question sur les actes lourds.
			if ( $rep == 'okCCMU3' ) {
				if ( $_POST['valider'] == 'Oui' ) {
					$session->setLogSup ( 'UHCD réponse : Oui' ) ;
                    $logs -> addLog ( "uhcd", $session->getNaviFull ( ), "UHCD réponse : Oui" ) ;
					$rep = 'okActes' ;
					$uf = $ufUHCD ;
				} elseif ( $_POST['valider'] == 'Non' ) {
					$session->setLogSup ( 'UHCD réponse : Non' ) ;
					$logs -> addLog ( "uhcd", $session->getNaviFull ( ), "UHCD réponse : Non" ) ;
                    $rep = 'noActes' ;
					$uf = $ufExec ;
				} elseif ( $etat != 'okActes' AND $etat != 'noActes' ) {
					$session->setLogSup ( 'Question UHCD' ) ;
					// Chargement du template ModeliXe.
	    			$mod = new ModeliXe ( "ErreurUHCD.html" ) ;
                    $this->setIHMBusy();
	    			$mod -> SetModeliXe ( ) ;
	        		// Récupération du code HTML généré.  
	        		$mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
	    			return $mod -> MxWrite ( "1" ) ;
				} else return '' ;
			}

			


			// Mise à jour des informations.
			if ( $etat != $rep AND $rep != 'okDuree' AND $rep != 'okCCMU3' ) {
				
				if ( $_POST['dateUHCD'] == 'now' ) $date = new clDate ( ) ;
				else $date = new clDate ( $_POST['dateUHCD'] ) ;
				$dateC = $date -> getDatetime ( ) ;
				if ( $rep == 'okCCMU45' OR $rep == 'okCriteres' OR $rep == 'okActes' OR $rep == 'okTransfert' OR $rep == 'okDeces' ) {
					//eko ( "oldUF : $olfUF - ufUHCD : $ufUHCD - ufUHCDrepere : $ufUHCDrepere") ;
					if ( $oldUF != $ufUHCD AND $oldUF != $ufUHCDrepere ) {
						if ( ! $ufUHCDrepere OR ereg ( $salleUHCD, $this->patient->getSalle ( ) ) ) {
							$this->addBAL ( $rep, 'uhcd' ) ;
                            //$logs -> addLog ( "uhcd", $session->getNaviFull ( ), "Passage automatique en UHCD (code > 3)" ) ;
							$this->patient->setAttribut ( 'DateUHCD', $dateC ) ;					
							$this->patient->setAttribut ( 'UF', $ufUHCD ) ;
						} else {
							$this->addBAL ( $rep, 'uhcdrepere' ) ;
                            //$logs -> addLog ( "uhcd", $session->getNaviFull ( ), "Passage automatique en UHCD repéré (code > 3)" ) ;
							$this->patient->setAttribut ( 'DateUHCD', $dateC ) ;					
							$this->patient->setAttribut ( 'UF', $ufUHCDrepere ) ;	
						}					
					} 
					$this->patient->setAttribut ( 'EtatUHCD', $rep ) ;
				} elseif ( $rep == 'noCCMU3' OR $rep == 'noCriteres' OR $rep == 'noActes' ) {
					if ( $oldUF AND ( $oldUF == $ufUHCD OR $oldUF == $ufUHCDrepere ) ) {
						$this->addBAL ( $rep, 'urg' ) ;
                        $logs -> addLog ( "uhcd", $session->getNaviFull ( ), "Annulation du passage en UHCD (code < 3)" ) ;
						$this->patient->setAttribut ( 'DateUHCD', $dateC ) ;					
						$this->patient->setAttribut ( 'UF', $ufExec ) ;
					}
					$this->patient->setAttribut ( 'EtatUHCD', $rep ) ;
					if ( $rep == 'noCriteres' ) {
						$sm = '&sendMessage2=Mails Alerte UHCD' ;
					}
				}
				if ( ( $_POST['Valider'] OR $_POST['Valider_x'] OR $_POST['ValiderMaintenant'] ) AND $session -> getNavi ( 3 ) == "modDateSortie" AND $session -> getDroit ( $this->type."_EtatCivil", "d" ) ) {
					
				} else {				
					global $stopAffichage ;
	    			$stopAffichage = 1 ;
	    			header ( 'Location:index.php?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)).$sm ) ;
                    print "Erreur de relocation";
                    die ;
				}	
			}
		}
		
	}
	
	function addBAL ( $rep, $type ) {
		global $session ;
		global $options ;
		if ( $_POST['dateUHCD'] == 'now' ) $date = new clDate ( ) ;
		else $date = new clDate ( $_POST['dateUHCD'] ) ;
		if ( ( $_POST['Valider'] OR $_POST['Valider_x'] OR $_POST['ValiderMaintenant'] ) AND $session -> getNavi ( 3 ) == "modDateSortie" AND $session -> getDroit ( $this->type."_EtatCivil", "d" ) )
			eko ( 'Le patient sort' ) ;
		else {
			// eko ( 'il sort pas' ) ;	
			$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
			$data['idpatient'] = $this->patient->getID ( ) ;
			$data['idu'] = $this->patient->getIDU ( ) ;
			$data['ilp'] = $this->patient->getILP ( ) ;
			$data['nsej'] = $this->patient->getNSej ( ) ;
			$data['uf'] = $this->patient->getUF ( ) ;
			$data['nom'] = $this->patient->getNom ( ) ;
			$data['prenom'] = $this->patient->getPrenom ( ) ;
			$data['dest_attendue'] = $this->patient->getDestinationAttendue ( ) ;
			$data['type'] = 'UHCD' ;
			$data['date'] = $date->getDatetime ( ) ;
			if ( $type == 'urg' ) $data['action'] = 'Annulation du passage en UF UHCD' ;
			elseif ( $type == 'asc' ) $data['action'] = 'Annulation du passage en UF Soins Continus' ;
			elseif ( $type == 'sc' ) $data['action'] = 'Passage en UF Soins Continus' ;
			elseif ( $type == 'uhcdrepere') $data['action'] = 'Passage en UF UHCD repéré' ;
			elseif ( $type == 'anonrpu' ) $data['action'] = 'Annulation du passage en UF '.$options->getOption ( 'numUFnonRPU' ) ;
			elseif ( $type == 'nonrpu' ) $data['action'] = 'Passage en UF '.$options->getOption ( 'numUFnonRPU' ) ;
			else $data['action'] = 'Passage en UF UHCD' ;
			$data['iduser'] = $session->getUid ( ) ;
			$requete = new clRequete ( BDD, 'bal', $data ) ;
      		$requete->addRecord ( ) ;
		}
	}

  	// pave "postit"
  	function mainCourante() {
    	global $session;
    	if ( isset ( $_POST['maincourantetexte'] ) ) {
    		$logstr = substr(addslashes($_POST['maincourantetexte']),0,30) ; 
      		$session->setLogSup ( 'Mise à jour du bloc-notes:'.$logstr) ;
      		$data['note'] = stripslashes ( $_POST['maincourantetexte'] ) ;
      		$requete = new clrequete ( BASEXHAM, TABLENOTES, $data ) ;
      		$requete -> uoiRecord ( "ids='".$this->patient->getID()."'" ) ;
    	}
    	$req = new clResultQuery ;
    	$param['cw'] = "WHERE ids='".$this->patient->getID()."'" ;
    	$param['table'] = TABLENOTES ;
    	$res = $req -> Execute ( "Fichier", "getGenXHAM", $param, "ResultQuery" ) ;
    	if ( $res['INDIC_SVC'][2] > 0 )	$message = $res['note'][0] ;
    	$mod = new ModeliXe ( "mainCourante.mxt" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$mod ->  MxFormField ( "contenu", 'textarea', 'maincourantetexte', $message," class=\"maincourante\"" ) ;
    	$mod -> MxHidden ( "hidden1", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
    	return $mod -> MxWrite ( "1" ) ;  
  	}

  // Affiche le tableau contenant la liste des BMR si au moins une alerte
  // est présente.
  function VerifBMR ( ) {
    // Recherche dans la table des BMR avec l'IDU du patient.
    $param['cw'] = "WHERE IDU='".$this->patient->getIDU ( )."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getBMR", $param, "ResultQuery" ) ; 
    
    // S'il y a un résultat, on charge le template.
    if ( $res['INDIC_SVC'][2] ) {
      // Chargement du template ModeliXe.
      $mod = new ModeliXe ( "AlerteBMR.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Pour chaque Germe, on affiche les différentes informations.
      for ( $i = 0 ; isset ( $res['GERME'][$i] ) ; $i++ ) {
	// Nom du germe.
	$mod -> MxText ( "germe.germ", $res['GERME'][$i] ) ;
	// Date de détection.
	$dtPrev = new clDate ( $res['DT_PRELEVT'][$i] ) ;
	$mod -> MxText ( "germe.date", $dtPrev -> getDate ( "d-m-Y" ) ) ;
	// Etablissement.
	$mod -> MxText ( "germe.etab", $res['ETAB'][$i] ) ;
	// Location du germe.
	$mod -> MxText ( "germe.site", $res['SITE'][$i] ) ;
	// Etat
	$mod -> MxText ( "germe.etat", $res['STATUT'][$i] ) ;
	// On ajoute une nouvelle ligne si besoin.
	$mod -> MxBloc ( "germe", "loop" ) ;
      }
      // Récupération du code HTML généré.  
      return $mod -> MxWrite ( "1" ) ;
    }
  }

  function getActesDiagnostics ( ) {
    $this->defineParamCCAM ( ) ;
    $cotationActes = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
    return $cotationActes -> cotationActes ( ) ;
  }
  
  function getQuestionSortie ( $image='question' ) {
  	global $session ;
	global $options ;
	
	if ( $options->getOption("QuestionSortie")) {

		if ( $this->patient->getValide ( ) == 2 ) {
			$mod = new ModeliXe ( "QuestionSortie.html" ) ;
			$mod -> SetModeliXe ( ) ;
			$mod -> MxText ( "image", $image ) ;
			$mod -> MxText ( "detail", $options->getOption('QuestionSortie') ) ;
			$mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
			return $mod -> MxWrite ( "1" ) ;
	  	} else return '' ;
	}
  }
  
  function getGenQuestionSortie ( ) {
  	global $session ;
  	global $options ;
  	global $pi ;
  	if ( $_REQUEST['validerQuestionSortie'] == 'Oui' ) {
		$this->patient->setAttribut ( "Valide", 0 ) ;
		if ( $options->getOption ( "numUFnonRPU" ) ) {
			$this->patient->setAttribut ( "UF", $options->getOption ( "numUFnonRPU" ) ) ;
			$this->addBal ( '', 'nonrpu' ) ;
		}
		$this->patient->setAttribut ( "Valide", 0 ) ;
		$session->setLogSup ( "Question de sortie : Oui (patient non rpu)" ) ;
	} elseif ( $_REQUEST['validerQuestionSortie'] == 'Non' ) {
		$this->patient->setAttribut ( "Valide", 1 ) ;
		if ( $options->getOption ( "numUFnonRPU" ) ) {
			$this->patient->setAttribut ( "UF", $options->getOption ( "numUFexec" ) ) ;
			$this->addBal ( '', 'anonrpu' ) ;		
		}
		$session->setLogSup ( "Question de sortie : Non (patient rpu)" ) ;
	}
	if ( $_REQUEST['validerQuestionSortie'] ) $this->patient = new clPatient ( $this->idpatient, $this->type ) ;
  	if ( $this->patient->getValide ( ) ) {
  		if ( $options->getOption("QuestionSortie") ) $question = "Cliquez ici si la réponse est oui : ".$options->getOption("QuestionSortie") ;
  		else $question = "Cliquez ici si le patient ne doit pas être envoyé dans les RPU." ;
  		$inf = $pi->genInfoBulle ( $question ) ;
  		$link = '<a href="'.URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 
      	      $session->getNavi(2) ).'&amp;validerQuestionSortie=Oui'.
			  '"><img src="'.URLIMG.'questionannuler.gif" alt="annulerValide" '.$inf.' /></a>' ;
  	} else {
  		if ( $options->getOption("QuestionSortie") ) $question = "Cliquez ici si la réponse est non : ".$options->getOption("QuestionSortie") ;
  		else $question = "Cliquez ici si le patient doit être envoyé dans les RPU." ;
  		$inf = $pi->genInfoBulle ( $question ) ;
  		$link = '<a href="'.URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 
      	      $session->getNavi(2) ).'&amp;validerQuestionSortie=Non'.
			  '"><img src="'.URLIMG.'questionvalider.gif" alt="validerValide" '.$inf.' /></a>' ;
  	}
	return $link ;
  }
  
  // Gestion de l'état civil d'un patient.
  function EtatCivil ( ) {
    global $session ;
    global $options ;
    global $pi ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "EtatCivil.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage du sexe de la personne.
    switch ( $this->patient->getSexe ( ) ) {
    case 'M': $img = URLIMG."mini_homme.png" ; $e = "" ; break ;
    case 'F': $img = URLIMG."mini_femme.png" ; $e = "e" ; break ;
    default: $img = URLIMG."mini_Indefini.png" ; $e = "(e)" ; break ;
    }
    // Calcul sur les différentes dates.
    $age = new clDate ( $this->patient->getDateNaissance ( ) ) ;
    $dateSimple = $age -> getDate ( "d-m-Y" ) ;
    $dateComple = $age -> getDateText ( ) ;
    $duree = new clDuree ( ) ;
    $adm = new clDate ( $this->patient->getDateAdmission ( ) ) ;
    $exa = new clDate ( $this->patient->getDateExamen ( ) ) ;
    $sor = new clDate ( $this->patient->getDateSortie ( ) ) ;
    // Affichage des attributs sexe, nom, prénom et date de naissance.
    $mod -> MxText ( "sexe", "<img src=\"$img\" alt=\"Sexe\" />" ) ;
    $mod -> MxText ( "nom", ucfirst(strtolower($this->patient->getPrenom ( )))." ".strtoupper ( $this->patient->getNom ( ) ) ) ;
    $pedi = $options -> getOption ( "FiltreSalleSup" ) ;
    if ( ereg ( $pedi, $this->patient->getSalle ( ) ) ) {
    	$mod -> MxText ( "etatUHCD", " (".($options->getOption('nomSalleSup')?$options->getOption('nomSalleSup'):'Pédiatrie').")" ) ;
    } elseif ( $this->patient->getEtatUHCD ( ) ) {
      $inf = $pi->genInfoBulle ( 'Cliquez ici pour changer l\'état UHCD' ) ;
      if ( $session->getDroit ( $this->type."_EtatCivil", "m" ) )
      $link = '<a href="'.URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 
      	      $session->getNavi(2), 'changerEtatUHCD' ).
			  '"><img src="'.URLIMG.'valider.png" alt="Changer l\'état" '.$inf.' /></a>' ; else $link = '' ;
      if ( $this->patient->isSoinsContinus ( ) ) $mod -> MxText ( "etatUHCD", " (<font color=\"red\">Soins Continus</font>)" ) ;
      elseif ( $this->patient->getUF ( ) == $options->getOption ( "numUFUHCD" ) )
    	$mod -> MxText ( "etatUHCD", " (UHCD $link)" ) ;
      elseif ( $this->patient->getUF ( ) == $options->getOption ( "numUFUHCDrepere" ) AND $options->getOption ( "numUFUHCDrepere" ) ) 
      	$mod -> MxText ( "etatUHCD", " (UHCD repéré $link)" ) ;
      elseif ( ! $this->patient->getUF ( ) ) $mod -> MxText ( "etatUHCD", " (Aucune UF)" ) ;
      else $mod -> MxText ( "etatUHCD", " (Urgences $link)" ) ;
    }
    if ( $options -> getOption ( "ModuleEtiquettes" ) == "Non" ) $mod -> MxText ( "etiquettes", "" ) ;
    else {
    	$inf = $pi->genInfoBulle ( 'Cliquez ici pour lancer l\'impression des étiquettes' ) ;
    	$link = '<a href="'.URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), 
      	      $session->getNavi(2), 'impressionEtiquettes' ).
			  '"><img src="'.URLIMG.'etiquettes.png" alt="Impression des étiquettes" '.$inf.' /></a>' ;
		$mod -> MxText ( "etiquettes", $link ) ;
    }
    
    //eko ( $this->patient->getManuel ( ) ) ;
    if ( $session->getDroit ( $this->type."_EtatCivil", "m" ) AND $this->patient->getManuel ( ) ) {
    	$link = URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "modPatient" ) ;
    	$inf = $pi->genInfoBulle ( 'Cliquez ici pour modifier les informations du patient manuel' ) ;
    	$mod -> MxText ( "lienModPatient", '<a href="'.$link.'" ><img src="images/modPat.png" alt="Modifier le patient" '.$inf.'/></a>' ) ;
    }
    
    if ( $session->getDroit ( $this->type."_EtatCivil", "m" ) )  {
    	$mod -> MxText ( "lienQuestion",$this->getGenQuestionSortie ( ) ) ;
	}
    
    if ( $this->patient->getDateNaissance ( ) != "0000-00-00 00:00:00" )
      $mod -> MxText ( "naissance", "Né$e le $dateComple (".str_replace("<br>"," et ",$duree->getAgePrecis ( $age -> getTimestamp ( ) ) ).")" ) ;
    else
      $mod -> MxText ( "naissance", "Date de naissance inconnue" ) ;
    // Affichage de l'adresse et du numéro de téléphone.
    $mod -> MxText ( "adresse", $this->patient->getAdresse ( )." - ".$this->patient->getCodePostal ( )." ".$this->patient->getVille ( ) ) ;
    $mod -> MxText ( "telephone", $this->patient->getTel ( ) ) ;

    // Affichage des autres blocs de l'état civil.
    $this->genBlocEtatCivil ( "Prevenir", $mod, "m" ) ;
    $this->genBlocEtatCivil ( "MedecinTraitant", $mod, "m" ) ;
    $this->genBlocEtatCivil ( "DateExamen", $mod, "m", $exa ) ;
    $this->genBlocEtatCivil ( "DateSortie", $mod, "d", $sor, '1' ) ; 
    
    

    // Affichage de la date d'admission.
    if ( $this->patient->getDateAdmission ( ) > 0 ) $mod -> MxText ( "admission", $adm -> getDate ( "d-m-Y H:i" ) ) ;
    else $mod -> MxText ( "admission", VIDEDEFAUT ) ;

    // On renvoit le code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }

  // Génération des petits blocs affichés dans l'état civil.
  function genBlocEtatCivil ( $nomBloc, $mod, $droit, $date='', $sortie='' ) { 
    global $session ;
    global $options ;
    // Initialisation des objets et des variables utilisées.
    // Pour générer des formulaires plus loin.
    $form = new clForm ( ) ;
    // Date d'exécution.
    $now = new clDate ( ) ;
    // Récupération des options.
    $retourmax = $options -> getOption ( "Dates Patient" ) ;
    $tranches  = $options -> getOption ( "DatesDécoup Patient" ) ;
    // Récupération de la valeur actuelle du bloc.
    $res = '' ;
    eval ( "\$res = \$this->patient->get$nomBloc ( ) ;" ) ;
    // Si une date a été transmise et qu'elle n'est pas nulle, on initialise $res avec cette valeur.
    if ( $date ) { 
      if ( $res != "0000-00-00 00:00:00" ) $res = $date -> getDate ( "d-m-Y H:i" ) ; 
      else $res = '' ; 
    }
    // Si toutes ces conditions sont réunies, alors on fait transiter le patient sorti vers la table des patients présents.
    if ( $session -> getNavi ( 3 ) == "mod".$nomBloc AND ($_POST['AnnulerSortie'] OR $_POST['AnnulerSortie_x']) AND $session -> getDroit ( $this->type."_EtatCivil", $droit ) ) {
      $modif = "0000-00-00 00:00:00" ;
      $this->patient->setAttribut ( $nomBloc, $modif ) ;
      $this->patient = new clPatient ( $this->idpatient, "Sortis" ) ;
      $this->patient->entrerPatient ( ) ;
      if ( $options -> getOption ( "Module_CCAM" ) ) {
	$ccam = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;	
	$ccam -> deleteBAL ( ) ;
	if ( $options->getOption ( 'HprimXML_Actif' ) ) {
        	$hprimXml = new clHprimXML ( ) ;
    	}
      }
      $res = "" ;
      // Si ces conditions sont réunies, alors on change la date du champs avec la valeur transmise.
    } elseif ( $session -> getNavi ( 3 ) == "mod".$nomBloc AND ( $_POST['Valider'] OR $_POST['Valider_x'] )AND $session -> getDroit ( $this->type."_EtatCivil", $droit ) ) {
      if ( $date ) {
	if ( $_POST['ValiderMaintenant'] OR $_POST['ValiderMaintenant_x'] ) {
	  $newdate = new clDate ( ) ;
	} elseif ( $_POST["mod".$nomBloc] == "now" ) {
	  $newdate = new clDate ( ) ;
	} else {
	  $newdate = new clDate ( $_POST["mod".$nomBloc] ) ;
	}
	$modif = $newdate -> getDatetime ( ) ;
	$res = $newdate -> getDate ( "d-m-Y H:i" ) ; 
	// Si le paramètre $sortie est vrai, alors on fait transiter le patient présent vers la table des patients sortis.
	if ( $sortie ) {
	  $this->patient->setAttribut ( $nomBloc, $modif ) ;
	  $this->patient = new clPatient ( $this->idpatient, "Presents" ) ;
	  clFoRmX_manip::rangerDossMedChrono($this->patient);
	  clFoRmX_manip::rangerDossMedAEV($this->patient);
	  $this->patient->sortirPatient ( ) ;
	  if ( $options -> getOption ( "Module_CCAM" ) ) {
	    $this->paramCCAM["dtFinInterv"]=$this->patient->getDateSortie ( );
      $ccam = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;	
	    $ccam -> writeBALSorti ( ) ;
		if ( $options->getOption ( 'HprimXML_Actif' ) ) {
        		$hprimXml = new clHprimXML ( ) ;
    		}
	  }
	  $this->rien = "1" ;
	}
	// Dans le dernier cas, il s'agit d'un bloc de type "textarea" tout simple à mettre à jour.
      } else { $modif = $_POST["mod".$nomBloc] ; $res = $modif ; }
      // Mise à jour du patient.
      $session->setLogSup ( "Modification du bloc $nomBloc" ) ;
      $this->patient->setAttribut ( $nomBloc, $modif ) ;
      $this->patient = new clPatient ( $this->idpatient, $this->type ) ;
    // Dans ce cas, nous devons afficher le formulaire adéquate à la modification du bloc.
    } elseif ( $session -> getNavi ( 3 ) == "mod".$nomBloc AND ! $_POST['Annuler'] AND ! $_POST['Annuler_x']) {
      // Cas des champs de type date.
      $session->setLogSup ( "Demande de modification du bloc $nomBloc" ) ;
      if ( $date ) {
      	
      	if ( $sortie AND $this->patient->getDateExamen ( ) != '0000-00-00 00:00:00' ) $dateMin = new clDate ( $this->patient->getDateExamen ( ) ) ;
      	else $dateMin = new clDate ( $this->patient->getDateAdmission ( ) ) ;
    	$dateNow = new clDate ( ) ;
    	if ( $tranches >= 5 ) {
	  		$minutes = $dateNow -> getMinutes ( ) ;
	  		$minutesless = ( $minutes % 5 ) ;
	  		$dateNow -> addMinutes ( -$minutesless ) ;
		}
    	$data[now] = 'Maintenant' ;
    	$min = $dateMin -> getTimestamp ( ) ;
    	$t = $dateNow -> getTimestamp ( ) ;
    	$data[$t] = $dateNow -> getDate ( "d-m-Y H:i" ) ; 
    	// On parcourt les dates en fonctions des options.
		for ( $i = 0 ; $dateNow -> getTimestamp ( ) >= $min ; $i += $tranches ) {
	  		$t = $dateNow -> getTimestamp ( ) ;
	  		$data[$t] = $dateNow -> getDate ( "d-m-Y H:i" ) ; 
	  		$dateNow -> addMinutes ( -$tranches ) ;
		}
		$f .= $res."<br />".$form -> genForm ( URL ) ;
    	
    	/*
		// Préparation de la première date de la liste.
		$now -> addHours ( -$retourmax ) ;
		$min = $now -> getTimestamp ( ) ;
		$now -> addHours ( $retourmax ) ;
		if ( $tranches >= 5 ) {
	  		$minutes = $now -> getMinutes ( ) ;
	  		$minutesless = ( $minutes % 5 ) ;
	  		$now -> addMinutes ( -$minutesless ) ;
		}
		$data[now] = 'Maintenant' ;
		$t = $now -> getTimestamp ( ) ;
		$data[$t] = $now -> getDate ( "d-m-Y H:i" ) ; 
		// On parcourt les dates en fonctions des options.
		for ( $i = 0 ; $now -> getTimestamp ( ) >= $min ; $i += $tranches ) {
	  		$t = $now -> getTimestamp ( ) ;
	  		$data[$t] = $now -> getDate ( "d-m-Y H:i" ) ; 
	  		$now -> addMinutes ( -$tranches ) ;
		}
		$f .= $res."<br />".$form -> genForm ( URL ) ;
		*/
	// S'il ne s'agit pas de la date de sortie et que le patient est sorti, 
	// alors on affiche le select contenant la liste des dates possibles.
	if ( ! ( $this->type == "Sortis" AND $sortie ) ) $f .= $form -> genSelect ( "mod".$nomBloc, 1, $data ) ;
      } else {
	// Cas d'un formulaire de type "textarea" tout simple.
	$f .= $form -> genForm ( URL ) ;      
	$f .= $form -> genTextArea ( "mod".$nomBloc, $res )."<br /><center>" ;
      }
      // Cas d'une date.
      if ( $date ) {
	// Cas de la date de sortie.
	if ( $sortie ) {
	  // Cas d'un patient déjà sorti : On affiche seulement un bouton d'annulation de sortie.
	  if ( $this->type == "Sortis" ) {
	    $f .= $form -> genImage ( "AnnulerSortie", "Annuler la sortie", URLIMG."annulerSortie.gif" ) ;
	    $f .= $form -> genHidden ( "navi", $session->genNavi ( "Liste_Presents", $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	    $f .= $form -> genEndForm ( ) ;
	    // $f .= $form -> genForm ( URL ) ;
	    // $f .= $form -> genImage ( "RetourPatient", "Retour du patient", URLIMGRET ) ;
	    // $f .= $form -> genHidden ( "navi", $session->genNavi ( "Liste_Presents", $this->patient->getID ( ) ) ) ;
          // Dans les autres cas, on affiche les boutons de validation / annulation de la sortie.
	  } else {
	    $contraintes = new clContraintes ( $this->patient->getID ( ), $this->paramCCAM ) ;

		//formulaire(s) à remplir bloquant la sortie ( pré-contraintes ) ?
		if ( clTuFormxTrigger::getWatcher($this->patient)->isTriggersOnOut() ) 	{
			$enquetes = clTuFormxTriggerWatcher::getInstance($this->patient)  ;
			$enquetes->launchTriggersOnOut();
            return '';
		}  else if ( $contraintes -> runCheck ( ) ) {
			
	      $f .= $form -> genImage ( "Valider", "Valider", URLIMGVAL, 'style="border: 0px; background-color: #FFFF99;"' ) ;
	      $f .= $form -> genHidden ( "navi", $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	      $f .= $form -> genEndForm ( ) ;
	      $f .= $form -> genForm ( URL ) ;      
	      $f .= $form -> genImage ( "Annuler", "Annuler", URLIMGANN, 'style="border: 0px; background-color: #FFFF99;"' ) ;
	      $f .= $form -> genHidden ( "navi", $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	    } else {
	      $session->setLogSup ( 'Contraintes non respectées pour la sortie' ) ;
	      $this->af .= $contraintes -> getContraintes ( ) ;
	    }
	  }
        // Cas d'un champs date normal : affichage des boutons valider et annuler.
	} else {
	  $f .= $form -> genImage ( "Valider", "Valider", URLIMGVAL, 'style="border: 0px; background-color: #FFFF99;"' ) ;
	  $f .= $form -> genHidden ( "navi", $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	  $f .= $form -> genEndForm ( ) ;
	  $f .= $form -> genForm ( URL ) ;
	  $f .= $form -> genImage ( "Annuler", "Annuler", URLIMGANN, 'style="border: 0px; background-color: #FFFF99;"' ) ;
	  $f .= $form -> genHidden ( "navi", $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	}
      // Cas d'un champs normal : affichage des boutons valider et annuler.
      } else {
	$f .= $form -> genImage ( "Valider", "Valider", URLIMGVAL, 'style="border: 0px; background-color: #FFFF99;"' ) ;
	$f .= $form -> genHidden ( "navi", $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	$f .= $form -> genEndForm ( ) ;
	$f .= $form -> genForm ( URL ) ;
	$f .= $form -> genImage ( "Annuler", "Annuler", URLIMGANN, 'style="border: 0px; background-color: #FFFF99;"' ) ;
	$f .= $form -> genHidden ( "navi", $session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
      }
      // On ferme la balise du formulaire.
      $f .= "</center>".$form -> genEndForm ( ) ;
      // On modifie le bloc ModeliXe passé en paramètres.
      $mod -> MxText ( $nomBloc, $f ) ;
      // On signale que le bloc a été modifié.
      $af = 1 ;
    }
    // S'il n'y a pas eu d'affichage jusqu'à maintenant, il ne s'agit que d'afficher la valeur contenue dans ce bloc.
    if ( ! $af ) {
      // Si le droit de modification est présent, alors nous affichons le lien pour modifier le bloc.
      if ( $session -> getDroit ( $this->type."_EtatCivil", $droit ) ) {
	if ( $date ) $image = URLIMGHOR ; else $image = URLIMGMOD ;
	if ( $nomBloc == "DateExamen" AND $_POST['Medecin'] AND ( $this->patient->getDateExamen ( ) == "0000-00-00 00:00:00" ) AND ( ! $this->patient->getMedecin ( ) ) ) {
	  $date = new clDate ( ) ;
	  $dateA = new clDate ( $this->patient->getDateAdmission ( ) ) ;
	  if ( $date -> getTimestamp ( ) > $dateA -> getTimestamp ( ) ) {
	  	$this->patient->setAttribut ( "DateExamen", $date->getDatetime ( ) ) ;
	  	$res = $date -> getDate ( "d-m-Y H:i" ) ;
	  } else {
	  	$dateA -> addSeconds ( 1 ) ;
	  	$this->patient->setAttribut ( "DateExamen", $dateA->getDatetime ( ) ) ;
	  	$res = $dateA -> getDate ( "d-m-Y H:i" ) ;
	  }
	}
	if ( $nomBloc == "DateExamen" AND isset ( $_POST['Medecin'] ) AND ! $_POST['Medecin'] ) {
	  $this->patient->setAttribut ( "DateExamen", "0000-00-00 00:00:00" ) ;
	  $res = "--" ; 
	}
	if ( ! $this->export ) {
	  if ( $nomBloc == "DateSortie" ) {
		$retour = '' ;
	  	if ( $options -> getOption ( 'EnqueteRadio' ) ) $retour = clListeRadios::getRetour ( $this->patient->getID ( ), IDAPPLICATION ) ;
	  	if ( $retour ) {
			$mod -> MxText ( "retourRadio", $retour ) ;
		}
	  	else {
	  		$questionSortie = $this->getQuestionSortie ( ) ;
	  		if ( $questionSortie ) $mod -> MxText ( "retourRadio", $questionSortie ) ;
	  		else {
	  			$mod -> MxImage ( "imgModifier".$nomBloc, $image, "Modifier $nomBloc" ) ;
	  			$mod -> MxUrl  ( "lien".$nomBloc, URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	  		}	
	  	}
	  } else {
	  	$mod -> MxImage ( "imgModifier".$nomBloc, $image, "Modifier $nomBloc" ) ;
	  	$mod -> MxUrl  ( "lien".$nomBloc, URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "mod".$nomBloc ) ) ;
	  }
	}
      }
      // Affichage du contenu du bloc s'il existe sinon on affiche la valeur par défaut.
      if ( $res ) $mod -> MxText ( $nomBloc, nl2br($res) ) ;
      else $mod -> MxText ( $nomBloc, VIDEDEFAUT ) ;
    }
  }

  // Affichage et gestion du bloc des informations sur un transfert.
  function Transfert ( ) {
    global $session ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "Transfert.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Récupération des passages précédents.
    if ( isset ( $_POST['DestinationAttendue'] ) ) $param['nomitem'] = $_POST['DestinationAttendue'] ;
    else $param['nomitem'] = addslashes(stripslashes($this->patient->getDestinationAttendue ( ))) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getTypeDestinationAttendue", $param, "ResultQuery" ) ; 
    //newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    if ( $res['localisation'][0] == "T" ) {
      // Si toutes les conditions sont réunies, alors on met à jour le moyen de transport associé au patient.
      if ( isset ( $_POST['listeMoyens'] ) AND $session -> getDroit ( $this->type."_Transfert", "m" ) ) {
	$session->setLogSup ( "Mise à jour des informations Transfert" ) ;
	$this->patient->setAttribut ( "MoyenTransport", $_POST['listeMoyens'] ) ;
	$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      }
      // Si toutes les conditions sont réunies, alors on met à jour le motif de transfert associé au patient.
      if ( isset ( $_POST['listeMotifs'] ) AND $session -> getDroit ( $this->type."_Transfert", "m" ) ) {
	$this->patient->setAttribut ( "MotifTransfert", $_POST['listeMotifs'] ) ;
	$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      }
      // Si toutes les conditions sont réunies, alors on met à jour le motif de transfert associé au patient.
      if ( isset ( $_POST['listeDestPMSI'] ) AND $session -> getDroit ( $this->type."_Transfert", "m" ) ) {
	$this->patient->setAttribut ( "DestPMSI", $_POST['listeDestPMSI'] ) ;
	$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      }
      // Si toutes les conditions sont réunies, alors on met à jour le motif de transfert associé au patient.
      if ( isset ( $_POST['listeOrients'] ) AND $session -> getDroit ( $this->type."_Transfert", "m" ) ) {
	$this->patient->setAttribut ( "Orientation", $_POST['listeOrients'] ) ;
	$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      }

      $listeGen = new clListesGenerales ( "recup" ) ;
      $listeMotifs = $listeGen -> getListeItems ( "Motifs Transfert", "1", '', '', "1" ) ;
      if ( $session -> getDroit ( $this->type."_Transfert", "m" ) ) 
	$mod -> MxSelect( "transfert.bloctrans.listeMotifs", "listeMotifs", $this->patient->getMotifTransfert ( ), $listeMotifs, '', '', "onChange=\"reload(this.form)\"") ; 
      else $mod -> MxText ( "transfert.bloctrans.Motif", $this->patient->getMotifTransfert ( ) ) ; 
      $listeMoyens = $listeGen -> getListeItems ( "Moyens de transport", "1", '', '', "1" ) ;
      if ( $session -> getDroit ( $this->type."_Transfert", "m" ) ) 
	$mod -> MxSelect( "transfert.bloctrans.listeMoyens", "listeMoyens", $this->patient->getMoyenTransport ( ), $listeMoyens, '', '', "onChange=\"reload(this.form)\"") ; 
      else $mod -> MxText ( "transfert.bloctrans.Moyen", $this->patient->getMoyenTransport ( ) ) ;
    } elseif ( $res['localisation'][0] == "H" ) {
      $mod -> MxBloc ( "transfert", "modify", "" ) ;
    } else {
      $mod -> MxBloc ( "transfert", "modify", "" ) ;
    }
    // Variable de navigation.
    $mod -> MxHidden ( "transfert.hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "majTransfert" ) ) ;
    // On retourne le code HTML généré par le template.
    return $mod -> MxWrite ( "1" ) ;
  }

  // Gestion et affichage du bloc des informations.
  function Informations ( ) {
    global $session ;
    global $options ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "InformationsPatient.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Appel des différents éléments de ce bloc.
    $this->genBlocInformation ( "Medecin", "Médecins", $mod ) ;
    $this->genBlocInformation ( "Ide", "I.D.E.", $mod ) ;
    $nomCat = $this->genBlocInformation ( "CategorieRecours", "Recours", $mod, 1, '__VIDE__' ) ;
    $this->genBlocInformation ( "Recours", "Recours", $mod, 1, $nomCat ) ;
    $this->genBlocInformation ( "CodeGravite", "Codes gravité", $mod ) ;
    $this->genBlocInformation ( "DestinationSouhaitee", "Destinations souhaitées", $mod ) ;
    if ( $options -> getOption ( 'GestionAdresseur' ) )
	$this->genBlocInformation ( "Adresseur", "Adresseurs", $mod, '', '', 'GestionAdresseur.' ) ;
    else $mod -> MxBloc ( 'GestionAdresseur', 'modify', '<td></td>' ) ;
    if ( $options -> getOption ( 'GestionModeAdmission' ) )
	$this->genBlocInformation ( "ModeAdmission", "Mode d'admission", $mod, '', '', 'GestionModeAdmission.', 1 ) ;
    else $mod -> MxBloc ( 'GestionModeAdmission', 'modify', '<td></td>' ) ;
    if ( $options -> getOption ( 'GestionCCMU' ) )
	$this->genBlocInformation ( "CCMU", "CCMU", $mod, '', '', 'GestionCCMU.' ) ;
    else $mod -> MxBloc ( 'GestionCCMU', 'modify', '<td></td>' ) ;
    if ( $options -> getOption ( 'GestionGEMSA' ) )
	$this->genBlocInformation ( "GEMSA", "GEMSA", $mod, '', '', 'GestionGEMSA.' ) ;
    else $mod -> MxBloc ( 'GestionGEMSA', 'modify', '<td></td>' ) ;
	if ( $options -> getOption ( 'GestionTraumato' ) )
	$this->genBlocInformation ( "Traumato", "Traumato", $mod, '', '', 'GestionTraumato.' ) ;
	else $mod -> MxBloc ( 'GestionTraumato', 'modify', '<td></td>' ) ;
	if ( $options -> getOption ( 'GestionProvenance' ) )
	$this->genBlocInformation ( "Provenance", "Provenances PMSI", $mod, '', '', 'GestionProvenance.', 1 ) ;
	else $mod -> MxBloc ( 'GestionProvenance', 'modify', '<td></td>' ) ;
	if ( $options -> getOption ( 'GestionTISS' ) )
	$this->genBlocInformation ( "TISS", "TISS", $mod, '', '', 'GestionTISS.', 1 ) ;
	else $mod -> MxBloc ( 'GestionTISS', 'modify', '<td></td>' ) ;
	$this->genBlocInformation ( "DestinationAttendue", "Destinations attendues", $mod ) ;
	$this->genBlocInformation ( "SalleExamen", "Salles d'examens", $mod ) ;
    // Variable de navigation.
    $mod -> MxHidden ( "hidden", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "majInformations" ) ) ;
    
    //eko ( $this->patient->getTypeMedecin ( ) ) ;
    
    if ( $this->remplissageAuto ) {
    	global $stopAffichage ;
    	$stopAffichage = 1 ;
    	header ( 'Location:?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)) ) ;
    }
    
    // On retourne le code HTML généré par le template.
    return $mod -> MxWrite ( "1" ) ;
  }

    //definit l'IHM dans un etat occupé
    function setIHMBusy()
    {
        $this->isIMHBusy = true ;
    }

	//regarde si l'IHM de la fiche patient est occupée ou dispo . Utilisé par les triggers de formulaire pour savoir si on peut afficher ou non les formx
	function isIHMDispo()
	{
        if ( $this->isIMHBusy )
            return false ;

		//eko($_POST);
		//dans codage diag/Acte ?
		if( isset($_POST['DetailDiagsActes_x']) && $_POST['DetailDiagsActes_x'] ) //demande de codage diag/Acte
			return false ;
		if( isset($_POST['lesion']) && $_POST['lesion']  && ! isset($_POST['sortir_x']) ) //dans codage dag/Acte, hors sortie
			return false ;
		//dans consulte spé ?
		if( isset($_POST['DetailConsult_x']) && $_POST['DetailConsult_x'] ) //demande de consulte spé
			return false ;
		if( isset($_POST['listeGauche'])   && ! isset($_POST['sortir_x']) ) //dans consulte spé, hors sortie
			return false ;
		//dans formulaire ?
		if( isset($_POST['FormX_ext_goto_']) && $_POST['FormX_ext_goto_'] ) //demande choix nouveau formulaire
			return false ;
		if( isset($_POST['FoRmX_selValid_x']) && $_POST['FoRmX_selValid_x'] )  //choix nouveau formulaire
			return false ;
		if( isset($_POST['FoRmX_INSTANCE']) && ! ( isset($_POST['FoRmX_close_x']) && $_POST['FoRmX_close_x'] ) ) //sortie formulaire
			return false ;
		//dans documents édités ?
			//a priori, pas de déclenchage apres documents édités possible pour l'instant
		//dans messages ?
			//a priori, pas de déclenchage apres documents édités possible pour l'instant
		//eko("IHM dispo");
		return true  ;
	}


	// Cette fonction lance le remplissage automatique si nécessaire.
	function remplissageAutomatique ( $nom, $val ) {
		global $options ;
		//eko ( $nom ) ;
		// Récupération des options sur les critères de remplissage.
		$nom1 = $options -> getOption ( '1critereRemplissage' ) ;
		$val1 = $options -> getOption ( '1valeurRemplissage' ) ;
		$nom2 = $options -> getOption ( '2critereRemplissage' ) ;
		$val2 = $options -> getOption ( '2valeurRemplissage' ) ;
		$nom3 = $options -> getOption ( '3critereRemplissage' ) ;
		$val3 = $options -> getOption ( '3valeurRemplissage' ) ;
		$n = 0 ;
		// On détermine quel critère est validé.
		if ( $nom1 == $nom AND $val1 == $val ) $n = 1 ;
		if ( $nom2 == $nom AND $val2 == $val ) $n = 2 ;
		if ( $nom3 == $nom AND $val3 == $val ) $n = 3 ;
		//eko($n);
		//eko ( $nom1.' '.$val1 ) ;
		//eko ( $nom.' '.$val ) ;
		// Si un critère est validé alors on lance la procédure de remplissage.
		if ( $n == 1 ) {
			$tab = array ( 'medecin_urgences', 'ide', 'salle_examen', 'ccmu', 'gemsa', 'traumato', 
			'dest_souhaitee', 'dest_attendue', 'moyen_transport', 'motif_transfert', 'recours_code', 
			'recours_categorie', 'type_destination', 'motif_recours', 'code_gravite', 'provenance',
			'adresseur', 'mode_admission', 'code_gravite' ) ;
			//eko ( $tab ) ;
			while ( list ( $key, $val ) = each ( $tab ) ) {
				$this->setRemplissage ( $n, $val ) ;				
			}
      
			$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
			
			//Gestion de la consultation non facturable
      //eko($this->paramCCAM);
      /*
      $this->paramCCAM["lieuInterv"]="CONSULT LIBERALE";
      $this->paramCCAM["nomIntervenant"]=$options->getOption('1Remplissage_medecin_urgences');
      $this->paramCCAM["matriculeIntervenant"]=$options->getOption('codeAdeliChefService');
      */
      $this -> paramCCAM["lieuInterv"] = $this -> patient -> getSalleExamen ( ) ;
      $this -> paramCCAM["nomIntervenant"] = $this -> patient -> getMedecin ( ) ;
      $this -> paramCCAM["matriculeIntervenant"] = $options -> getOption ( 'codeAdeliChefService') ;
           
      $cotationActes = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
      $codeActe = $options -> getOption ( '1actesRemplissage' ) ;
      if ( $codeActe )
      	$cotationActes -> consultNonFacturable ( $codeActe ) ;
      if ( $options -> getOption ( $n.'diagRemplissage' ) ) {
      	$_POST['idListeSelection1'] = $options -> getOption ( $n.'diagRemplissage' ) ;
      	$cotationActes -> addActesPatient ( ) ;
      }
      if ( $this->remplissageAuto ) {
     	 global $session ;
         global $stopAffichage ;
         $stopAffichage = 1 ;
         header ( 'Location:?navi='.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2)) ) ;
      }
		}
		/*if ( $n == 2 ) {
		  $this->patient->setAttribut ( "Medecin", $options -> getOption ( '2Remplissage_medecin_urgences' ) );
      }*/
	}

	// Récupère la valeur d'un item à remplir automatiquement.
	function setRemplissage ( $n, $item ) {
		global $options ;
		global $errs ;
		$val = $options -> getOption ( $n."Remplissage_".$item ) ;
    	if ( $val ) {
			if ( $item	 == 'medecin_urgences' AND $this->patient->getDateExamen ( ) == '0000-00-00 00:00:00') {
				$date = new clDate ( ) ;
				$data['dt_examen'] = $date->getDatetime ( ) ;
			}
			$data[$item] = $val ;
			$this->patient->setPatient ( $data ) ;	
			$this->remplissageAuto = 1 ;	
		}		
	}
	
	// Affiche le bloc formx 
	function genBlocFormx() {
	
	global $options;
	global $session ;
	if ( $options->getOption('showFormulaires'))
		if ( $session->getDroit('formulaires') && $options->getOption('showFormulaires')  ) {
			$this->formulaires = new clFoRmX_manip($this->patient->getIdu(),'') ;
			return 	$this->formulaires->genCase( null,'formulaires') ;
  		}
 	return "";
	}
	
	
	// Gestion des sous blocs du bloc des informations.
	function genBlocInformation ( $nomBloc, $nomListe, $mod, $retour='', $nomCategorie='', $nomBlocOptionnel='', $code='' ) {
  		global $session ;
  		$res = '' ;
  		if ( isset ( $_POST['Medecin'] ) ) $session->setLogSup ( 'Mise à jour des informations de passage' ) ;
    	// Si une modification de ce bloc a été transmise, alors on met à jour les informations du patient.
    	if ( isset ( $_POST[$nomBloc] ) ) {
    		$this->remplissageAutomatique ( $nomBloc, $_POST[$nomBloc] ) ;
      		$this->patient->setAttribut ( $nomBloc, stripslashes($_POST[$nomBloc]) ) ;
      		$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      		if ( $nomBloc == "DestinationAttendue" ) {
				$this->patient->setAttribut ( "TypeDestAttendue", $this->patient->getTypeAdmission ( ) ) ;
				$this->patient->setAttribut ( "DestPMSI", $this->patient->getNewDestPMSI ( ) ) ;
				$this->patient->setAttribut ( "Orientation", $this->patient->getNewOrientation ( ) ) ;
				$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      		}
      		if ( $nomBloc == "Recours" ) {
				$newCR = $this->patient->getCodeRecoursFirst ( ) ;
				if ( $newCR != $this->patient->getCodeRecours ( ) ) $this->newRecours = 1 ;
				$this->patient->setAttribut ( "CodeRecours", $newCR ) ;
				$this->patient = new clPatient ( $this->idpatient, $this->type ) ;
      		}
    	}
    	// Récupération de la valeur actuelle du bloc.
    	eval ( "\$res = \$this->patient->get$nomBloc ( ) ;" ) ;
    	// Si le droit de modification est présent, alors on affiche le select,
    	if ( $session -> getDroit ( $this->type."_Informations", "m" ) AND ! $this->export ) {
      		// Si $retour a été transmis alors nous sommes dans la sous-liste 
      		// des catégories de recours : la liste des recours.
      		if ( $retour ) {
				// On récupère les recours contenus dans la catégorie associée au patient.
				if ( $nomCategorie ) {
	  				$listeCom = new clListes ( "Recours", "recup" ) ;
	  				if ( $nomCategorie == '__VIDE__' ) $nomCategorie = '' ;
	  				$liste = $listeCom -> getListes ( $nomCategorie, "1" ) ;
				}
      		// Sinon, on récupère les informations à mettre dans le select.
      		} else {
				$listeGen = new clListesGenerales ( "recup" ) ;
				if ( $code ) $liste = $listeGen -> getListeItemsV2 ( $nomListe, "1", '', "1" ) ;
				else $liste = $listeGen -> getListeItems ( $nomListe, "1", '', '', "1" ) ;
				//eko ( $liste ) ;
      		}
      		if ( is_array ( $liste ) ) $mod -> MxSelect( $nomBlocOptionnel."select".$nomBloc, $nomBloc, $res, $liste, '', '', "onChange=\"reload(this.form)\"" ) ; 
      		else $mod -> MxBloc ( $nomBlocOptionnel."recours", "modifiy", " " ) ;
    	// Sinon (le droit de modification n'est pas présent), on affiche seulement la valeur actuelle.
    	} else {
      		$mod -> MxText ( $nomBlocOptionnel."text".$nomBloc, $res ) ;
    	}
    	if ( $retour ) return $res ;
  	}

  // Gestion des messages.
  function Messages ( ) {
    global $session ;
    // Alertes automatiques en fonction du code de recours.
    if ( $_POST['Recours'] AND $this->newRecours ) {
      $listeGen = new clListesGenerales ( "recup" ) ;
      $liste1 = $listeGen -> getListeItems ( "Codes maltraitance", "1", '', '', "1" ) ;
      while ( list ( $key, $val ) = each ( $liste1 ) ) {
	if ( $val == $this->patient->getCodeRecours ( ) ) { $_POST['Envoyer'] =  1 ; $this->newMessage ( "Mails Maltraitance" ) ; }
      }
      $liste2 = $listeGen -> getListeItems ( "Geriatrie - Codes maltraitance", "1", '', '', "1" ) ;
      while ( list ( $key, $val ) = each ( $liste2 ) ) {
	if ( $val == $this->patient->getCodeRecours ( ) ) { $_POST['Envoyer'] =  1 ; $this->newMessage ( "Mails Gériatrie" ) ; }
      }
      //$liste3 = $listeGen -> getListeItems ( "Alerte Virus - Code Recours", "1", '', '', "1" ) ;
      //while ( list ( $key, $val ) = each ( $liste3 ) ) {
      // if ( $val == $this->patient->getCodeRecours ( ) ) { $_POST['type'] = "virus" ; $_POST['Envoyer'] =  1 ; $this->newMessage ( "Mails Alerte Virus" ) ; }
      //}
    }
    if ( $_POST['sendMessage'] ) { $_POST['type'] = "virus" ; $_POST['Envoyer'] =  1 ; $this->newMessage ( $_POST['sendMessage'] ) ; }
    if ( $_REQUEST['sendMessage2'] ) { $_POST['type'] = "non uhcd" ; $_POST['Envoyer'] =  1 ; $this->newMessage ( $_POST['sendMessage2'] ) ; }
    if ( $_REQUEST['sendMessage3'] ) { $_POST['type'] = "sortie forcée" ; $_POST['Envoyer'] =  1 ; $this->newMessage ( $_POST['sendMessage3'] ) ; }
    //eko ( $_REQUEST ) ;
    // Si les conditions sont remplies, alors on affiche la fenêtre de création de nouveau message.
    if ( $session -> getNavi ( 3 ) == 'newMessage' AND $session -> getDroit ( $this->type."_Messages", "w" ) )
      $af .= $this->newMessage ( ) ;
    // Si les conditions sont remplies, alors on affiche la liste des messages associés à ce patient.
    if ( $session -> getNavi ( 3 ) == 'viewMessages' AND $session -> getDroit ( $this->type."_Messages", "r" ) )
      $af .= $this->viewMessages ( ) ;
    // Si le droit de lecture est là, alors nous affichons le nombre de messages associés au patient.
    if ( $session -> getDroit ( $this->type."_Messages", "r" ) ) {
      $param['cw'] = "WHERE idpatient='".$this->patient->getID ( )."'" ;
      $req = new clResultQuery ;
      $res = $req -> Execute ( "Fichier", "getMessages", $param, "ResultQuery" ) ; 
      $mod = new ModeliXe ( "Messages.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      $mod -> MxBloc ( "new", "modify", " " ) ;
      $mod -> MxBloc ( "view", "modify", " " ) ;
      $mod -> MxImage ( "list.imgNewMessage", URLIMGMOD, "Nouveau message" ) ;
      $mod -> MxUrl  ( "list.lienNewMessage", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "newMessage" ) ) ;
      if ( $res['INDIC_SVC'][2] == 0 ) {
	$mod -> MxText ( "list.messages.nbMessages", "Aucun message transmis." ) ;
      } else {
	if ( $res['INDIC_SVC'][2] > 1 ) $s = "s" ;
	$mod -> MxText ( "list.messages.nbMessages", $res['INDIC_SVC'][2]." message$s transmis " ) ;
	$mod -> MxImage ( "list.messages.imgViewMessages", URLIMGLOU, "Voir les messages" ) ;
	$mod -> MxUrl  ( "list.messages.lienViewMessages", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "viewMessages" ) ) ;
      }
      $af .= $mod -> MxWrite ( "1" ) ;
    }
    return $af ;
  }

  // Fonction de visualisation des messages d'un patient.
  function viewMessages ( ) {
    global $session ;
    // Récupération des messages.
    $param['cw'] = "WHERE idpatient='".$this->patient->getID ( )."'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getMessages", $param, "ResultQuery" ) ; 
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "Messages.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $mod -> MxBloc ( "new", "modify", " " ) ;
    $mod -> MxBloc ( "list", "modify", " " ) ;
    // Parcours de la liste des messages.
    for ( $i = 0 ; isset ( $res['idmail'][$i] ) ; $i++ ) {
      // Affichage des informations de chaque message.
      $dateEmission = new clDate ( $res['dt_mail'][$i] ) ;
      $mod -> MxImage ( "view.imgCloseMessages", URLIMGFER, "Voir les messages" ) ;
      $mod -> MxUrl  ( "view.lienCloseMessages", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
      $mod -> MxText ( "view.messages.type", $res['type_mail'][$i] ) ;
      $mod -> MxText ( "view.messages.date", $dateEmission->getDate ( "d-m-Y H:i" ) ) ;
      $mod -> MxText ( "view.messages.contenu", $res['contenu'][$i] ) ;
      $mod -> MxBloc ( "view.messages", "loop" ) ;
    }
    // Retourne le code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }

  // Fonction d'ajout d'un nouveau message.
  function newMessage ( $listeMails='' ) {
    global $session ;
    global $options ;
    if ( !$this->patient->getILP ( ) ) $this->patient = new clPatient ( $this->idpatient, "Sortis" ) ;
    
    // Préparation des textes et dates...
    $dest2    = '' ;
    $message .= "Médecins urgences : ".($this->patient->getMedecin ( )?$this->patient->getMedecin ( ):'--')."<br />" ;
    $age      = new clDate ( $this->patient->getDateNaissance ( ) ) ;
    $duree    = new clDuree ( ) ;
    $adm      = new clDate ( $this->patient->getDateAdmission ( ) ) ;
    $exa      = new clDate ( $this->patient->getDateExamen ( ) ) ;
    $message .= "Concerne le patient : ".$this->patient->getSexe ( )." ".$this->patient->getPrenom ( )." ".
      strtoupper ( $this->patient->getNom ( ) )." (".$duree->getAge ( $age -> getTimestamp ( ) ).")<br/>" ;
    $message .= "Numéro de séjour : ".$this->patient->getNSEJ ( )."<br />" ;
    $message .= "ILP : ".$this->patient->getILP ( )."<br />" ;
    $message .= "Médecin traitant : ".$this->patient->getMedecinTraitant ( )."<br /><hr />" ;
    if ( $this->patient->getDateAdmission ( ) != '0000-00-00 00:00:00' )
    	$message .= "Admis aux urgences le : ".$adm->getDate ( "d-m-Y à H:i" )."<br />" ;
    else $message .= "Admis aux urgences le : --<br />" ;
    $message .= "Pour ".$this->patient->getRecours ( )." (".$this->patient->getCategorieRecours ( ).")<br />" ;
    if ( $this->patient->getDateExamen ( ) != '0000-00-00 00:00:00' )
    	$message .= "Heure Examen : ".$exa->getDate ( "d-m-Y à H:i" )."<br />" ;
    else $message .= "Heure Examen : --<br />" ;
    $message .= "Dest. confirmée : ".($this->patient->getDestinationAttendue()?$this->patient->getDestinationAttendue():'--')."<br />" ;
    $message .= "Message : ".stripslashes($_POST['observations']) ;
    // Préparation des destinataires du message.
    $listeGen = new clListesGenerales ( "recup" ) ;

    // Type de message.
    switch ( $_POST['type'] ) {
    case 'conflit': 
      $sujet = "[Terminurg] Signalement conflit" ; 
      if ( ! $listeMails ) $listeMails = "Mails Conflit" ;
      break ;
    case 'social': 
      $sujet = "[Terminurg] Signalement social" ;
      if ( ! $listeMails ) $listeMails = "Mails Gériatrie" ; 
      break ;
    case 'virus':
      $sujet = "[Terminurg] ".$_POST['sendMessage'] ;
      if ( ! $listeMails ) $listeMails = "Mails Alerte Virus" ;
      break ;
    case 'non uhcd':
      $sujet = "[Terminurg] Etat UHCD à vérifier" ;
      if ( ! $listeMails ) $listeMails = "Mails Alerte UHCD" ;
    break;
    case 'sortie forcée':
      $sujet = "[Terminurg] Sortie forcée détectée" ;
      if ( ! $listeMails ) $listeMails = "Mails Sortie forcée" ;
    break;
    default: 
      $sujet = "[Terminurg] Procédure dépistage maltraitance" ;
      if ( ! $listeMails ) $listeMails = "Mails Maltraitance" ;
      break ;
    }

	//eko ( "test" ) ;

    $liste = $listeGen -> getListeItems ( $listeMails, "1", '', '' ) ;
    $to = '' ;
    if ( ! is_array ( $liste ) ) $liste = array ( ) ;
    while ( list ( $key, $val ) =  each ( $liste ) ) {
      $dest .= $val."<br />" ;
      if ( $dest2 ) $vir = "," ; else $vir = "" ;
      $dest2 .= $vir.$val ;
      if ( ! $to ) $to = $val ;
      else $dest3 .= "To: $val\r\n" ;
    }
	// print $dest2 ;
    // Si une confirmation a été transmise, alors on ajoute et on envoie le nouveau message.
    if ( $_POST['Envoyer'] OR $_POST['Envoyer_x'] ) {
      $date = new clDate ( ) ;
      $data['idpatient'] = $this->patient->getID ( ) ;
      $data['dt_mail'] = $date->getDatetime ( ) ;
      $data['contenu'] = addslashes($message."<hr />".nl2br ( stripslashes($_POST['observations']) )) ;
      $data['nsej'] = $this->patient->getNSEJ ( ) ;
      if ( $_POST['type'] ) $data['type_mail'] = $_POST['type'] ;
      else $data['type_mail'] = "Procédure dépistage maltraitance" ;

      $requete = new clRequete ( BDD, MAILSLOGS, $data ) ;
      $requete->addRecord ( ) ;

      // eko ( $sujet ) ;
      // Préparation des informations du mail.
      $entete  = "<html><head><title>$subject</title><body>" ;
      $fin     = "</table></body></html>" ;
      $message = $entete.$message.$fin ;
      $headers  = "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
      $headers .= "From: ".Erreurs_NomApp." <".Erreurs_MailApp.">\r\n";
      //$headers .= "Bcc: dborel@ch-hyeres.fr\r\n";
      if ( $options->getOption('SMTP_BCC') ) {
      	$headers .= "Cc: <".$options->getOption('SMTP_BCC').">\r\n" ;
      	$cc = $options->getOption('SMTP_BCC') ;
      } else $cc = '' ;
      // Envoi du mail.
      // mail ( $dest2, $sujet, $message, $headers ) ;
	  if ( $options->getOption ( 'SMTP_Type' ) == 'localhost' )
      	mail ( $dest2, $sujet, $message, $headers ) ;
      else { 
      	//$headers .= 'To: '.$dest2."\r\n" ;
      	// print $headers ;
      	if ( $options->getOption ( 'SMTP_Type' ) == 'autreAvecAuth' ) $auth = 1 ; else $auth = 0 ;
      	sock_mail ( $auth, $dest2, $sujet, $message, $headers, Erreurs_MailApp, $cc ) ;
      }
	  //eko ( $dest2 ) ;
    // Sinon, on affiche le formulaire d'envoi / création de message.
    } else {
      // Chargement du template ModeliXe.
      $mod = new ModeliXe ( "Messages.mxt" ) ;
      $mod -> SetModeliXe ( ) ;
      // Affichage des différents champs.
      $mod -> MxBloc ( "view", "modify", " " ) ;
      $mod -> MxBloc ( "list", "modify", " " ) ;
      $mod -> MxText ( "new.nomApplication", NOMAPPLICATION ) ;
      $mod -> MxText ( "new.mailApplication", Erreurs_MailApp ) ;
      $mod -> MxText ( "new.nomsDestinataires", $dest ) ;
      $mod -> MxCheckerField ( "new.type1", "radio", "type", "maltraitance", ($_POST['type']=="maltraitance"or!$_POST['type'])?1:0 ) ;
      $mod -> MxCheckerField ( "new.type2", "radio", "type", "conflit", ($_POST['type']=="conflit")?1:0 ) ;
      $mod -> MxCheckerField ( "new.type3", "radio", "type", "social", ($_POST['type']=="social")?1:0 ) ;
      $mod -> MxText ( "new.sujet", $sujet ) ;
      $mod -> MxText ( "new.message", $message ) ;
      $form = new clForm ( ) ;
      $mod -> MxText ( "new.observations", $form -> genTextArea ( "observations", stripslashes($_POST['observations']), 45, 4, "width: 420px; height: 80px;" ) ) ;
      // Variable de navigation dans le cas d'une action normale.
      $mod -> MxHidden ( "new.hidden1", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "newMessage" ) ) ;
      // Variable de navigation dans le cas d'une action d'annulation.
      $mod -> MxHidden ( "new.hidden2", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
      // Récupération du code HTML généré.
      return $mod -> MxWrite ( "1" ) ;
    }
  }
  
//Recuperation au format resultQuery de l'historique AS400, merci à Jacques Chareyron
  function getHistoriqueAs400()
  {
    global $options ;
    global $errs ;

    //nécessite la configuration du systeme en odbc, voir wiki
    $login = $options->getOption('As400_Login');
    $pass = $options->getOption('As400_Pass');
    $dsn = $options->getOption('As400_Dsn');
    $conn = odbc_connect($dsn,$login,$pass);

    if ($conn <= 0)
    {
        $errs->addErreur('Connexion AS400 impossible');
        return array('INDIC_SVC'=>array(2=>0));
    }
    $ipp = $this->patient->getIDU() ;
    $query = "SELECT MHIUHO, MHCETA, UFCIUF, UFNMUF, MHJENT, MHMENT, MHSENT, MHAENT, MHJSOR, MHMSOR, MHSSOR, MHASOR FROM PIHOPI416.MHP01, PIHOPI416.MVP01, PIHOPI416.UFP01 WHERE MHIUPA='".$ipp."' AND MHCANN<>'A' AND MHIUHO=MVIUHO AND MVIUUF=UFIUUF AND MHJENT=MVJOUR AND MHMENT=MVMOIS AND MHSENT=MVSIEC AND MHAENT=MVANNE AND MHHREN=MVHEUR ORDER BY MHIUHO DESC";
    $result = odbc_exec($conn, $query);
    if ($result == 0)
    {
        $errs->addErreur('Requete vers AS400 incorrecte '.odbc_error().':'.odbc_errormsg());
        odbc_close($conn);
        return array('INDIC_SVC'=>array(2=>0));
    }

    //if (odbc_num_rows($result) == 0)  //marche pas : toujours -1
    //{
     // odbc_close($conn);
    //    return array('INDIC_SVC'=>array(2=>0));
    //}

    $nbPassages = -1 ;
    $tabRetour = array();
    while (odbc_fetch_row($result)) {
        $nbPassages ++ ;
        $tabRetour['IDPASS'][$nbPassages] = odbc_result($result,1) ;
        $tabRetour['ENTREE'][$nbPassages] = sprintf("%02d",odbc_result($result,5))."/".sprintf("%02d",odbc_result($result,6))."/".sprintf("%02d",odbc_result($result,7)).sprintf("%02d",odbc_result($result,8));
        $tabRetour['LIBELLE_UF'][$nbPassages] = odbc_result($result,4);
        $tabRetour['DTSOR'][$nbPassages] = sprintf("%02d",odbc_result($result,9))."/".sprintf("%02d",odbc_result($result,10))."/".sprintf("%02d",odbc_result($result,11)).sprintf("%02d",odbc_result($result,12)) ;
        if ($tabRetour['DTSOR'][$nbPassages]='00/00/0000')
        {
            $tabRetour['DTSOR'][$nbPassages]='';
        }
        //$etat_dossier=odbc_result($result,2);
        //$code_uf=odbc_result($result,3);
    }
    $tabRetour['INDIC_SVC'][2] = $nbPassages + 1 ;
  odbc_close($conn);
  return $tabRetour ;
  }

  // Affichage de l'historique d'un patient dans l'établissement.
  function Historique ( ) {
     global $options ;
    // Récupération des passages précédents.
    $param[IDU] = $this->patient->getIDU ( ) ;
    $req = new clResultQuery ;
    if ( ! $this->patient->getManuel ( ) ) {
         switch($options->getOption('ProvenanceHistorique')) {
			 case 'as400':
				$res = $this->getHistoriqueAs400();
				break ;
             case 'requete':
             default :
				$res = $req -> Execute ( "Fichier", "getHistorique", $param, "ResultQuery" ) ;
         }
     }
     else $res['INDIC_SVC'][2] = 0 ; 
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "HistoriquePatient.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage des différents champs.
    if ( ! $res['INDIC_SVC'][2] ) {
      $mod -> MxBloc ( "nomscolonnes", "modify", " " ) ;
      $mod -> MxBloc ( "hospitalisations", "modify", " " ) ;
    } else {
      $mod -> MxBloc ( "aucunehospitalisation", "modify", " " ) ;
      for ( $i = 0 ; isset ( $res['IDPASS'][$i] ) ; $i++ ) {
	$mod -> MxText ( "hospitalisations.nsej", $res['IDPASS'][$i] ) ;
	$mod -> MxText ( "hospitalisations.entree", $res['ENTREE'][$i] ) ;
	$mod -> MxText ( "hospitalisations.admission", $res['LIBELLE_UF'][$i] ) ;
	$mod -> MxText ( "hospitalisations.sortie", $res['DTSOR'][$i] ) ;
	// Boucle sur le bloc hospitalisations.
	$mod -> MxBloc ( "hospitalisations", "loop" ) ;
      }
    }
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }
  
  // Affichage de l'historique d'un patient via le terminal des urgences.
  function HistoriqueUrgences ( ) {
    // Récupération des passages précédents.
    $param['cw'] = "WHERE idu='".$this->patient->getIDU ( )."'" ;
    $param['table'] = PSORTIS ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ; 
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "HistoriquePatientUrgences.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    $date = new clDate ( DATELANCEMENT ) ;
    $mod -> MxText ( 'dateHistorique', $date->getDate ( "d/m/Y" ) ) ;
    // Affichage des différents champs.
    if ( ! $res['INDIC_SVC'][2] ) {
      $mod -> MxBloc ( "nomscolonnes", "modify", " " ) ;
      $mod -> MxBloc ( "passages", "modify", " " ) ;
    } else {
      $mod -> MxBloc ( "aucunpassage", "modify", " " ) ;
      for ( $i = 0 ; isset ( $res['nsej'][$i] ) ; $i++ ) {
	$mod -> MxText ( "passages.nsej", $res['nsej'][$i] ) ;
	$date -> setDate ( $res['dt_admission'][$i] ) ;
	$mod -> MxText ( "passages.entree", $date->getDate ( 'd/m/y H:i' ) ) ;
	$mod -> MxText ( "passages.diag", $res['diagnostic_libelle'][$i] ) ;
	$date -> setDate ( $res['dt_sortie'][$i] ) ;
	$mod -> MxText ( "passages.sortie", $date->getDate ( 'd/m/y H:i' ) ) ;
	// Boucle sur le bloc hospitalisations.
	$mod -> MxBloc ( "passages", "loop" ) ;
      }
    }
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }  

  // Affichage de l'historique d'un patient dans l'établissement.
  function HistoriqueDocs ( ) {
    global $session;
    if ( $session->getNavi(3) == 'genDoc' ) {
	global $stopAffichage ;
	$stopAffichage = 1 ;
	//	print "Récupération du document numéro ".$session->getNavi(4) ;
	$param['DOCNUM'] = $session->getNavi(4) ;
	$req = new clResultQuery ;
	$res = $req -> Execute ( "Fichier", "getHistoriqueDoc", $param, "ResultQuery" ) ;
	//print affTab($res['INDIC_SVC']);
	//print $res['URLDOC'][0];
	$nomFic = "doc".date('YmdHis').".doc" ;
	$FIC = fopen ( URLCACHE.$nomFic, 'w' ) ;
	$t = '' ;
        $t = $res['URLDOC'][0] ;
        for ( $i = 1 ; isset ( $res['URLDOC'][$i] ) ; $i++ ) {
                $t .= '|'.$res['URLDOC'][$i] ;  
                //print $res['URLDOC'][$i] ;^M
        }
    fwrite ( $FIC, $t ) ;
	//fwrite ( $FIC, $res['URLDOC'][0] ) ;
	fclose ( $FIC ) ;
	$poub = new clPoubelle ( URLCACHE ) ;
	$poub -> purgerRepertoire ( '2' ) ;
	 header ( 'Location:'.URL.'cache/'.$nomFic ) ;
    }
    // Récupération des passages précédents.
    $param['ILP'] = $this->patient->getILP ( ) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getHistoriqueDocs", $param, "ResultQuery" ) ; 
    //eko ( $res ) ;
    //eko ( $res['INDIC_SVC'] ) ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "HistoriqueDocsPatient.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage des différents champs.
    if ( ! $res['INDIC_SVC'][2] ) {
      $mod -> MxBloc ( "nomscolonnes", "modify", " " ) ;
      $mod -> MxBloc ( "hospitalisationdocs", "modify", " " ) ;
    } else {
      $mod -> MxBloc ( "aucunhospitalisationdoc", "modify", " " ) ;
      for ( $i = 0 ; isset ( $res['TITREDOC'][$i] ) ; $i++ ) {
	$mod -> MxText ( "hospitalisationdocs.nsej", $res['SEJDOC'][$i] ) ;
	$date = new clDate ( $res['DTDOC'][$i] ) ;
	//eko ( $res[DTDOC][$i]." - ".$date->getDateTextFull ( ) ) ;
	$mod -> MxText ( "hospitalisationdocs.date", $date->getDate ( "d-m-Y" ) ) ;
	if ( $res['URLDOC'][$i] )
		$mod -> MxText ( "hospitalisationdocs.titre", "<a href=\"".$res['URLDOC'][$i]."\" target=\"_new\">".$res['TITREDOC'][$i]."</a>" ) ;
	else {
		if ( $res['DOCNUM'][$i] ) {
        	$mod -> MxText ( "hospitalisationdocs.titre", "<a href=\"".
            URLNAVI.$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2),'genDoc',$res['DOCNUM'][$i])."\" target=\"_new\">".$res['TITREDOC'][$i]."</a>" ) ;
        } else $mod -> MxText ( "hospitalisationdocs.titre", $res['TITREDOC'][$i] )  ;
	}
		
	// Boucle sur le bloc hospitalisations.
	$mod -> MxBloc ( "hospitalisationdocs", "loop" ) ;
      }
    }
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }

/*
  // Affichage de l'historique d'un patient dans l'établissement.
  function HistoriqueDocs ( ) {
    // Récupération des passages précédents.
    $param['ILP'] = $this->patient->getILP ( ) ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getHistoriqueDocs", $param, "ResultQuery" ) ; 
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "HistoriqueDocsPatient.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    // Affichage des différents champs.
    if ( ! $res['INDIC_SVC'][2] ) {
      $mod -> MxBloc ( "nomscolonnes", "modify", " " ) ;
      $mod -> MxBloc ( "hospitalisationdocs", "modify", " " ) ;
    } else {
      $mod -> MxBloc ( "aucunhospitalisationdoc", "modify", " " ) ;
      for ( $i = 0 ; isset ( $res['TITREDOC'][$i] ) ; $i++ ) {
	$mod -> MxText ( "hospitalisationdocs.nsej", $res['SEJDOC'][$i] ) ;
	$date = new clDate ( $res['DTDOC'][$i] ) ;
	//eko ( $res[DTDOC][$i]." - ".$date->getDateTextFull ( ) ) ;
	$mod -> MxText ( "hospitalisationdocs.date", $date->getDate ( "d-m-Y H:i" ) ) ;
	$mod -> MxText ( "hospitalisationdocs.titre", "<a href=\"".$res['URLDOC'][$i]."\" target=\"_new\">".$res['TITREDOC'][$i]."</a>" ) ;
	// Boucle sur le bloc hospitalisations.
	$mod -> MxBloc ( "hospitalisationdocs", "loop" ) ;
      }
    }
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }
*/

  // Impression de documents.
  function Documents ( ) {
    global $session ;
    if ( ( $session -> getNavi ( 3 ) == "newEdition" OR $session -> getNavi ( 3 ) == "genEdition" ) AND $session->getDroit ( $this->type."_Documents", "w" ) AND ( ! $_POST['Annuler'] AND ! $_POST['Annuler_x'] ) ){
      $session->setLogSup ( 'Lancement du module d\'impression' ) ;
      $af .= $this->imprDocuments ( ) ;
    }
    // Récupération des passages précédents.
    $param[cw] = "WHERE idpatient='".$this->patient->getID()."' ORDER BY date DESC" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getDocumentsEdites", $param, "ResultQuery" ) ;
    // newfct ( gen_affiche_tableau, $res[INDIC_SVC] ) ;
    // Chargement du template ModeliXe.
    $mod = new ModeliXe ( "DocumentsEdites.mxt" ) ;
    $mod -> SetModeliXe ( ) ;
    if ( $session->getDroit ( $this->type."_Documents", "w" ) AND ! $this->export) {
      $mod -> MxImage ( "imgNewEdition", URLIMGEDI, "Nouvelle édition de document" ) ;
      $mod -> MxUrl  ( "lienNewEdition", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "newEdition" ) ) ;    
    }
    // Affichage des différents champs.
    if ( ! $res['INDIC_SVC'][2] ) {
      $mod -> MxBloc ( "nomscolonnes", "modify", " " ) ;
      $mod -> MxBloc ( "docs", "modify", " " ) ;
    } else {
      $mod -> MxBloc ( "aucundoc", "modify", " " ) ;
      for ( $i = 0 ; isset ( $res['idedition'][$i] ) ; $i++ ) {
	    $date = new clDate ( $res['date'][$i] ) ;
	    $mod -> MxText ( "docs.date", $date -> getDate ( 'd/m/y H:i' ) ) ;
	    $mod -> MxText ( "docs.nom", $res['nomedition'][$i] ) ;
	    if ( ereg ( '^http://', $res['urledition'][$i] ) )
	      $mod -> MxText ( "docs.action", "<a href=\"".$res['urledition'][$i]."\" target=\"_new\" ><img src=\"".URLIMGPDF."\" alt=\"Voir\" /></a>" ) ;
	    else $mod -> MxText ( "docs.action", "<a href=\"".URLDOCSWEB.$res['urledition'][$i]."\" target=\"_new\" ><img src=\"".URLIMGPDF."\" alt=\"Voir\" /></a>" ) ;
	    // Boucle sur le bloc hospitalisations.
	    $mod -> MxBloc ( "docs", "loop" ) ;
      }
    }
    // Récupération du code HTML généré.
    return $mod -> MxWrite ( "1" ) ;
  }

  // Cette fonction génère la fenêtre gérant l'impression de documents.
  function imprDocuments ( ) {
    global $session ;
    global $options ;
	// Si on se sert du module externe de documents (Brignoles) 
    if ( $options -> getOption ( "documentsExterne" ) ) {
	    $this->err1 = "";
	    $this->err2 = "";
		// Chargement du template ModeliXe
	    $mod = new ModeliXe ( "Mobidoc.html" ) ;
	    $mod -> SetModeliXe ( ) ;
	    $mod -> MxText ( "errs", $this->err1."<br/>".$this->err2 ) ;
	    // Récupération du code HTML généré. 
	    $mod -> MxHidden ( "hidden", "navi=".$session -> genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
	    
	    // CHB Start
      	$cMobidoc = new clMobidocTU($this->patient->getID() );
      	$str = $cMobidoc->OpenMobidoc();
		//eko ( $str ) ;
		
        if ( ereg('NACK-',$str ) ) {
        	$mod -> MxText ( "errs", "<b>L'appel à mobidoc a échoué (Erreur: COMRPC Impossible)</b>'") ;
        }
        // CHB Stop
	    /* Ancienne version
	    $str = OpenMobidoc($this->patient->getID ( ),$_SERVER["REMOTE_ADDR"],$options->getOption('CCAMExterne_MRPCPORT'),$options->getOption('CCAMExterne_MRPCTIMEOUT'));
	    //$str = 'ERROR' ;
	    if ( $str == 'ERROR' ) {
	    	$mod -> MxText ( "errs", "<b>L'appel à mobidoc a échoué (Erreur: COMRPC Impossible)</b>'") ;	
	    }
	    */	    
	    return $this->af .= $mod -> MxWrite ( "1" ) ;
    } else {
	    if ( $_POST['Imprimer'] OR $_POST['Imprimer_x'] ) {
	      $session->setLogSup ( 'Impression de documents' ) ;
	      $sel = $_POST['selection'] ;
	      $docs = new clDocuments ( "impr" ) ;
	      $date = new clDate ( ) ;
	      for ( $i = 0 ; isset ( $sel[$i] ) ; $i++ ) {
		$param[cw] = "WHERE iddocument='".$sel[$i]."'" ;
		$req = new clResultQuery ;
		$ras = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;
		$rep = $date->getYear()."/".$date->getMonthNumber()."/";
		$output[$i] = $date->getTimestamp()."-".$this->patient->getID()."-".$sel[$i].".pdf" ;
		$data['idpatient'] = $this->patient->getID ( ) ;
		$data['iddocument'] = $sel[$i] ;
		$data['nomedition'] = $ras['nom'][0] ;
		$data['urledition'] = $rep.$output[$i] ;
		$data['iduser'] = $session->getUser ( ) ;
		$data['date'] = $date->getDatetime ( ) ;
		$requete = new clRequete ( BDD, DOCSEDITES, $data ) ;
		$requete->addRecord ( ) ;
	      }
	      $rep = $date->getYear()."/".$date->getMonthNumber()."/";
	      $docs -> genDoc ( $sel, $this->patient->getID ( ), $output, URLDOCS.$rep, $rep ) ;
	    } else {
	      // Chargement du template ModeliXe.
	      $mod = new ModeliXe ( "ImprimerDocuments.mxt" ) ;
	      $mod -> SetModeliXe ( ) ;
	      $mod -> MxImage ( "imgCloseImprimer", URLIMGFER, "Fermer" ) ;
	      $mod -> MxUrl  ( "lienCloseImprimer", URLNAVI.$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;    
	      
	      // Récupération de la liste des catégories de documents.
	      $param['cw'] = " ORDER BY categorie" ;
	      $req = new clResultQuery ;
	      $res = $req -> Execute ( "Fichier", "getCategoriesDocuments", $param, "ResultQuery" ) ;  
	      // Parcours des différentes catégories de documents.
	      for ( $i = 0 ; isset ( $res['categorie'][$i] ) ; $i++ ) {
		// Recherche des dernières versions des documents de la catégorie parcourue.
		$param['cw'] = "WHERE categorie='".$res['categorie'][$i]."' AND fin_validite='0000-00-00 00:00:00' ORDER BY nom" ;
		$req = new clResultQuery ;
		$ras = $req -> Execute ( "Fichier", "getDocuments", $param, "ResultQuery" ) ;  
		// Si on a un résultat, alors on parcourt ces documents.
		if ( $ras['INDIC_SVC'][2] ) {
		  // Et on affiche le titre de la catégorie.
		  $mod -> MxText ( "categorie.titre", $res['categorie'][$i] ) ;
		  $k = 0 ;
		  for ( $j = 0 ; isset ( $ras['iddocument'][$j] ) ; $j++ ) {
		    // Nom du document.
		    $param['cw'] = "WHERE iddocument='".$ras['iddocument'][$j]."' AND idpatient='".$this->patient->getID()."'" ;
		    $req = new clResultQuery ;
		    $rus = $req -> Execute ( "Fichier", "getDocumentsEdites", $param, "ResultQuery" ) ;
		    if ( $rus['INDIC_SVC'][2] ) $td = "<td class=\"dejaedite\" style=\"text-align: left;\">" ; else $td = "<td style=\"text-align: left;\">" ;
		    if ( ( ! ( $j % ( $options -> getOption ( "DocumentsParLigne" ) ) ) ) AND $j != 0 ) { $tr = "<tr>" ; if ( $k ) $tr .= "<td class=\"nostyle\"></td>" ; } else $tr = "" ;
		    if ( ! ( ($j+1) % ( $options -> getOption ( "DocumentsParLigne" ) ) ) OR ( ! isset ( $ras['iddocument'][$j+1] ) ) ) { $ftr = "</tr>" ; $k++; } else $ftr = "" ;
		    $mod -> MxText ( "categorie.documents.tdo", $tr.$td ) ;
		    $mod -> MxCheckerField ( "categorie.documents.c", "checkbox", "selection[]", $ras['iddocument'][$j] ) ;
		    $mod -> MxText ( "categorie.documents.doc", $ras['nom'][$j] ) ;
		    $mod -> MxText ( "categorie.documents.tdf", "</td>$ftr" ) ;
		    $mod -> MxBloc ( "categorie.documents", "loop" ) ;
		  }
		  $mod -> MxBloc ( "categorie", "loop" ) ;
		}
	      }
	      // Variable de navigation dans le cas du lancement des éditions.
	      $mod -> MxHidden ( "hidden1", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2), "genEdition" ) ) ;
	      // Variable de navigation dans le cas d'une action d'annulation.
	      $mod -> MxHidden ( "hidden2", "navi=".$session->genNavi ( $session->getNavi(0), $session->getNavi(1), $session->getNavi(2) ) ) ;
	      // Récupération du code HTML généré.
	      $this->af .= $mod -> MxWrite ( "1" ) ;
	    }
     }
  }  

  // Retourne l'affichage généré par la classe.
  function getAffichage ( ) {
    if ( $this->rien ) return "RIEN" ;
	if ( is_object( $this->formulaires) )
		$this->af.=$this->formulaires->getAffichage() ;
		
		//eko($this->type);
		//eko($this->table);
    	//$AEV = new clAEV ();
   		//$this->af.=$AEV->getAffichage ();
    
    return $this->af ;
  }

}

?>
