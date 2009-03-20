<?php

// Titre  : Classe Purge
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 01 octobre 2008

// Description : 
// Purge du terminal des urgences.

class clReActes {

  	// Constructeur.
  	function __construct ( ) {
   		$this->showForm ( ) ;
   		if ( $_POST['replayNsej'] ) $this->replayNsej ( $_POST['nsej'] ) ;
   		if ( $_POST['replayNsejWithDelete'] ) $this->replayNsejWithDelete ( $_POST['nsej'] ) ;
   		if ( $_POST['remplacerCodeMed'] ) $this->remplacerCodeMedecin ( $_POST['codemed1'], $_POST['codemed2'] ) ;
   		if ( $_POST['remplacerCodeDiag'] ) $this->remplacerCodeDiag ( $_POST['diag1'], $_POST['diag2'] ) ;
   		if ( $_POST['remplacerUF'] ) $this->remplacerUF ( $_POST['uf1'], $_POST['uf2'] ) ;
  	}

	function showForm ( ) {
		global $session ;
		$mod = new ModeliXe ( "ReActes.html" ) ;
    	$mod -> SetModeliXe ( ) ;
    	$mod -> MxHidden ( 'hidden', "navi=".$session->genNaviFull ( ) ) ;
    	$this->af = $mod -> MxWrite ( "1" ) ;
	}

  function remplacerCodeMedecin ( $codemed1, $codemed2 ) {
    if ( $codemed1 AND $codemed2 ) {
      $data['matriculeIntervenant'] = $codemed2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
      $res = $requete->updRecord ( "matriculeIntervenant='".$codemed1."'" ) ;
      $nb = $res['affected_rows'] ;
      unset ( $data ) ;
      $data['matriculeIntervenant'] = $codemed2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
      $res = $requete->updRecord ( "matriculeIntervenant='".$codemed1."'" ) ;
      $nb += $res['affected_rows'] ;
      unset ( $data ) ;
      $data['code'] = $codemed2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
      $res = $requete->updRecord ( "code='".$codemed1."' AND categorie='ListeMédecins'" ) ;
      $nbm = $res['affected_rows'] ;
      $this->af .= "Remplacement du code médecin <b>$codemed1</b> par <b>$codemed2</b> dans $nb actes et dans $nbm médecins...<br/><br/>" ;
    }
  }

  function remplacerCodeDiag ( $diag1, $diag2 ) {
    if ( $diag1 AND $diag2 ) {
      $data['codeActe'] = $diag2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
      $res = $requete->updRecord ( "codeActe='".$diag1."'" ) ;
      $nbe = $res['affected_rows'] ;
      unset ( $data ) ;
      $data['code'] = $diag2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
      $res = $requete->updRecord ( "code='".$diag1."' AND categorie='Diagnostics'" ) ;
      $nbd = $res['affected_rows'] ;
      unset ( $data ) ;
      $data['idDiag'] = $diag2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_actes_diagnostic", $data ) ;
      $res = $requete->updRecord ( "idDiag='".$diag1."'" ) ;
      $nba = $res['affected_rows'] ;
      $this->af .= "Remplacement du code diagnostic <b>$diag1</b> par <b>$diag2</b> dans $nbe diagnostics rattachés à des patients, dans $nbd diagnostics configurés et dans $nba associations...<br/><br/>" ;
    }
  }

  function remplacerUF ( $uf1, $uf2 ) {
    if ( $uf1 AND $uf2 ) {
      $data['numUFexec'] = $uf2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
      $res = $requete->updRecord ( "numUFexec='".$uf1."'" ) ;
      $nbe = $res['affected_rows'] ;
      unset ( $data ) ;
      $data['numUFdem'] = $uf2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_cotation_actes", $data ) ;
      $res = $requete->updRecord ( "numUFdem='".$uf1."'" ) ;
      $nbe += $res['affected_rows'] ;
      unset ( $data ) ;
      $data['code'] = $uf2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_liste", $data ) ;
      $res = $requete->updRecord ( "code='".$uf1."' AND categorie='ListeMédecins' AND nomitem='LISTE'" ) ;
      $nbd = $res['affected_rows'] ;
      unset ( $data ) ;
      $data['numeroUF'] = $uf2 ;
      $requete = new clRequete ( CCAM_BDD, "ccam_uf_spe", $data ) ;
      $res = $requete->updRecord ( "numeroUF='".$uf1."'" ) ;
      $nbs = $res['affected_rows'] ;
      $this->af .= "Remplacement de l'UF <b>$uf1</b> par <b>$uf2</b> dans $nbe actes et dans $nbs spécialités...<br/><br/>" ;
    }
  }
	function replayNsej ( $nsej ) {
		global $session ;
  		global $fusion  ;
  		global $options ;
  		global $patient ;
  		$req = new clResultQuery ;
      	$param['table'] = PSORTIS ;
      	$param['cw'] = "WHERE nsej IN ($nsej)" ;
      	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      	//print affTab ( $res['INDIC_SVC'] ) ;
      	for ( $i = 0 ; isset ( $res['idu'][$i] ) ; $i++ ) {
	       	//print ($res['idu'][$i]) ;
	        $this->patient   = new clPatient ( $res['idpatient'][$i], "Sortis" ) ;
	        $patient = $this->patient ;
	        $this->defineParamCCAM ( ) ;
	        $fusion = 0 ;
	        //print affTab ( $this->paramCCAM ) ;
	        $cot = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
	        $cot -> writeBALSorti ( ) ;
      	}
      	if ( $options->getOption ( 'HprimXML_Actif' ) ) {
        	$hprim = new clHprimXML ( ) ;
        	$this->af .= $hprim->getAffichage ( ) ;
    	}
	}
	
	function replayNsejWithDelete ( $nsej ) {
		global $session ;
  		global $fusion  ;
  		global $options ;
  		global $patient ;
  		$req = new clResultQuery ;
      	$param['table'] = PSORTIS ;
      	$param['cw'] = "WHERE nsej IN ($nsej)" ;
      	$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      	//print affTab ( $res['INDIC_SVC'] ) ;
      	for ( $i = 0 ; isset ( $res['idu'][$i] ) ; $i++ ) {
	       	//print ($res['idu'][$i]) ;
	        $this->patient   = new clPatient ( $res['idpatient'][$i], "Sortis" ) ;
	        $patient = $this->patient ;
	        $this->defineParamCCAM ( ) ;
	        $fusion = 0 ;
	        //print affTab ( $this->paramCCAM ) ;
	        $cot = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
	        $cot -> deleteBAL ( ) ;
      	}
      	if ( $options->getOption ( 'HprimXML_Actif' ) ) {
        	$hprim = new clHprimXML ( ) ;
        	$this->af .= $hprim->getAffichage ( ) ;
    	}
      	for ( $i = 0 ; isset ( $res['idu'][$i] ) ; $i++ ) {
	       	//print ($res['idu'][$i]) ;
	        $this->patient   = new clPatient ( $res['idpatient'][$i], "Sortis" ) ;
	        $patient = $this->patient ;
	        $this->defineParamCCAM ( ) ;
	        $fusion = 0 ;
	        //print affTab ( $this->paramCCAM ) ;
	        $cot = new clCCAMCotationActesDiags ( $this->paramCCAM ) ;
	        $cot -> writeBALSorti ( ) ;
      	}
      	if ( $options->getOption ( 'HprimXML_Actif' ) ) {
        	$hprim = new clHprimXML ( ) ;
        	$this->af .= $hprim->getAffichage ( ) ;
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


  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	return $this->af ;
  	}
}

?>