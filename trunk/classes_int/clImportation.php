<?php

// Titre  : Classe Importation
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 25 Avril 2005

// Description : 
// Gestion des contraintes pour autoriser la sortie d'un patient.

class clImportation {

  // Attributs de la classe.
  // Contient l'affichage généré par la classe.
  private $af ;
  // Type de l'importation en cours (vide pour le mode automatique,
  // 'manuel' pour le mode... manuel !).
  private $type ;

  // Constructeur.
  function __construct ( $type='' )
  {
	//appel de la tache planifiee enquetes	
	clTuFormxTrigger::crontab();
    $this->type = $type ;
    new clTbExport();
  }

  // Lancement de l'import automatique.
  function runImport ( ) {
    global $errs ;
    global $options ;
/*    
    if ( $options->getOption ( 'HprimXML_Actif' ) ) {
    	$hprimXml = new clHprimXML ( ) ;
    }    
*/
    if ( $this->checkImport ( ) ) {
    	if ( file_exists ( 'modules/Importation.php') ) {
	      		include_once ( 'modules/Importation.php' ) ;
    	} elseif ( $options->getOption ( 'typeImport' ) == 'BALMySQL' ) {
	      	if ( file_exists ( 'modules/Importation.php') ) {
	      		include_once ( 'modules/Importation.php' ) ;
	      	} else {
		      	$mod = new ModeliXe ( "Importation.mxt" ) ;
	    	  	$mod -> SetModeliXe ( ) ;
	      		$this->news1 = 0 ;
	      		$this->mods1 = 0 ;
	      		$this->errs1 = 0 ;
	      		// Lancement des imports en provenance de la BAL MySQL.
	      		$this->runImportSQL ( ) ;
	      		// Nombre d'entrées de la BAL MySQL
	      		$mod -> MxText ( "titre", "Table imports dans la base MySQL." ) ;
	      		$mod -> MxText ( "total1", $this->news1 + $this->mods1 + $this->errs1 ) ;
	      		$mod -> MxText ( "news1", $this->news1 ) ;
	      		$mod -> MxText ( "modif1", $this->mods1 ) ;
	      		$mod -> MxText ( "errs1", $this->errs1 ) ;
	      		// Récupération du code HTML généré.
	      		$this->af .= $mod -> MxWrite ( "1" ) ;
	      		// Pour débugage des imports automatiques (un mail est envoyé à chaque import...).
	      		// $errs -> addErreur ( "Résultats de l'import automatique : <br>".$this->af ) ;
	      	}
    	} elseif ( $options->getOption ( 'typeImport' ) == 'HPRIM' ) {
    		$hprim = new clHprim ( ) ;
    		$this->af .= $hprim -> getAffichage ( ) ;
    	} elseif ( $options->getOption ( 'typeImport' ) == 'HL7' ) {
    		$hl7 = new clHL7 ( ) ;
    		$this->af .= $hl7 -> getAffichage ( ) ;
    	}
    }

  }

  // Lance l'import des données.
  function runImportSQL ( ) {
    // Récupération des entrées à importer.
    $param['cw'] = "WHERE dt_traitement='0000-00-00 00:00:00'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getImports", $param, "ResultQuery" ) ; 
    // Parcours des entrées trouvées.
    for ( $i = 0 ; isset ( $res['idimport'][$i] ) ; $i++ ) {
      // On vérifie que l'entrée n'existe pas déjà dans la table des patients présents.
      $param2['table'] = PPRESENTS ;
      $param2['cw'] = "WHERE nsej='".$res['idpass'][$i]."'" ;
      $ras = $req -> Execute ( "Fichier", "getPatients", $param2, "ResultQuery" ) ;
      // On vérifie que l'entrée n'existe pas déjà dans la table des patients sortis.
      $param3['table'] = PSORTIS ;
      $param3['cw'] = "WHERE nsej='".$res['idpass'][$i]."'" ;
      $rus = $req -> Execute ( "Fichier", "getPatients", $param3, "ResultQuery" ) ;
      if ( $ras['INDIC_SVC'][2] ) {
	$this->majPatientSQL ( $res['idimport'][$i], $ras['idpatient'][0], PPRESENTS ) ;
      } elseif ( $rus['INDIC_SVC'][2] ) {
	$this->majPatientSQL ( $res['idimport'][$i], $rus['idpatient'][0], PSORTIS ) ;
      } else {
	$this->addPatientSQL ( $res['idimport'][$i], PPRESENTS ) ;
      }
    }
  }
  
  /*
   #0:     t:=t+'\0';

        #$27:   t:=t+'\'+#$27;  // Apostrophe '

        '"':    t:=t+'\"';

        #9:     t:=t+'\b';      // backspace

        #13:    t:=t+'\r';      // carriage return

        #10:    t:=t+'\n';      // new Line

        #8:     t:=t+'\t';      // tab

        #26:    t:=t+'\z';      // ctrl-z; EOF

        '\':    t:=t+'\\';

        '%':    t:=t+'\%';

        '_':    t:=t+'\_';


   */

  function filtreHubHL7 ( $str ) {
  	$str = str_replace ( '#0', '\0', $str ) ;
  	$str = str_replace ( '#$27', '\#$27', $str ) ;
  	$str = str_replace ( '"', '\"', $str ) ;
  	$str = str_replace ( '#9', '\b', $str ) ;
  	$str = str_replace ( '#13', '\r', $str ) ;
  	$str = str_replace ( '#10', '\n', $str ) ;
  	$str = str_replace ( '#8', '\t', $str ) ;
  	$str = str_replace ( '#26', '\z', $str ) ;
  	$str = str_replace ( '\\', '\\\\', $str ) ;
  	$str = str_replace ( '%', '\%', $str ) ;
  	$str = str_replace ( '_', '\_', $str ) ;
  	return $str ;
  }  

  // Ajout d'un patient dans une des tables du terminal (présents ou sortis).
  function addPatientSQL ( $idimport, $table ) {
    global $errs ;
    global $options ;
    // Récupération des informations sur le patient à créer.
    $param['cw'] = "WHERE idimport='$idimport'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getImports", $param, "ResultQuery" ) ; 
    if ( $res['INDIC_SVC'][2] ) {
      $i = 0 ;
      $data['idu']                  = $res['idu'][$i] ;	
      $data['ilp']                  = $res['ilp'][$i] ;	
      $data['nsej']                 = $res['idpass'][$i] ;	
      $data['uf']                   = $res['uf'][$i] ;	
      $data['nom']                  = $res['nom'][$i] ;	
      $data['prenom']               = $res['prenom'][$i] ;	
      $data['sexe']                 = $res['sexe'][$i] ;	
      $data['dt_naissance']         = $res['dt_naissance'][$i] ;	
      $data['adresse_libre']        = $res['adresse_libre'][$i] ;	
      $data['adresse_cp']           = $res['adresse_cp'][$i] ;	
      $data['adresse_ville']        = $res['adresse_ville'][$i] ;	
      $data['telephone']            = $res['telephone'][$i] ;	
      $data['prevenir']             = str_replace( '^', '<br/>', $res['prevenir'][$i] ) ;	
      $data['medecin_traitant']     = $res['medecin_traitant'][$i] ;	
      $data['dt_admission']         = $res['dt_admission'][$i] ;
      $data['mode_admission']       = $res['mode_admission'][$i] ;	
      $data['iduser']               = "IMPORT" ;	
      $data['manuel']               = 0 ;
      if ( $data['uf'] == $options -> getOption ( 'numUFsansRPU' ) ) {
      	$data['valide'] = 0 ;
      	$data['etatUHCD'] = 'noCriteres' ;
      } 
      //newfct ( gen_affiche_tableau, $data ) ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BDD, $table, $data ) ;
      // Exécution de la requete.
      $requete->addRecord ( ) ;

      // Mise à jour de la date de traitement de l'import.
      $date = new clDate ( ) ;
      $data2['dt_traitement'] = $date -> getDatetime ( ) ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BDD, IMPORTS, $data2 ) ;
      // Exécution de la requete.
      $requete->updRecord ( "idimport='$idimport'" ) ;

      $this -> news1++ ;
    } else {
      // En cas d'erreur, on la signale...
      $errs -> addErreur ( "clImportation : Impossible d'importer ce nouveau patient, l'identifiant de l'import est introuvable (idimport=\"$idimport\")." ) ;
      $this -> errs1++ ;
    }
  }

  // Mise à jour des informations d'un patient dans une des tables du terminal (présents ou sortis).
  function majPatientSQL ( $idimport, $idpatient, $table ) {
    global $errs ;
    // Récupération des informations sur le patient à mettre à jour.
    $param['cw'] = "WHERE idimport='$idimport'" ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getImports", $param, "ResultQuery" ) ; 
    if ( $res['INDIC_SVC'][2] ) {
      $i = 0 ;
      $data['idu']                  = $res['idu'][$i] ;	
      $data['ilp']                  = $res['ilp'][$i] ;	
      $data['nsej']                 = $res['idpass'][$i] ;	
      $data['uf']                   = $res['uf'][$i] ;	
      $data['nom']                  = $res['nom'][$i] ;	
      $data['prenom']               = $res['prenom'][$i] ;	
      $data['sexe']                 = $res['sexe'][$i] ;	
      $data['dt_naissance']         = $res['dt_naissance'][$i] ;	
      $data['adresse_libre']        = $res['adresse_libre'][$i] ;	
      $data['adresse_cp']           = $res['adresse_cp'][$i] ;	
      $data['adresse_ville']        = $res['adresse_ville'][$i] ;	
      $data['telephone']            = $res['telephone'][$i] ;	
      $data['prevenir']             = str_replace( '^', '<br/>', $res['prevenir'][$i] ) ;	
      $data['medecin_traitant']     = $res['medecin_nom'][$i] ;	
      $data['dt_admission']         = $res['dt_admission'][$i] ;
      $data['mode_admission']       = $res['mode_admission'][$i] ;	
      $data['iduser']               = "IMPORT" ;	
      $data['manuel']               = 0 ;
      //newfct ( gen_affiche_tableau, $data ) ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BDD, $table, $data ) ;
      // Exécution de la requete.
      $requete->updRecord ( "idpatient='$idpatient'" ) ;

      // Mise à jour de la date de traitement de l'import.
      $date = new clDate ( ) ;
      $data2['dt_traitement'] = $date -> getDatetime ( ) ;
      // Appel de la classe Requete.
      $requete = new clRequete ( BDD, IMPORTS, $data2 ) ;
      // Exécution de la requete.
      $requete->updRecord ( "idimport='$idimport'" ) ;

      $this -> mods1++ ;
    } else {
      // En cas d'erreur, on la signale...
      $errs -> addErreur ( "clImportation : Impossible d'importer ce nouveau patient, l'identifiant de l'import est introuvable (idimport=\"$idimport\")." ) ;
      $this -> errs1++ ;
    }
  }

  // Vérification des conditions de lancement de l'import automatique.
  function checkImport ( ) {
    global $options ;
    // Si nous sommes en mode manuel, on vérifie seulement que l'option d'import est activée.
    if ( $this->type ) {
      if ( $options -> getOption ( "ImportsAutomatiques" ) ) return 1 ;
    } else {
	  // Lancement automatique des alertes
	  $al = new clAlertes ( ) ;
      // Sinon, on vérifie que l'interval entre deux exécutions automatiques est respecté.
      $date = new clDate ( ) ;
      // Conversion en minutes
      $secs = 60 * $date -> getHours ( ) + $date -> getMinutes ( ) ;
      // Purge journalière des répertoires.
      if ( $date->getHours ( ) == 1 AND $date -> getMinutes ( ) == 0 ) $purge = new clPurge ( ) ;
      // Données ARH
      if ( $date->getHours ( ) == $options->getOption ( "ARH_Heure" ) AND $options->getOption ( "ARH_Actif" ) AND $date -> getMinutes ( ) == 0 ) $arh = new clEnvoiARH ( ) ;
      // RPU
      if ( $date->getHours ( ) == $options->getOption ( "RPU_Heure" ) AND $options->getOption ( "RPU_Actif" ) AND $date -> getMinutes ( ) == 0 ) { 
      	$_REQUEST['EnvoyerRPU'] = 1 ;
      	$rpu = new clRPU ( ) ;
      }
      // Calcul avec un modulo de l'interval requis... et on vérifie que l'option est bien activée.
      if ( ! ( $secs % $options -> getOption ( "ImportsDelai" ) ) AND $options -> getOption ( "ImportsAutomatiques" ) ) return 1 ;
    }
  }

  // Renvoie l'affichage généré par la classe.
  function getAffichage ( ) {
    return $this->af ;
  }
}

?>
