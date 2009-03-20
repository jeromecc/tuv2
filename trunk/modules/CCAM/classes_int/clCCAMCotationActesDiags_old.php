<?php
/* Titre  : Classe Cotation des diagnostics et des actes CCAM & NGAP
	Auteur : Christophe BOULAY <cboulay@ch-hyeres.fr>
	Date   : 11 avril 2005

	Description : Affectation/Désaffectation des actes de la liste restreinte,
	ou associés au diagnostic en cours, pour le patient en cours
	
  ------------------------------------------------------------------------------
  ------------------------------------------------------------------------------
  Titre       : Ajout de nouvelles fonctionalitées au module des actes CCAM 
                de Christophe Boulay
  Auteur      : François Derock <fderock@ch-hyeres.fr>
  Date        : Juin 2007
  
  Description : Nouvelles fonctions :
  envoiActesNGAPdansMBTV2           : Envoi des actes NGAP à la facturation.
  envoiNGAPouCCAMdansMBTV2          : Comparaison des tarifs dans le cas des médecins Urgentistes & Spécialistes
  envoiDiagnosticsdansMBTV2         : Envoi des diagnostics à la facturation
  gestionEnvoiActeATU               : Gestion de la majoration ATU à la sortie des urgences.
  gestionEnvoiActeMTU               : Gestion du parcours du soins suivant la consultation.
  gestionMajorationsActesInfirmiers : Majoration suivant l’heure d’affectation des actes.
  gestionMajorationsNGAPlesCetCS    : Majoration suivant l’age du patient pour les actes NGAP.
  controleActesPresents             : Contrôle des actes presents dans la table
                                      ccam_cotation_actes
  
  Dear :
  Utilisation  des   requêtes  sql  et  non  plus  de   tableau
  comme c'était le cas  avant pour les fonctions que j'ai crée. 
  Optimisation des fonctions avec utilisation des requêtes sql.
  
  Modification   de   quelques  fonctions  de  Christophe  pour 
  adapter les besoins  des  utilisateurs  et  les modifications
  surtout         que                 j'ai             apporté.
  
  Ma participation à ce module consiste à apporter de nouvelles
  fonctionnalités.  Il    n'était     pas      question      de  
  modifier                le        code     de     Christophe.
  
  Il  ne  me  vient  pas  à  l ' esprit  aussi  d'être l'auteur
  ou  de  m ' approprier   le   code   source   de   Christophe.
  
  Best Regards.
  
  Ne pas effacer merci ----------------------------------------------------MEMO
  LISTE MAJO
  REQUEST MAJO
  
  -- CCAM_getConsultCotes permet de voir les differentes consultations 
  specialistes d'un patient
  -- CCAM_getActesCotes: liste l'ensemble des actes CCAM et les consultations urgentistes
  et spécialistes en dehors des majorations et ATU et MTU et des consultations
  spécialistes.
  -- CCAM_getActesNonListe : liste l'ensemble des actes CCAM sauf ATU et MTU
  -- CCAM_getActesNonListe_ccam : liste les actes ccam sauf ATU et MTU
  -- CCAM_getActesNonListe_ngap : liste les actes ngap sauf ATU et MTU
  -- CCAM_getActesNonListe_pack : liste des packs sauf ATU et MTU
  -- CCAM_getAutresActesNonListe : liste les packs sauf ATU et MTU
  -------------------------------------------------------------------------MEMO
  
  
  
  MODIFICATION A FAIRE PAR BOBLASTIC
  
  Prendre les lignes de code avec le commentaire
  MODIFICATION A FAIRE
  
  
  Requête à modififier :CCAM_getActesCotes.qry
  
  select cot.codeActe identifiant,cot.libelleActe libelle,cot.identifiant id,
				cot.quantite,cot.periodicite,cot.lesionMultiple,cot.dateDemande,cot.nomIntervenant medecin
			from ccam_cotation_actes cot
			where cot.idDomaine=$idDomaine and cot.idEvent=$idEvent and cot.type='ACTE' and cot.codeActe not like 'CONS%'
			AND cot.cotationNGAP NOT IN ('MCG 1','MNO 1','MGE 1','MINFD 1','INFN2 1','INFN1 1') AND cot.cotationNGAP NOT IN ('MNP 1','MPJ 1') and cot.libelleActe <>'ATU'
			and cot.libelleActe <>'MTU' order by id
			
	
  
  select cot.codeActe identifiant,cot.libelleActe libelle,cot.identifiant id,
				cot.quantite,cot.periodicite,cot.lesionMultiple,cot.dateDemande,cot.nomIntervenant medecin
			from ccam_cotation_actes cot
			where cot.idDomaine=$idDomaine and cot.idEvent=$idEvent and cot.type='ACTE' and cot.codeActe not like 'CONS%'
			
      AND cot.cotationNGAP NOT IN ('MCG 1','MNO 1','MGE 1','MINFD 1','INFN2 1','INFN1 1')
      // majoration pour consultation urgentiste 
      
      AND cot.cotationNGAP NOT IN ('MNP 1','MPJ 1')
      // majoration des pédiatres
      
      AND cot.cotationNGAP NOT IN ('aaa 1','bbb 1')
      // si majoration des sages - femmes
      
       AND cot.cotationNGAP NOT IN ('ccc 1','ddd 1')
      // si majoration des gyneco - obstetrique 
      
      and cot.libelleActe <>'ATU'
			and cot.libelleActe <>'MTU' order by id
			
François,

Comme tu vas le remarquer, j'ai apporté quelques modifications au module CCAM. Tu peux retrouver les différentes modifications en recherchant les TAG suivants :

- DBDEB1 : On n'affiche plus le choix de l'anesthésiste dans le module CCAM.
- DBDEB2 : Ajout de couleur pour les sous-catégories de diagnostics.
- DBDEB3 : Correction car l'ATU était toujours envoyé même si non valide.
- DBDEB4 : On vérifie que le patient est valide (fonction getValide) et qu'il n'est pas dans l'UFUHCDrepere non plus pour envoyer l'ATU.

Damien

*/
 
include (MODULE_CCAM."ccam_define.php");

class clCCAMCotationActesDiags{
// Attribut contenant l'affichage
private $af;
private $infos;
private $erreurs;
private $debug;

function __construct($paramCCAM){
global $session;
global $options;
//eko($paramCCAM);
$this->idEvent=$paramCCAM[idEvent];
$this->dateEvent=$paramCCAM[dateEvent];
$this->dtFinInterv=$paramCCAM[dtFinInterv];
$this->idu=$paramCCAM[idu];
$this->ipp=$paramCCAM[ipp];
$this->nomu=addslashes(strtoupper($paramCCAM[nomu]));
$this->pren=addslashes(strtoupper($paramCCAM[pren]));
$this->sexe=$paramCCAM[sexe];
$this->dtnai=substr($paramCCAM[dtnai],0,10);
$this->nsej=$paramCCAM[nsej];
$this->typeAdm=$paramCCAM[typeAdm];
$this->lieuInterv=$paramCCAM[lieuInterv];
$this->matriculeIDE=$paramCCAM[matriculeIDE];
$this->nomIDE=$paramCCAM[nomIDE];
$this->matriculeIntervenant=$paramCCAM[matriculeIntervenant];
$this->typeIntervenant=$paramCCAM[typeIntervenant];
$this->nomIntervenant=addslashes($paramCCAM[nomIntervenant]);
$this->numUFdem=$paramCCAM[numUFdem];
//if ($this->numUFdem=="2702") $this->numUFexec="2702"; else $this->numUFexec=$options->getOption("numUFexec");
$this->numUFexec=$this->numUFdem;
$this->typeListe=$paramCCAM[typeListe];
$this->manuel=$paramCCAM[manuel];
$this->dtAdmission=$paramCCAM[dateAdmission];

$this->liste_anesth_facult="'ZZLF012','ZZLF013','ZZLF014','AFLB010'";

$age=new clDate($paramCCAM[dtnai]);
$date=new clDate($paramCCAM[dateEvent]);
$duree=new clDuree($date->getDifference($age));
$this->agePatient=$duree->getYears();
//$this->cotationActes();
}

/*Gestion de la cotation des diagnostics et des actes 
pour le patient en cours dans la fiche patient*/
function cotationActes(){
global $session ;
global $options ;

// Ouverture de CORA dans la fenêtre Cotations Actes Diags
if ( $options->getOption ( "ActiverCORAModuleActes" ) ) {
  if ($_POST['cora_x'] or $session->getNavi(3) == "AppelCora" ) {
    global $idpat ;
    $dll     =  new clDllCoraTU ( $idpat ) ;

    // CHB Start
    $str = $dll->openCora();
	//return XhamTools::genFenetreBloquante("fenetreFermerCora.html") ;
    if ( $str == 'ERROR' ) {
      // Appel du template récap des diagnostics et des actes.
      $mod_recap=new ModeliXe("CCAM_RecapCCAMExterne.mxt");
      $mod_recap->SetModeliXe();

      $mod_recap -> MxText ( "afficheActesDiag.titreFormulaire", "Récapitulatif cotation diagnostics/actes") ;
      $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"red\">Erreur : mobidoc injoignable (COMRPC Impossible).</font>" ) ;
      $mod_recap->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2)));
	  if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"w") and $this->typeListe!="Sortis")
		$mod_recap->MxFormField("afficheActesDiag.DetailDiagsActes","image","DetailDiagsActes","","src=\"".URLIMG."modifier.png\"");
      return $this->af.=$mod_recap->MxWrite("1") ;
    } else {
    	return XhamTools::genFenetreBloquante("fenetreFermerCora.html") ;
    	//$mod->MxText("fenetreFermerCora",$html);
    }
    // CHB Stop
    /* Ancienne version
    if ( ! $options->getOption ( "CCAMExterne" ) ) {
      global $idpat ;
      $dll     =  new clDllCoraTU ( $idpat ) ;
      $dll     -> openCora( );
    } else {
      $str = OpenCora ( $this->idEvent, $session->getUid ( ), session_id ( ), 
      $_SERVER["REMOTE_ADDR"], $options->getOption('CCAMExterne_MRPCPORT'), 
      $options->getOption('CCAMExterne_MRPCTIMEOUT') ) ;
    }
    */
  }
}

/*
// Spécifique au CH-Brignoles
// Auteur : Alain Falanga (a.falanga@ch-brignoles.fr)
if ( $options->getOption ( "CCAMExterneBrignoles" ) ) {
  if ($_POST['DetailDiagsActes_x'] or $session->getNavi(3)=="DetailDiagsActes") {
    $this->err1 = "";
    $this->err2 = "";
        
    $str = OpenCora ( $this->idEvent, $session->getUid ( ), session_id ( ), 
    $_SERVER["REMOTE_ADDR"], $options->getOption('CCAMExterne_MRPCPORT'), 
    $options->getOption('CCAMExterne_MRPCTIMEOUT') ) ;
        
    $str = 'ERROR' ;
    if ( $str = 'ERROR' ) {
      // Appel du template récap des diagnostics et des actes.
 			$mod_recap=new ModeliXe("CCAM_RecapCCAMExterne.mxt");
			$mod_recap->SetModeliXe();
	       	
      $mod_recap -> MxText ( "afficheActesDiag.titreFormulaire", "Récapitulatif cotation diagnostics/actes") ;
      $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"red\">Erreur : mobidoc injoignable (COMRPC Impossible).</font>" ) ;
      $mod_recap->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2)));
        	
      return $this->af.=$mod_recap->MxWrite("1") ;
    }
  }
  	
  // Appel du template récap des diagnsotics et des actes
	$mod_recap=new ModeliXe("CCAM_RecapCCAMExterne.mxt");
	$mod_recap->SetModeliXe();
	
	$mod_recap->MxText("afficheActesDiag.titreFormulaire","Récapitulatif cotation diagnostics/actes");
	if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"w") and $this->typeListe!="Sortis")
		$mod_recap->MxFormField("afficheActesDiag.DetailDiagsActes","image","DetailDiagsActes","",
			"src=\"".URLIMG."modifier.png\"");
	
	//$actescora = getcoraactes($this->nsej);
	if ( $actescora <> '' ) $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"green\">Actes présents dans Cora.</font><br>".$actescora ) ;
	else $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"red\">Penser à coder vos actes dans CORA.</font>" ) ;

	// Précédente version.
	//if ( ! $this->verifyactesession() ) $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"red\">Vous devez saisir l'activité CCAM.</font>" ) ;
	//else $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"green\">La saisie a été effectuée dans CORA.</font>" ) ;

	$mod_recap->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2)));
	return $this->af.=$mod_recap->MxWrite("1");
}
*/
/*
// Activation du module externe des actes (CORA).
if ( $options->getOption ( "CCAMExterne" ) ) {
	//Gestion de la fenêtre de saisie des diagnostics et des actes
	if ($_POST['DetailDiagsActes_x'] or $session->getNavi(3)=="DetailDiagsActes") {
    global $idpat ;
    $dll     =  new clDllCoraTU ( $idpat ) ;
    $dll     -> openCora( );
	}
	
  // Appel du template récap des diagnsotics et des actes
	$mod_recap=new ModeliXe("CCAM_RecapCCAMExterne.mxt");
	$mod_recap->SetModeliXe();
	
	$mod_recap->MxText("afficheActesDiag.titreFormulaire","Récapitulatif cotation diagnostics/actes");
	if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"w") and $this->typeListe!="Sortis")
		$mod_recap->MxFormField("afficheActesDiag.DetailDiagsActes","image","DetailDiagsActes","",
			"src=\"".URLIMG."modifier.png\"");
	
	//$actescora = getcoraactes($this->nsej);
	if ( $actescora <> '' ) $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"green\">Actes présents dans Cora.</font><br>".$actescora ) ;
	else $mod_recap -> MxText ( "afficheActesDiag.etatCCAM", "<font color=\"red\">Penser à coder vos actes dans CORA.</font>" ) ;

	$mod_recap->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),$session->getNavi(1),$session->getNavi(2)));
	return $this->af.=$mod_recap->MxWrite("1");
	
} else*/ 

{

	$tabPeriodicite[aucune]="Sans objet";
	$tabPeriodicite[quart]="1 quart d'heure";
	$tabPeriodicite[demi]="1 demi-heure";
	$tabPeriodicite[1]="1 heure";
	$tabPeriodicite[2]="2 heures";
	$tabPeriodicite[3]="3 heures";
	$tabPeriodicite[4]="4 heures";
	$tabPeriodicite[6]="6 heures";
	$tabPeriodicite[8]="8 heures";
	$tabPeriodicite[12]="12 heures";
	$tabPeriodicite[24]="24 heures";
	$tabPeriodicite[48]="48 heures";
	
	//Gestion de la fenêtre de saisie des diagnostics et des actes
	if ($_POST['DetailDiagsActes_x'] or $session->getNavi(3)=="DetailDiagsActes"){
		$this->gestionDiagsActes($tabPeriodicite,$_POST['diag0']);
	}
	
	//Gestion de la fenêtre de saisie des consultations spécialisées
	if ($_POST['DetailConsult_x'] or $session->getNavi(3)=="DetailConsult"){
		$this->gestionConsult();
	}
	
	// Appel du template récap des diagnsotics et des actes
	$mod_recap=new ModeliXe("CCAM_RecapCotationActes.mxt");
	$mod_recap->SetModeliXe();
	
	$mod_recap->MxText("afficheActesDiag.titreFormulaire","Récapitulatif cotation diagnostics/actes");
  if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"w") and $this->typeListe!="Sortis")
		$mod_recap->MxFormField("afficheActesDiag.DetailDiagsActes","image","DetailDiagsActes","",
			"src=\"".URLIMG."modifier.png\"");
	if ($session->getDroit("CCAM_CONSULT_".$this->typeListe,"w") and $this->typeListe!="Sortis")
		$mod_recap->MxFormField("afficheConsult.DetailConsult","image","DetailConsult","",
			"src=\"".URLIMG."modifier.png\"");
	
	if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"r")){
		//Récupération des diagnostics côtés pour le patient en cours
		unset($param);
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[idEvent]=$this->idEvent;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getDiagCotes",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		$diag0="";
		if ($res[INDIC_SVC][2]==0){
			$mod_recap->MxBloc("afficheActesDiag.diagCotes","delete");
		}
		else{
			$mod_recap->MxBloc("afficheActesDiag.aucunDiag","delete");
			for ($i=0;isset($res[identifiant][$i]);$i++){ 
				$mod_recap->MxText("afficheActesDiag.diagCotes.codeActe",$res[identifiant][$i]);
				$mod_recap->MxText("afficheActesDiag.diagCotes.libActe",ucfirst($res[libelle][$i]));
				
				$mod_recap->MxText("afficheActesDiag.diagCotes.action.codeActe",$res[identifiant][$i]);
				$mod_recap->MxBloc("afficheActesDiag.diagCotes","loop");
				($i==0)?$diag0=$res[identifiant][$i]:"";
			}
		}
	}
	else $mod_recap->MxBloc("afficheActesDiag","delete");
	
	if ($options->getOption("ActiverModuleActes")){
		if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"r")){
			//Récupération des actes côtés pour le patient en cours
			unset($param);
			$param[idDomaine]=CCAM_IDDOMAINE;
			$param[idEvent]=$this->idEvent;
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_getActesCotes",$param,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			if ($res[INDIC_SVC][2]==0){
				$mod_recap->MxBloc("afficheActesDiag.afficheActe.actesCotes","delete");
			}
			else{
				$mod_recap->MxBloc("afficheActesDiag.afficheActe.aucunActe","delete");
				for ($i=0;isset($res[identifiant][$i]);$i++){ 
					$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.codeActe",$res[identifiant][$i]);
					$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.libActe",$res[libelle][$i]);
					if ( strlen ( $res[medecin][$i] ) > 8 ) $med = substr ( $res[medecin][$i], 0, 7 )."..." ;
					else $med = $res[medecin][$i] ;
					$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.medecin",$med);
					$periodicite=$res[periodicite][$i];
					if ($res[quantite][$i]==1 or $periodicite=="aucune"){
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActeLibQte"," ");
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActeQte"," ");
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActeLibPeriod"," ");
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActePeriod"," ");
					}
					else{
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActeLibQte","Quantité :");
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActeQte",$res[quantite][$i]);
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActeLibPeriod",
							"Latence entre les actes :");
						$mod_recap->MxText("afficheActesDiag.afficheActe.actesCotes.complementActePeriod",$tabPeriodicite[$periodicite]);
					}
					$mod_recap->MxBloc("afficheActesDiag.afficheActe.actesCotes","loop");
				}
			}
		}
		else $mod_recap->MxBloc("afficheActesDiag.afficheActe","delete");
		
		if ($session->getDroit("CCAM_CONSULT_".$this->typeListe,"r") ){
			//Récupération des consultations spécialisées pour le patient en cours
			unset($param);
			$param[idDomaine]=CCAM_IDDOMAINE;
			$param[idEvent]=$this->idEvent;
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_getConsultCotes",$param,"ResultQuery");

			if ($res[INDIC_SVC][2]==0){
				$mod_recap->MxBloc("afficheConsult.actesCotes","delete");
			}
			else{
				$mod_recap->MxBloc("afficheConsult.aucunActe","delete");
				for ($i=0;isset($res[identifiant][$i]);$i++){ 
					$mod_recap->MxText("afficheConsult.actesCotes.specialite",$res[spe][$i]);
					$mod_recap->MxText("afficheConsult.actesCotes.nomConsultant",$res[libelle][$i]);
					
					$mod_recap->MxBloc("afficheConsult.actesCotes","loop");
				}
			}
		}
	  else $mod_recap->MxBloc("afficheConsult","delete");
  } elseif ( $options -> getOption ( 'ActiverCORAModuleActes' ) ) {
		/*if ( $options -> getOption ( 'CCAMCoraMSSQL' ) ) {
			// Affichage des actes récupérés dans CORA dans une base MSSQL
			// Date : 2007
			// Auteur : 
			$mod_recap->MxBloc("afficheConsult","delete");
			if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"r")){  
					$af = "A FAIRE : Affichage du récapitulatif des actes de CORA" ;
					$mod_recap->MxBloc("afficheActesDiag.afficheActe","replace",$af);
				}
			} else $mod_recap->MxBloc("afficheActesDiag.afficheActe","delete");
		} else {*/
			// Affichage des actes récupérés dans CORA
			// Date : 12/10/2007
			// Auteur : Damien Borel <dborel@ch-hyeres.fr>
			$mod_recap->MxBloc("afficheConsult","delete");
			if ($session->getDroit("CCAM_ACTES_".$this->typeListe,"r")){  
				$param['nsej'] = $this->nsej ;
	              		$req = new clResultQuery ;
	              		$res = $req -> Execute ( "Fichier", "CCAM_Cora", $param, "ResultQuery" ) ;
	              		// eko ( $res ) ;
				if ($res[INDIC_SVC][2]==0) $mod_recap->MxBloc("afficheActesDiag.afficheActe","replace","<table width=\"100%\"><tr><th>Code Acte</th><th>M&eacute;decin</th></tr><tr><td colspan=2 style=\"color:red;text-align:center;\">Aucun acte saisi</td></tr></table>");
	                       	else {
					/*
					for ( $i=0 ; isset ( $res['CODE_ACTE'][$i] ) ; $i++ ) {
						$af .= "<tr><td>".$res['CODE_ACTE'][$i]."</td><td>".$res['NOM_UTILISATEUR_CORA'][$i].' '.$res['PRENOM_UTILISATEUR_CORA'][$i]."</td></tr>" ;
					}*/
					
					$af = "<table width=\"100%\"><tr><th>M&eacute;decin</th><th>Actes</th></tr>" ;
					$list = array ( ) ;
					for ( $i=0 ; isset ( $res['CODE_ACTE'][$i] ) ; $i++ ) {
						if ( isset ( $list[$res['NOM_UTILISATEUR_CORA'][$i].' '.$res['PRENOM_UTILISATEUR_CORA'][$i]] ) AND $list[$res['NOM_UTILISATEUR_CORA'][$i].' '.$res['PRENOM_UTILISATEUR_CORA'][$i]] )
							$list[$res['NOM_UTILISATEUR_CORA'][$i].' '.$res['PRENOM_UTILISATEUR_CORA'][$i]] .= ', '.$res['CODE_ACTE'][$i] ;
						else $list[$res['NOM_UTILISATEUR_CORA'][$i].' '.$res['PRENOM_UTILISATEUR_CORA'][$i]] = $res['CODE_ACTE'][$i] ;
					}
					while ( list ( $key, $val ) = each ( $list ) ) {
						$af .= '<tr><td>'.$key.'</td><td>'.$val.'</td></tr>' ;
					}
					$af .= "</table>" ;
					$mod_recap->MxBloc("afficheActesDiag.afficheActe","replace",$af);
				}
			} else $mod_recap->MxBloc("afficheActesDiag.afficheActe","delete");
		//}
	}
	else{
		$mod_recap->MxBloc("afficheActesDiag.afficheActe","delete");
		$mod_recap->MxBloc("afficheConsult","delete");
	}
	
	$mod_recap->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),$session->getNavi(2)));
	$mod_recap->MxHidden("hidden2","diag0=$diag0");
	return $this->af.=$mod_recap->MxWrite("1");
	}
}

//Gestion des diagnostics et des actes ===========================================================
function gestionDiagsActes($tabPeriodicite,$diag0){
global $session;
global $options ;

// Appel du template permettant la saisie des diagnostics et des actes

if ($options->getOption("ActiverCORAModuleActes")) {
  $mod=new ModeliXe("CCAM_CotationActesCORA.mxt");
}
else
  $mod=new ModeliXe("CCAM_CotationActes.mxt");
  
  
$mod->SetModeliXe();

$mod->MxImage("imgQuitter",URLIMG."QuitterSansValider.gif");
$mod->MxUrl("lienQuitter",URLNAVI.$session->genNavi($session->getNavi(0),
	$session->getNavi(1),$session->getNavi(2)));

//Initialisation des valeurs
if (!$_POST['idListeSelection0']){
	if ($diag0!=""){
		unset($param);
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[code]=$diag0;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getCat1Diag",$param,"ResultQuery");
		$idListeSelection0=$res[categorie][0];
	}
	else $idListeSelection0="aucun#";
}
else{
	if ($_POST['actualiserListe_x']){
		unset($param);
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[code]=$_POST['actualiserListe'];
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getCat1Diag",$param,"ResultQuery");
		$idListeSelection0=$res[categorie][0];
	}
	else $idListeSelection0=$_POST['idListeSelection0'];
}

if (!$_POST['idListeSelection1']){
	($diag0!="")?$idListeSelection1=$diag0:$idListeSelection1="aucun#";
}
else{
	if ($_POST['actualiserListe_x']) $idListeSelection1=$_POST['actualiserListe'];
	else $idListeSelection1=$_POST['idListeSelection1'];
}

(!$_POST['idListeSelection2'])?$idListeSelection2="diag":
	$idListeSelection2=$_POST['idListeSelection2'];

($_POST['aDroite_x'] or $_POST['aDroite'])?$aDroite=1:"";
($_POST['sortir_x'] or $_POST['sortir'])?$sortir=1:"";
	
//Ajout des actes sélectionnés dans la liste des actes affectés à la liste des actes
//rattachés au patient en cours
if ($aDroite or $sortir){
	$retourInfos=$this->addActesPatient();
	if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
	elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
}

if ($_POST['lesion'] and $_POST['lesion']!=$_POST['lesion_old']){
  unset($param);
  $param[lesionMultiple]=$_POST['lesion'];
  $majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
	$sql=$majrq->updRecord("idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE);
}

if (!$sortir){
	//Si on a choisi de modifier un acte
	//if ($_POST['imgOK_x'] or $_POST['imgOK']) $retourInfos=$this->modifyActe();
	
	//Si on a choisi de supprimer un acte ou un diagnostic
	if ($_POST['supprimerActe_x'] or $_POST['supprimerActe']){
	  //eko ( htmlentities($_POST['supprimerActe']) ) ;
		$idActeSuppr=$_POST['supprimerActe'];
		$this->infos=$this->delActesPatient($idActeSuppr);
	}
	
	if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
	if ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
	
	//Récupération des valeurs pour Selection0
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$tabListeSelection0=$this->tableauValeurs("CCAM_getListeCatDiag",$param,
		"Choisir une catégorie de diagnostics");
	
	//Récupération des valeurs pour Selection1
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[idListeSelection0]=addslashes(stripslashes($idListeSelection0));
	$optionTri=$options->getOption("CCAM_TriDiagnostics");
  if ($optionTri=="Manuel") $tri="rang";
	elseif ($optionTri=="Alphabétique") $tri="libelle";
	elseif ($optionTri=="Alphabétique inversé") $tri="libelle desc";
	else $tri="libelle";
  $param[order]=$tri;
  $tabListeSelection1=$this->tableauValeurs("CCAM_getListeDiags",$param,"Choisir un diagnostic");
	
	if ($options->getOption("ActiverModuleActes") and $session->getDroit("CCAM_ACTES_ACTES","r")){
		$tabListeSelection2[diag]="Actes associés au diagnostic sélectionné";
		$tabListeSelection2[tous]="Tous les actes de la liste restreinte";
		$tabListeSelection2[NGAP]="Actes NGAP";
		$tabListeSelection2[PACK]="Packs d'actes";
		
		$optionTri=$options->getOption("CCAM_TriListeActes");
		if (strcmp($optionTri,"Code de l'acte")==0) $tri="code";
		elseif (strcmp($optionTri,"Libellé de l'acte")==0) $tri="libelle";
		elseif (strcmp($optionTri,"Fréquence d'utilisation")==0) $tri="frequence";
		else $tri="code";
		
			
		//Récupération des actes pour la liste de gauche en ignorant les valeurs de la liste de droite
		//en fonction de la famille sélectionnéee dans Selection1
		unset($paramRelation);
		unset($paramA);
		$paramRelation[idDomaine]=CCAM_IDDOMAINE;
		$paramA[idDomaine]=CCAM_IDDOMAINE;
		if ($idListeSelection2 and $idListeSelection1){
			$paramA[idListeSelection1]=$idListeSelection2;
			$paramRelation[idEvent]=$this->idEvent;
			
      if (strcmp($tri,"code")==0) $paramA["order"]="rel.idActe";
				elseif (strcmp($tri,"libelle")==0) $paramA["order"]="rel.libelleActe";
				elseif (strcmp($tri,"frequence")==0) $paramA["order"]="rel.frequence desc,rel.idActe";
			
// Nous sommes dans la fenetre de Cotation des diagnostics et des actes
eko("fenetre de Cotation des diagnostics et des actes");
eko ($this->typeIntervenant);	
      
      if ($idListeSelection2=="tous"){
        
        $requete       = new clResultQuery;
        unset($paramRq);
          
        if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
          // On prend en compte l'acte NGAP des urgentistes
          // Consultation urgentiste

          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP2f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP2nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
        }
        elseif ( strcmp ($this->typeIntervenant,"PED") == 0 ) {
          // On prend en compte l'acte NGAP des urgentistes spécialistes
          // Consultation urgentiste spécialiste

          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAPf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAPnf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP2f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP2nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
        }
        elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 ) {
          // On prend en compte l'acte NGAP des sage-femmes
          // Consultation sage-femme spécialiste
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAP2f      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAP2nf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
        }
        elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 ) {
          // On prend en compte l'acte NGAP des obstétrique et gynécologique
          // Consultation obstétrique et gynécologique spécialiste
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAP2f      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAP2nf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
        }   
				
        eko($codeNGAPf);
        eko($codeNGAPnf);
        eko($codeNGAP2f);
        eko($codeNGAP2nf);
        eko($codeNGAP3f);
        eko($codeNGAP3nf);
        
        $paramA[cw]=" and rel.idActe not like 'NGAP%') or (rel.idActe like 'NGAP%' and rel.idActe<>'".$codeNGAP3f[idActe][0]."' and rel.idActe<>'".$codeNGAP3nf[idActe][0]."' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and rel.idActe<>'".$codeNGAP2f[idActe][0]."' and rel.idActe<>'".$codeNGAP2nf[idActe][0]."' and rel.cotationNGAP <>''";
				//eko($paramA);
        $requete="CCAM_getActesNonListe";
			}
			elseif ($idListeSelection2=="NGAP"){
				
        // Pour lister l'ensemble des actes NGAP on doit soit afficher
        // l'acte de consultation urgentiste soit l'acte de consultation
        // urgentiste spécialiste soit lacte de consultation de sage-femmes
        // soit l'acte de consultation de gynecologie obstetrique 
        // (pareil pour non facturable)
        
        $paramA[type]  ="NGAP";
        $requete       = new clResultQuery;
        unset($paramRq);
        
        if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
          // On prend en compte l'acte NGAP des urgentistes
          // Consultation urgentiste
          
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP2f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP2nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
          
          $paramA[cw]    = "and rel.idActe<>'".$codeNGAP3f[idActe][0]."' and rel.idActe<>'".$codeNGAP3nf[idActe][0]."' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and rel.idActe<>'".$codeNGAP2f[idActe][0]."' and rel.idActe<>'".$codeNGAP2nf[idActe][0]."' and rel.cotationNGAP<>''";
        }
        elseif ( strcmp ($this->typeIntervenant,"PED") == 0 ) {
          // On prend en compte l'acte NGAP des urgentistes spécialistes
          // Consultation urgentiste spécialiste
          

          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP2f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP2nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
          $paramA[cw]    = "and rel.idActe<>'".$codeNGAP3f[idActe][0]."' and rel.idActe<>'".$codeNGAP3nf[idActe][0]."' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and rel.idActe<>'".$codeNGAP2f[idActe][0]."' and rel.idActe<>'".$codeNGAP2nf[idActe][0]."' and rel.cotationNGAP<>''";
        } 
        elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 )  {
          // On prend en compte l'acte NGAP des sage-femmes
          // Consultation sage-femmes


          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAP2f     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAP2nf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
        
          $paramA[cw]    = "and rel.idActe<>'".$codeNGAP3f[idActe][0]."' and rel.idActe<>'".$codeNGAP3nf[idActe][0]."' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and rel.idActe<>'".$codeNGAP2f[idActe][0]."' and rel.idActe<>'".$codeNGAP2nf[idActe][0]."' and rel.cotationNGAP<>''";
        
        } 
        
        elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 )  {
          // On prend en compte l'acte NGAP gynécologie et obstétrique
          // Consultation gynécologie et obstétrique


          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAP2f     = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAP2nf    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP3f    = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP3nf   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramA[cw]    = "and rel.idActe<>'".$codeNGAP3f[idActe][0]."' and rel.idActe<>'".$codeNGAP3nf[idActe][0]."' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and rel.idActe<>'".$codeNGAP2f[idActe][0]."' and rel.idActe<>'".$codeNGAP2nf[idActe][0]."' and rel.cotationNGAP<>''";
        
        } 
        			
        $requete="CCAM_getAutresActesNonListe";
			}
			elseif ($idListeSelection2=="PACK"){
				$paramA[type]="PACK";
				$paramA[cw]="";
				
				$requete="CCAM_getAutresActesNonListe";
			}
			else{
			  
        /*if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
          // On prend en compte l'acte NGAP des urgentistes
          // Consultation urgentiste
          // on récupere le code NGAP des urgentistes spécialistes
          $requete       = new clResultQuery;
          unset($paramRq);
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAPf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAPnf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");

          $paramA[cw]="and act.date_fin='0000-00-00' and (rel.idActe not like 'NGAP%' or 
						rel.idActe like 'NGAP%' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and act.cotationNGAP<>'')";
          }
        else {
          // On prend en compte l'acte NGAP des urgentistes spécialistes
          // Consultation urgentiste spécialiste
          // on récupere le code NGAP des urgentistes
          $requete       = new clResultQuery;
          unset($paramRq);
          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAPf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAPnf      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");

          
          $paramA[cw]="and act.date_fin='0000-00-00' and (rel.idActe not like 'NGAP%' or 
						rel.idActe like 'NGAP%' and rel.idActe<>'".$codeNGAPf[idActe][0]."' and rel.idActe<>'".$codeNGAPnf[idActe][0]."' and act.cotationNGAP<>'')";
          
        } */ 
			
				$paramA[idListeSelection1]=$idListeSelection1;
				$paramA[cw]="and act.date_fin='0000-00-00' and (rel.idActe not like 'NGAP%' or 
						rel.idActe like 'NGAP%' and act.cotationNGAP<>'')";
				
				if (strcmp($tri,"code")==0) $paramA["order"]="act.idActe";
				elseif (strcmp($tri,"libelle")==0) $paramA["order"]="act.libelleActe";
				elseif (strcmp($tri,"frequence")==0) $paramA["order"]="act.frequence desc,act.idActe";

				$requete="CCAM_getActesDiags";
				
			}
			
			$tabListeGauche=$this->valeursListeGauche($requete,"CCAM_getActesCotes",
				$paramA,$paramRelation,"Choisir un acte");
				
      // On affiche d'abord les actes CCAM puis NGAP puis PACK
      $ccamtableau=array();
      $ngaptableau=array();  
      $packtableau=array();
      
      foreach($tabListeGauche as $cle=>$valeur) {
        if (substr($cle,0,4) == "NGAP") $ngaptableau[$cle] = $valeur;
        elseif (substr($cle,0,4) == "PACK") $packtableau[$cle] = $valeur;
        else $ccamtableau[$cle] = $valeur;
      }
      
      foreach($ccamtableau as $cle=>$valeur) $alltableau[$cle]=$valeur;
      foreach($ngaptableau as $cle=>$valeur) $alltableau[$cle]=$valeur;
      foreach($packtableau as $cle=>$valeur) $alltableau[$cle]=$valeur;
        
      $tabListeGauche=$alltableau;
		}
		else	
			$tabListeGauche[0]="Choisir un acte";
		
		$mod->MxText("actesBlocGauche.titreSelection2","Actes");
		$mod->MxSelect("actesBlocGauche.idListeSelection2","idListeSelection2",$idListeSelection2,
			$tabListeSelection2,'','',"onChange=reload(this.form) size=\"4\"");
			
		$mod->MxSelect("actesBlocGauche.listeGauche","listeGauche[]",'', $tabListeGauche,'','',
			"size=\"13\" multiple=\"yes\"");
		
		//Liste des anesthésistes
		// DBDEB1 : masquage de l'anesthésiste.
		unset($param);
		//$param[idDomaine]=CCAM_IDDOMAINE;
		//$param[idListeSelection0]="Anesthésie";
    	//$param[order]="libelle";
		//$tabAnesth=$this->tableauValeurs("CCAM_getListeDiags",$param,"Choisir un anesthésiste");
		$nomUrgentiste=$this->nomIntervenant;
		$matriculeUrgentiste=$this->matriculeIntervenant;
		//$tabAnesth[$matriculeUrgentiste]=$nomUrgentiste;
		(!isset($_POST['anesthesiste']))?$anesthesiste=$matriculeUrgentiste:$anesthesiste=$_POST['anesthesiste'];
		$mod->MxHidden("actesBlocGauche.anesthesiste","anesthesiste=$matriculeUrgentiste" ) ;
		//$mod->MxSelect("actesBlocGauche.anesthesiste","anesthesiste",$anesthesiste,$tabAnesth); 
		// DBFIN1
		
		//Récupération des actes côtés pour le patient en cours
		unset($param);
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[idEvent]=$this->idEvent;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getActesCotes",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		
		/*if (!$session->getDroit("CCAM_".$this->typeListe,"m")){
			$mod->MxBloc("actesBlocDroite.actesCotes.actionModif","delete");
		}*/
		if (!$session->getDroit("CCAM_ACTES_".$this->typeListe,"d")){
			$mod->MxBloc("actesBlocDroite.actesCotes.actionSuppr","delete");
		}
		
		if ($res[INDIC_SVC][2]==0) $mod->MxBloc("actesBlocDroite.actesCotes","delete");
		else{
			$mod->MxBloc("actesBlocDroite.aucunActe","delete");
			$idListeActes="";

			for ($i=0;isset($res[identifiant][$i]);$i++){ 
				$idListeActes.="'".$res[identifiant][$i]."',";
				$lesion=$res[lesionMultiple][$i];
				list($annee,$mois,$jour)=explode("-",substr($res[dateDemande][$i],0,10));
				$dateActe=$jour."/".$mois."/".$annee;
				
				$mod->MxText("actesBlocDroite.actesCotes.dateActe",$dateActe);
				
        if ( $options->getOption("AffichageTarifsCCAM") ) {
          unset($paramRq);
          $paramRq["cw"]="and a.code='".$res[identifiant][$i]."' order by t.aadt_modif desc";
          $fat=$req->Execute("Fichier","CCAM_get1TarifCCAM",$paramRq,"ResultQuery");
          //eko($fat);
          if ( $fat[INDIC_SVC][2] == 1 ) {
            $prix=$fat["pu_base"][0]."&euro;."; 
          $mod->MxText("actesBlocDroite.actesCotes.codeActe",$res[identifiant][$i]."<br>".$prix);
          }
        else
          $mod->MxText("actesBlocDroite.actesCotes.codeActe",$res[identifiant][$i]);
        }
        else
          $mod->MxText("actesBlocDroite.actesCotes.codeActe",$res[identifiant][$i]);
				
        $mod->MxText("actesBlocDroite.actesCotes.libActe",$res[libelle][$i]);
				$mod->MxText("actesBlocDroite.actesCotes.medecin",$res[medecin][$i]);
				/*$mod->MxText("actesBlocDroite.actesCotes.actionModif.codeActe",
					$res[identifiant][$i]);*/
				$mod->MxText("actesBlocDroite.actesCotes.actionSuppr.codeActe",
					$res[identifiant][$i]);
				
				$periodicite=$res[periodicite][$i];
				if ($_POST['modifierActe_x'] and $res[identifiant][$i]==$_POST['modifierActe']){
					//$this->saisieComplementActe($res,$periodicite,$tabPeriodicite,$i);
				}
				elseif ($res[quantite][$i]==1){
					$mod->MxText("actesBlocDroite.actesCotes.complementActeLibQte"," ");
					$mod->MxText("actesBlocDroite.actesCotes.complementActeQte","");
				}
				elseif ($periodicite=="aucune"){
					$mod->MxText("actesBlocDroite.actesCotes.complementActeLibPeriod","");
					$mod->MxText("actesBlocDroite.actesCotes.complementActePeriod","");
					/*if ($res[quantite][$i]>1){
						$mod->MxText("actesBlocDroite.actesCotes.complementActeLibQte",
							"Quantité :");
						$mod->MxText("actesBlocDroite.actesCotes.complementActeQte",
							$res[quantite][$i]);
					}*/
				}
				else{
					/*$mod->MxText("actesBlocDroite.actesCotes.complementActeLibQte","Quantité :");
					$mod->MxText("actesBlocDroite.actesCotes.complementActeQte",$res[quantite][$i]);
					if ($periodicite!="aucune"){
						$mod->MxText("actesBlocDroite.actesCotes.complementActeLibPeriod",
							"<br>Temps latence :");
						$mod->MxText("actesBlocDroite.actesCotes.complementActePeriod",
							$tabPeriodicite[$periodicite]);
					}*/
				}
				$mod->MxBloc("actesBlocDroite.actesCotes","loop");
				
				/*//Mise à jour des modificateurs
				$codeActe=$res[identifiant][$i];
				
				unset($param);
				$param[modificateurs]="";
				$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
				$sql=$requete->updRecord("codeActe='$codeActe' and idDomaine=".CCAM_IDDOMAINE.
					" and idEvent=".$this->idEvent);
				
				unset($param);
				$param[cw]="and substring(rel.aa_code,1,7)='$codeActe' and 
					rel.modifi_cod in ('7')";
				$req=new clResultQuery;
				$res2=$req->Execute("Fichier","CCAM_getModificateursActe",$param,"ResultQuery");
				//eko($res2[INDIC_SVC]);
				$listeModificateurs="";
				for ($j=0;isset($res2[modifi_cod][$j]);$j++){
					$CCModificateur="Modificateur_".$res2[modifi_cod][$j];
					//eko("CCmodificateur:$CCModificateur:".$_POST[$CCModificateur]);
					if ($_POST[$CCModificateur]){
						$listeModificateurs.=$res2[modifi_cod][$j]."~";
					}
				}
				//eko("listemodif:$listeModificateurs");
				if ($listeModificateurs){
					$listeModificateurs=substr($listeModificateurs,0,-1);
					unset($param);
					$param[modificateurs]=$listeModificateurs;
					$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
					$sql=$requete->updRecord("codeActe='$codeActe' and idDomaine=".CCAM_IDDOMAINE.
						" and idEvent=".$this->idEvent);
					//eko($sql);
				}*/
			}
			$idListeActes=substr($idListeActes,0,-1);
			
			/*//Récupération des modificateurs cotés pour les actes et le patient en cours
			unset($param);
			$param[cw]="codeActe in ($idListeActes) and idDomaine=".CCAM_IDDOMAINE.
				" and idEvent=".$this->idEvent;
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_getModificateursCotes",$param,"ResultQuery");
			//eko($res[INDIC_SVC]);
			unset($tabModificateurs);
			for ($i=0;isset($res[modificateurs][$i]);$i++){
				unset($listeModificateurs);
				$listeModificateurs=explode("~",$res[modificateurs][$i]);
				while (list($key,$val)=each($listeModificateurs)){ 
					$tabModificateurs[$val]=$val;
				}
			}
			//eko($tabModificateurs);*/
			
			//Gestion des modificateurs
			/*unset($param);
			$param[cw]="and substring(rel.aa_code,1,7) in ($idListeActes) and 
				rel.modifi_cod in ('7')";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_getModificateursActe",$param,"ResultQuery");
			//eko($res[INDIC_SVC]);
			for ($i=0;isset($res[modifi_cod][$i]);$i++){
				$CCModificateur="Modificateur_".$res[modifi_cod][$i];
				$modificateur=$res[modifi_cod][$i];
				//eko("CCmodificateur:$CCModificateur:".$_POST[$CCModificateur]);
				
				$mod->MxCheckerField("anesthesie.ligneModificateur.CCModificateur",
					"checkbox",$CCModificateur,1,
					(($tabModificateurs[$modificateur]==$modificateur)?true:false));
				$mod->MxText("anesthesie.ligneModificateur.libModificateur",
					$res[libelle][$i]);
				$mod->MxBloc("anesthesie.ligneModificateur","loop");
			}
			
			$mod->MxFormField("anesthesie.imgOK","image","imgOK","",
				"value=\"".$res[identifiant][$i]."\" src=\"".URLIMG."Ok.gif\"");
			$mod->MxFormField("anesthesie.imgAnnul","image","imgAnnul","",
				"src=\"".URLIMG."annuler2.gif\"");*/
		}
		$mod->MxText("titreFormulaire","Cotation des diagnostics et des actes");
		$mod->MxText("titreDispo","Diagnostics, actes et packs disponibles");
		$mod->MxText("titreAffecte","Diagnostics et actes affectés au patient");
	}
	else{
		$mod->MxText("titreFormulaire","Cotation des diagnostics");
		$mod->MxBloc("actesBlocGauche","delete");
		$mod->MxBloc("actesBlocDroite","delete");
		$mod->MxText("titreDispo","Diagnostics disponibles");
		$mod->MxText("titreAffecte","Diagnostics affectés au patient");
			// Nous faisons appel à la DLL de CORA ici pour l'affectation des actes CCAM
    // aux patients. Seul les diagnostics seront saisies.
    if ($options->getOption("ActiverCORAModuleActes")) {
      $mod->MxText("titreFormulaire","Cotation des diagnostics et appel de CORA Recueil pour les actes");
      $mod->MxText("fenetreFermerCora","");
      
      if ( $_POST['cora_x'] or $session->getNavi(3) == "AppelCora" ) {
      $html = XhamTools::genFenetreBloquante("fenetreFermerCora.html") ;
      $mod->MxText("fenetreFermerCora",$html);
      }
    }
	}
	
	/*list($anneeNais,$moisNais,$jourNais)=explode("-",$this->dtnai);
	if ($this->lieuInterv=="0") $lieuInterv=""; else $lieuInterv=", ".$this->lieuInterv;
	$mod->MxText("infosPatient",$this->nomu." ".$this->pren.", né(e) le ".$jourNais."/".$moisNais."/".$anneeNais.
		$lieuInterv);*/
	$mod->MxText("nomPatient",$this->nomu." ".ucfirst(strtolower($this->pren)));
	$mod->MxText("sallePatient",$this->lieuInterv);
	
	if (!$_POST['lesion']){
    if (!$lesion) $lesion="Non";
  }
  else $lesion=$_POST['lesion'];
	$tabLesion[Oui]="Oui";
  $tabLesion[Non]="Non";
  while (list($key,$val)=each($tabLesion)){
  	$mod->MxCheckerField("lesionMultiple.btn","radio","lesion",$key,
  		(($lesion==$key)?true:false),"onChange=\"reload(this.form)\"");
  	$mod->MxText("lesionMultiple.libelle",$val);
  	$mod->MxBloc("lesionMultiple","loop");
  }

	
	//Récupération des diagnostics côtés pour le patient en cours
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[idEvent]=$this->idEvent;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getDiagCotes",$param,"ResultQuery");
	
	if (!$session->getDroit("CCAM_ACTES_".$this->typeListe,"d")){
		$mod->MxBloc("diagnostics.diagCotes.action","delete");
	}
	
	
	if ($res[INDIC_SVC][2]==0){
		$mod->MxBloc("diagnostics.diagCotes","delete");
	}
	else{
		$mod->MxBloc("diagnostics.aucunDiag","delete");
		for ($i=0;isset($res[identifiant][$i]);$i++){ 
		  list($annee,$mois,$jour)=explode("-",substr($res[dateDemande][$i],0,10));
			$dateActe=$jour."/".$mois."/".$annee;
			
			$mod->MxText("diagnostics.diagCotes.dateActe",$dateActe);
      $mod->MxText("diagnostics.diagCotes.codeActe",$res[identifiant][$i]);
		
      $mod->MxText("diagnostics.diagCotes.libActe",ucfirst($res[libelle][$i]));
			$mod->MxText("diagnostics.diagCotes.action.codeActe",$res[identifiant][$i]);
			$mod->MxBloc("diagnostics.diagCotes","loop");
		}
	}
	
	//Gestion du template
	$mod->MxText("titreSelection0","Catégories");
	$mod->MxSelect("idListeSelection0","idListeSelection0",stripslashes($idListeSelection0),
		$tabListeSelection0,'','',"onChange=reload(this.form)"); 
	
	$mod->MxText("titreSelection1","Diagnostics");
	$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
		$tabListeSelection1,'','',"onChange=reload(this.form)");
	
	//Afficher les boutons suivants si droits autorisés
	if (!$session->getDroit("CCAM_ACTES_".$this->typeListe,"w")){
		$mod->MxBloc("flDroite","delete");
		$mod->MxBloc("flSortir","delete");
	}
	
	
	//Ne jamais afficher les boutons suivants
	
	// Affichage ou non du champs d'informations.
	if ($this->infos) $mod->MxText("informations.infos",$this->infos);
	else $mod->MxBloc("informations","delete");
	
	// Affichage ou non du champs d'erreurs.
	if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
	else $mod->MxBloc("erreurs","delete");
		
	if ($sortir){
		$mod->MxHidden("hidden","lesion_old=$lesion&navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),$session->getNavi(2)));
		}
	elseif($cora) {
    $mod->MxHidden("hidden","lesion_old=$lesion&navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),$session->getNavi(2),"AppelCora"));
  }
	else{
		$mod->MxHidden("hidden","lesion_old=$lesion&navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),$session->getNavi(2),"DetailDiagsActes"));
	}
	
	return $this->af.=$mod->MxWrite("1"); 
}
}

//Gestion du complément d'actes===================================================================
function saisieComplementActe($res,$periodicite,$tabPeriodicite,$i){
global $session ;
$mod_saisie_complement=new ModeliXe("CCAM_FormComplementActe.mxt");
$mod_saisie_complement->SetModeliXe();

$mod_saisie_complement->MxText("titreFormulaire","Saisie complément 
	pour l'acte '".$res[identifiant][$i]." - ".$res[libelle][$i]."'");

$mod_saisie_complement->MxText("complementActeLibQte","Quantité :");
$mod_saisie_complement->MxFormField("complementActeQte","text","qte",
	$res[quantite][$i],"size=\"3\"");
/*$mod_saisie_complement->MxText("complementActeLibPeriod",
	"Temps de latence entre les actes :");
$mod_saisie_complement->MxSelect("complementActePeriod","periodicite",
	$periodicite,$tabPeriodicite,'','',"class=\"selectperiod\"");*/

$mod_saisie_complement->MxFormField("imgOK","image","imgOK","",
	"value=\"".$res[identifiant][$i]."\" src=\"".URLIMG."Ok.gif\"");
$mod_saisie_complement->MxFormField("imgAnnul","image","imgAnnul","",
	"src=\"".URLIMG."annuler2.gif\"");
	
$mod_saisie_complement->MxHidden("hidden","navi=".$session->genNavi($session->
	getNavi(0),$session->getNavi(1),$session->getNavi(2),$session->getNavi(3)));
	
return $this->af.=$mod_saisie_complement->MxWrite("1");  
}

//Ajout des diagnostics et actes sélectionnés dans la liste de gauche à la liste de droite
function addActesPatient(){
global $session;
global $options;

//Ajout du diagnostic sélectionné
if ($_POST['idListeSelection1']!="aucun#"){
	$codeDiag=$_POST['idListeSelection1'];
	unset($paramRq);
	$paramRq[codeActe]=$codeDiag;
	$paramRq[idEvent]=$this->idEvent;
	$paramRq[idDomaine]=CCAM_IDDOMAINE;
	$paramRq[type]="DIAG";
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_get1ActeCote",$paramRq,"ResultQuery");
	global $logs ;
	$logs -> addLog ( "actes", $session->getNaviFull ( ), "Ajout d'un diag '$codeDiag'" ) ;
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	if ($res[INDIC_SVC][2]==0){
		//Envoyer le diagnostic au terminal des urgences si c'est le 1er
		unset($paramRq);
		$paramRq[idEvent]=$this->idEvent;
		$paramRq[idDomaine]=CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_verifDiag",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		if ($res[INDIC_SVC][2]==0){
			unset($param);
			$param[diagnostic_categorie]=$_POST['idListeSelection0'];
			$param[diagnostic_code]=$codeDiag;
			
			//Recherche du libellé du diagnostic
			unset($paramRq);
			$paramRq[code]=$codeDiag;
			$paramRq[idDomaine]=CCAM_IDDOMAINE;
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_getLib1Diag",$paramRq,"ResultQuery");
			//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
			$param[diagnostic_libelle]=$res[nomItem][0];
			$libDiag=$res[nomItem][0];
			
			//Insertion dans la table des patients_presents ou patients_sortis
			if ($this->typeListe=="Sortis") $nomTable=PSORTIS; else $nomTable=PPRESENTS;
			$requete=new clRequete(BDD,$nomTable,$param);
			$sql=$requete->updRecord("idpatient=".$this->idEvent);
		}
		
		//Insertion du diagnostic dans la table des cotations
		unset($param);
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[idEvent]=$this->idEvent;
		$param[dateEvent]=date("Y-m-d H:i:")."00";
  	if (substr($this->lieuInterv,0,4)=="UHCD"){
      if (substr($this->dateEvent,0,10)==date("Y-m-d")) $param[dateDemande]=$this->dateEvent;
      else $param[dateDemande]=date("Y-m-d")." 08:00:00";
     } 
    else $param[dateDemande]=$this->dateEvent;
		$param[dtFinInterv]=$this->dtFinInterv;
		$param[idu]=$this->idu;
		$param[ipp]=$this->ipp;
		$param[nomu]=$this->nomu;
		$param[pren]=$this->pren;
		$param[sexe]=$this->sexe;
		$param[dtnai]=$this->dtnai;
		$param[numSejour]=$this->nsej;
		$param[typeAdm]=$this->typeAdm;
		$param[matriculeIntervenant]=$this->matriculeIntervenant;
		$param[nomIntervenant]=$this->nomIntervenant;
		$param[numUFdem]=$this->numUFdem;
		$param[numUFexec]=$this->numUFexec;
		$param[lieuInterv]=$this->lieuInterv;
		$param[type]="DIAG";
		$param[Urgence]="O";
		$param[codeActe]=$codeDiag;
		
		//Recherche du libellé du diagnostic
		unset($paramRq);
		$paramRq[code]=$codeDiag;
		$paramRq[idDomaine]=CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getLib1Diag",$paramRq,"ResultQuery");

		$libDiag=$res[nomItem][0];
		$param[libelleActe]=addslashes($libDiag);
		
		$param[categorie]=$_POST['idListeSelection0'];
		$param[lesionMultiple]=$_POST['lesion'];
		$majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
		$sql=$majrq->addRecord();
		
		//Ecriture dans la BAL
		//if ($this->typeListe=="Sortis") $this->writeBAL($param[codeActe],"creation");
		
		//Envoi d'une alerte grippe/méningite si le nvx diag inséré est concerné par l'alerte
    $listeGen=new clListesGenerales("recup");
    $liste=$listeGen->getListeItems("Alerte Virus - Code Diagnostics", "1",'','',"1");
    while (list($key,$val)=each($liste)){
      if ($val==$codeDiag){
        $_POST['type']="virus";
        $_POST['Envoyer']=1;
        $_POST['sendMessage']="Signalement grippe/méningite ($codeDiag)";
      }
    }
	}
}

//Ajout des actes
if (is_array($_POST['listeGauche'])){
	while (list($key,$val)=each($_POST['listeGauche'])){ 
		if ($val and $val!="aucun#"){
			if (substr($val,0,4)=="PACK"){
				
eko("Affectation d'un pack à un patient");

        //Si la ligne sélectionnée est un pack, recherche des actes qui le composent
				// pour insertion dans la liste des actes affectés
				unset($paramRq);
				$paramRq[idDomaine]=CCAM_IDDOMAINE;
				$paramRq[idListeSelection1]=$val;
				$paramRq[cw]="and act.date_fin='0000-00-00' and (rel.idActe not like 'NGAP%' or 
					rel.idActe like 'NGAP%' and act.cotationNGAP<>'')";
				$req=new clResultQuery;
				$res=$req->Execute("Fichier","CCAM_getActesPack",$paramRq,"ResultQuery");
				$requete=new clResultQuery;
         
        
        // URGENTISTE
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation urgentiste'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        $codeNGAPConsultationUrgentiste=$codeNGAP[idActe][0];
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation urgentiste non facturable'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationUrgentisteNonFacturable=$codeNGAP[idActe][0];

        // SPECIALISTE
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationUrgentisteSpecialiste=$codeNGAP[idActe][0];
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationUrgentisteSpecialisteNonFacturable=$codeNGAP[idActe][0];
        
        // SAGE-FEMMES
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation sage-femme'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationSageFemme=$codeNGAP[idActe][0];
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation sage-femme non facturable'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationSageFemmeNonFacturable=$codeNGAP[idActe][0];
        
        // GYNECO-OBSTETRIQUE
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationGynécologieObstétrique=$codeNGAP[idActe][0];
        unset($paramRq2);
        $paramRq2["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq2,"ResultQuery");
        //eko ( $codeNGAP );
        $codeNGAPConsultationGynécologieObstétriqueNonFacturable=$codeNGAP[idActe][0];
        
        
        
        for ($i=0;isset($res[identifiant][$i]);$i++){ 
					unset($paramRq);
					
					$paramRq[codeActe]=$res[identifiant][$i];
			
          // ||||||||||||||||||| 
          // cas d'une consultation urgentiste et si dans le pack on a
          // ||||||||||||||||||| 
          if ( strcmp ($this->typeIntervenant,"URG") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteSpecialiste)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentiste;
          
          elseif ( strcmp ($this->typeIntervenant,"URG") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteSpecialisteNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"URG") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationSageFemme)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentiste;
          
          elseif ( strcmp ($this->typeIntervenant,"URG") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationSageFemmeNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"URG") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationGynécologieObstétrique)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentiste;
          
          elseif ( strcmp ($this->typeIntervenant,"URG") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationGynécologieObstétriqueNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteNonFacturable;
          
          // ||||||||||||||||||| 
          // cas d'une consultation specialiste (pédiatre) et si dans le pack on a
          // ||||||||||||||||||| 
          elseif ( strcmp ($this->typeIntervenant,"PED") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentiste)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteSpecialiste;
          
          elseif ( strcmp ($this->typeIntervenant,"PED") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteSpecialisteNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"PED") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationSageFemme)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteSpecialiste;
          
          elseif ( strcmp ($this->typeIntervenant,"PED") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationSageFemmeNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteSpecialisteNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"PED") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationGynécologieObstétrique)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteSpecialiste;
          
          elseif ( strcmp ($this->typeIntervenant,"PED") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationGynécologieObstétriqueNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationUrgentisteSpecialisteNonFacturable;
          
          
          // ||||||||||||||||||| 
          // cas d'une consultation sage-femmes et si dans le pack on a
          // ||||||||||||||||||| 
          elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentiste)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationSageFemme;
          
          elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationSageFemmeNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteSpecialiste)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationSageFemme;
          
          elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteSpecialisteNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationSageFemmeNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationGynécologieObstétrique)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationSageFemme;
          
          elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationGynécologieObstétriqueNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationSageFemmeNonFacturable;
          
          // ||||||||||||||||||| 
          // cas d'une consultation Gynécologie Obstétrique et si dans le pack on a
          // ||||||||||||||||||| 
          elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentiste)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationGynécologieObstétrique;
          
          elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationGynécologieObstétriqueNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteSpecialiste)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationGynécologieObstétrique;
          
          elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationUrgentisteSpecialisteNonFacturable)==0 )

            $paramRq[codeActe]=$codeNGAPConsultationGynécologieObstétriqueNonFacturable;
          
          elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationSageFemme)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationGynécologieObstétrique;
          
          elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 && 
          strcmp($res[identifiant][$i],$codeNGAPConsultationSageFemmeNonFacturable)==0 )
            $paramRq[codeActe]=$codeNGAPConsultationGynécologieObstétriqueNonFacturable;
          
        
					
          $paramRq[idEvent]=$this->idEvent;
					$paramRq[idDomaine]=CCAM_IDDOMAINE;
					$paramRq[type]="ACTE";
					$req2=new clResultQuery;
					$res2=$req2->Execute("Fichier","CCAM_get1ActeCote",$paramRq,"ResultQuery");
					global $logs ;
					$logs -> addLog ( "actes", $session->getNaviFull ( ), "Ajout d'un acte '".$paramRq[codeActe]."'" ) ;
	
					//newfct(gen_affiche_tableau,$res2[INDIC_SVC]);
					if ($res2[INDIC_SVC][2]==0) 
						$message.=$this->add1Acte($paramRq[codeActe],$res[quantite][$i],
							$res[periodicite][$i]);
				}
			}
			else $message=$this->add1Acte($val);
		}
	}
}

($message)?$retourInfos[erreur]=$message:
	$retourInfos[infos]="Les diagnostics et les actes sélectionnés ont été affectés au patient en cours";
return $retourInfos;
}

function add1Acte($codeActe,$qte=1,$periodicite="aucune"){

global $options;

unset($paramRq);
$paramRq[idActe]=$codeActe;
$paramRq[cw]="and idDomaine=".CCAM_IDDOMAINE;
$req3=new clResultQuery;
$res3=$req3->Execute("Fichier","CCAM_get1Acte",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res3[INDIC_SVC]);

//Recherche s'il existe un code d'actvité 4
unset($paramRq);
$paramRq[idActe]=$codeActe;
$req4=new clResultQuery;
$res4=$req4->Execute("Fichier","CCAM_getInfos1ActeCCAM",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res4[INDIC_SVC]);
($res4[ACTIV_ANESTHESISTE][0]=="O")?$activ4="O":$activ4="";

if ($activ4=="O" and $_POST['anesthesiste']=="aucun#")
	return $message="L'anesthésiste doit être précisé pour l'acte '$codeActe'. L'acte n'a donc pu être affecté au patient.";
else{
	unset($param);
	$param[codeActe]=$codeActe;
	$param[libelleActe]=addslashes($res3[libelle][0]);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[idEvent]=$this->idEvent;
	$param[dateEvent]=date("Y-m-d H:i:")."00";
	/*if (substr($this->lieuInterv,0,4)=="UHCD"){
    if (substr($this->dateEvent,0,10)==date("Y-m-d")) $param[dateDemande]=$this->dateEvent;
    else $param[dateDemande]=date("Y-m-d")." 08:00:00";
   } 
  else $param[dateDemande]=$this->dateEvent;*/
  $param[dateDemande]=$this->dateEvent;
	$param[dtFinInterv]=$this->dtFinInterv;
	$param[idu]=$this->idu;
	$param[ipp]=$this->ipp;
	$param[nomu]=$this->nomu;
	$param[pren]=$this->pren;
	$param[sexe]=$this->sexe;
	$param[dtnai]=$this->dtnai;
	$param[numSejour]=$this->nsej;
	$param[typeAdm]=$this->typeAdm;
	$param[matriculeIntervenant]=$this->matriculeIntervenant;
	$param[nomIntervenant]=$this->nomIntervenant;
	$param[numUFdem]=$this->numUFdem;
	$param[numUFexec]=$this->numUFexec;
	$param[lieuInterv]=$this->lieuInterv;
	$param[type]="ACTE";
	$param[Urgence]="O";
	$param[cotationNGAP]=$res3[cotationNGAP][0];
	$param[quantite]=$qte;
	$param[periodicite]=$periodicite; 	
	
	list($dateActe,$heureActe)=explode(" ",$this->dateEvent);
	list($annee,$mois,$jour)=explode("-",$dateActe);
	(substr($mois,0,1)==0)?$mois=substr($mois,1,1):"";
	(substr($jour,0,1)==0)?$jour=substr($jour,1,1):"";
	list($heure,$minute,$seconde)=explode(":",$heureActe);
	(substr($heure,0,1)==0)?$heure=substr($heure,1,1):"";
	$dateActe=mktime(0,0,0,$mois,$jour,$annee);
	//eko("annee:$annee-mois:$mois-jour:$jour-heure:$heure-dateActe:$dateActe-dateEvent:".$this->dateEvent);
  if (substr($codeActe,0,4)!="NGAP"){
		//Gestion des modificateurs par défaut : A, F, P, S
		//Récupération des modificateurs autorisés pour l'acte en cours
		unset($paramRq);
		/*$paramRq[cw]="and substring(rel.aa_code,1,7)='$codeActe' and 
			rel.modifi_cod in ('A','F','P','S','L')";*/
		$paramRq["cw"]="and a.code='$codeActe'";
    $req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getModificateursActe",$paramRq,"ResultQuery");

		unset($modificateursOK);
		for ($i=0;isset($res["modifi_cod"][$i]);$i++){
			$modif=$res["modifi_cod"][$i];
			$modificateursOK[$modif]=$modif;
		}
		//eko ($modificateursOK);
		$modificateurs="";
		//Gestion des tranches horaires
		if (($heure>=20 or ($heure>=6 and $heure<8)) and $modificateursOK[P]) $modificateurs="P";
		elseif ($heure<6 and $modificateursOK[S]) $modificateurs="S";
		//elseif ($modificateursOK[F]) $modificateurs="F";
		
    // MODIFICATION A FAIRE SI NECESSAIRE pour les sages - femmes et gyneco-obstetrique
    // eko($this->patient->getTypeMedecin());
    // ajout du modificateur U 
    // elseif (($heure<9 or $heure>=20) and $modificateursOK[U]) $modificateurs="U";
		
		//Gestion des dimanches
		if (date("w",$dateActe)==0 and $modificateurs=="" and $modificateursOK[F]) $modificateurs="F";
		
		//Gestion des jours fériés ferié
		$dateFerie=new clDate($dateActe);
		if ($dateFerie->isHoliday() and $modificateurs=="" and $modificateursOK[F]) $modificateurs="F";
		
		//Gestion du modificateur fracture ouverte
		$modifSansL=$modificateurs;
		unset($paramRq);
		$paramRq[code]=$_POST['idListeSelection1'];
		$paramRq[idDomaine]=CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res3=$req->Execute("Fichier","CCAM_getLib1Diag",$paramRq,"ResultQuery");
		//eko($res3[INDIC_SVC]);
		$libDiag=$res3[nomItem][0];
		if (ereg("(ouverte)",$libDiag) and $modificateursOK[L]) $modificateurs.="~L";
		
		//Si une activité 4 est associée
		if ($activ4=="O"){
			if ($_POST['anesthesiste']==$this->matriculeIntervenant) $nomMed4=$this->nomIntervenant;
      else{
        unset($paramRq);
  			$paramRq[code]=$_POST['anesthesiste'];
  			$paramRq[idDomaine]=CCAM_IDDOMAINE;
  			$paramRq[nomListe]="Anesthésie";
  			$req=new clResultQuery;
  			$res2=$req->Execute("Fichier","CCAM_getNomMed",$paramRq,"ResultQuery");
  			//eko($res2[INDIC_SVC]);
  			$nomMed4=$res2[nomItem][0];
  		}
			
			$modificateurs.="|".$modifSansL;
			$param[matriculeIntervenant].="|".$_POST['anesthesiste'];
			$param[nomIntervenant].="|".$nomMed4;
			$param[codeActivite4]=$activ4;
			if (($this->agePatient<4 or $this->agePatient>=80) and $modificateursOK[A]) $modificateurs.="~A";
		}
	}
	else{
    
    
    //Gestion des modificateurs de nuit et jours fériés
    //Gestion des tranches horaires pour tous les actes NGAP
    //On gère les majo infirmiers au momment ou l'on génére les messages pour MBTV2
    if (($heure>=20 or ($heure>=6 and $heure<8))) $majoNGAP="N";
    elseif ($heure<6) $majoNGAP="NM";
    
    //Gestion des dimanches
    if (date("w",$dateActe)==0 and $majoNGAP=="") $majoNGAP="F";
    
    //Gestion des jours fériés
    $dateFerie=new clDate($dateActe);
    if ($dateFerie->isHoliday() and $majoNGAP=="") $majoNGAP="F";
    $modificateurs=$majoNGAP;
	}
	
	// A.T.U Si presence de cette acte pas de modificateur : 
  //===========================================================================================================
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "libelleActe='ATU'";
  $codeNGAPATU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  if (strcmp($codeActe,$codeNGAPATU["idActe"][0])==0) $modificateurs="";
  //===========================================================================================================
	
	// M.T.U Si presence de cette acte pas de modificateur : 
  //===========================================================================================================
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "libelleActe='MTU'";
  $codeNGAPMTU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  if (strcmp($codeActe,$codeNGAPMTU["idActe"][0])==0) $modificateurs="";
  //===========================================================================================================
  
	
  //===========================================================================================================
  if ($codeActe=="GLQP005") $modificateurs="";// A supprimer quand décision sera prise pour cet acte
  //===========================================================================================================
  //($modificateurs)?$param["modificateurs"]=$modificateurs:"";
	if ($_POST['lesion']) $param[lesionMultiple]=$_POST['lesion']; else $param[lesionMultiple]="Non";
	$majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
	$sql=$majrq->addRecord();
	
	//Ecriture dans la BAL
	//if ($this->typeListe=="Sortis") $this->writeBAL($param[codeActe],"creation");
	
	//MAJ Fréquence d'utilisation de l'acte
	$paramRq[idDomaine]=CCAM_IDDOMAINE;
	$paramRq[idActe]=$codeActe;
	$req3=new clResultQuery;
	$res3=$req3->Execute("Fichier","CCAM_getMaxFreqActe",$paramRq,"ResultQuery");
	//newfct(gen_affiche_tableau,$res3[INDIC_SVC]);
	unset($param);
	$param[frequence]=$res3[freq_max][0]+1;
	$majrq=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
	$sql=$majrq->updRecord("idActe='$paramRq[idActe]' AND idDomaine=".CCAM_IDDOMAINE);
}
}

//Modification de l'acte sélectionné dans la table ccam_cotation_actes
function modifyActe(){
/*global $session;
unset($retourInfos);
$idActe=$_POST['imgOK'];
$qte=$_POST['qte'];
($qte=="")?$qte=1:"";
($qte<=1)?$periodicite="aucune":$periodicite=$_POST['periodicite'];
unset($param);
$param[quantite]=$qte;
$param[periodicite]=$periodicite;
$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
$sql=$requete->updRecord("codeActe='$idActe' and idDomaine=".CCAM_IDDOMAINE.
	" and idEvent=".$this->idEvent);
$retourInfos[infos]="L'acte '$idActe' a été modifié";

return $retourInfos;*/
}

//Suppression de l'acte ou diagnostic sélectionné 
//dans la liste des actes/diagnostics affectés au patient en cours
function delActesPatient($idActe){
global $session;
unset($retourInfos);

//Ecriture de la suppression dans la BAL si le patient est à nouveau rentré dans la liste des présents
unset($paramRq);
/*$paramRq[cw]="codeActe='$idActe' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE;
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
if ($res[validDefinitive][0]=="O") $this->writeBAL($idActe,"suppression");*/

//Recherche si l'enregistrement supprimé est un diagnostic
unset($paramRq);
$paramRq[codeActe]=$idActe;
$paramRq[type]="DIAG";
$paramRq[idEvent]=$this->idEvent;
$paramRq[idDomaine]=CCAM_IDDOMAINE;
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_get1ActeCote",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
if ($res[INDIC_SVC][2]!=0) $diag="O"; else $diag="N";

//Suppression de l'enregistrement dans la table des cotations
$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
$sql=$requete->delRecord("codeActe='$idActe' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE);

global $logs ;
if ( $diag )
	$logs -> addLog ( "actes", $session->getNaviFull ( ), "Suppression diag '$idActe'" ) ;
else
	$logs -> addLog ( "actes", $session->getNaviFull ( ), "Suppression acte '$idActe'" ) ;
			

//Si l'enregistrement supprimé est un diagnostic, mise à jour de la base du terminal
if ($diag=="O"){
	//Recherche du diagnostic suivant
	unset($paramRq);
	$paramRq[idDomaine]=CCAM_IDDOMAINE;
	$paramRq[idEvent]=$this->idEvent;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getDiagCotes",$paramRq,"ResultQuery");
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	unset($param);
	if ($res[INDIC_SVC][2]!=0){
		//Recherche de la catégorie du diagnostic
		unset($paramRq);
		$paramRq[codeActe]=$res[identifiant][0];
		$paramRq[type]="DIAG";
		$paramRq[idEvent]=$this->idEvent;
		$paramRq[idDomaine]=CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_get1ActeCote",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		$param[diagnostic_categorie]=$res[categorie][0];
		$param[diagnostic_code]=$paramRq[codeActe];
		
		//Recherche du libellé du diagnostic
		unset($paramRq);
		$paramRq[code]=$param[diagnostic_code];
		$paramRq[idDomaine]=CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getLib1Diag",$paramRq,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		$param[diagnostic_libelle]=$res[nomItem][0];
	}
	else $param[diagnostic_categorie]=$param[diagnostic_code]=$param[diagnostic_libelle]="";
	
	//MAJ du diagnostic dans la table des patients_presents ou patients_sortis
	if ($this->typeListe=="Sortis") $nomTable=PSORTIS; else $nomTable=PPRESENTS;
	$requete=new clRequete(BDD,$nomTable,$param);
	$sql=$requete->updRecord("idpatient=".$this->idEvent);
}

//MAJ Fréquence d'utilisation de l'acte
unset($paramRq);
$paramRq[idDomaine]=CCAM_IDDOMAINE;
$paramRq[idActe]=$idActe;
$req3=new clResultQuery;
$res3=$req3->Execute("Fichier","CCAM_getMaxFreqActe",$paramRq,"ResultQuery");
//newfct(gen_affiche_tableau,$res3[INDIC_SVC]);
unset($param);
$param[frequence]=$res3[freq_max][0]-1;
$majrq=new clRequete(CCAM_BDD,"ccam_actes_domaine",$param);
$sql=$majrq->updRecord("idActe='$paramRq[idActe]' AND idDomaine=".CCAM_IDDOMAINE);

$retourInfos="L'acte (ou diagnostic) '".$idActe."' n'est plus affecté au patient en cours";

return $retourInfos;
}

//Fabrication d'une liste de valeurs à partir d'une requête
function tableauValeurs($requete,$param="",$lignePresentation=""){

	global $options;

	// Récupération de la liste de valeurs
	$req=new clResultQuery;
	$res=$req->Execute("Fichier",$requete,$param,"ResultQuery");

	//eko ( $requete ) ;
	//eko($res);
	// Dans le cas des actes NGAP et CCAM associés aux diagnostics
	// un urgentiste spec  consultation urgentiste -> consultation urgentiste spe est vise versa
	// un urgentiste spec  consultation urgentiste non fac -> consultation urgentiste spec non facturable 
	if ( strcmp ($requete,"CCAM_getActesDiags") == 0 ) {

          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste'";
          $codeNGAP_S_f      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste spécialiste non facturable'";
          $codeNGAP_S_nf      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation urgentiste'";
          $codeNGAP_U_f      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation urgentiste non facturable'";
          $codeNGAP_U_nf      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation sage-femme'";
          $codeNGAP_Sf_f      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation sage-femme non facturable'";
          $codeNGAP_Sf_nf      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique'";
          $codeNGAP_Ob_f      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          $paramRq["cw"] = "libelleActe='Consultation gynécologie et obstétrique non facturable'";
          $codeNGAP_Ob_nf      = $req->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
          
			/*if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
			
			  $param["netforce"] = "and act.idActe<>'".$codeNGAP_S_f[idActe][0]."' and act.idActe<>'".$codeNGAP_S_nf[idActe][0]."'";
			  $param["net"] = "or ( act.idActe='".$codeNGAP_U_f[idActe][0]."' or act.idActe='".$codeNGAP_U_nf[idActe][0]."') ";
			  }
			else {
			
			  $param["netforce"] = "and act.idActe<>'".$codeNGAP_U_f[idActe][0]."' and act.idActe<>'".$codeNGAP_U_nf[idActe][0]."'";
			  $param["net"] = "or ( act.idActe='".$codeNGAP_S_f[idActe][0]."' or act.idActe='".$codeNGAP_S_nf[idActe][0]."') ";
			  }
			// depend de la specialite du medecin
			$param["cw"] = ereg_replace("and rel.idActe not in","and act.idActe not in",$param["cw"]);
			$res=$req->Execute("Fichier","CCAM_getActesDiags2",$param,"ResultQuery");*/

		  $res=$req->Execute("Fichier","CCAM_getActesDiags",$param,"ResultQuery"); 

		  $param["codeNGAP_S_f"]     = $codeNGAP_S_f[idActe][0];
		  $param["codeNGAP_S_nf"]    = $codeNGAP_S_nf[idActe][0];
		  $param["LibelleNGAP_S_f"]  = "Consultation urgentiste spécialiste";
		  $param["LibelleNGAP_S_nf"] = "Consultation urgentiste spécialiste non facturable";
		
		  $param["codeNGAP_U_f"]     = $codeNGAP_U_f[idActe][0];
		  $param["codeNGAP_U_nf"]    = $codeNGAP_U_nf[idActe][0];
		  $param["LibelleNGAP_U_f"]  = "Consultation urgentiste";
		  $param["LibelleNGAP_U_nf"] = "Consultation urgentiste non facturable";
		  
		  $param["codeNGAP_Sf_f"]     = $codeNGAP_Sf_f[idActe][0];
		  $param["codeNGAP_Sf_nf"]    = $codeNGAP_Sf_nf[idActe][0];
		  $param["LibelleNGAP_Sf_f"]  = "Consultation sage-femme";
		  $param["LibelleNGAP_Sf_nf"] = "Consultation sage-femme non facturable";
		  
		  $param["codeNGAP_Ob_f"]     = $codeNGAP_Ob_f[idActe][0];
		  $param["codeNGAP_Ob_nf"]    = $codeNGAP_Ob_nf[idActe][0];
		  $param["LibelleNGAP_Ob_f"]  = "Consultation gynécologie et obstétrique";
		  $param["LibelleNGAP_Ob_nf"] = "Consultation gynécologie et obstétrique non facturable";
		  		  
		  /*eko($param["codeNGAP_Sf_f"]);
		  eko($param["codeNGAP_Sf_nf"]);
		  eko($param["LibelleNGAP_Sf_f"]);
		  eko($param["LibelleNGAP_Sf_nf"]);*/
  

		if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
  
		  	for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
			    // Spécialiste (pédiatre) dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_S_f"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_U_f"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_U_f"];
			    }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_S_nf"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_U_nf"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_U_nf"];
			    }
			    // Sage-femme dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Sf_f"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_U_f"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_U_f"];
			    }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Sf_nf"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_U_nf"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_U_nf"];
			    }
			    // gynécologie et obstétrique dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Ob_f"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_U_f"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_U_f"];
			    }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Ob_nf"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_U_nf"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_U_nf"];
			    }
			}
		} // if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
		elseif ( strcmp ($this->typeIntervenant,"PED") == 0 ) {  
  			for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
			    // Urgentiste dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_U_f"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_S_f"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_S_f"];
			      }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_U_nf"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_S_nf"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_S_nf"];
			      }
			    // Sage-femme dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Sf_f"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_S_f"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_S_f"];
			      }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Sf_nf"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_S_nf"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_S_nf"];
			      }
			    // gynécologie et obstétrique dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Ob_f"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_S_f"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_S_f"];
			      }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Ob_nf"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_S_nf"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_S_nf"];
			      }
		    }
  		} // if ( strcmp ($this->typeIntervenant,"PED") == 0 ) {
		elseif ( strcmp ($this->typeIntervenant,"SAF") == 0 ) {
		  for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
			  // Urgentiste dans les diags
			  if ( $res["identifiant"][$i] == $param["codeNGAP_U_f"] ) {
			    $res["identifiant"][$i] = $param["codeNGAP_Sf_f"];
			    $res["libelle"][$i]     = $param["LibelleNGAP_Sf_f"];
			  }
			  if ( $res["identifiant"][$i] == $param["codeNGAP_U_nf"] ) {
			    $res["identifiant"][$i] = $param["codeNGAP_Sf_nf"];
			    $res["libelle"][$i]     = $param["LibelleNGAP_Sf_nf"];
			  }
			  // Spécialiste (pédiatre) dans les diags
			  if ( $res["identifiant"][$i] == $param["codeNGAP_S_f"] ) {
			    $res["identifiant"][$i] = $param["codeNGAP_Sf_f"];
			    $res["libelle"][$i]     = $param["LibelleNGAP_Sf_f"];
			  }
			  if ( $res["identifiant"][$i] == $param["codeNGAP_S_nf"] ) {
			    $res["identifiant"][$i] = $param["codeNGAP_Sf_nf"];
			    $res["libelle"][$i]     = $param["LibelleNGAP_Sf_nf"];
			  }
			  // gynécologie et obstétrique dans les diags
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Ob_f"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_Sf_f"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_Sf_f"];
			      }
			    if ( $res["identifiant"][$i] == $param["codeNGAP_Ob_nf"] ) {
			      $res["identifiant"][$i] = $param["codeNGAP_Sf_nf"];
			      $res["libelle"][$i]     = $param["LibelleNGAP_Sf_nf"];
			      }
		  }

  		} //if ( strcmp ($this->typeIntervenant,"SAF") == 0 )
		elseif ( strcmp ($this->typeIntervenant,"OBS") == 0 ) {
			  for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
				  // Urgentiste dans les diags
				  if ( $res["identifiant"][$i] == $param["codeNGAP_U_f"] ) {
				    $res["identifiant"][$i] = $param["codeNGAP_Ob_f"];
				    $res["libelle"][$i]     = $param["LibelleNGAP_Ob_f"];
				  }
				  if ( $res["identifiant"][$i] == $param["codeNGAP_U_nf"] ) {
				    $res["identifiant"][$i] = $param["codeNGAP_Ob_nf"];
				    $res["libelle"][$i]     = $param["LibelleNGAP_Ob_nf"];
				  }
				  // Spécialiste (pédiatre) dans les diags
				  if ( $res["identifiant"][$i] == $param["codeNGAP_S_f"] ) {
				    $res["identifiant"][$i] = $param["codeNGAP_Ob_f"];
				    $res["libelle"][$i]     = $param["LibelleNGAP_Ob_f"];
				  }
				  if ( $res["identifiant"][$i] == $param["codeNGAP_S_nf"] ) {
				    $res["identifiant"][$i] = $param["codeNGAP_Ob_nf"];
				    $res["libelle"][$i]     = $param["LibelleNGAP_Ob_nf"];
				  }
				  // gynécologie et obstétrique dans les diags
				  if ( $res["identifiant"][$i] == $param["codeNGAP_Sf_f"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_Ob_f"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_Ob_f"];
				  }
				  if ( $res["identifiant"][$i] == $param["codeNGAP_Sf_nf"] ) {
				      $res["identifiant"][$i] = $param["codeNGAP_Ob_nf"];
				      $res["libelle"][$i]     = $param["LibelleNGAP_Ob_nf"];
				  }
  			}

 	 	} //if ( strcmp ($this->typeIntervenant,"SAF") == 0 )
	} //if ( strcmp ($requete,"CCAM_getActesDiags") == 0 )

	if ( strcmp($options->getOption("CCAM_TriListeActes"),"Libellé de l'acte") == 0 && strcmp ($requete,"CCAM_getActesNonListe") == 0 ) {

		  $ngaptableau=$req->Execute("Fichier","CCAM_getActesNonListe_ngap",$param,"ResultQuery");
		  $packtableau=$req->Execute("Fichier","CCAM_getActesNonListe_pack",$param,"ResultQuery");
		  $param["order"]="rel.idActe";
		  $ccamtableau=$req->Execute("Fichier","CCAM_getActesNonListe_ccam",$param,"ResultQuery");
		  
		  $nombre_ccam=$ccamtableau[INDIC_SVC][2];
		  $nombre_ngap=$ngaptableau[INDIC_SVC][2];
		  $nombre_pack=$packtableau[INDIC_SVC][2];
		  
		  unset($ccamtableau[INDIC_SVC]);unset($ngaptableau[INDIC_SVC]);unset($packtableau[INDIC_SVC]);
		  
		  $azerty=array();
		  $j=0;
		  for ( $i=0 ; $i < $nombre_ccam ; $i++ ) {
		    while (list($key,$val)=each($ccamtableau)){
		      $azerty[$key][$i] = $ccamtableau[$key][$j];
		    }
		    reset($ccamtableau);$j++;
		  }
		  $j=0;
		  for ( $k=$i ; $k < $nombre_ccam+$nombre_ngap ; $k++ ) {
		    while (list($key,$val)=each($ngaptableau)){
		      $azerty[$key][$k] = $ngaptableau[$key][$j];
		    }
		    reset($ngaptableau);$j++;
		  }
		  $j=0;
		  for ( $i=$k ; $i <$nombre_ccam+$nombre_ngap+$nombre_pack ; $i++ ) {
		    while (list($key,$val)=each($packtableau)){
		      $azerty[$key][$i] = $packtableau[$key][$j];
		    }
		    reset($packtableau);$j++;
		  }
		  
		  $azerty[INDIC_SVC][2] = $nombre_ccam+$nombre_ngap+$nombre_pack;
		  $res=$azerty;
	}

	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	($requete=="CCAM_getListeSpe")?$tab[tous]="Toutes les spécialités":
		$tab["aucun#"]=$lignePresentation;
	for ($i=0;isset($res[identifiant][$i]);$i++){ 
		$libelle=strtr($res[libelle][$i],"ÉÈÊÀ","éèêà");
		if ($res[title][$i]=="" ) {
			if ($res[cotationNGAPvide][$i]=="" and substr($res[identifiant][$i],0,4)=="NGAP"){
				$tab[$res[identifiant][$i]].="*";
			}
			if ( ! ( strcmp ( $requete, "CCAM_getListeDiags" ) == 0 AND ereg ( 'DIAG', $res[identifiant][$i] ) ) ) $tab[$res[identifiant][$i]].=$res[identifiant][$i];
			if ($res[cotationNGAP][$i]!=""){
				$tab[$res[identifiant][$i]].=" - ".$res[cotationNGAP][$i];
			}
			
			if ($libelle!=""){
				if ( strcmp ( $requete, "CCAM_getListeDiags" ) == 0 AND ! ereg ( 'DIAG', $res[identifiant][$i] ) )  $tab[$res[identifiant][$i]] = ucfirst(strtolower($libelle));
				elseif ( strcmp ( $requete, "CCAM_getListeDiags" ) == 0 AND ereg ( 'DIAG', $res[identifiant][$i] ) ) { 
					$id = $res[identifiant][$i].'" style="color:red;' ;
					$tab[$id] .= $res[identifiant][$i].' - '.strtoupper($libelle);
				} else $tab[$res[identifiant][$i]].=" - ".ucfirst(strtolower($libelle));
			}
			
		} else {
			$title=strtr($res[title][$i],"ÉÈÊÀ","éèêà");
			// DBDEB2 : Ajout de couleur pour les sous-catégories de diagnostics.
			if ( strcmp ( $requete, "CCAM_getListeDiags" ) == 0 AND eregi ( 'DIAG', $res['libelle'][$i] ) ) $style = 'style="color: #AA2222;"' ; else $style = '' ;
			// DBFIN2
			$identifiant=$res[identifiant][$i]."\" $style onmouseover=\"window.status='Voir cet événement'; 
				show(event, 'id".$res[identifiant][$i]."'); return true;\" 
				onmouseout=\"hide('id".$res[identifiant][$i]."'); return true;";
			if ($res[cotationNGAPvide][$i]=="" and substr($res[identifiant][$i],0,4)=="NGAP"){
				$tab[$identifiant].="*";
			}
			$tab[$identifiant].=$res[identifiant][$i];
			if ($res[cotationNGAP][$i]!=""){
				$tab[$identifiant].=" - ".$res[cotationNGAP][$i];
			}
			
			//if ($libelle!=""){
				//$tab[$identifiant].=" - ".ucfirst(strtolower($libelle));
			//}
			if ( $options->getOption("AffichageTarifsCCAM") ) {
			
				if ($libelle!="" && strcmp($requete,"CCAM_getActesNonListe")==0){
		        	unset($paramRq);
		        	$paramRq["cw"]="and a.code='".$res[identifiant][$i]."' order by t.aadt_modif desc";
		        	$fat=$req->Execute("Fichier","CCAM_get1TarifCCAM",$paramRq,"ResultQuery");
		        	//eko ($fat);
		        	//eko($fat["pu_base"][0]);
		        	if ( $fat[INDIC_SVC][2] != 0 )
		          		$tab[$identifiant].=" - ".$fat[pu_base][0]."&euro;. - ".ucfirst(strtolower($libelle));
		        	else
		          		$tab[$identifiant].=" - ".ucfirst(strtolower($libelle));
		      	}
		      	if ($libelle!="" && strcmp($requete,"CCAM_getActesNonListe")!=0){
		        	$tab[$identifiant].=" - ".ucfirst(strtolower($libelle));
		      	}
		     
		    } else {
	    
	      		if ($libelle!=""){
					 $tab[$identifiant].= " - ".ucfirst(strtolower($libelle));
				}
	    
	    	}
			
			$mod = new ModeliXe ( "CCAM_InfoBulleActes.mxt" ) ;
			$mod -> SetModeliXe ( ) ;
			$mod -> MxText ( "iddiv", "id".$res[identifiant][$i] ) ;
			$mod -> MxText ( "libelleActe", $libelle ) ;
			// Récupération du code HTML généré.
			$this->af .= $mod -> MxWrite ( "1" ) ;
	
		}
	}
	//eko ( $tab ) ;
	return $tab;
}

		

//Fabrication d'une liste de valeurs pour la liste de gauche
//en ignorant les valeurs présentes dans la liste de droite
function valeursListeGauche($requeteTableA,$requeteTableRelation,$paramA="",$paramRelation="",
	$lignePresentation=""){
//Récupération des lignes figurant dans la liste de droite
$req=new clResultQuery;
$res=$req->Execute("Fichier",$requeteTableRelation,$paramRelation,"ResultQuery");

$listeIdRelation="";
for ($i=0;isset($res[identifiant][$i]);$i++){ 
	$tabRelation[$res[identifiant][$i]]=$res[identifiant][$i];
	$listeIdRelation.="'".$res[identifiant][$i]."',";
}
($listeIdRelation=="")?$listeIdRelation="''":$listeIdRelation=substr($listeIdRelation,0,-1);
//echo "listeIdRelation:$listeIdRelation<br>";

// Récupération de la liste de valeurs pour la liste de gauche
($requeteTableA=="CCAM_getActesDiags")?
	$paramA[cw].=" and rel.idActe not in ($listeIdRelation)":"";
$paramA[listeIdRelation]=$listeIdRelation;
$tab=$this->tableauValeurs($requeteTableA,$paramA,$lignePresentation);
return $tab;
}

function getIdSuiv($typeCode){
//Retourne la valeur suivante du plus grand code commençant par NGPA... ou Anatomie...
$req=new clResultQuery;
$param[typeCode]=$typeCode;
$res=$req->Execute("Fichier","CCAM_getIdSuiv",$param,"ResultQuery");
$maxCodeRq=$res[maxCode][0];
if ($maxCodeRq!=""){
	$derniers=substr($maxCodeRq,4,3);
	if (substr($derniers,0,2)=="00") $maxCode=substr($derniers,2,1)+1;
	elseif (substr($derniers,0,1)=="0") $maxCode=substr($derniers,1,2)+1;
	else $maxCode=substr($derniers,0,3)+1;
	
	if ($maxCode<10) $maxCode="00".$maxCode;
	elseif ($maxCode<100) $maxCode="0".$maxCode;
	$maxCode=$typeCode.$maxCode;
}
else $maxCode=$typeCode."001";
return $maxCode;
}

//Gère le template relatif à la saisie ou la modif d'un acte de la liste restreinte
function getForm1Anatomie($paramForm){
global $session;
$mod=new ModeliXe("CCAM_Form1Acte.mxt");
$mod->SetModeliXe();

$mod->MxText("titreEnCours",$paramForm[titreEnCours]);

$codeAnatomie=$paramForm[codeAnatomie];
$mod->MxText("titreCodeActe","Code de la partie anatomique : ");
$mod->MxText("codeActe",$codeAnatomie);

$mod->MxText("titreLibActe","Libellé de la partie anatomique : ");
if ($paramForm[action2]!="supprimer")
	$mod->MxFormField("libActe","textarea","libActe",$paramForm[libActe],
		"rows=\"3\" cols=\"50\"	wrap=\"virtual\"");
else{
	$mod->MxText("libVisuActe",$paramForm[libVisuActe]);
	$mod->MxText("confirmSuppr","La suppression de la partie anatomique va également entraîner 
		<br>la suppression de l'association actes/partie anatomique. 
		<br>Confirmez la suppression en cliquant sur 'OK'");
}
//Afficher les boutons suivants si droits autorisés
if (!$session->getDroit("CCAM_Listes","w")){
	$mod->MxBloc("validerActe","delete");
	$mod->MxBloc("annulerActe","delete");
}
$mod->MxHidden("hidden2","nvxCode=$codeAnatomie&action2=$paramForm[action2]");
$af=$mod->MxWrite("1");
return $af;
}

//Calcul des contraintes de sortie
function getContraintes(){
global $options;
global $session;

$ctrl=1;
unset($tabContrainte);
//eko($this->typeIntervenant);
//Contrôle de la présence d'un acte au moins pour le patient en cours
if ($options->getOption("ActiverModuleActes") and $session->getDroit("CCAM_ACTES_ACTES","r")){
	if ($options->getOption("SaisieActeObligatoire")){
		unset($param);
		$param[idDomaine]=CCAM_IDDOMAINE;
		$param[idEvent]=$this->idEvent;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getActesCotes",$param,"ResultQuery");
		//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
		if ($res[INDIC_SVC][2]==0 OR ( $options->getOption ( "ActiverCORAModuleActes" ) ) ) {
			$ctrl=0;
			$tabContrainte[acte][nom]="Acte obligatoire";
			$tabContrainte[acte][description]="La saisie d'un acte au moins est obligatoire";
			
		}
	}
	// Contrôle présence d'une et d'une seule consultation : C 1, C 0, CS 1, CS 0  , CF 0 et CF 1
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "idEvent=".$this->idEvent;
  $consultation  = $requete->Execute("Fichier","CCAM_getConsultationPropre",$paramRq,"ResultQuery");
	//eko($consultation);
	
	if ( $consultation[INDIC_SVC][2] > 1 ) {
    $ctrl=0;
    if ( strcmp($this->typeIntervenant,"URG") == 0 ) {
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste. Plusieurs Consultes.";
		}
		elseif ( strcmp($this->typeIntervenant,"PED") == 0 ) {
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste Spécialiste. Plusieurs Consultes.";
		}
		elseif ( strcmp($this->typeIntervenant,"SAF") == 0 ) {
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Sage-femme. Plusieurs Consultes.";
		}
		elseif ( strcmp($this->typeIntervenant,"OBS") == 0 ) {
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Gynécologie et Obstétrique. Plusieurs Consultes.";
		}
  }
  /*elseif ( $consultation[INDIC_SVC][2] == 0 ) {  
    $ctrl=0;
    if ( strcmp($this->typeIntervenant,"URG") == 0 ) {
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste non présente";
		}
		else {
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste Spécialiste non présente";
		}  
  }*/
  else {
    if ( strcmp($this->typeIntervenant,"URG") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation urgentiste spécialiste non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation urgentiste spécialiste") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste. Le médecin est urgentiste.";
		}
		elseif ( strcmp($this->typeIntervenant,"URG") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation sage-femme non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation sage-femme") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste. Le médecin est urgentiste.";
		}
		elseif ( strcmp($this->typeIntervenant,"URG") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation gynécologie et obstétrique non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation gynécologie et obstétrique") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste. Le médecin est urgentiste.";
		}
		elseif ( strcmp($this->typeIntervenant,"PED") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation urgentiste non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation urgentiste") == 0 ) )  {
      $ctrl=0;
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste Spécialiste. Le médecin est spécialiste.";
		}
		elseif ( strcmp($this->typeIntervenant,"PED") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation sage-femme non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation sage-femme") == 0 ) )  {
      $ctrl=0;
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste Spécialiste. Le médecin est spécialiste.";
		}
		elseif ( strcmp($this->typeIntervenant,"PED") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation gynécologie et obstétrique non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation gynécologie et obstétrique") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Urgentiste Spécialiste. Le médecin est spécialiste.";
		}
		elseif ( strcmp($this->typeIntervenant,"SAF") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation urgentiste non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation urgentiste") == 0 ) )  {
      $ctrl=0;
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Sage-Femme. Le médecin est sage-femme.";
		}
		elseif ( strcmp($this->typeIntervenant,"SAF") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation urgentiste spécialiste non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation urgentiste spécialiste") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Sage-Femme. Le médecin est sage-femme.";
		}
		elseif ( strcmp($this->typeIntervenant,"SAF") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation gynécologie et obstétrique non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation gynécologie et obstétrique") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Sage-Femme. Le médecin est sage-femme.";
		}
		elseif ( strcmp($this->typeIntervenant,"OBS") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation urgentiste non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation urgentiste") == 0 ) )  {
      $ctrl=0;
		  $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Gynécologie et Obstétrique. Le médecin est Gynéco-Obstétrique.";
		}
		elseif ( strcmp($this->typeIntervenant,"OBS") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation urgentiste spécialiste non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation urgentiste spécialiste") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Gynécologie et Obstétrique. Le médecin est Gynéco-Obstétrique.";
		}
		elseif ( strcmp($this->typeIntervenant,"OBS") == 0 &&  
( strcmp($consultation["libelleActe"][0],"Consultation sage-femme non facturable") == 0 ||
  strcmp($consultation["libelleActe"][0],"Consultation sage-femme") == 0 ) ) {
      $ctrl=0;
      $tabContrainte[consultation][nom]="Libellé acte";
		  $tabContrainte[consultation][description]="Problème avec la Consultation Gynécologie et Obstétrique. Le médecin est Gynéco-Obstétrique.";
		}
  }
}
//Contrôle de la présence d'un diagnostic au moins pour le patient en cours
if ($options->getOption("SaisieDiagObligatoire")){
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[idEvent]=$this->idEvent;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getDiagCotes",$param,"ResultQuery");
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	if ($res[INDIC_SVC][2]==0 OR ( $options->getOption ( "ActiverCORAModuleActes" ) ) ){
		$ctrl=0;
		$tabContrainte[diag][nom]="Diagnostic obligatoire";
		$tabContrainte[diag][description]="La saisie d'un diagnostic au moins est obligatoire";
	}
}
if ($ctrl==0) return $tabContrainte;
}

//Ecriture des diagnostics et actes au fil de l'eau dans la boîte aux lettres pour envoi au serveur d'actes
//Ne sert que pour les modifications et suppressions d'actes et de diagnostics déjà envoyés auparavant
/*function writeBAL($codeActe,$action){
if (!$this->manuel){
	//Préparation du champs CONTENU - Initialisation avec les données patients
	$dateDem=substr($this->dateEvent,0,10);
	$heureDem=substr($this->dateEvent,11,6)."00";
	$contenuInit=$this->nsej."|".$this->idu."|".$this->nomu."|".$this->pren."|".$this->sexe."|".
		$this->dtnai."|".$dateDem."|".$heureDem."|".$this->numUFdem;
	
	//Récupération des infos liées à l'acte
	unset($paramRq);
	$paramRq[cw]="codeActe='$codeActe' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
	//eko($res[INDIC_SVC]);
	if ($res[type][0]=="ACTE"){
		if ($this->typeListe=="Sortis"){
			unset($paramModif);
			$paramModif[validDefinitive]="O";
			$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
			$sql=$requete->updRecord("codeActe='".$res[codeActe][0]."' and idDomaine=".CCAM_IDDOMAINE.
				" and idEvent=".$this->idEvent);
		}
		
		if (substr($res[codeActe][0],0,4)=="NGAP" or substr($res[codeActe][0],0,4)=="CONS"){
			$cotation=explode("+",$res[cotationNGAP][0]);
			while (list($key,$val)=each($cotation)){
				list($lc,$coeff)=explode(" ",$val);
				if ($coeff==0){
					$factu="non";
					$coeff=1;
				}
				else $factu="oui";
				$contenuSuite="|$action|".$res[identifiant][0]."|".$res[codeActe][0].
					"|||".date("Y-m-d")."|".date("H:i").":00|".$res[nomIntervenant][0].
					"||".$res[matriculeIntervenant][0]."|".$this->numUFexec."||$lc|$coeff|$factu";
				$contenu=$contenuInit.$contenuSuite;
				
				unset($param);
				$param[DTINS]=date("Y-m-d H:i:")."00";
				$param[ETAT]="P";
				$param[DISCR]=$this->idEvent;
				$param[TYPE]="NGAP";
				$param[CONTENU]=$contenu;
				$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
				$sql=$majrq->addRecord();
			}
		}
		else{
			list($modificateurs1,$modificateurs4)=explode("|",$res[modificateurs][0]);
			list($intervenant1,$intervenant4)=explode("|",$res[matriculeIntervenant][0]);
			
			$contenuSuite="|$action|".$res[identifiant][0]."|".$res[codeActe][0].
				"|1|0|".date("Y-m-d")."|".date("H:i").":00|".$this->nomIntervenant.
				"||".$this->matriculeIntervenant."|".$this->numUFexec."|".$modificateurs1.
				"|||oui";
			$contenu=$contenuInit.$contenuSuite;
			unset($param);
			$param[DTINS]=date("Y-m-d H:i:")."00";
			$param[ETAT]="P";
			$param[DISCR]=$this->idEvent;
			$param[TYPE]="CCAM";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
			
			if ($res[codeActivite4][0]=="O"){
				//Recherche du nom de l'anesthésiste dans la liste
				unset($paramRq);
				$paramRq[code]=$intervenant4;
				$paramRq[idDomaine]=CCAM_IDDOMAINE;
				$paramRq[nomListe]="Anesthésie";
				$req=new clResultQuery;
				$res2=$req->Execute("Fichier","CCAM_getNomMed",$paramRq,"ResultQuery");
				//eko($res2[INDIC_SVC]);
				$nomMed4=$res2[nomItem][0];
				
				$contenuSuite="|$action|".$res[identifiant][0]."|".$res[codeActe][0].
					"|4|0|".date("Y-m-d")."|".date("H:i").":00|".$nomMed4.
					"||".$intervenant4."|".$this->numUFexec."|".
					$modificateurs4."|||oui";
				$contenu=$contenuInit.$contenuSuite;
				unset($param);
				$param[DTINS]=date("Y-m-d H:i:")."00";
				$param[ETAT]="P";
				$param[DISCR]=$this->idEvent;
				$param[TYPE]="CCAM";
				$param[CONTENU]=$contenu;
				$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
				$sql=$majrq->addRecord();
			}
		}
	}
	else{
		if ($action=="suppression") $cwCode="and codeActe!='$codeActe'";
		else $cwCode="";
		unset($paramRq);
		$paramRq[cw]="type='DIAG' $cwCode and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE;
		$req=new clResultQuery;
		$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
		//eko($res[INDIC_SVC]);
		if ($res[INDIC_SVC][2]==0){
			$type="suppression";
			$contenuDiag=$codeActe;
		}
		else{
			$type="création";
			$contenuDiag="";
			for ($i=0;isset($res[identifiant][$i]);$i++){ 
				($i==0)?$sep="|":$sep="~";
				$contenuDiag.=$res[codeActe][$i].$sep;
				
				unset($paramModif);
				$paramModif[validDefinitive]="O";
				$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
				$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
					" and idEvent=".$this->idEvent);
			}
			$contenuDiag=substr($contenuDiag,0,-1);
		}
		
		$contenuSuite="|$type|".$this->idEvent."|".$contenuDiag.
			"|||".date("Y-m-d")."|".date("H:i").":00|".$this->nomIntervenant.
			"||".$this->matriculeIntervenant."|".$this->numUFexec."|||||";
		$contenu=$contenuInit.$contenuSuite;
		unset($param);
		$param[DTINS]=date("Y-m-d H:i:")."00";
		$param[ETAT]="P";
		$param[DISCR]=$this->idEvent;
		$param[TYPE]="DIAG";
		$param[CONTENU]=$contenu;
		$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
		$sql=$majrq->addRecord();
	}
}
}*/

//Dans la BAL, insertion des actes pour le patient manuel et suppression des actes du patient issu
//de la fusion
function writeBALall($tabIdPatient){

global $fusion;
global $table_patient_manuel;
global $table_patient_automatique;

//newfct(gen_affiche_tableau,$tabIdPatient);
for ($ind=0;$ind<=1;$ind++){
	if ($ind==0){
    //controleActesPresents
		$this->controleActesPresents($tabIdPatient[$ind+1]);
		//
    unset($paramRq);
  	$paramRq[cw]="idEvent=".$tabIdPatient[$ind+1]." and idDomaine=".CCAM_IDDOMAINE;
  	$req=new clResultQuery;
  	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  	$dateDemande = $res["dateDemande"][0];
  	//eko($res);
		// Mise à jour des données de $tabIdPatient[$ind]
		// Prendre le nom du medecin et son code ADELI de $tabIdPatient[$ind+1]
		
		// On prend le nom du medecin du patient
    $param[cw] = "WHERE idpatient='".$tabIdPatient[$ind+1]."'" ;
    $param[table] = $table_patient_manuel;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    // On prend le numero ADELI
    unset($param);
    $param[nomitem] = $res["medecin_urgences"][0] ;
    $req = new clResultQuery ;
    $res2 = $req -> Execute ( "Fichier", "getMatriculeMedecin", $param, "ResultQuery" ) ;

    $Medecin_urgences = $res["medecin_urgences"][0];
    $Matricule        = $res2["matricule"][0];
    
    // Mise à jour de la fiche patient $tabIdPatient[$ind] seulement pour l'acte ATU
    
    $paramModif["envoi_nomIntervenant"]       = $Medecin_urgences;
    $paramModif["envoi_matriculeIntervenant"] = $Matricule;
    $paramModif["dateDemande"] = $dateDemande;
    $requete = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
    $sql     = $requete->updRecord("idEvent=".$tabIdPatient[$ind]." and idDomaine=".CCAM_IDDOMAINE);
	
    unset($paramRq); 
  	$paramRq[cw]="idEvent=".$tabIdPatient[$ind]." and idDomaine=".CCAM_IDDOMAINE;
  	$req=new clResultQuery;
  	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
    
    //eko($table_patient_automatique);
    if ( $table_patient_automatique == PSORTIS ) {
      $type="suppression";
		  $this->contenuBAL($res,$type);
		}
		
		//Suppression dans la table ccam_cotation_actes (patient pastel pour le Ch-Hyeres)
		$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",array());
		$sql=$requete->delRecord("idEvent=".$tabIdPatient[$ind]." and idDomaine=".CCAM_IDDOMAINE);
	}
	else{
    //Récup des infos de pastel pour maj identité patient manuel
    if ($this->patientSorti($tabIdPatient[$ind])) $table="Sortis"; else $table="";
    $patient=new clPatient($tabIdPatient[$ind],$table);
		
    unset($param);
		$param[numSejour]=$patient->getNSej();
		$param[idu]=$patient->getIDU();

		$param[ipp]=$patient->getILP();
		$param[nomu]=$patient->getNom();
		$param[pren]=$patient->getPrenom();
		$param[sexe]=$patient->getSexe();
		$param[dtnai]=substr($patient->getDateNaissance(),0,10);
		//eko($param);
		
		$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
		$sql=$requete->updRecord("idEvent=".$tabIdPatient[$ind]." and idDomaine=".CCAM_IDDOMAINE);
		
    if ($table){
      //Le patient manuel maintenant fusionné était sorti, on peut donc maintenant envoyer ses actes à la BAL
      /*$lesion=$this->lesion($tabIdPatient[$ind]);
      $tabAsso=$this->tabAsso($tabIdPatient[$ind],$lesion);
      
      unset($paramRq);
    	$paramRq[cw]="idEvent=$tabIdPatient[$ind] and idDomaine=".CCAM_IDDOMAINE;
    	$req=new clResultQuery;
    	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
    	//eko($res[INDIC_SVC]);
      $type="creation";
      $this->contenuBAL($res,$type,$tabAsso);*/
      $this-> writeBALSorti("",$tabIdPatient[$ind]);
    }
  }
  //eko("ind:$ind-idPatient:".$tabIdPatient[$ind]);
}
}

function patientSorti($idEvent){
//Recherche si le patient manuel est sorti
unset($paramRq);
$paramRq["table"]=PSORTIS;
$paramRq["cw"]="where idpatient='".$idEvent."'";
$req=new clResultQuery;
$res=$req->Execute("Fichier","getPatients",$paramRq,"ResultQuery");
//eko($res["INDIC_SVC"]);
if ($res["INDIC_SVC"][2]) return 1;
}

//Gestion d'une consultation non facturable si consult libérale
function consultNonFacturable($codeActe){
unset($paramRq);
$paramRq["cw"]="codeActe='$codeActe' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE;
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
if (!$res[INDIC_SVC][2]) $this->add1Acte($codeActe);
}

//Dans la BAL, insertion des actes pour le patient lors de sa sortie de la liste des présents
function writeBALSorti($cw="",$idEvent=""){
  
  global $session;
	global $options ;
	
  global $fusion;
	global $table_patient_manuel;
  global $table_patient_automatique;
	
  if (!$this->manuel or $idEvent or $options->getOption ( 'CCAMEnvoiManuel') ){
  if (!$idEvent){
    $idEvent=$this->idEvent;
    $agePatient=$this->agePatient;
    $fusion=0;
  }
  else{
    unset($paramRq);
    $paramRq["table"]="patients_sortis";
    $paramRq["cw"]="where idPatient=".$idEvent;
  	$req=new clResultQuery;
  	$res=$req->Execute("Fichier","getPatients",$paramRq,"ResultQuery");
  	//eko($res[INDIC_SVC]);
  	$dtFinInterv=$res["dt_sortie"][0];
  	$dtNaiss=$res["dt_naissance"][0];
  	$fusion=1;
  	$this->idEvent = $idEvent;
  }
  
  //Récupération de l'indicateur Lésion multiple
  $lesion=$this->lesion($idEvent);
 	$tabAsso=$this->tabAsso($idEvent,$lesion);
 	//Récupération des tarifs par lettre-clé NGAP et code CCAM
  $tarifNGAP=$this->tarifNGAP();
  $tarifCCAM=$this->tarifCCAM($idEvent);
  //eko($tarifCCAM);
  //eko($tabAsso);
  //Recherche des diagnostics fracture ouverte pour prépération modificateur L
  unset($paramRq);
	$paramRq["cw"]="type='DIAG' and idEvent=".$idEvent." $cw and idDomaine=".CCAM_IDDOMAINE;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
	//eko($res[INDIC_SVC]);
	$fractureOuverte=0;
	for ($i=0;isset($res["identifiant"][$i]);$i++){
	   if (ereg("(ouverte)",$res["libelleActe"][$i])) $fractureOuverte=1;
	   //eko("libDiag:".$res["libelleActe"][$i]);
	}    
  
  // Gestion de l'envoi des majorations NGAP suivant l'age des patients.
  $this->gestionMajorationsNGAPlesCetCS ( );
  
  // Gestion de l'envoi de l' acte A.T.U 
  $this->gestionEnvoiActeATU ( );
  
  // Gestion de l'envoi de l' acte M.T.U 
  //$this->gestionEnvoiActeMTU ( );
  
  // Gestion de l'envoi des majorations des actes infirmiers AMI et AIS suivant l'heure
  //$this->gestionMajorationsActesInfirmiers ( );
  
  // On va affectuer un contrôle des actes presents dans la table ccam_cotation_actes
  // pour que tous les actes/diags soient au meme nom que la personne qui prend en charge
  // le patient. Sauf dans le cas des consultations spécialisées
  if ( $fusion == 1 )
    $this->controleActesPresents ($idEvent);
  else
    $this->controleActesPresents ( );
  
  unset($paramRq);
 	$paramRq["cw"]="idEvent=".$idEvent." $cw and idDomaine=".CCAM_IDDOMAINE;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
	//eko($res[INDIC_SVC]);
  //Préparation de l'analyse de la séquence d'actes pour gérer les majorations de nuit et jours fériés
  unset($indicateurs);unset($maxLC);unset($maxCCAM);
  $maxTarifNGAP=$maxTarifCCAM=0;
  for ($i=0;isset($res["identifiant"][$i]);$i++){
    $code=$res["codeActe"][$i];
    if (!$fusion){
      $dateEvent=$this->dateEvent;
      $dtFinInterv=$this->dtFinInterv;
    }
    else{
      $dateEvent=$res["dateDemande"][$i];
      $age=new clDate($dtNaiss);
      $date=new clDate($dateEvent);
      $duree=new clDuree($date->getDifference($age));
      $agePatient=$duree->getYears();
    }
    
    unset($param);
    $clDateDeb=new clDate($dateEvent);
    $clDateFin=new clDate($dtFinInterv);
    
    if ( strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure médiane")==0 ) {
      $nbHeures=$clDateFin->getDifference($clDateDeb)/3600;
      $dateMediane=$clDateDeb->addHours($nbHeures/2);
      $dateMediane=$clDateDeb->getDate("Y-m-d H:i:s");
      $heureCalcule=$dateMediane;
    }
    elseif (strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure d'admission")==0) {
      $clDateAdm=new clDate ( $this->dtAdmission );
      $heureCalcule=$clDateAdm->getDate("Y-m-d H:i:s");
    }
    elseif (strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure d'examen")==0) {
      $clDateAdm=new clDate ( $this->dateEvent );
      $heureCalcule=$clDateAdm->getDate("Y-m-d H:i:s");
    }
    elseif (strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure de sorti")==0) {
      $clDateAdm=$clDateFin;
      $heureCalcule=$clDateAdm->getDate("Y-m-d H:i:s");
    }
    
    $param["dateDemande"]=$res["dateDemande"][$i]=$heureCalcule;
    $param["dtFinInterv"]=$dtFinInterv;
    
    //Gestion des modificateurs
    if ($res["type"][$i]=="ACTE"){
      list($dateActe,$heureActe)=explode(" ",$heureCalcule);
    	list($annee,$mois,$jour)=explode("-",$dateActe);
    	(substr($mois,0,1)==0)?$mois=substr($mois,1,1):"";
    	(substr($jour,0,1)==0)?$jour=substr($jour,1,1):"";
    	list($heure,$minute,$seconde)=explode(":",$heureActe);
    	(substr($heure,0,1)==0)?$heure=substr($heure,1,1):"";
    	$dateActe=mktime(0,0,0,$mois,$jour,$annee);
    	//eko("annee:$annee-mois:$mois-jour:$jour-heure:$heure-dateActe:$dateActe-dateEvent:".$dateEvent);
      if (substr($code,0,4)!="NGAP"){
    		//Gestion des modificateurs par défaut : A, F, P, S
    		//Récupération des modificateurs autorisés pour l'acte en cours
    		unset($paramRq);
    		/*$paramRq[cw]="and substring(rel.aa_code,1,7)='$code' and 

    			rel.modifi_cod in ('A','F','P','S','L')";*/
    		$paramRq["cw"]="and a.code='$code'";
    		$req=new clResultQuery;
    		$res2=$req->Execute("Fichier","CCAM_getModificateursActe",$paramRq,"ResultQuery");
    		//eko($res[INDIC_SVC]);
    		unset($modificateursOK);
    		for ($j=0;isset($res2["modifi_cod"][$j]);$j++){
    			$modif=$res2["modifi_cod"][$j];
    			$modificateursOK[$modif]=$modif;
    		}
    		//eko ($modificateursOK);
    		$modificateurs="";
    		//Gestion des tranches horaires
    		if (($heure>=20 or ($heure>=6 and $heure<8)) and $modificateursOK["P"]) $modificateurs="P";
    		elseif ($heure<6 and $modificateursOK["S"]) $modificateurs="S";
    		//elseif ($modificateursOK[F]) $modificateurs="F";
    		
        // MODIFICATION A FAIRE SI NECESSAIRE pour les sages - femmes et gyneco-obstetrique
        // ajout du modificateur U
        //eko($this->patient->getTypeMedecin()); 
        //elseif (($heure<9 or $heure>=20) and $modificateursOK[U]) $modificateurs="U";
        
    		
    		//Gestion des dimanches
    		if (date("w",$dateActe)==0 and $modificateurs=="" and $modificateursOK["F"]) $modificateurs="F";
    		
    		//Gestion des jours fériés
    		$dateFerie=new clDate($dateActe);
    		if ($dateFerie->isHoliday() and $modificateurs=="" and $modificateursOK["F"]) $modificateurs="F";
    		
    		//Gestion du modificateur fracture ouverte
    		$modifSansL=$modificateurs;
    		if ($fractureOuverte and $modificateursOK["L"]) $modificateurs.="~L";
    		//eko("fractureOuverte:$fractureOuverte-modificateursOK[L]:".$modificateursOK["L"]);
    		
    		//Si une activité 4 est associée
    		unset($paramRq);
        $paramRq["idActe"]=$code;
        $req4=new clResultQuery;
        $res4=$req4->Execute("Fichier","CCAM_getInfos1ActeCCAM",$paramRq,"ResultQuery");
        //newfct(gen_affiche_tableau,$res4[INDIC_SVC]);
        if ($res4["ACTIV_ANESTHESISTE"][0]=="O") $activ4="O"; else $activ4="";
    		if ($activ4=="O"){
    			$modificateurs.="|".$modifSansL;
    			if (($agePatient<4 or $agePatient>=80) and $modificateursOK["A"]) $modificateurs.="~A";
    		}
    	}
    	else{
        //Gestion des modificateurs de nuit et jours fériés
        //Gestion des tranches horaires
        if (($heure>=20 or ($heure>=6 and $heure<8))) $majoNGAP="N";
        elseif ($heure<6) $majoNGAP="NM";
        
        //Gestion des dimanches
        if (date("w",$dateActe)==0 and $majoNGAP=="") $majoNGAP="F";
        
        //Gestion des jours fériés
        $dateFerie=new clDate($dateActe);
        if ($dateFerie->isHoliday() and $majoNGAP=="") $majoNGAP="F";
        $modificateurs=$majoNGAP;
    	}
    	
    	// A.T.U Si presence de cette acte pas de modificateur : 
  //===========================================================================================================
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "libelleActe='ATU'";
  $codeNGAPATU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  if (strcmp($code,$codeNGAPATU["idActe"][0])==0) $modificateurs="";
  //===========================================================================================================
  
  // M.T.U Si presence de cette acte pas de modificateur : 
  //===========================================================================================================
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "libelleActe='MTU'";
  $codeNGAPMTU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  if (strcmp($code,$codeNGAPMTU["idActe"][0])==0) $modificateurs="";
  //===========================================================================================================
  //eko("2----------------------------------------------------".$code);
      
      //===========================================================================================================
      //if ($code=="GLQP005") $modificateurs="";// A supprimer quand décision sera prise pour cet acte
      //===========================================================================================================
      if ($modificateurs) $param["modificateurs"]=$res["modificateurs"][$i]=$modificateurs;
    }
    //Update de la table des actes
    $majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
	  $sql=$majrq->updRecord("codeActe='$code' and idEvent=$idEvent and idDomaine=".CCAM_IDDOMAINE);
    
    //Recherche des tarifs les plus chers pour la gestion des codes association
    if (substr($code,0,4)=="NGAP" or substr($code,0,4)=="CONS"){
  		$cotation=explode("+",$res["cotationNGAP"][$i]);
  		unset($indicateurs);
      while (list($key,$val)=each($cotation)){
  			list($lc,$coeff)=explode(" ",$val);
  			if (substr($lc,0,1)=="C") $indicateurs["C"]=1;
  			else{
          if ($tarifNGAP[$lc] and $tarifNGAP[$lc]>$maxTarifNGAP){
            $maxLC["lc"]=$lc;
            $maxLC["tarif"]=$tarifNGAP[$lc];
            $maxTarifNGAP=$maxLC["tarif"];
          }
  			}
  		}
    }
    else{
      if ($tarifCCAM[$code] and 
          (ereg("F",$res["modificateurs"][$i]) 
          or ereg("P",$res["modificateurs"][$i]) 
          or ereg("S",$res["modificateurs"][$i]))){
        $indicateurs["CCAM"]=1;
        if ($tarifCCAM[$code]>$maxTarifCCAM){
          $maxCCAM["code"]=$code;
          $maxCCAM["tarif"]=$tarifCCAM[$code];
          $maxTarifCCAM=$maxCCAM["tarif"];
        }
      }
      //if (substr($res[codeActe][$i],0,3)=="DEQ") $indicateurs["ECG"]=1;
    }
  }
  
  $idEvent       = $this->idEvent;
  $agePatient    = $this->agePatient;
  unset($paramRq);
  $paramRq["cw"] = "idEvent=".$idEvent." and (type='ACTE' or type='DIAG')";
  $requete       = new clResultQuery;
   
  // On récupère la liste des actes pour un patient
  $resultat      = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  //eko ($resultat);
  
  // Gestion de l'envoi des actes NGAP à la facturation
  $this->envoiActesNGAPdansMBTV2 ( );
  
  // Acte CCAM avec Code 4 envoi si nom urgentiste = nom praticien : non pour Ch-Hyères
  if ( $options->getOption ( 'EnvoiActeCCAMCode4') == 0 )
    $this->EnvoiActeCCAMCode4 ( );

  // Détermination en fonction du meilleur tarif de l'envoi des NGAP ou CCAM
  if ( $options->getOption ( 'ActiverComparaisonTarifCCSouCCAM') )
    $this->envoiNGAPouCCAMdansMBTV2 ( );
  
  // Envoi des diagnostics à la facturation
  $this->envoiDiagnosticsdansMBTV2 ( );
  
  //Récupération de l'indicateur Lésion multiple
  $lesion=$this->lesion($idEvent);
 	$tabAsso=$this->tabAsso($idEvent,$lesion);
 	//Récupération des tarifs par lettre-clé NGAP et code CCAM
  $tarifNGAP=$this->tarifNGAP();
  $tarifCCAM=$this->tarifCCAM($idEvent);
  
  
  $idEvent       = $this->idEvent;
  $agePatient    = $this->agePatient;
  unset($paramRq);
  $paramRq["cw"] = "idEvent=".$idEvent." and (type='ACTE' or type='DIAG') and envoi_facturation=1";
  $requete       = new clResultQuery;
   
  // On récupère la liste des actes pour un patient
  $res      = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");

  
  $type="creation";
	
  /*if ( strcmp($session->getNom( ),"BOREL") == 0  || strcmp($session->getNom( ),"DEROCK") == 0 )
    //eko("NO WRITE IN MBTV2");
    $this->contenuBAL($res,$type,$tabAsso,$indicateurs,$maxLC,$maxCCAM);
  else*/
    $this->contenuBAL($res,$type,$tabAsso,$indicateurs,$maxLC,$maxCCAM);
}
}


// actes avec un code activité 4 
// Auteur Derock François
// fderock@ch-hyeres.fr
/******************************************************************************/
function EnvoiActeCCAMCode4 ( ) {
/******************************************************************************/
  
  /* Concernant les actes avec un code activité 4 : CH-HYERERS
     Les codes CCAM liés à une activité 4 (anesthésie obligatoire) sont rejetés
     en facturation si le praticien est le même que celui qui a réalisé l'acte 
     comme noté dans la doc TU Facturation V1.04
     Il faudrait tester si l'exécutant est le même sur les deux activités de
     cet acte et bloquer l'envoi de l'acte dans ce cas. (vu avec Gilles et Christophe)*/
  
  $idEvent       = $this->idEvent;
  
  // on prend les actes avec la présence d'une activité 4
  unset($paramRq);
  unset($paramModif);
  $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActivite4 ='O'";
  $req=new clResultQuery;
  $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  if ( $res[INDIC_SVC][2] >=1 ) {
    for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {

      $nom        = explode("|",$res["nomIntervenant"][$i]);
      if ( strcmp ( $nom[0],$nom[1] ) == 0 ) {
        eko("ici");
        $paramModif["envoi_facturation"] = 0;
        $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
        $sql        = $requete->updRecord("idEvent=".$idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
        }
    }
  }
  
  $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActivite4 ='O'";


  $req=new clResultQuery;
  $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  //eko($res);

}

// end function controleActesPresents
/******************************************************************************/ 


// Controle des actes 
// Auteur Derock François
// fderock@ch-hyeres.fr
/******************************************************************************/
function controleActesPresents ( $valeur='' ) {
/******************************************************************************/
  
  
  // Version 2 START
  
  global $fusion;
  global $table_patient_manuel;
  global $table_patient_automatique;
  
  if ( $fusion == 1 ) {
  // presence d'une fusion
    $idEvent  = $valeur;
    $thetable = $table_patient_automatique;
  }
  else {
  // non presence d'une fusion
    $idEvent  = $this->idEvent;
    $thetable = PSORTIS;
  }
  
  // On prend le nom du medecin du patient
  $param[cw] = "WHERE idpatient='".$idEvent."'" ;
  $param[table] = $thetable ;
  $req = new clResultQuery ;
  $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;

   
  // On prend le numero ADELI
  unset($param);
  $param[nomitem] = $res["medecin_urgences"][0] ;
  $req = new clResultQuery ;
  $res2 = $req -> Execute ( "Fichier", "getMatriculeMedecin", $param, "ResultQuery" ) ;
  
  // Mise à jour de la table ccam_cotation_actes en affectant le nom du medecin
  // à tous les actes NGAP, consultation, actes CCAM et diagnostics
  // sauf les consultations spécialisées

  $Medecin_urgences = $res["medecin_urgences"][0];
  $Matricule        = $res2["matricule"][0];
  
  $paramModif["envoi_nomIntervenant"]       = $Medecin_urgences;
  $paramModif["envoi_matriculeIntervenant"] = $Matricule;
  $requete = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	$sql     = $requete->updRecord("idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActe not like 'CONS%' and codeActivite4 != 'O'");
	
	
 
	// dans le cas d'une activité 4
	// on prend les actes avec la présence d'une activité 4
	unset($paramRq);
  $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActivite4 ='O'";
  $req=new clResultQuery;
  $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  if ( $res[INDIC_SVC][2] >=1 ) {
    for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
      $matricule  = explode("|",$res["matriculeIntervenant"][$i]);
      $nom        = explode("|",$res["nomIntervenant"][$i]);
      $paramModif["envoi_nomIntervenant"]       = $Medecin_urgences."|".$nom[1];
      $paramModif["envoi_matriculeIntervenant"] = $Matricule."|".$matricule[1];
      $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	    $sql        = $requete->updRecord("idEvent=".$idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
    }
  }
  
  // Mise à jour de la table pour les autres actes les CONS 
  unset($paramRq);
  $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActe like 'CONS%'";
  $req=new clResultQuery;
  $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
    $paramModif["envoi_nomIntervenant"]       = $res["nomIntervenant"][$i];
    $paramModif["envoi_matriculeIntervenant"] = $res["matriculeIntervenant"][$i];
    $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	  $sql        = $requete->updRecord("idEvent=".$idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
  }
  // Version 2 END
  
  // Version 1 START
  /*
  // Mise à jour de la table ccam_cotation_actes en affectant le nom du medecin
  // à tous les actes NGAP, consultation, actes CCAM et diagnostics
  // sauf les consultations spécialisées
  if ( $medecin[INDIC_SVC][2] >=1 ) {
    $paramModif["envoi_nomIntervenant"]       = $medecin["nomIntervenant"][0];
    $paramModif["envoi_matriculeIntervenant"] = $medecin["matriculeIntervenant"][0];
    $requete = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	  $sql     = $requete->updRecord("idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActe not like 'CONS%' and codeActivite4 != 'O'");
	  
	  // dans le cas d'une activité 4
	  
	  // on prend les actes avec la présence d'une activité 4
	  unset($paramRq);
    $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActivite4 ='O'";
    $req=new clResultQuery;
    $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
    if ( $res[INDIC_SVC][2] >=1 ) {
      for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
        $matricule  = explode("|",$res["matriculeIntervenant"][$i]);
        $nom        = explode("|",$res["nomIntervenant"][$i]);
        $paramModif["envoi_nomIntervenant"]       = $medecin["nomIntervenant"][0]."|".$nom[1];
        $paramModif["envoi_matriculeIntervenant"] = $medecin["matriculeIntervenant"][0]."|".$matricule[1];
        $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	      $sql        = $requete->updRecord("idEvent=".$idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
      }
    }
  }
  
  else {
    
    $requete       = new clResultQuery;
    unset($paramRq);
    $paramRq["cw"] = "idEvent=".$idEvent;
    $medecin       = $requete->Execute("Fichier","CCAM_getMedecinCCAM",$paramRq,"ResultQuery");
    //eko($medecin);
    //eko(substr($medecin["matriculeIntervenant"][0],9,1));
    
    if ( substr($medecin["matriculeIntervenant"][0],9,1) == "|" ) {
      $matricule  = explode("|",$medecin["matriculeIntervenant"][0]);
      $nom        = explode("|",$medecin["nomIntervenant"][0]);
      $paramModif["envoi_nomIntervenant"]       = $nom[0];
      $paramModif["envoi_matriculeIntervenant"] = $matricule[0];
     } 
     else {
      $paramModif["envoi_nomIntervenant"]       = $medecin["nomIntervenant"][0];
      $paramModif["envoi_matriculeIntervenant"] = $medecin["matriculeIntervenant"][0];
     } 
    
    $nom_temp       = $paramModif["envoi_nomIntervenant"];
    $matricule_temp = $paramModif["envoi_matriculeIntervenant"];
    
    //eko($nom_temp);
    //eko($matricule_temp);
    
    $requete = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	  $sql     = $requete->updRecord("idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActe not like 'CONS%' and codeActivite4 != 'O'");
	  
	  // dans le cas d'une activité 4
	  
	  // on prend les actes avec la présence d'une activité 4
	  unset($paramRq);
    $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActivite4 ='O'";
    $req=new clResultQuery;
    $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
    if ( $res[INDIC_SVC][2] >=1 ) {
      for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
        $matricule  = explode("|",$res["matriculeIntervenant"][$i]);
        $nom        = explode("|",$res["nomIntervenant"][$i]);
        $paramModif["envoi_nomIntervenant"]       = $nom_temp."|".$nom[1];
        $paramModif["envoi_matriculeIntervenant"] = $matricule_temp."|".$matricule[1];
        $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	      $sql        = $requete->updRecord("idEvent=".$idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
      }
    }
  
  }
  
  // Mise à jour de la table pour les autres actes les CONS 
  unset($paramRq);
  $paramRq["cw"]="idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE." and codeActe like 'CONS%'";
  $req=new clResultQuery;
  $res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  for ( $i = 0 ; $i < $res[INDIC_SVC][2] ; $i++ ) {
    $paramModif["envoi_nomIntervenant"]       = $res["nomIntervenant"][$i];
    $paramModif["envoi_matriculeIntervenant"] = $res["matriculeIntervenant"][$i];
    $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	  $sql        = $requete->updRecord("idEvent=".$idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
  }
  */
  // Version 1 END
  
    
}

// end function controleActesPresents
/******************************************************************************/ 

// Gestion de l'envoi des M.T.U 
// Auteur Derock François
// fderock@ch-hyeres.fr
/******************************************************************************/
function gestionEnvoiActeMTU ( ) {
/******************************************************************************/
  
  $idEvent       = $this->idEvent;
  
  // on récupere le code NGAP de l' M.T.U
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "libelleActe='MTU'";
  $codeNGAPMTU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  
  // Suppression de l'enregistrement MTU dans la table des cotations
  $requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
  $sql=$requete->delRecord("codeActe='".$codeNGAPMTU["idActe"][0]."' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE);

  unset($paramRq);
  $paramRq["cw"] = "idEvent=".$idEvent." and ( cotationNGAP like 'CS 1%' or cotationNGAP like 'C 1%' or cotationNGAP like 'CSC 1%' or cotationNGAP like 'CNPSY 1%')";
  $requete       = new clResultQuery;
  // On récupère la liste des consultations pour un patient
  $resultat      = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");  
  
  // Si presence d'une consultation
  if ( $resultat[INDIC_SVC][2] != 0 ) {
  
    // on recupere la valeur booleenne de "envoi_facturation" pour l'M.T.U
    // dans la table ccam_actes_domaine
    unset($paramRq);
    $paramRq[cw]     =  "and idDomaine=".CCAM_IDDOMAINE;
    $paramRq[idActe] =  $codeNGAPMTU["idActe"][0];
    $envoi           =  $requete->Execute("Fichier","CCAM_get1ActeAllData",$paramRq,"ResultQuery");

    
    $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");

      
    // On prepare la nouvelle ligne à inserer dans la table ccam_actes_domaine
    unset($paramRq);
    $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
    $paramRq["idEvent"]             =   $idEvent;
    $paramRq["dateEvent"]           =   date("Y-m-d H:i:")."00";
    $paramRq["idDomaine"]           =   CCAM_IDDOMAINE;
    $paramRq["dtFinInterv"]         =   $this->dtFinInterv;
    $paramRq["idu"]                 =   $this->idu;
    $paramRq["ipp"]                 =   $this->ipp;
    $paramRq["nomu"]                =   $this->nomu;
		$paramRq["pren"]                =   $this->pren;
		$paramRq["sexe"]                =   $this->sexe;
		$paramRq["dtnai"]               =   $this->dtnai;
    $paramRq["dateDemande"]         =   $this->dateEvent;
    $paramRq["typeAdm"]             =   $this->typeAdm;
		$paramRq["lieuInterv"]          =   $this->lieuInterv;	
		$paramRq["numUFdem"]            =   $this->numUFdem;	
		$paramRq["numSejour"]           =   $this->nsej;
    $paramRq["type"]                =   "ACTE";
    $paramRq["Urgence"]             =   "O"; 
    $paramRq["matriculeIntervenant"]=   $this->matriculeIntervenant; 
    $paramRq["nomIntervenant"]      =   $this->nomIntervenant;
    $paramRq["numUFexec"]           =   $this->numUFexec;
    $paramRq["codeActe"]            =   $codeNGAPMTU["idActe"][0];
    $paramRq["libelleActe"]         =   "MTU";
    $paramRq["cotationNGAP"]        =	  "MTU 1";
    $paramRq["codeActivite4"]       =   "";
    $paramRq["modificateurs"]       =   "";
    $paramRq["categorie"]           =   "";
    $paramRq["extensionDoc"]        =   "";
    $paramRq["validDefinitive"]     =   "";
    $paramRq["quantite"]            =   "1";
    $paramRq["periodicite"]         =   "aucune";
    $paramRq["lesionMultiple"]      =   "Non";
    
    if ( $envoi["envoi_facturation"][0] == 0 )
      $paramRq["envoi_facturation"]   =   0;
    else
      $paramRq["envoi_facturation"]   =   1;
    
    // Insertion de la nouvelle ligne
    $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
    $requete -> addRecord();
      
  } //if ( $resultat[INDIC_SVC][2] != 0 ) {
  
}
// end function gestionEnvoiActeMTU
/******************************************************************************/ 

// Gestion de l'envoi des A.T.U 
// Auteur Derock François
// fderock@ch-hyeres.fr
// DBDEB4 : Ajout de la vérification de getValide pour envoyer ou non l'ATU + UFUHCDrepere
// DBFIN4
/******************************************************************************/
function gestionEnvoiActeATU ( ) {
/******************************************************************************/
  
  global $patient;
  global $options;
  global $fusion;
  global $table_patient_manuel;
  global $table_patient_automatique;
  
  $idEvent       = $this->idEvent;
  
  //eko($patient->getTypeDestination()); // savoir sil est en hospitalisation
  //eko($patient->isUHCD ( ));           // savoir s'il est en UHCD
  
  // on récupere le code NGAP de l' A.T.U
  $requete       = new clResultQuery;
  unset($paramRq);
  $paramRq["cw"] = "libelleActe='ATU'";
  $codeNGAPATU   = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");
  
  // on recupere la valeur booleenne de "envoi_facturation" pour l'A.T.U
  // dans la table ccam_actes_domaine
  unset($paramRq);
  $paramRq[cw]     =  "and idDomaine=".CCAM_IDDOMAINE;
  $paramRq[idActe] =  $codeNGAPATU["idActe"][0];
  $envoi           =  $requete->Execute("Fichier","CCAM_get1ActeAllData",$paramRq,"ResultQuery");
  
  // Presence d'une fusion
  if ( $fusion == 1 ) {
    // On recupere le type de destination
    $param[cw] = "WHERE idpatient='".$idEvent."'" ;
    $param[table] = $table_patient_automatique ;
    $req = new clResultQuery ;
    $res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    
    unset($param);
    $param[nomitem] = $res["medecin_urgences"][0] ;
    $req = new clResultQuery ;
    $res2 = $req -> Execute ( "Fichier", "getMatriculeMedecin", $param, "ResultQuery" ) ;
  }
       
  
  if ( $options->getOption('EnvoiATURegleFacturation') ) {
    
    // On envoi l'ATU suivant le parametre dans liste restreinte 
    // et on suit la régle de facturation
    
    // Présence d'une fusion
    if ( $fusion == 1 ) {
      // MODIFICATION A FAIRE : regles de facturation de l'atu
      
      // On recupere le type de destination
      $info_TypeDestination = $res["type_destination"][0];
      // On recupere l'information de passage en UHCD
      if ( $res["uf"][0] == $options->getOption ( 'numUFUHCD' ) OR $res["uf"][0] == $options->getOption ( 'numUFUHCDrepere' ) )
        $info_ifInUHCD = 1;
      else
        $info_ifInUHCD = 0;
      if  ( $info_TypeDestination == "H" || $info_ifInUHCD == 1  || ! $res['valide'][0] )
        $test = 1;
      else
        $test = 0;
    }
      
    else
      // MODIFICATION A FAIRE : regle de facturation de l'atu
      if ( $patient->getTypeDestination( ) == "H" || $patient->ifInUHCD ( ) == 1 || ! $patient->getValide ( ) )
        $test = 1;
      else
        $test = 0;
	
    if ( $test ) {
      unset($paramModif);
      $paramModif["envoi_facturation"]   = 0;
      $requete                           = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
      $sql                               = $requete->updRecord("idEvent='".$idEvent."' and  cotationNGAP='ATU 1'");
      }
    else {
      // Mise à jour de la ligne A.T.U dans la table ccam_cotation_actes
      // cas ou le patient revient.
      unset($paramModif);
      $paramModif["envoi_facturation"] = $envoi["envoi_facturation"][0];
      $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);

      $sql                             = $requete->updRecord("idEvent='".$idEvent."' and  cotationNGAP='ATU 1'");
  
      // Insertion dans la table ccam_cotation_actes de l'acte NGAP
      // On regarde si l'A.T.U est present 
      // ou pas dans la table ccam_cotation_actes
      unset($paramRq);
      $paramRq["cw"]      = "idEvent=".$idEvent." and type='ACTE' and  cotationNGAP='ATU 1'";
      // On récupère la ligne qui contient l'acte A.T.U
      $requete            = new clResultQuery;
      $codeNGAPATUexiste  = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");

  
      if ( $codeNGAPATUexiste[INDIC_SVC][2] == 0 ) {
        // l'A.T.U existe et est present dans la table. (On a annulé la sortie du patient)
        // sinon il faut faire une insertion
        $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
    
    
        if ( $fusion == 1 ) {
        // On prepare la nouvelle ligne à inserer dans la table ccam_actes_domaine
        unset($paramRq);
        $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
        $paramRq["idEvent"]             =   $idEvent;
        $paramRq["dateEvent"]           =   date("Y-m-d H:i:")."00";
        $paramRq["idDomaine"]           =   CCAM_IDDOMAINE;
        $paramRq["dtFinInterv"]         =   $this->dtFinInterv;
        $paramRq["idu"]                 =   $res["idu"][0];
        $paramRq["ipp"]                 =   $res["ilp"][0];
        $paramRq["nomu"]                =   $res["nom"][0];
		    $paramRq["pren"]                =   $res["prenom"][0];
		    $paramRq["sexe"]                =   $res["sexe"][0];
		    $temp = explode (" ",$res["dt_naissance"][0]);
        $paramRq["dtnai"]               =   $temp[0];
        $paramRq["dateDemande"]         =   $res["dt_examen"][0];
        $paramRq["typeAdm"]             =   $res["type_destination"][0];
		    $paramRq["lieuInterv"]          =   $res["salle_examen"][0];
		    $paramRq["numUFdem"]            =   $res["uf"][0];
		    $paramRq["numSejour"]           =   $res["nsej"][0];
        $paramRq["type"]                =   "ACTE";
        $paramRq["Urgence"]             =   "O"; 
        
        // Recherche du code adeli du médecin
        $paramRq["matriculeIntervenant"]=   $res2["matricule"][0];
        $paramRq["nomIntervenant"]      =   $res["medecin_urgences"][0]; 	
        $paramRq["numUFexec"]           =   $res["uf"][0];
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
        }
        else {
        
        // On prepare la nouvelle ligne à inserer dans la table ccam_actes_domaine
        unset($paramRq);
        $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
        $paramRq["idEvent"]             =   $idEvent;
        $paramRq["dateEvent"]           =   date("Y-m-d H:i:")."00";
        $paramRq["idDomaine"]           =   CCAM_IDDOMAINE;
        $paramRq["dtFinInterv"]         =   $this->dtFinInterv;
        $paramRq["idu"]                 =   $this->idu;
        $paramRq["ipp"]                 =   $this->ipp;
        $paramRq["nomu"]                =   $this->nomu;
		    $paramRq["pren"]                =   $this->pren;
		    $paramRq["sexe"]                =   $this->sexe;
		    $paramRq["dtnai"]               =   $this->dtnai;
        $paramRq["dateDemande"]         =   $this->dateEvent;
        $paramRq["typeAdm"]             =   $this->typeAdm;
		    $paramRq["lieuInterv"]          =   $this->lieuInterv;	
		    $paramRq["numUFdem"]            =   $this->numUFdem;	
		    $paramRq["numSejour"]           =   $this->nsej;
        $paramRq["type"]                =   "ACTE";
        $paramRq["Urgence"]             =   "O"; 
        $paramRq["matriculeIntervenant"]=   $this->matriculeIntervenant; 
        $paramRq["nomIntervenant"]      =   $this->nomIntervenant;
        $paramRq["numUFexec"]           =   $this->numUFexec;
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
        }
        
    
        if ( $envoi["envoi_facturation"][0] == 0 )
          $paramRq["envoi_facturation"]   =   0;
        else
          $paramRq["envoi_facturation"]   =   1;

        // Insertion de la nouvelle ligne
        $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
        $requete -> addRecord();
      } // if ( $codeNGAPATUexiste[INDIC_SVC][2] == 0 )
    }
  } //if ( $options->getOption('EnvoiATURegleFacturation') )
  
  // On envoi l'ATU suivant le parametre dans liste restreinte
  // et on ne suit pas la régle de facturation
  else {
  
    // Mise à jour de la ligne A.T.U dans la table ccam_cotation_actes
    // cas ou le patient revient.
    unset($paramModif);
    $paramModif["envoi_facturation"] =  ((is_object($patient) AND ! $patient->getValide ( ))?0:$envoi["envoi_facturation"][0]);
    $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	  $sql                             = $requete->updRecord("idEvent='".$idEvent."' and  cotationNGAP='ATU 1'");
  	//eko ( $sql ) ;
  
    // Insertion dans la table ccam_cotation_actes de l'acte NGAP
    // On regarde si l'A.T.U est present 
    // ou pas dans la table ccam_cotation_actes
    unset($paramRq);
    $paramRq["cw"]      = "idEvent=".$idEvent." and type='ACTE' and  cotationNGAP='ATU 1'";
    // On récupère la ligne qui contient l'acte A.T.U
    $requete            = new clResultQuery;
    $codeNGAPATUexiste  = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  
    if ( $codeNGAPATUexiste[INDIC_SVC][2] == 0 ) {
      // l'A.T.U existe et est present dans la table. (On a annulé la sortie du patient)
      // sinon il faut faire une insertion
      $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
    
      // On prepare la nouvelle ligne à inserer dans la table ccam_actes_domaine
      unset($paramRq);
      $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
      $paramRq["idEvent"]             =   $idEvent;
      $paramRq["dateEvent"]           =   date("Y-m-d H:i:")."00";
      $paramRq["idDomaine"]           =   CCAM_IDDOMAINE;
      $paramRq["dtFinInterv"]         =   $this->dtFinInterv;
      $paramRq["idu"]                 =   $this->idu;
      $paramRq["ipp"]                 =   $this->ipp;
      $paramRq["nomu"]                =   $this->nomu;
		  $paramRq["pren"]                =   $this->pren;
		  $paramRq["sexe"]                =   $this->sexe;
		  $paramRq["dtnai"]               =   $this->dtnai;
      $paramRq["dateDemande"]         =   $this->dateEvent;
      $paramRq["typeAdm"]             =   $this->typeAdm;
		  $paramRq["lieuInterv"]          =   $this->lieuInterv;	
		  $paramRq["numUFdem"]            =   $this->numUFdem;	
		  $paramRq["numSejour"]           =   $this->nsej;
      $paramRq["type"]                =   "ACTE";
      $paramRq["Urgence"]             =   "O"; 
      $paramRq["matriculeIntervenant"]=   $this->matriculeIntervenant; 
      $paramRq["nomIntervenant"]      =   $this->nomIntervenant;
      $paramRq["numUFexec"]           =   $this->numUFexec;
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
    
      if ( $envoi["envoi_facturation"][0] == 0  || (is_object($patient) AND ! $patient->getValide ( )) )
        $paramRq["envoi_facturation"]   =   0;
      else
        $paramRq["envoi_facturation"]   =   1;

      // Insertion de la nouvelle ligne
      $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
      $sql = $requete -> addRecord();
      //eko ( $sql ) ;
    } // if ( $codeNGAPATUexiste[INDIC_SVC][2] == 0 )
  
  
  }
  
}
// end function gestionEnvoiActeATU
/******************************************************************************/ 

// Fonction  :  Affectation des Majo pour les actes infirmiers
// Paramètre :  Aucun paramètre. Mise à jour de la table ccam_cotation_actes
/******************************************************************************/ 
function gestionMajorationsActesInfirmiers ( ) {
/******************************************************************************/
    
    global $options;
    
    $idEvent     = $this->idEvent;
    $dateEvent   = $this->dateEvent;
    $dtFinInterv = $this->dtFinInterv;
    
    $clDateDeb   = new clDate($dateEvent);
    $clDateFin   = new clDate($dtFinInterv);
    
    if ( strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure médiane")==0 ) {
      $nbHeures     = $clDateFin->getDifference($clDateDeb)/3600;
      $dateMediane  = $clDateDeb->addHours($nbHeures/2);
      $dateMediane  = $clDateDeb->getDate("Y-m-d H:i:s");
      $heureCalcule = $dateMediane;
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
    
    
    list($dateActe,$heureActe)=explode(" ",$heureCalcule);
    list($annee,$mois,$jour)=explode("-",$dateActe);
    (substr($mois,0,1)==0)?$mois=substr($mois,1,1):"";
    (substr($jour,0,1)==0)?$jour=substr($jour,1,1):"";
    list($heure,$minute,$seconde)=explode(":",$heureActe);
    (substr($heure,0,1)==0)?$heure=substr($heure,1,1):"";
    $dateActe=mktime(0,0,0,$mois,$jour,$annee);
    
    
    if ( $options->getOption ('EnvoiMajorationsActesInfirmiers'))
                              //EnvoiMajorationsActesInfirmiers
      // Dans le cas ou le patient revient sur le terminal on gére le mise à
      // jour de la table ccam_cotation_actes en fonction de l' option
      // de la valeur de EnvoiMajorationsActesInfirmiers
      $paramModif["envoi_facturation"] = 1;
    else
      $paramModif["envoi_facturation"] = 0;
    
    $requete       = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		//LISTE MAJO
    $sql           = $requete->updRecord("idEvent='".$idEvent."' and  cotationNGAP in ('INFN1 1','INFN2 1','MINFD 1')");
		

    unset($paramRq);
    $paramRq["cw"] = "idEvent=".$idEvent." and type='ACTE'";
    $requete       = new clResultQuery;
    
    // On récupère la liste des actes pour un patient
    $resultat      = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
    
    // On traite les informations contenues dans la table resultat
    for ( $i = 0 ; $i < $resultat[INDIC_SVC][2] ; $i++ ) { 
      $MajorationNGAP1         = "";
      $MajorationNGAP2         = "";
      $Majoration_A_Effectuee  = "Non";
      
      // On regarde si la cotation NGAP est de type AMI ou AIS
      if ( eregi ("AMI",$resultat["cotationNGAP"][$i]) || eregi ("AIS",$resultat["cotationNGAP"][$i]) ) {
        
        // On regarde le code de l'acte et on regarde si dans la liste restreinte 
        // on a demandé ou pas d'envoyer l'acte NGAP à la facturation.
        $requete       = new clResultQuery;
        unset($paramRq);
        $paramRq["cw"] = "idActe='".$resultat["codeActe"][$i]."'";
        $codeNGAP      = $requete->Execute("Fichier","CCAM_getActesDomaine3",$paramRq,"ResultQuery");

        if ( $codeNGAP["envoi_facturation"][0] == 0 )
          $paramModif["envoi_facturation"] = 0;
        
        // Mise à jour de la table ccam_cotation_actes pour les majorations correspondants à l'acte NGAP
        $requete       = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
        $sql           = $requete->updRecord("idEvent='".$idEvent."' and  codeActe='".$resultat["codeActe"][$i]."'");
        
        // On gére les heures
        if ( ( $heure >=20 && $heure < 23 ) || ( $heure >= 5 && $heure <8 ) ){
          $MajorationNGAP1        = "INFN1 1";
          $Majoration_A_Effectuee = "Oui";
          }
        if ( $heure >=23 || $heure < 5 ) {
          $MajorationNGAP1        = "INFN2 1";
          $Majoration_A_Effectuee = "Oui";
          }

        // pour les jours féries
        $dateFerie=new clDate($dateActe);

        if ( $dateFerie->isHoliday() && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1        = "MINFD 1";
          $Majoration_A_Effectuee = "Oui";
          }
          
        // pour les dimanches
        if ( date("w",$dateActe)==0 && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1        = "MINFD 1";
          $Majoration_A_Effectuee = "Oui";
          }
        
        // pour les samedis
        if ( date("w",$dateActe)==6 && $heure>=8 && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1        = "MINFD 1";
          $Majoration_A_Effectuee = "Oui";
          }
      }
      

      // Maintenant on gère le cas s'il y a une Majoration à effectuer
      // pour l'acte $resultat["libelleActe"][$i]
      if ( strcmp($Majoration_A_Effectuee,"Oui") == 0 ) {
        
        $paramRq = array ( );
        $temp = array ( );
        $temp = $resultat;
        
        while ( list($key,$val )= each ( $temp ) ){
          $paramRq[$key] = $temp[$key][$i];
          }
        unset($paramRq[INDIC_SVC]);
        reset ($temp);
        reset ($paramRq);
        // On insere la majoration NGAP dans la nouvelle ligne à inserer dans
        // la table ccam_cotation_actes
        $paramRq["cotationNGAP"]          =   $MajorationNGAP1;
        $paramRq["envoi_facturation"]     =   $paramModif["envoi_facturation"];
        
        // On teste si on a déjà inserer l'acte NGAP avec la mojoration
        // Au cas ou le patient revient dans le terminal

        $requete               = new clResultQuery;
        $test_insertion_existe = $requete->Execute("Fichier","CCAM_testInsertionCCAMCotationActes",$paramRq,"ResultQuery");

         
        if ( $test_insertion_existe["INDIC_SVC"][2] == 0 ) {
          // On recherche le max de identifiant pour l'incrementation
          // dans la table
          $requete        = new clResultQuery;
          $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
          $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
          // Insertion de la nouvelle ligne
          $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
          $requete -> addRecord();
        }
          
        if ( $MajorationNGAP2 != "" ) {
          // On insere la majoration NGAP dans la nouvelle ligne à inserer dans
          // la table ccam_cotation_actes
          $paramRq["cotationNGAP"]         =   $MajorationNGAP2;
          // On teste si on a déjà inserer l'acte NGAP avec la majoration
          // Au cas ou le patient revient dans le terminal 
          $requete               = new clResultQuery;
          $test_insertion_existe = $requete->Execute("Fichier","CCAM_testInsertionCCAMCotationActes",$paramRq,"ResultQuery");
            
          if ( $test_insertion_existe["INDIC_SVC"][2] == 0 ) {
            // recherche de l'idEvent max
            $requete        = new clResultQuery;
            $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
            $paramRq["identifiant"]        =   $maxIdentifiant["maximun"][0]+1;
            // Insertion de la nouvelle ligne
            $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
            $requete -> addRecord();
          } // end if
        } // en if
      } // if ( strcmp($Majoration_A_Effectuee,"Oui") == 0 )
    } // for ( $i = 0 ; $i <= $resultat[INDIC_SVC][2] ; $i++ )
}
// end function gestionMajorationsActesInfirmiers 
/******************************************************************************/

// Fonction  :  Affectation des Majo NGAP pour une C et CS suivant l'age du patient
// Paramètre :  Aucun paramètre. Mise à jour de la table ccam_cotation_actes
// Autres    :  Maintenant on gére le cas des urgences pédiatriques pour les
//              majorations des consultations urgentistes spécialistes
//              de pédiatre ( présence de terminal urgentiste ou terminal urgentiste
//              pédiatrique )
/******************************************************************************/ 
function gestionMajorationsNGAPlesCetCS ( ) {
/******************************************************************************/
    
    global $options;
    global $fusion;
    global $table_patient_manuel;
    global $table_patient_automatique;
    
    
    $idEvent    = $this->idEvent;
    
    if ( $fusion == 0 )
      $agePatient = $this->agePatient;
    else {
      // Presence d'une fusion
      $param[cw]   = "WHERE idpatient='".$idEvent."'" ;
      $param[table]= $table_patient_automatique ;
      $req         = new clResultQuery ;
      $res         = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      
      $age         = new clDate($res["dt_naissance"][0]);
      $date        = new clDate($res["dt_examen"][0]);
      $duree       = new clDuree($date->getDifference($age));
      $agePatient  = $duree->getYears();
    }
    
    unset($paramModif);
    
    if ( $options->getOption ('EnvoiMajorationsNGAPpourCetCS') )

      // Dans le cas ou le patient revient sur le terminal on gére le mise à
      // jour de la table ccam_cotation_actes en fonction de l' option
      // de la valeur de EnvoiMajorationsNGAPpourCetCS
      $paramModif["envoi_facturation"] = 1;
    else
      $paramModif["envoi_facturation"] = 0;
    
    $requete       = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		
    // LISTE MAJO
		// MODIFICATION A FAIRE 
    // Ajout des majos sages-femmes et gyneco-obstetrique  dans les requetes
    $sql           = $requete->updRecord("idEvent='".$idEvent."' and  cotationNGAP in ('MCG 1','MNO 1','MGE 1','MCC 1','MNP 1','MPJ 1','MPC 1','MCS 1')");
		
    unset($paramRq);
    $paramRq["cw"] = "idEvent=".$idEvent." and type='ACTE'";
    $requete       = new clResultQuery;
    // On récupère la liste des actes pour un patient
    $resultat      = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");

    
    // On traite les informations contenues dans la table resultat
    for ( $i = 0 ; $i <= $resultat[INDIC_SVC][2] ; $i++ ) {

      $MajorationNGAP1         = "";
      $MajorationNGAP2         = "";
      $Majoration_A_Effectuee  = "Non";
      // On regarde si la cotation NGAP est de type C 1
      // ceci concerne les consultations de généralistes (C)
      if ( strcmp($resultat["cotationNGAP"][$i],"C 1") == 0 ) {

        // On gére les ages 
        if ( $agePatient < 2 ) {
          $MajorationNGAP1        = "MNO 1";
          $Majoration_A_Effectuee = "Oui";
          }
        /*
        if ( $agePatient >= 2 && $agePatient < 6 ) {
          $MajorationNGAP1        = "MGE 1";
          $Majoration_A_Effectuee = "Oui";
          }
        */
        
        /*if ( $agePatient >= 16 ) {
          $MajorationNGAP1        = "MCG 1";
          $Majoration_A_Effectuee = "Oui";
          }*/
          
      } //if ( strcmp($resultat["cotationNGAP"][$i],"C 1") == 0 )
      
      // On regarde si la cotation NGAP est de type CSC 1
      // ceci concerne les consultations de cardio
      /*
      if ( strcmp($resultat["cotationNGAP"][$i],"CSC 1") == 0 ) {
        // Cardiologues:
        if ( eregi ('Cardiologie',$resultat["libelleActe"][$i]) ){
 
            if ( $agePatient >= 16 ) {
            $MajorationNGAP1        = "MCC 1";
            $Majoration_A_Effectuee = "Oui";
            }
          }
      } 
      */
      // if ( strcmp($resultat["cotationNGAP"][$i],"CSC 1") == 0 )
      
      // On regarde si la cotation NGAP est de type CNPSY 1
      // ceci concerne les consultations de Spy et de Neurologue.
      
      /*if ( strcmp($resultat["cotationNGAP"][$i],"CNPSY 1") == 0 ) {
        // Neurologue et Psychiatres:
        if ( eregi ('Neurologie' ,$resultat["libelleActe"][$i] ) ||
             eregi ('Psychiatrie',$resultat["libelleActe"][$i] ) ){

            if ( $agePatient >= 16 ) {
              //$MajorationNGAP1          = "MPC 1";
              $MajorationNGAP1          = "MCS 1";
              $Majoration_A_Effectuee   = "Oui";
            }
            
            
            else { 
              $MajorationNGAP1        = "MPJ 1";
              $Majoration_A_Effectuee = "Oui";
            }
        }
      } */
      
      //if ( strcmp($resultat["cotationNGAP"][$i],"CNPSY 1") == 0 ) {
      
      // On regarde si la cotation NGAP est de type CS 1
      // ceci concerne les consultations de spécialistes (CS)
      /*
      if ( strcmp($resultat["cotationNGAP"][$i],"CS 1") == 0 ) {

        // Maintenant en gérant le cas des urgences pédiatriques
        if ( (   strcmp($resultat["libelleActe"][$i],"Consultation urgentiste spécialiste") == 0
              && strcmp($this->typeIntervenant,"PED") == 0 ) ||
                 eregi('Pédiatrie',$resultat["libelleActe"][$i]) ||
                 eregi('PEDIATRIE',$resultat["libelleActe"][$i]) ){
          // On gére les ages 
          if ( $agePatient < 2 ) {

            $MajorationNGAP1        = "MNP 1";
            $Majoration_A_Effectuee = "Oui";
          }
          if ( $agePatient >= 2 && $agePatient < 16 ) {
            $MajorationNGAP1        = "MPJ 1";
            $Majoration_A_Effectuee = "Oui";
          }
        }
        ////////////////////////////////////////////////////////
        
        /*
        
        // Maintenant en gérant le cas des urgences Gynécologie et Obstétrique
        elseif ( (strcmp($resultat["libelleActe"][$i],"Consultation Gynéco Obstétrique") == 0
               && strcmp($this->typeIntervenant,"OBS") == 0 ) ||
                  eregi('Gynécologie et Obstétrique',$resultat["libelleActe"][$i]) ) {
        
        // MODIFICATION A FAIRE majoration suivant l'age du patient
        eko("majoration suivant l'age du patient");
        
        
        }
        ////////////////////////////////////////////////////////
        
        */
        
        // Autres Spécialistes
        /*
        else {
            
            if ( $agePatient >= 16 )
              $MajorationNGAP1        = "MCS 1";
            else
              $MajorationNGAP1        = "MPJ 1";
            
            if ( $agePatient < 16 )
              $MajorationNGAP1        = "MPJ 1";
            
            //$MajorationNGAP2          = "MPC 1";
            $Majoration_A_Effectuee   = "Oui";
        }
      } //if ( strcmp($resultat["cotationNGAP"][$i],"CS 1") == 0 )
      */
      
      // Maintenant on gère le cas s'il y a une Majoration à effectuer
      // pour l'acte $resultat["libelleActe"][$i]
      if ( strcmp($Majoration_A_Effectuee,"Oui") == 0 ) {
        
        $paramRq = array ( );
        $temp = array ( );
        $temp = $resultat;
        
        while ( list($key,$val )= each ( $temp ) ){
          $paramRq[$key] = $temp[$key][$i];
          }
        unset($paramRq[INDIC_SVC]);
        reset ($temp);
        reset ($paramRq);
        // On insere la majoration NGAP dans la nouvelle ligne à inserer dans
        // la table ccam_cotation_actes
        $paramRq["cotationNGAP"]            =   $MajorationNGAP1;
        
        if ( $options->getOption ('EnvoiMajorationsNGAPpourCetCS') )
          $paramRq["envoi_facturation"]     =   1;
        else
          $paramRq["envoi_facturation"]     =   0;

        // On teste si on a déjà inserer l'acte NGAP avec la mojoration
        // Au cas ou le patient revient dans le terminal

        $requete               = new clResultQuery;
        $test_insertion_existe = $requete->Execute("Fichier","CCAM_testInsertionCCAMCotationActes",$paramRq,"ResultQuery");

         
        if ( $test_insertion_existe["INDIC_SVC"][2] == 0 ) {
          // On recherche le max de identifiant pour l'incrementation
          // dans la table
          $requete        = new clResultQuery;
          $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
          $paramRq["identifiant"]         =   $maxIdentifiant["maximun"][0]+1;
          // Insertion de la nouvelle ligne
          $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
          $requete -> addRecord();
        }
          
        if ( $MajorationNGAP2 != "" ) {
          // On insere la majoration NGAP dans la nouvelle ligne à inserer dans
          // la table ccam_cotation_actes
          $paramRq["cotationNGAP"]         =   $MajorationNGAP2;
          // On teste si on a déjà inserer l'acte NGAP avec la majoration
          // Au cas ou le patient revient dans le terminal 
          $requete               = new clResultQuery;
          $test_insertion_existe = $requete->Execute("Fichier","CCAM_testInsertionCCAMCotationActes",$paramRq,"ResultQuery");
            
          if ( $test_insertion_existe["INDIC_SVC"][2] == 0 ) {
            // recherche de l'idEvent max
            $requete        = new clResultQuery;
            $maxIdentifiant = $requete->Execute("Fichier","CCAM_getMaxIdentifiantCCAMCotationActes",array(),"ResultQuery");
            $paramRq["identifiant"]        =   $maxIdentifiant["maximun"][0]+1;
            // Insertion de la nouvelle ligne
            $requete =  new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramRq);
            $requete -> addRecord();
          } // end if
        } // en if
      } // if ( strcmp($Majoration_A_Effectuee,"Oui") == 0 )
    } // for ( $i = 0 ; $i <= $resultat[INDIC_SVC][2] ; $i++ )
}
// end function gestionMajorationsNGAPlesCetCS 
/******************************************************************************/

// Fonction  :  Gestion de l'envoi des actes NGAP dans la boite aux lettres
// Paramètre :  Aucun
/******************************************************************************/
function envoiActesNGAPdansMBTV2 ( ) {
/******************************************************************************/

  global $patient;
  $idEvent       = $this->idEvent;
  $paramRq["cw"] = "idEvent=".$idEvent." and (type='ACTE' or type='DIAG')";
  $requete       = new clResultQuery;
   
  // On récupère la liste des actes pour un patient
  $resultat      = $requete->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");

  // on parcours le tableau des actes
  for ( $i = 0 ; $i < $resultat[INDIC_SVC][2] ; $i++ ) {
      // on recherche si on est en presence d'un acte NGAP
      if ( substr($resultat["codeActe"][$i],0,4) == "NGAP" ) {
        // on verifie dans la table cam_actes_domaine si on envoie ou pas 
        // à la facturation l'acte NGAP
        unset($paramRq);
        $requete         = new clResultQuery;
        $paramRq[cw]     =  "and idDomaine=".CCAM_IDDOMAINE;
        $paramRq[idActe] =  $resultat["codeActe"][$i];
        $envoi           =  $requete->Execute("Fichier","CCAM_get1ActeAllData",$paramRq,"ResultQuery");
        
        // on met à jour en même temps la table ccam_cotation_actes pour préciser 
        // que l'acte NGAP est à envoyer ou pas à la facturation.
        
        unset($paramModif);
        // DBDEB3 : Correction car l'ATU était toujours envoyé même si non valide.
        if ( $resultat['cotationNGAP'][$i] == 'ATU 1' ) {
        	$paramModif["envoi_facturation"] = (( $envoi["envoi_facturation"][0] == 0  || (is_object($patient) AND ! $patient->getValide ( )))?'0':$envoi[envoi_facturation][0]);
        // DBFIN3
        } else $paramModif["envoi_facturation"] = $envoi[envoi_facturation][0];
		    $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		    $sql                             = $requete->updRecord("identifiant='".$resultat["identifiant"][$i]."'");
      } // if
    } // for
  
// Fin Gestion de l'envoi des actes NGAP à la facturation
}
// end function envoiActesNGAPdansMBTV2
/******************************************************************************/


// Fonction  :  Gestion de l'envoi des C ou CCAM en fonction du meilleur tarif
//              dans la boite aux lettres
//              envoi automatique d'une CS et des autres actes NGAP facturable
//              DANS LE CAS DES URGENCES UNIQUEMENT
//           :  Gestion de l'envoi des CS ou CCAM en fonction du meilleur tarif
//              dans la boite aux lettres
//              envoi automatique d'une CS et des autres actes NGAP facturable
//              DANS LE CAS DES URGENCES SPECIALISTES
// Paramètre :  Aucun
/******************************************************************************/
function envoiNGAPouCCAMdansMBTV2 (  ) {
/******************************************************************************/
  
  global $fusion;
  global $table_patient_manuel;
  global $table_patient_automatique;
  
  $idEvent = $this->idEvent;
  
  
  if ( $fusion == 0 ) {
    $typeIntervevant = $this->typeIntervenant;
  }
  else {
    // On recupere le type de l'intervenant
    $param[cw] = "WHERE idpatient='".$idEvent."'" ;
    $param[table] = $table_patient_automatique ;
    $req = new clResultQuery ;
    $res5 = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
    
    unset($param);
    $param[nomitem] = $res5["medecin_urgences"][0];
    $res5 = $req -> Execute ( "Fichier", "getTypeMedecin", $param, "ResultQuery" ) ;
    switch ( $res5[localisation][0] ) {
    	case 'U': $typeIntervevant = "URG"; break ;
    	case 'P': $typeIntervevant = "PED"; break ;
    	case 'F': $typeIntervevant = "SAF"; break ;
    	case 'G': $typeIntervevant = "OBS"; break ;
    	default : $typeIntervevant = "URG"; break ;
      } 
  }
  
    
  
  // LISTE MAJO
  
  // Il faut gerer le cas des médecins qui prescrivent les actes. Il y a qu'un
  // seul médecin qui se charge du patient. Si plusieurs il faut en donner
  // qu'un seul (nom et code adeli).
  // Donc une mise à jour de la table serait nécessaire. Cette mise
  // à jour doit etre faite avant d'envoyer les informations dans la
  // boite aux lettres. Pas dans le cas des anesthesistes des infirmieres et des 
  // consultations spécialistes
  
	$paramRq["cw"] = "idEvent=".$idEvent." and type='ACTE'";
	$requete       = new clResultQuery;
	$req           = new clResultQuery;
	$res           = $req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
  //eko($res);
  //Récupération de l'indicateur Lésion multiple
  $idEvent   = $this->idEvent;
  $lesion    = $this->lesion($idEvent);
  
 	$tabAsso   = $this->tabAsso($idEvent,$lesion);
 	//Récupération des tarifs par lettre-clé NGAP et code CCAM
  $tarifNGAP = $this->tarifNGAP();
  $tarifCCAM = $this->tarifCCAM($idEvent);
  
  //Tarif NGAP et CCAM à zéro
  $tarifTotalActesNGAP = 0;
  $tarifTotalActesCCAM = 0;

// Exception DEQP003 +C/CS possible de facturer les deux.
// Requete qui retourne le nombre d'actes ccam
$paramRq["cw"] = "idEvent=".$idEvent." and type='ACTE' and codeActe not like 'NGAP%' and codeActe not like 'CONS%' and envoi_facturation=1";
$requete       = new clResultQuery;
$req           = new clResultQuery;
$res1           = $req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
//eko($res1);

if ($res1["INDIC_SVC"][2] == 1 and $res1["codeActe"][0] == "DEQP003") {

eko("DEQP003");
}
else {  
  
  if ( strcmp ($typeIntervevant,"URG") == 0 ) {
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  // Cas des urgentistes simples.
  //
  ///////////////////////////////////////////////////////////////////////////////////////////

  //On parcourt le tableau res
  for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
    
    $cotation = explode(" ",$res["cotationNGAP"][$i]);
      
      // Presence d'une CS
      //if ( $cotation[0] == "CS" ) {
      // Recherche du tarif de la CS
        //$paramRq["cw"]          = "where lc='CS';";
        //$resultat               = $requete->Execute("Fichier","CCAM_get1TarifNGAP",$paramRq,"ResultQuery");
        //$tarifTotalActesNGAP   += $resultat["tarif"][0];
        //}
        
    //LISTE MAJO suivant l'age du patient
    if ( strcmp($cotation[0],"C") == 0 && strcmp($cotation[1],"1") == 0 ) {
        // presence d'une C facturable C 1 et non d'une C non facturable C 0 
        // recherche du tarif de la C
      $tarifTotalActesNGAP   += $tarifNGAP["C"];
      }
    elseif ( strcmp($cotation[0],"MGE") == 0 ) {
        // presence de la majoration MGE pour une C 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["MGE"];
      }
    elseif ( strcmp($cotation[0],"MNO") == 0 ) {
        // presence de la majoration MNO pour une C 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["MNO"];
      }
    elseif ( strcmp($cotation[0],"MCG") == 0 ) {
        // presence de la majoration MNO pour une C 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["MCG"];
      }
      //LISTE MAJO all lettre cle 
    elseif ( strcmp($cotation[0],"CS")  != 0 &&
              strcmp($cotation[0],"CF")!= 0 &&
               strcmp($cotation[0],"CNPSY")!= 0 &&
               strcmp($cotation[0],"CSC") != 0 && 
               strcmp($cotation[0],"ATU") != 0 &&
               strcmp($cotation[0],"MTU") != 0 ) {
        // On regarde si on est en presence d'un acte CCAM
      $code                   = $res["codeActe"][$i];
      $paramRq["cw"]          = "CODE='$code';";
      $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
      if ( $resultat['INDIC_SVC'][2] == 1 ) {
          // presence d'un acte CCAM et recherche de son tarif
          // on tient compte aussi des codes associations
        $code_associations = $tabAsso["$code"];
        if ( isset($code_associations) ) {
          if ($code_associations == "2") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*50/100;
              //eko ("code association 2");
            }
          elseif ($code_associations == "3") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*75/100;
              //eko ("code association 3");
            }
          else $tarifTotalActesCCAM += $tarifCCAM["$code"];
            
        }
      }
    }
  } // fin du for
  
  eko("CCAM: ".$tarifTotalActesCCAM);
  eko("NGAP: ".$tarifTotalActesNGAP);
  
  
  // On compare maintenant les tarifs
  if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    //eko ("CCAM");
    //eko ($res[INDIC_SVC][2]);
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        
        $cotation = explode(" ",$res["cotationNGAP"][$i]);
        if ( strcmp($cotation[0],"C") == 0 || 
            strcmp($cotation[0],"MGE") == 0 ||  //LISTE MAJO urgentiste suivant l'age
            strcmp($cotation[0],"MNO") == 0 || 
            strcmp($cotation[0],"MCG") == 0 ) {
          // Mise à jour de la table ccam_cotations_actes pour indiquer de ne pas
          // envoyer l'acte à la facturation.
          unset($paramModif);
          //eko("code non envoyé");
          //eko($res["codeActe"][$i]);
          $paramModif["envoi_facturation"] = 0;
          $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
          $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		      //eko ($sql);
        }
        else {
        
          // On test si on est en presence d'un acte CCAM. Dans ce cas on met à jour la table
          // ccam_cotations_actes pour indiquer d 'envoyer l'acte CCAM à la facturation
          $code                   = $res["codeActe"][$i];
          //eko("code envoyé");
          //eko($res["codeActe"][$i]);
          $paramRq["cw"]          = "CODE='$code';";
          $requete                = new clResultQuery;
          $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
          if ( $resultat['INDIC_SVC'][2] == 1 ) {
            unset($paramModif);
            $paramModif["envoi_facturation"] = 1;
            $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
            $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
           }
          }   
      }
    } // For
  } //if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  else { // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    
    
    // on envoie pas les actes CCAM 
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      // Test presence d'un acte
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        // On retourne son code NGAP ou CCAM
        $requete                = new clResultQuery;
        $code                   = $res["codeActe"][$i];
        $paramRq["cw"]          = "CODE='$code';";
        $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
        //eko ($resultat);
        // Si presence d'un code CCAM
        if ( $resultat['INDIC_SVC'][2] == 1 ) {

        unset($paramModif);
        $paramModif["envoi_facturation"] = 0;
        $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
        $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		    //eko ($sql);
		    
        }
      }
    }
  } // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  
  } // if ( strcmp ($this->typeIntervenant,"URG") == 0 ) {
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  // Cas des urgentistes spécialistes. urgentistes pédiatres
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  elseif ( strcmp ($typeIntervevant,"PED") == 0 ) {
  
    //On parcourt le tableau res
  for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      
    $cotation = explode(" ",$res["cotationNGAP"][$i]);
      
      // Presence d'une CS
      //if ( $cotation[0] == "CS" ) {
      // Recherche du tarif de la CS
        //$paramRq["cw"]          = "where lc='CS';";
        //$resultat               = $requete->Execute("Fichier","CCAM_get1TarifNGAP",$paramRq,"ResultQuery");
        //$tarifTotalActesNGAP   += $resultat["tarif"][0];
        //}
        
    //LISTE MAJO
    if ( strcmp($cotation[0],"CS") == 0 &&
         strcmp($cotation[1],"1") == 0 &&
         strcmp ($res["libelleActe"][$i],"Consultation urgentiste spécialiste") == 0
         ) {
        // Cas donc ici d'une consultation urgentiste spécialiste (pédiatre)
        // presence d'une CS facturable CS 1 et non d'une CS non facturable CS 0 
        // recherche du tarif de la CS
      $tarifTotalActesNGAP   += $tarifNGAP["CS"];
      } //LISTE MAJO pediatre suivant l'age
    elseif ( strcmp($cotation[0],"MPJ") == 0 &&
    strcmp ( $res["libelleActe"][$i],"Consultation urgentiste spécialiste") == 0 ) {
        // presence de la majoration MPJ pour une CS 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["MPJ"];
      }
    elseif ( strcmp($cotation[0],"MNP") == 0 &&
    strcmp ( $res["libelleActe"][$i],"Consultation urgentiste spécialiste") == 0 ) {
        // presence de la majoration MNP pour une C 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["MNP"];
      }
      //LISTE MAJO
    elseif ( strcmp($cotation[0],"CS")  != 0 &&
             strcmp($cotation[0],"CF")!= 0 &&
               strcmp($cotation[0],"CNPSY")!= 0 &&
               strcmp($cotation[0],"CSC") != 0 && 
               strcmp($cotation[0],"ATU") != 0 &&
               strcmp($cotation[0],"MTU") != 0 ) {
        // On regarde si on est en presence d'un acte CCAM
      $code                   = $res["codeActe"][$i];
      $paramRq["cw"]          = "CODE='$code';";
      $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
      if ( $resultat['INDIC_SVC'][2] == 1 ) {
          // presence d'un acte CCAM et recherche de son tarif
          // on tient compte aussi des codes associations
        $code_associations = $tabAsso["$code"];
        if ( isset($code_associations) ) {
          if ($code_associations == "2") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*50/100;
              //eko ("code association 2");
            }
          elseif ($code_associations == "3") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*75/100;
              //eko ("code association 3");
            }
          else $tarifTotalActesCCAM += $tarifCCAM["$code"];
        }
      }
    }
  } // fin du for
  
  eko("CCAM: ".$tarifTotalActesCCAM);
  eko("NGAP: ".$tarifTotalActesNGAP);
  
  
  // On compare maintenant les tarifs
  if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    //eko ("CCAM");
    //eko ($res[INDIC_SVC][2]);
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        
        $cotation = explode(" ",$res["cotationNGAP"][$i]);
        if ( ( strcmp($cotation[0],"CS") == 0  || 
               strcmp($cotation[0],"MNP") == 0 || //LISTE MAJO pediatre suivant l'age
               strcmp($cotation[0],"MPJ") == 0 ) &&
     strcmp ( $res["libelleActe"][$i],"Consultation urgentiste spécialiste") == 0 )
                  {
          // Mise à jour de la table ccam_cotations_actes pour indiquer de ne pas
          // envoyer l'acte à la facturation.
          unset($paramModif);
          $paramModif["envoi_facturation"] = 0;
          $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
          $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		      //eko ($sql);
          }
        else {
        
          // On test si on est en presence d'un acte CCAM. Dans ce cas on met à jour la table
          // ccam_cotations_actes pour indiquer d 'envoyer l'acte CCAM à la facturation
          $code                   = $res["codeActe"][$i];
          $paramRq["cw"]          = "CODE='$code';";
          $requete                = new clResultQuery;
          $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
          if ( $resultat['INDIC_SVC'][2] == 1 ) {
            unset($paramModif);
            $paramModif["envoi_facturation"] = 1;
            $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
            $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
            }
          }   
      }
    } // For
  } //if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  else { // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    
    
    // on envoie pas les actes CCAM 
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      // Test presence d'un acte
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        // On retourne son code NGAP ou CCAM
        $requete                = new clResultQuery;
        $code                   = $res["codeActe"][$i];
        $paramRq["cw"]          = "CODE='$code';";
        $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
        //eko ($resultat);
        // Si presence d'un code CCAM
        if ( $resultat['INDIC_SVC'][2] == 1 ) {
        
        unset($paramModif);
        $paramModif["envoi_facturation"] = 0;
        $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
        $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		    //eko ($sql);
		    
        }
      }
    }
  } // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  
  
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  // Cas des sages-femmes
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  elseif ( strcmp ($typeIntervevant,"SAF") == 0 ) {
  eko("sage femme");
  // Cas des sages-femmes
  // On compare la CF et les actes CCAM
  
    //On parcourt le tableau res
  for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      
    $cotation = explode(" ",$res["cotationNGAP"][$i]);
        
    //LISTE MAJO suivant l'age du patient
    if ( strcmp($cotation[0],"CF") == 0 &&
         strcmp($cotation[1],"1") == 0 &&
         strcmp ($res["libelleActe"][$i],"Consultation sage-femme") == 0
         ) {
        // Cas donc ici d'une consultation urgentiste sage femme
        // presence d'une CF facturable CF 1 et non d'une CF non facturable CF 0 
        // recherche du tarif de la CF
      $tarifTotalActesNGAP   += $tarifNGAP["CF"];
      eko($tarifNGAP);
      }
    // MODIFICATION A FAIRE si majoration pour cf
    /*
    elseif ( strcmp($cotation[0],"aaa") == 0 &&
    strcmp ( $res["libelleActe"][$i],"Consultation sage-femme") == 0 ) {
        // presence de la majoration aaa pour une CF 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["aaa"];
      }
    */
    //LISTE MAJO
    elseif ( strcmp($cotation[0],"CS")  != 0 &&
               strcmp($cotation[0],"CF")  != 0 &&
               strcmp($cotation[0],"CNPSY")!= 0 &&
               strcmp($cotation[0],"CSC") != 0 && 
               strcmp($cotation[0],"ATU") != 0 &&
               strcmp($cotation[0],"MTU") != 0 ) {
        // On regarde si on est en presence d'un acte CCAM
      $code                   = $res["codeActe"][$i];
      $paramRq["cw"]          = "CODE='$code';";
      $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
      if ( $resultat['INDIC_SVC'][2] == 1 ) {
          // presence d'un acte CCAM et recherche de son tarif
          // on tient compte aussi des codes associations
        $code_associations = $tabAsso["$code"];
        if ( isset($code_associations) ) {
          if ($code_associations == "2") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*50/100;
              //eko ("code association 2");
            }
          elseif ($code_associations == "3") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*75/100;
              //eko ("code association 3");
            }
          else $tarifTotalActesCCAM += $tarifCCAM["$code"];
        }
      }
    }
  } // fin du for
  
  eko("CCAM: ".$tarifTotalActesCCAM);
  eko("NGAP: ".$tarifTotalActesNGAP);
  
  
  // On compare maintenant les tarifs
  if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    //eko ("CCAM");
    //eko ($res[INDIC_SVC][2]);
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        
        $cotation = explode(" ",$res["cotationNGAP"][$i]);
        if ( ( strcmp($cotation[0],"CF") == 0 
              
              // MODIFICATION A FAIRE si majoration pour cf
              /* 
              || strcmp($cotation[0],"aaa") == 0 || //LISTE MAJO sage-femme suivant l'age
              strcmp($cotation[0],"bbb") == 0
              */
               
               ) &&
     strcmp ( $res["libelleActe"][$i],"Consultation sage-femme") == 0 )
     // LISTE MAJO suivant l'age du patient
                  {
          // Mise à jour de la table ccam_cotations_actes pour indiquer de ne pas
          // envoyer l'acte à la facturation.
          unset($paramModif);
          $paramModif["envoi_facturation"] = 0;
          $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
          $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		      //eko ($sql);
          }
        else {
        
          // On test si on est en presence d'un acte CCAM. Dans ce cas on met à jour la table
          // ccam_cotations_actes pour indiquer d 'envoyer l'acte CCAM à la facturation
          $code                   = $res["codeActe"][$i];
          $paramRq["cw"]          = "CODE='$code';";
          $requete                = new clResultQuery;
          $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
          if ( $resultat['INDIC_SVC'][2] == 1 ) {
            unset($paramModif);
            $paramModif["envoi_facturation"] = 1;
            $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
            $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
            }
          }   
      }
    } // For
  } //if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  else { // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    
    
    // on envoie pas les actes CCAM 
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      // Test presence d'un acte
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        // On retourne son code NGAP ou CCAM
        $requete                = new clResultQuery;
        $code                   = $res["codeActe"][$i];
        $paramRq["cw"]          = "CODE='$code';";
        $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
        //eko ($resultat);
        // Si presence d'un code CCAM
        if ( $resultat['INDIC_SVC'][2] == 1 ) {
        
        unset($paramModif);
        $paramModif["envoi_facturation"] = 0;
        $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
        $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		    //eko ($sql);
		    
        }
      }
    }
  } // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  
  
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  // Cas des Gyneco - Obstetrique
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  elseif ( strcmp ($typeIntervevant,"OBS") == 0 ) {
  // Cas des Gyneco - Obstetrique
  // On compare la CS et les actes CCAM
  
    //On parcourt le tableau res
  for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      
    $cotation = explode(" ",$res["cotationNGAP"][$i]);
        
    // LISTE MAJO suivant l'age du patient
    if ( strcmp($cotation[0],"CS") == 0 &&
         strcmp($cotation[1],"1") == 0 &&
         strcmp ($res["libelleActe"][$i],"Consultation gynécologie et obstétrique") == 0
         ) {
        // Cas donc ici d'une Consultation Gynécologie et Obstétrique
        // presence d'une CS facturable CS 1 et non d'une CS non facturable CS 0 
        // recherche du tarif de la CS
      $tarifTotalActesNGAP   += $tarifNGAP["CS"];
      }
    
    // MODIFICATION A FAIRE si majoration pour cs
    /*elseif ( strcmp($cotation[0],"aaa") == 0 &&
    strcmp ( $res["libelleActe"][$i],"Consultation gynécologie et obstétrique") == 0 ) {
        // presence de la majoration aaa pour une CS 
        // recherche du tarif
      $tarifTotalActesNGAP   += $tarifNGAP["aaa"];
      }*/
      
      
    //LISTE MAJO
    elseif ( strcmp($cotation[0],"CS")  != 0 &&
               strcmp($cotation[0],"CF")  != 0 &&
               strcmp($cotation[0],"CNPSY")!= 0 &&
               strcmp($cotation[0],"CSC") != 0 && 
               strcmp($cotation[0],"ATU") != 0 &&
               strcmp($cotation[0],"MTU") != 0 ) {
        // On regarde si on est en presence d'un acte CCAM
      $code                   = $res["codeActe"][$i];
      $paramRq["cw"]          = "CODE='$code';";
      $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
      if ( $resultat['INDIC_SVC'][2] == 1 ) {
          // presence d'un acte CCAM et recherche de son tarif
          // on tient compte aussi des codes associations
        $code_associations = $tabAsso["$code"];
        if ( isset($code_associations) ) {
          if ($code_associations == "2") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*50/100;
              //eko ("code association 2");
            }
          elseif ($code_associations == "3") {
            $tarifTotalActesCCAM += $tarifCCAM["$code"]*75/100;
              //eko ("code association 3");
            }
          else $tarifTotalActesCCAM += $tarifCCAM["$code"];
        }
      }
    }
  } // fin du for
  
  eko("CCAM: ".$tarifTotalActesCCAM);
  eko("NGAP: ".$tarifTotalActesNGAP);
  
  
  // On compare maintenant les tarifs
  if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    //eko ("CCAM");
    //eko ($res[INDIC_SVC][2]);
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        
        $cotation = explode(" ",$res["cotationNGAP"][$i]);
        if ( ( strcmp($cotation[0],"CS") == 0 
        
            // MODIFICATION A FAIRE si majoration pour cs
              /* 
              || strcmp($cotation[0],"aaa") == 0 || //LISTE MAJO gynécologie et obstétrique suivant l'age
              strcmp($cotation[0],"bbb") == 0
              */
              
              ) &&
     strcmp ( $res["libelleActe"][$i],"Consultation gynécologie et obstétrique") == 0 )
                  // LISTE MAJO suivant l'age du patient
                  {
          // Mise à jour de la table ccam_cotations_actes pour indiquer de ne pas
          // envoyer l'acte à la facturation.
          unset($paramModif);
          $paramModif["envoi_facturation"] = 0;
          $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
          $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		      //eko ($sql);
          }
        else {
        
          // On test si on est en presence d'un acte CCAM. Dans ce cas on met à jour la table
          // ccam_cotations_actes pour indiquer d 'envoyer l'acte CCAM à la facturation
          $code                   = $res["codeActe"][$i];
          $paramRq["cw"]          = "CODE='$code';";
          $requete                = new clResultQuery;
          $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
          if ( $resultat['INDIC_SVC'][2] == 1 ) {
            unset($paramModif);
            $paramModif["envoi_facturation"] = 1;
            $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
            $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
            }
          }   
      }
    } // For
  } //if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  else { // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
    
    
    // on envoie pas les actes CCAM 
    for ( $i = 0 ; $i < $res['INDIC_SVC'][2] ; $i++ ) {
      // Test presence d'un acte
      if ( strcmp($res["type"][$i],"ACTE") == 0 ) {
        // On retourne son code NGAP ou CCAM
        $requete                = new clResultQuery;
        $code                   = $res["codeActe"][$i];
        $paramRq["cw"]          = "CODE='$code';";
        $resultat               = $requete->Execute("Fichier","CCAM_getActesCCAM",$paramRq,"ResultQuery");
        //eko ($resultat);
        // Si presence d'un code CCAM
        if ( $resultat['INDIC_SVC'][2] == 1 ) {
        
        unset($paramModif);
        $paramModif["envoi_facturation"] = 0;
        $requete                         = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
        $sql                             = $requete->updRecord("identifiant='".$res["identifiant"][$i]."'");
		    //eko ($sql);
		    
        }
      }
    }
  } // else if ( $tarifTotalActesCCAM >= $tarifTotalActesNGAP ) {
  
  
  }

} // fin du else if ($res1["INDIC_SVC"][2] == 1 and $res1["codeActe"][0] == "DEQP003") {
  
  
  
  // fin de comparaison des tarifs
  
// Gestion de l'envoi des NGAP ou CCAM en fonction du meilleur tarif
// dans la boite aux lettres 
}


// end function envoiNGAPouCCAMdansMBTV2
/******************************************************************************/

// Fonction  :  Gestion de l'envoi des diagnostics 
//              dans la boite aux lettres
// Paramètre :  Aucun
/******************************************************************************/
function envoiDiagnosticsdansMBTV2 (  ) {
/******************************************************************************/

  global $options;

  $idEvent       = $this->idEvent;
      
  if ( !$options->getOption ( 'EnvoiDiagnostics' ) ) 
    $paramModif["envoi_facturation"] = 0;   	 
  else 
    $paramModif["envoi_facturation"] = 1;
      
  $requete = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
  $sql     = $requete->updRecord("idEvent=".$idEvent." and type='DIAG'");

}
// end function envoiDiagnosticsdansMBTV2
/******************************************************************************/

function tarifCCAM($idEvent){
//Tri des actes CCAM affectés par tarif décroissant, création du tableau des codes association
unset($paramRq);
$paramRq["cw"]="and c.idEvent=$idEvent and c.idDomaine=".CCAM_IDDOMAINE." and envoi_facturation=1";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getTarifsCCAM_2",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
//eko($res);
for ($i=0;isset($res["codeActe"][$i]);$i++){
  $code=$res["codeActe"][$i];
	$tarifCCAM[$code]=str_replace(",",".",$res["pu_base"][$i]);
}
return $tarifCCAM;
}

function tarifNGAP(){
	global $options ;
$req=new clResultQuery;
$res=$req->Execute("Fichier","Tarifs_getTarifNGAP",array(),"ResultQuery");

//eko($res[INDIC_SVC]);
unset($tarifNGAP);
for ($i=0;isset($res[LC][$i]);$i++){
	$lc=$res[LC][$i];
	$tarifNGAP[$lc]=str_replace(",",".",$res[TARIF][$i]);
}
//eko ($tarifNGAP);
return $tarifNGAP;
}

function lesion($idEvent){
unset($paramRq);
$paramRq[cw]="idEvent=$idEvent and idDomaine=".CCAM_IDDOMAINE." and envoi_facturation=1";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
if ($res["lesionMultiple"][0]) $lesion=$res["lesionMultiple"][0]; else $lesion="Non";
return $lesion;
}

function tabAsso($idEvent,$lesion){
//Tri des actes CCAM affectés par tarif décroissant, création du tableau des codes association
unset($paramRq);
$paramRq["cw"]="and c.idEvent=$idEvent and c.idDomaine=".CCAM_IDDOMAINE." and envoi_facturation=1";
$req=new clResultQuery;
$res2=$req->Execute("Fichier","CCAM_getActeCCAMDecroissant",$paramRq,"ResultQuery");
//eko($res2[INDIC_SVC]);
//eko ($res2);
for( $i=0 ; $i < $res2["INDIC_SVC"][2] ; $i++ ) {
  $paramRq["cw"]="and a.code='".$res2["codeActe"][$i]."' order by t.aadt_modif desc";
  $req=new clResultQuery;
  $res3=$req->Execute("Fichier","CCAM_get1TarifCCAM",$paramRq,"ResultQuery");
  //eko($res3);
  $res2["pu_base"][$i] = $res3["pu_base"][0];
  }
//eko($res2);
unset($tabAsso);
if ($res2["INDIC_SVC"][2]==1){
  $codeActe=$res2["codeActe"][0];
  $tabAsso[$codeActe]="";
}
else{
  $j=1;
  for ($i=0;isset($res2["codeActe"][$i]);$i++){
    /*if (!isset($date_old)) $date_old=$res2[dateDemande][$i];
    else{
      if ($date_old==$res2[dateDemande][$i]) $j++; else $j=1;
    }*/
   $codeActe=$res2["codeActe"][$i];
   if ($lesion=="Non"){
     // if ($j<=2) $tabAsso[$codeActe]=$j; else $tabAsso[$codeActe]=""; //ligne 1 
     // ligne 1 devient ligne 2 en tenant compte des suppléments pour les actes YYYY
     if ($j<=2) 
      $tabAsso[$codeActe]=$j;
     elseif ( substr($codeActe,0,4) == "YYYY" ) 
      $tabAsso[$codeActe]=1;
    else
      $tabAsso[$codeActe]="";
   }
   else{
    if ($j<=3){
      if ($j==2) $k=3; elseif ($j==3) $k=2; else $k=1;
      $tabAsso[$codeActe]=$k; 
    }
    else $tabAsso[$codeActe]="";
   }
   if ($res2["pu_base"][$i]=="0.00") $tabAsso[$codeActe]="";
   //$date_old=$res2[dateDemande][$i];
   $j++;
  }
}
//eko ($tabAsso);
return $tabAsso;
}

function deleteBAL(){
global $options ;
if (!$this->manuel or $options->getOption ( 'CCAMEnvoiManuel' )){
//Envoi du message de suppression  dans la BAL pour les éléments déjà envoyés
//lorsque le patient revient dans le terminal.
unset($paramRq);
$paramRq[cw]="idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE." and envoi_facturation='1'";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
//eko ("-------------------------------------------------------------------------");
//eko ($res);
//eko ("-------------------------------------------------------------------------");
$type="suppression";
$this->contenuBAL($res,$type);

// On supprime ici toutes les majorations,ATU et MTU affectées aux actes du patient quand le patient
// revient dans le terminal pour une raison ou pour une autre.
// MAJO
$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);

// MOFIFICATION A FAIRE 
// suppression des majo sage-femme et gyneco obstetrique
$sql=$requete->delRecord("idEvent=".$this->idEvent." and cotationNGAP in ('ATU 1','MTU 1','MINFD 1','INFN2 1','INFN1 1','MNO 1','MGE 1','MCG 1','MCC 1','MPC 1','MCS 1','MPJ 1','MNP 1') and idDomaine=".CCAM_IDDOMAINE);

// Mise à jour de la table en initialisant tous les actes: On les parametres de telle manière
// à tous les envoyer à la facturation
unset($paramModif);
$paramModif[envoi_facturation] = "1";
$requete = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
$sql = $requete->updRecord("idEvent=".$this->idEvent);
}
}

// Ecriture dans MBTV2
function contenuBAL($res,$type,$tabAsso=array(),$indicateurs=array(),$maxLC=array(),$maxCCAM=array()){
// res contient tous les actes qu'on envoie à la facturation
global $options;

global $fusion;
global $table_patient_manuel;
global $table_patient_automatique;


//eko($indicateurs);eko($maxLC);eko($maxCCAM);
//Préparation du champs CONTENU - Initialisation avec les données patients
$contenuDiag="";
$cptDiag=1;
$NGAPjoue=0;
unset($modificateursJoues);
reset($res);
// Variable pour les majo des actes infirmiers
$Majoration_A_Effectuee = "Non";

$idEvent     = $this->idEvent;


// On va affecter dans une variable le nombre de CONS
/*$Nombre_Cons = 0;
$req=new clResultQuery;
$paramRq["cw"] = "idEvent='".$idEvent."'";
$res=$req->Execute("Fichier","CCAM_getNombreCONS",$paramRq,"ResultQuery");
$Nombre_Cons = $res["nombre"][0];
$tab_Cons[0] = 0;
for ( $i=1 ; $i<=$Nombre_Cons ; $i++)
  $tab_Cons[$i] = 0;*/

// Presence d'une fusion
if ( $fusion == 1 ) {

// Presence d'une fusion
// On recupere le nom de l'infirmier plus son code ADELI
// idem pour le medecin

      unset($param);
      $param[cw] = "WHERE idpatient='".$idEvent."'" ;
      $param[table] = $table_patient_automatique ;
      $req = new clResultQuery ;
      $res4 = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
      
      $nomIDEfusion = $res4["ide"][0];
      $nomMedecinfusion = $res4["medecin_urgences"][0];
      
      $dateEvent   = $res4["dt_examen"][0] ;
      $dtFinInterv = $res4["dt_sortie"][0] ;
      $clDateDeb   = new clDate($dateEvent);
      $clDateFin   = new clDate($dtFinInterv);
      
      unset($param);
      $param[nomitem] = $nomIDEfusion ;
      $res4 = $req -> Execute ( "Fichier", "getMatriculeIDE", $param, "ResultQuery" ) ;
      $matriculeIDEfusion = $res4["matricule"][0] ;
      
      unset($param);
      $param[nomitem] = $nomMedecinfusion ;
      $res4 = $req -> Execute ( "Fichier", "getMatriculeMedecin", $param, "ResultQuery" ) ;
      $matriculeMedecinfusion = $res4["matricule"][0] ;
      
      
      
}
else {
  
  $dateEvent   = $this->dateEvent;
  $dtFinInterv = $this->dtFinInterv;
  $clDateDeb   = new clDate($dateEvent);
  $clDateFin   = new clDate($dtFinInterv);
}

if ( strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure médiane")==0 ) {
      $nbHeures     = $clDateFin->getDifference($clDateDeb)/3600;
      $dateMediane  = $clDateDeb->addHours($nbHeures/2);
      $dateMediane  = $clDateDeb->getDate("Y-m-d H:i:s");
      $heureCalcule = $dateMediane;
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

      
list($dateActe,$heureActe)=explode(" ",$heureCalcule);
      list($annee,$mois,$jour)=explode("-",$dateActe);
      (substr($mois,0,1)==0)?$mois=substr($mois,1,1):"";
      (substr($jour,0,1)==0)?$jour=substr($jour,1,1):"";
      list($heure,$minute,$seconde)=explode(":",$heureActe);
      (substr($heure,0,1)==0)?$heure=substr($heure,1,1):"";
      $dateActe=mktime(0,0,0,$mois,$jour,$annee);
      
//majoration pour les consultations et les sages-femmes
if (($heure>=20 or ($heure>=6 and $heure<8))) $majoCONS="N";
elseif ($heure<6) $majoCONS="NM";
      
//Gestion des dimanches
if (date("w",$dateActe)==0 and $majoCONS=="") $majoCONS="F";
    
//Gestion des jours fériés
$dateFerie=new clDate($dateActe);
if ($dateFerie->isHoliday() and $majoCONS=="") $majoCONS="F";


for ($i=0;isset($res[identifiant][$i]);$i++){ 
  $contenuInit=$res[numSejour][$i]."|".$res[idu][$i]."|".$res[nomu][$i]."|".$res[pren][$i]."|".$res[sexe][$i].
    "|".$res[dtnai][$i];
  $contenuInit3="|".$res[numUFdem][$i];
  
  $dateDem=substr($res[dateDemande][$i],0,10);
  $heureDem=substr($res[dateDemande][$i],11,6)."00";
  $dateReal=substr($res[dateEvent][$i],0,10);
  $heureReal=substr($res[dateEvent][$i],11,6)."00";
  
  /*$dateDem=substr($res[dateDemande][$i],0,10);
  $heureDem=substr($res[dateDemande][$i],11,6)."00";
  $dateReal=substr($res[dateEvent][$i],0,10);
  $heureReal=substr($res[dateEvent][$i],11,6)."00";*/
  
  $contenuInit2="|".$dateDem."|".$heureDem;
	
  // Cas des consultations spécialisées.
  if (substr($res[codeActe][$i],0,4)=="CONS") {
    
    $cotation=explode("+",$res[cotationNGAP][$i]);
		
		while (list($key,$val)=each($cotation)){
      
      // On gere le cas des actes du docteur qui prend en charge la patient
      // Etape1
      // Recupere l'indice de facturation
      list($lc,$coeff)=explode(" ",$val);
			if ($coeff==0){$factu="non";$coeff=1;}
			else $factu="oui";
			
			// Etape2
			// On colle le modificateur sur les consultes specialites
      $majoNGAP="";
      //eko($lc);
			if ( substr($lc,0,1)=="C" ) { // MAJO CS 1 CSC 1 CNPSY 1
        $majoNGAP = $majoCONS;
      }
      
      $contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||||$dateReal|$heureReal|".$res[envoi_nomIntervenant][$i].
				"||".$res[envoi_matriculeIntervenant][$i]."|".$res[numUFexec][$i]."||$lc|$coeff|$factu||$majoNGAP";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
			
			//eko($contenu);
			unset($param);
			$param[DTINS]=date("Y-m-d H:i").":00";
			$param[ETAT]="P";
			$param[DISCR]=$res[idEvent][$i];
			$param[TYPE]="NGAP";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
		}
		
		unset($paramModif);
		$paramModif[validDefinitive]="O";
		$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
			" and idEvent=".$res[idEvent][$i]);
	
  } // if (substr($res[codeActe][$i],0,4)=="CONS")
  
  

  
  // Cas des actes ngap du medecin et de l'infirmier qui prennent
  // en charge le patient
  elseif (substr($res[codeActe][$i],0,4)=="NGAP") {
	
    // Il y aura deux intervenants le docteur et l'infirmier
    // Donc deux majorations possibles au maximun
    // La majoration forfaire collée pour l'un des actes ngap du docteur
    // La majoration forfaire collée pour l'un des actes ngap de l'infirmier
    
    
    $cotation=explode("+",$res[cotationNGAP][$i]);
    
    //eko ($cotation);
		
		while (list($key,$val)=each($cotation)){
			
       // On gere le cas des actes du docteur qui prend en charge la patient
       
       // Etape1
      // Recupere l'indice de facturation
      list($lc,$coeff)=explode(" ",$val);
			if ($coeff==0){$factu="non";$coeff=1;}
			else $factu="oui";
			
			// Etape2
			// On colle le modificateur sur le premier acte ngap du medecin la C ou CS
      $majoNGAP="";
      //eko($lc);
			if ( substr($lc,0,1)=="C" and !$NGAPjoue ) { // C ou CS
        $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;
      }
      /*else {
        if ( !ereg ("AMI",$lc ) and 
             !ereg ("AIS",$lc ) and
             !ereg ("K",$lc   ) and
             !ereg ("KC",$lc  ) and
             !ereg ("PH",$lc  ) and

             !ereg ("MNO",$lc ) and // LISTE MAJO
             !ereg ("MGE",$lc ) and
             !ereg ("MCG",$lc ) and
             !ereg ("MCC",$lc ) and
             !ereg ("MPC",$lc ) and
             !ereg ("MCS",$lc ) and
             !ereg ("MPJ",$lc ) and
             !ereg ("MNP",$lc ) and
             !$NGAPjoue ) {
          $majoNGAP = $res[modificateurs][$i];
          $NGAPjoue = 1;
        }
      }*/
     
     // On gere le cas des actes infirmiers
     //LISTE MAJO pour les actes infirmiers
     if ( ereg ("AMI",$lc)   || 
          ereg ("AIS",$lc )  ||
          ereg ("K",$lc )    ||
          ereg ("KC",$lc )   ||
          ereg ("PH",$lc )   ||
          ereg ("INFN1",$lc) ||
          ereg ("INFN2",$lc )||
          ereg ("MINFD",$lc )
        ) {
      
      if ( $fusion == 0 ) {
      
      
      if ( strcmp($this->matriculeIDE,"") == 0 ) {
        if ( strcmp( $options->getOption("codeAdeliInfirmier"),"" ) == 0 ) {
          if ( strcmp( $this->matriculeIntervenant,"" ) == 0 ) {
            $matriculeIDE = $options->getOption("codeAdeliChefService");$nomIDE = $this->nomIntervenant;
            if ( !$NGAPjoue ) { $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;$Majoration_A_Effectuee = "Oui"; }
            }
          else {
            $matriculeIDE = $this->matriculeIntervenant;$nomIDE = $this->nomIntervenant;
            if ( !$NGAPjoue ) { $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;$Majoration_A_Effectuee = "Oui"; }
            }
        }  
        else {$matriculeIDE = $options->getOption("codeAdeliInfirmier");$nomIDE = $this->nomIDE;}
      }
      else {$matriculeIDE = $this->matriculeIDE;$nomIDE = $this->nomIDE;}
      
      }
      
      else {
      
      if ( strcmp($matriculeIDEfusion,"") == 0 )
        if ( strcmp( $options->getOption("codeAdeliInfirmier"),"" ) == 0 )
          if ( strcmp( $matriculeMedecinfusion,"" ) == 0 ) {
            $matriculeIDE = $options->getOption("codeAdeliChefService");$nomIDE = $nomMedecinfusion;
            if ( !$NGAPjoue ) { $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;$Majoration_A_Effectuee = "Oui"; }
            }
          else {
            $matriculeIDE = $matriculeMedecinfusion;$nomIDE = $nomMedecinfusion;
            if ( !$NGAPjoue ) { $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;$Majoration_A_Effectuee = "Oui"; }
            }  
        else {$matriculeIDE = $options->getOption("codeAdeliInfirmier");$nomIDE = $nomIDEfusion;}
      else {$matriculeIDE = $matriculeIDEfusion;$nomIDE = $nomIDEfusion;}
      
       
    
      
      }
        
      //Premiere version avec la fonction gestionMajorationsActesInfirmiers
      //                      et l'option EnvoiMajorationsActesInfirmiers
      /* 
      // Calcul des majo "NGAP" si on envoi pas les majo des "AMI" et "AIS"
      if ( !$options->getOption ('EnvoiMajorationsActesInfirmiers') ){
      //eko("majo ngap :".$lc);
      // Concernant les actes infirmiers
      // Soit on envoie les modificateurs pour les actes AMI et AIS
      // Soit on envoie les majorations des actes AMI et AIS
      // Mais pas les deux.
	  
      //Gestion des modificateurs de nuit et jours fériés pour las actes NGAP infirmiers
      //Gestion des tranches horaires pour las actes NGAP infirmiers
      if (($heure>=20 or ($heure>=6 and $heure<8))) $majoNGAP="N";
      elseif ($heure<6) $majoNGAP="NM";
    
      //Gestion des dimanches pour las actes NGAP infirmiers
      if (date("w",$dateActe)==0 and $majoNGAP=="") $majoNGAP="F";
    
      //Gestion des jours fériés pour las actes NGAP infirmiers
      $dateFerie=new clDate($dateActe);
      if ($dateFerie->isHoliday() and $majoNGAP=="") $majoNGAP="F";
        $modificateurs=$majoNGAP; 
      }
      */
      
    // Majoration de nuit pour les actes infirmiers
    // Nous avons l'heure
    if ( strcmp($Majoration_A_Effectuee,"Non")==0 ) {   
        
        if ( ( $heure >=20 && $heure < 23 ) || ( $heure >= 5 && $heure <8 ) ){
          $MajorationNGAP1 = "N";$Majoration_A_Effectuee = "Oui";}
        if ( $heure >=23 || $heure < 5 ) {
          $MajorationNGAP1 = "NM";$Majoration_A_Effectuee = "Oui";}

        // pour les jours féries
        $dateFerie=new clDate($dateActe);

        if ( $dateFerie->isHoliday() && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1 = "F";$Majoration_A_Effectuee = "Oui";}
          
        // pour les dimanches
        if ( date("w",$dateActe)==0 && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1 = "F";$Majoration_A_Effectuee = "Oui";}
        
        // pour les samedis
        if ( date("w",$dateActe)==6 && $heure>=8 && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1 = "F";$Majoration_A_Effectuee = "Oui";}
        
        $majoNGAP = $MajorationNGAP1;
      
      }
      
      
      
      
      $contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||||$dateReal|$heureReal|".$nomIDE.
				"||".$matriculeIDE."|".$res[numUFexec][$i]."||$lc|$coeff|$factu||$majoNGAP";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
			//eko($contenu);
      // Mise à jour de la table ccam_cotation_acte
      if (strcmp($type,"creation") == 0 ) {
        $paramModif["envoi_nomIntervenant"]       = $nomIDE;
        $paramModif["envoi_matriculeIntervenant"] = $matriculeIDE;
        $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	      $sql        = $requete->updRecord("idEvent=".$this->idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
        //eko($res["identifiant"][$i]);
        } 
      }
// if ( ereg ("AMI",$lc) || ereg ("AIS",$lc ) || ereg ("INFN1",$lc) || ereg ("INFN2",$lc ) || ereg ("MINFD",$lc )) {
     
    else {
      $contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||||$dateReal|$heureReal|".$res[envoi_nomIntervenant][$i].
				"||".$res[envoi_matriculeIntervenant][$i]."|".$res[numUFexec][$i]."||$lc|$coeff|$factu||$majoNGAP";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
				//eko($contenu);
			}
			
			unset($param);
			$param[DTINS]=date("Y-m-d H:i").":00";
			$param[ETAT]="P";
			$param[DISCR]=$res[idEvent][$i];
			$param[TYPE]="NGAP";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
		}
		
		unset($paramModif);
		$paramModif[validDefinitive]="O";
		$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
			" and idEvent=".$res[idEvent][$i]);
	} //if (substr($res[codeActe][$i],0,4)=="NGAP"
	
	// Cas des actes CCAM
  else{
		if ($res[type][$i]=="ACTE"){
      list($modificateurs1,$modificateurs4)=explode("|",$res[modificateurs][$i]);
      if ($modificateurs1){
        $modificateursCotes=explode("~",$modificateurs1);
        $modificateurs1="";
        while (list($key,$val)=each($modificateursCotes)){
          if ($val=="F" or $val=="P" or $val=="S"){
            if ((!$modificateursJoues[$val] and !$indicateurs["C"]) or 
                ($indicateurs["C"] and substr($res[codeActe][$i],0,3)=="DEQ")) $modificateurs1.="$val~";
            //eko("indicC:$indicateurs[C]-val:$val-modif1:$modificateurs1-modifJoueAvant:".$modificateursJoues[$val]);
          }
          else $modificateurs1.="$val~";
          $modificateursJoues[$val]=1;
          //eko("val:$val-modif1:$modificateurs1-modifJoue:".$modificateursJoues[$val]);
        }
        /*eko("codeActe:".$res[codeActe][$i]);
        eko($modificateursCotes);
        eko("modif1Final:$modificateurs1");
        eko($modificateursJoues);*/
        if ($modificateurs1) $modificateurs1=substr($modificateurs1,0,-1);
      }
      $modificateurs4="";
		
			list($intervenant1,$intervenant4)=explode("|",$res[envoi_matriculeIntervenant][$i]);
			list($nomIntervenant1,$nomIntervenant4)=explode("|",$res[envoi_nomIntervenant][$i]);
			$codeActe=$res[codeActe][$i];
      $codeAssociation=$tabAsso[$codeActe];
			
			$contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||1|0|$dateReal|$heureReal|".$nomIntervenant1.
				"||".$intervenant1."|".$res[numUFexec][$i]."|".$modificateurs1.
				"|||oui|$codeAssociation";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
			unset($param);
			$param[DTINS]=date("Y-m-d H:i").":00";
			$param[ETAT]="P";
			$param[DISCR]=$res[idEvent][$i];
			$param[TYPE]="CCAM";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
			
			if ($res[codeActivite4][$i]=="O"){
				/*//Recherche du nom de l'anesthésiste dans la liste
				unset($paramRq);
				$paramRq[code]=$intervenant4;
				$paramRq[idDomaine]=CCAM_IDDOMAINE;
				$paramRq[nomListe]="Anesthésie";
				$req=new clResultQuery;
				$res2=$req->Execute("Fichier","CCAM_getNomMed",$paramRq,"ResultQuery");
				//eko($res2[INDIC_SVC]);
				$nomMed4=$res2[nomItem][0];*/
				
				$contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
					"||4|0|$dateReal|$heureReal|".$nomIntervenant4.
					"||".$intervenant4."|".$res[numUFexec][$i]."|".
					$modificateurs4."|||oui|$codeAssociation";
				$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
				unset($param);
				$param[DTINS]=date("Y-m-d H:i:")."00";
				$param[ETAT]="P";
				$param[DISCR]=$res[idEvent][$i];
				$param[TYPE]="CCAM";
				$param[CONTENU]=$contenu;
				$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
				$sql=$majrq->addRecord();
			}
			
			unset($paramModif);
			$paramModif[validDefinitive]="O";
			$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
			$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
				" and idEvent=".$res[idEvent][$i]);
		} //if ($res[type][$i]=="ACTE")
		// Cas du diagnostic
    else{
			$idEvent=$res[idEvent][$i];
      /*$numSejour=$res[numSejour][$i];
    	$idu=$res[idu][$i];
    	$nomu=$res[nomu][$i];
    	$pren=$res[pren][$i];
    	$sexe=$res[sexe][$i];
    	$dtnai=$res[dtnai][$i];
    	$numUFdem=$res[numUFdem][$i];*/
    	$numUFexec=$res[numUFexec][$i];
    	$nomIntervenant=$res[envoi_nomIntervenant][$i];
    	$matriculeIntervenant=$res[envoi_matriculeIntervenant][$i];
      ($cptDiag==1)?$sep="|":$sep="~";
			$contenuDiag.=$res[codeActe][$i].$sep;
			$cptDiag++;
		} // Cas du diagnostic
	} // Cas des actes CCAM
} //for ($i=0;isset($res[identifiant][$i]);$i++)

if ($contenuDiag){
	/*unset($paramRq);
	$paramRq[cw]="type='DIAG' and validDefinitive='O' and idEvent=".$res[idEvent][$i]." and idDomaine=".CCAM_IDDOMAINE;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
	//eko($res[INDIC_SVC]);
	if ($res[INDIC_SVC][2]!=0) $type="modification";*/
	
	$cptDiag--;
	if ($cptDiag==1) $sepDiag="|"; else $sepDiag="";
	$contenuDiag=substr($contenuDiag,0,-1);
	$contenuSuite="|$type|".$idEvent."|".$contenuDiag.$sepDiag.
		"|||$dateReal|$heureReal|".$nomIntervenant.
		"||".$matriculeIntervenant."|".$numUFexec."||||";
	$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
	unset($param);
	$param[DTINS]=date("Y-m-d H:i:")."00";
	$param[ETAT]="P";
	$param[DISCR]=$idEvent;
	$param[TYPE]="DIAG";
	$param[CONTENU]=$contenu;
	$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
	$sql=$majrq->addRecord();
	
	unset($paramModif);
	$paramModif[validDefinitive]="O";
	$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	$sql=$requete->updRecord("type='DIAG' and idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE);
}
}


// Ecriture dans MBTV2
function contenuBAL_Netforce($res,$type,$tabAsso=array(),$indicateurs=array(),$maxLC=array(),$maxCCAM=array()){
// res contient tous les actes qu'on envoie à la facturation
global $options;

//eko($indicateurs);eko($maxLC);eko($maxCCAM);
//Préparation du champs CONTENU - Initialisation avec les données patients
$contenuDiag="";
$cptDiag=1;
$NGAPjoue=0;
unset($modificateursJoues);
reset($res);
// Variable pour les majo des actes infirmiers
$Majoration_A_Effectuee = "Non";

$idEvent     = $this->idEvent;
$dateEvent   = $this->dateEvent;
$dtFinInterv = $this->dtFinInterv;
$clDateDeb   = new clDate($dateEvent);
$clDateFin   = new clDate($dtFinInterv);

// On va affecter dans une variable le nombre de CONS
/*$Nombre_Cons = 0;
$req=new clResultQuery;
$paramRq["cw"] = "idEvent='".$idEvent."'";
$res=$req->Execute("Fichier","CCAM_getNombreCONS",$paramRq,"ResultQuery");
$Nombre_Cons = $res["nombre"][0];
$tab_Cons[0] = 0;
for ( $i=1 ; $i<=$Nombre_Cons ; $i++)
  $tab_Cons[$i] = 0;*/

if ( strcmp($options->getOption('ChoixHeureAffectationActes'),"Heure médiane")==0 ) {
      $nbHeures     = $clDateFin->getDifference($clDateDeb)/3600;
      $dateMediane  = $clDateDeb->addHours($nbHeures/2);
      $dateMediane  = $clDateDeb->getDate("Y-m-d H:i:s");
      $heureCalcule = $dateMediane;
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
      
list($dateActe,$heureActe)=explode(" ",$heureCalcule);
      list($annee,$mois,$jour)=explode("-",$dateActe);
      (substr($mois,0,1)==0)?$mois=substr($mois,1,1):"";
      (substr($jour,0,1)==0)?$jour=substr($jour,1,1):"";
      list($heure,$minute,$seconde)=explode(":",$heureActe);
      (substr($heure,0,1)==0)?$heure=substr($heure,1,1):"";
      $dateActe=mktime(0,0,0,$mois,$jour,$annee);
      
if (($heure>=20 or ($heure>=6 and $heure<8))) $majoCONS="N";
elseif ($heure<6) $majoCONS="NM";
      
//Gestion des dimanches
if (date("w",$dateActe)==0 and $majoCONS=="") $majoCONS="F";
    
//Gestion des jours fériés
$dateFerie=new clDate($dateActe);
if ($dateFerie->isHoliday() and $majoCONS=="") $majoCONS="F";


for ($i=0;isset($res[identifiant][$i]);$i++){ 
  $contenuInit=$res[numSejour][$i]."|".$res[idu][$i]."|".$res[nomu][$i]."|".$res[pren][$i]."|".$res[sexe][$i].
    "|".$res[dtnai][$i];
  $contenuInit3="|".$res[numUFdem][$i];
  
  $dateDem=substr($res[dateDemande][$i],0,10);
	$heureDem=substr($res[dateDemande][$i],11,6)."00";
	
	$dateReal=substr($res[dateEvent][$i],0,10);
	$heureReal=substr($res[dateEvent][$i],11,6)."00";
	$contenuInit2="|".$dateDem."|".$heureDem;
	
  // Cas des consultations spécialisées.
  if (substr($res[codeActe][$i],0,4)=="CONS") {
    
    $cotation=explode("+",$res[cotationNGAP][$i]);
		
		while (list($key,$val)=each($cotation)){
      
      // On gere le cas des actes du docteur qui prend en charge la patient
      // Etape1
      // Recupere l'indice de facturation
      list($lc,$coeff)=explode(" ",$val);
			if ($coeff==0){$factu="non";$coeff=1;}
			else $factu="oui";
			
			// Etape2
			// On colle le modificateur sur les consultes specialites
      $majoNGAP="";
      //eko($lc);
			if ( substr($lc,0,1)=="C" ) { // MAJO CS 1 CSC 1 CNPSY 1
        $majoNGAP = $majoCONS;
      }
      
      $contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||||$dateReal|$heureReal|".$res[nomIntervenant][$i].
				"||".$res[matriculeIntervenant][$i]."|".$res[numUFexec][$i]."||$lc|$coeff|$factu||$majoNGAP";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
			
			//eko($contenu);
			unset($param);
			$param[DTINS]=date("Y-m-d H:i").":00";
			$param[ETAT]="P";
			$param[DISCR]=$res[idEvent][$i];
			$param[TYPE]="NGAP";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
		}
		
		unset($paramModif);
		$paramModif[validDefinitive]="O";
		$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
			" and idEvent=".$res[idEvent][$i]);
	
  } // if (substr($res[codeActe][$i],0,4)=="CONS")
  
  

  
  // Cas des actes ngap du medecin et de l'infirmier qui prennent
  // en charge le patient
  elseif (substr($res[codeActe][$i],0,4)=="NGAP") {
	
    // Il y aura deux intervenants le docteur et l'infirmier
    // Donc deux majorations possibles au maximun
    // La majoration forfaire collée pour l'un des actes ngap du docteur
    // La majoration forfaire collée pour l'un des actes ngap de l'infirmier
    
    
    $cotation=explode("+",$res[cotationNGAP][$i]);
    
    //eko ($cotation);
		
		while (list($key,$val)=each($cotation)){
			
       // On gere le cas des actes du docteur qui prend en charge la patient
       
       // Etape1
      // Recupere l'indice de facturation
      list($lc,$coeff)=explode(" ",$val);
			if ($coeff==0){$factu="non";$coeff=1;}
			else $factu="oui";
			
			// Etape2
			// On colle le modificateur sur le premier acte ngap du medecin la C ou CS
      $majoNGAP="";
      //eko($lc);
			if ( substr($lc,0,1)=="C" and !$NGAPjoue ) { // C ou CS
        $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;
      }
      /*else {
        if ( !ereg ("AMI",$lc ) and 
             !ereg ("AIS",$lc ) and
             !ereg ("K",$lc   ) and
             !ereg ("KC",$lc  ) and
             !ereg ("PH",$lc  ) and

             !ereg ("MNO",$lc ) and // LISTE MAJO
             !ereg ("MGE",$lc ) and
             !ereg ("MCG",$lc ) and
             !ereg ("MCC",$lc ) and
             !ereg ("MPC",$lc ) and
             !ereg ("MCS",$lc ) and
             !ereg ("MPJ",$lc ) and
             !ereg ("MNP",$lc ) and
             !$NGAPjoue ) {
          $majoNGAP = $res[modificateurs][$i];
          $NGAPjoue = 1;
        }
      }*/
     
     // On gere le cas des actes infirmiers
     //LISTE MAJO pour les actes infirmiers
     if ( ereg ("AMI",$lc)   || 
          ereg ("AIS",$lc )  ||
          ereg ("K",$lc )    ||
          ereg ("KC",$lc )   ||
          ereg ("PH",$lc )   ||
          ereg ("INFN1",$lc) ||
          ereg ("INFN2",$lc )||
          ereg ("MINFD",$lc )
        ) {
      if ( strcmp($this->matriculeIDE,"") == 0 )
        if ( strcmp( $options->getOption("codeAdeliInfirmier"),"" ) == 0 )
          if ( strcmp( $this->matriculeIntervenant,"" ) == 0 ) {
            $matriculeIDE = $options->getOption("codeAdeliChefService");$nomIDE = $this->nomIntervenant;
            if ( !$NGAPjoue ) { $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;$Majoration_A_Effectuee = "Oui"; }
            }
          else {
            $matriculeIDE = $this->matriculeIntervenant;$nomIDE = $this->nomIntervenant;
            if ( !$NGAPjoue ) { $majoNGAP = $res[modificateurs][$i];$NGAPjoue = 1;$Majoration_A_Effectuee = "Oui"; }
            }  
        else {$matriculeIDE = $options->getOption("codeAdeliInfirmier");$nomIDE = $this->nomIDE;}
      else {$matriculeIDE = $this->matriculeIDE;$nomIDE = $this->nomIDE;}
        
      //Premiere version avec la fonction gestionMajorationsActesInfirmiers
      //                      et l'option EnvoiMajorationsActesInfirmiers
      /* 
      // Calcul des majo "NGAP" si on envoi pas les majo des "AMI" et "AIS"
      if ( !$options->getOption ('EnvoiMajorationsActesInfirmiers') ){
      //eko("majo ngap :".$lc);
      // Concernant les actes infirmiers
      // Soit on envoie les modificateurs pour les actes AMI et AIS
      // Soit on envoie les majorations des actes AMI et AIS
      // Mais pas les deux.
	  
      //Gestion des modificateurs de nuit et jours fériés pour las actes NGAP infirmiers
      //Gestion des tranches horaires pour las actes NGAP infirmiers
      if (($heure>=20 or ($heure>=6 and $heure<8))) $majoNGAP="N";
      elseif ($heure<6) $majoNGAP="NM";
    
      //Gestion des dimanches pour las actes NGAP infirmiers
      if (date("w",$dateActe)==0 and $majoNGAP=="") $majoNGAP="F";
    
      //Gestion des jours fériés pour las actes NGAP infirmiers
      $dateFerie=new clDate($dateActe);
      if ($dateFerie->isHoliday() and $majoNGAP=="") $majoNGAP="F";
        $modificateurs=$majoNGAP; 
      }
      */
      
    
    // Nous avons l'heure
    if ( strcmp($Majoration_A_Effectuee,"Non")==0 ) {   
        
        if ( ( $heure >=20 && $heure < 23 ) || ( $heure >= 5 && $heure <8 ) ){
          $MajorationNGAP1 = "N";$Majoration_A_Effectuee = "Oui";}
        if ( $heure >=23 || $heure < 5 ) {
          $MajorationNGAP1 = "NM";$Majoration_A_Effectuee = "Oui";}

        // pour les jours féries
        $dateFerie=new clDate($dateActe);

        if ( $dateFerie->isHoliday() && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1 = "F";$Majoration_A_Effectuee = "Oui";}
          
        // pour les dimanches
        if ( date("w",$dateActe)==0 && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1 = "F";$Majoration_A_Effectuee = "Oui";}
        
        // pour les samedis
        if ( date("w",$dateActe)==6 && $heure>=8 && strcmp($Majoration_A_Effectuee,"Non")==0 ) {
          $MajorationNGAP1 = "F";$Majoration_A_Effectuee = "Oui";}
        
        $majoNGAP = $MajorationNGAP1;
      
      }
      
      
      
      
      $contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||||$dateReal|$heureReal|".$nomIDE.
				"||".$matriculeIDE."|".$res[numUFexec][$i]."||$lc|$coeff|$factu||$majoNGAP";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
			//eko($contenu);
      // Mise à jour de la table ccam_cotation_acte
      if (strcmp($type,"creation") == 0 ) {
        $paramModif["nomIntervenant"]       = $nomIDE;
        $paramModif["matriculeIntervenant"] = $matriculeIDE;
        $requete    = new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	      $sql        = $requete->updRecord("idEvent=".$this->idEvent." and identifiant=".$res["identifiant"][$i]." and idDomaine=".CCAM_IDDOMAINE); 
        //eko($res["identifiant"][$i]);
        } 
      }
// if ( ereg ("AMI",$lc) || ereg ("AIS",$lc ) || ereg ("INFN1",$lc) || ereg ("INFN2",$lc ) || ereg ("MINFD",$lc )) {
     
    else {
      $contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||||$dateReal|$heureReal|".$res[nomIntervenant][$i].
				"||".$res[matriculeIntervenant][$i]."|".$res[numUFexec][$i]."||$lc|$coeff|$factu||$majoNGAP";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
				//eko($contenu);
			}
			
			unset($param);
			$param[DTINS]=date("Y-m-d H:i").":00";
			$param[ETAT]="P";
			$param[DISCR]=$res[idEvent][$i];
			$param[TYPE]="NGAP";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
		}
		
		unset($paramModif);
		$paramModif[validDefinitive]="O";
		$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
		$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
			" and idEvent=".$res[idEvent][$i]);
	} //if (substr($res[codeActe][$i],0,4)=="NGAP"
	
	// Cas des actes CCAM
  else{
		if ($res[type][$i]=="ACTE"){
      list($modificateurs1,$modificateurs4)=explode("|",$res[modificateurs][$i]);
      if ($modificateurs1){
        $modificateursCotes=explode("~",$modificateurs1);
        $modificateurs1="";
        while (list($key,$val)=each($modificateursCotes)){
          if ($val=="F" or $val=="P" or $val=="S"){
            if ((!$modificateursJoues[$val] and !$indicateurs["C"]) or 
                ($indicateurs["C"] and substr($res[codeActe][$i],0,3)=="DEQ")) $modificateurs1.="$val~";
            //eko("indicC:$indicateurs[C]-val:$val-modif1:$modificateurs1-modifJoueAvant:".$modificateursJoues[$val]);
          }
          else $modificateurs1.="$val~";
          $modificateursJoues[$val]=1;
          //eko("val:$val-modif1:$modificateurs1-modifJoue:".$modificateursJoues[$val]);
        }
        /*eko("codeActe:".$res[codeActe][$i]);
        eko($modificateursCotes);
        eko("modif1Final:$modificateurs1");
        eko($modificateursJoues);*/
        if ($modificateurs1) $modificateurs1=substr($modificateurs1,0,-1);
      }
      $modificateurs4="";
		
			list($intervenant1,$intervenant4)=explode("|",$res[matriculeIntervenant][$i]);
			list($nomIntervenant1,$nomIntervenant4)=explode("|",$res[nomIntervenant][$i]);
			$codeActe=$res[codeActe][$i];
      $codeAssociation=$tabAsso[$codeActe];
			
			$contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
				"||1|0|$dateReal|$heureReal|".$nomIntervenant1.
				"||".$intervenant1."|".$res[numUFexec][$i]."|".$modificateurs1.
				"|||oui|$codeAssociation";
			$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
			unset($param);
			$param[DTINS]=date("Y-m-d H:i").":00";
			$param[ETAT]="P";
			$param[DISCR]=$res[idEvent][$i];
			$param[TYPE]="CCAM";
			$param[CONTENU]=$contenu;
			$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
			$sql=$majrq->addRecord();
			
			if ($res[codeActivite4][$i]=="O"){
				/*//Recherche du nom de l'anesthésiste dans la liste
				unset($paramRq);
				$paramRq[code]=$intervenant4;
				$paramRq[idDomaine]=CCAM_IDDOMAINE;
				$paramRq[nomListe]="Anesthésie";
				$req=new clResultQuery;
				$res2=$req->Execute("Fichier","CCAM_getNomMed",$paramRq,"ResultQuery");
				//eko($res2[INDIC_SVC]);
				$nomMed4=$res2[nomItem][0];*/
				
				$contenuSuite="|$type|".$res[identifiant][$i]."|".$res[codeActe][$i].
					"||4|0|$dateReal|$heureReal|".$nomIntervenant4.
					"||".$intervenant4."|".$res[numUFexec][$i]."|".
					$modificateurs4."|||oui|$codeAssociation";
				$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
				unset($param);
				$param[DTINS]=date("Y-m-d H:i:")."00";
				$param[ETAT]="P";
				$param[DISCR]=$res[idEvent][$i];
				$param[TYPE]="CCAM";
				$param[CONTENU]=$contenu;
				$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
				$sql=$majrq->addRecord();
			}
			
			unset($paramModif);
			$paramModif[validDefinitive]="O";
			$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
			$sql=$requete->updRecord("codeActe='".$res[codeActe][$i]."' and idDomaine=".CCAM_IDDOMAINE.
				" and idEvent=".$res[idEvent][$i]);
		} //if ($res[type][$i]=="ACTE")
		// Cas du diagnostic
    else{
			$idEvent=$res[idEvent][$i];
      /*$numSejour=$res[numSejour][$i];
    	$idu=$res[idu][$i];
    	$nomu=$res[nomu][$i];
    	$pren=$res[pren][$i];
    	$sexe=$res[sexe][$i];
    	$dtnai=$res[dtnai][$i];
    	$numUFdem=$res[numUFdem][$i];*/
    	$numUFexec=$res[numUFexec][$i];
    	$nomIntervenant=$res[nomIntervenant][$i];
    	$matriculeIntervenant=$res[matriculeIntervenant][$i];
      ($cptDiag==1)?$sep="|":$sep="~";
			$contenuDiag.=$res[codeActe][$i].$sep;
			$cptDiag++;
		} // Cas du diagnostic
	} // Cas des actes CCAM
} //for ($i=0;isset($res[identifiant][$i]);$i++)

if ($contenuDiag){
	/*unset($paramRq);
	$paramRq[cw]="type='DIAG' and validDefinitive='O' and idEvent=".$res[idEvent][$i]." and idDomaine=".CCAM_IDDOMAINE;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
	//eko($res[INDIC_SVC]);
	if ($res[INDIC_SVC][2]!=0) $type="modification";*/
	
	$cptDiag--;
	if ($cptDiag==1) $sepDiag="|"; else $sepDiag="";
	$contenuDiag=substr($contenuDiag,0,-1);
	$contenuSuite="|$type|".$idEvent."|".$contenuDiag.$sepDiag.
		"|||$dateReal|$heureReal|".$nomIntervenant.
		"||".$matriculeIntervenant."|".$numUFexec."||||";
	$contenu=$contenuInit.$contenuInit2.$contenuInit3.$contenuSuite;
	unset($param);
	$param[DTINS]=date("Y-m-d H:i:")."00";
	$param[ETAT]="P";
	$param[DISCR]=$idEvent;
	$param[TYPE]="DIAG";
	$param[CONTENU]=$contenu;
	$majrq=new clRequete(CCAM_BDD,"MBTV2",$param);
	$sql=$majrq->addRecord();
	
	unset($paramModif);
	$paramModif[validDefinitive]="O";
	$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$paramModif);
	$sql=$requete->updRecord("type='DIAG' and idEvent=".$idEvent." and idDomaine=".CCAM_IDDOMAINE);
}
}


//Gestion des consultations spécialisées ===========================================================
function gestionConsult(){
global $session;
global $options ;
// Appel du template permettant la saisie des consultations spécialisées
$mod=new ModeliXe("CCAM_Consult.mxt");
$mod->SetModeliXe();

$mod->MxImage("imgQuitter",URLIMG."QuitterSansValider.gif");
$mod->MxUrl("lienQuitter",URLNAVI.$session->genNavi($session->getNavi(0),
	$session->getNavi(1),$session->getNavi(2)));

//Initialisation des valeurs
if (!$_POST['idListeSelection1']) $idListeSelection1="tous";
else $idListeSelection1=$_POST['idListeSelection1'];

($_POST['aDroite_x'] or $_POST['aDroite'])?$aDroite=1:"";
($_POST['sortir_x'] or $_POST['sortir'])?$sortir=1:"";
	
//Ajout des consultants sélectionnés dans la liste des consultants disponibles à la liste des consultants
//rattachés au patient en cours
if ($aDroite or $sortir){
	$retourInfos=$this->addConsultPatient();
	if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
	elseif ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
}

if (!$sortir){
	//Si on a choisi de supprimer une consultation
	if ($_POST['supprimerConsult_x'] or $_POST['supprimerConsult']){
		$idConsultSuppr=$_POST['supprimerConsult'];
		
		$this->infos=$this->delConsultPatient($idConsultSuppr);
	}
	
	if ($retourInfos[infos]) $this->infos=$retourInfos[infos];
	if ($retourInfos[erreur]) $this->erreurs=$retourInfos[erreur];
	
	//Récupération des valeurs pour Selection1
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$tabListeSelection1=$this->tableauValeurs("CCAM_getListeSpe",$param,"Choisir une spécialité");
	
	//Récupération des consult pour la liste de gauche en ignorant les valeurs de la liste de droite
	//en fonction de la spécialité sélectionnée dans Selection1
	unset($paramRelation);
	unset($paramA);
	$paramRelation[idDomaine]=CCAM_IDDOMAINE;
	$paramRelation[idEvent]=$this->idEvent;
	
	$paramA[idDomaine]=CCAM_IDDOMAINE;
	if ($idListeSelection1!="tous") $paramA[cw]="and nomListe='$idListeSelection1'";
	else $paramA[cw]="";
		
	$tabListeGauche=$this->valeursListeGauche("CCAM_getConsultListeGauche","CCAM_getConsultCotes",
		$paramA,$paramRelation,"Choisir un consultant");
		
		
	$mod->MxSelect("consultBlocGauche.listeGauche","listeGauche[]",'', $tabListeGauche,'','',
		"size=\"20\" multiple=\"yes\"");
	
	
	//Récupération des consult côtés pour le patient en cours
	unset($param);
	$param[idDomaine]=CCAM_IDDOMAINE;
	$param[idEvent]=$this->idEvent;
	$req=new clResultQuery;
	$res=$req->Execute("Fichier","CCAM_getConsultCotes",$param,"ResultQuery");
	//newfct(gen_affiche_tableau,$res[INDIC_SVC]);
	//eko($res);
	if (!$session->getDroit("CCAM_CONSULT_".$this->typeListe,"d")){
		$mod->MxBloc("consultBlocDroite.consultCotes.actionSuppr","delete");
	}
	
	if ($res[INDIC_SVC][2]==0) $mod->MxBloc("consultBlocDroite.consultCotes","delete");
	else{
		$mod->MxBloc("consultBlocDroite.aucunConsult","delete");
		for ($i=0;isset($res[identifiant][$i]);$i++){ 
			$mod->MxText("consultBlocDroite.consultCotes.speConsult",$res[spe][$i]);
			$mod->MxText("consultBlocDroite.consultCotes.nomConsultant",$res[libelle][$i]);
			$mod->MxText("consultBlocDroite.consultCotes.actionSuppr.idConsultSuppr",
				$res[codeActe][$i]);
			$mod->MxBloc("consultBlocDroite.consultCotes","loop");
			
		}
	}
	
	/*list($anneeNais,$moisNais,$jourNais)=explode("-",$this->dtnai);
	if ($this->lieuInterv=="0") $lieuInterv=""; else $lieuInterv=", ".$this->lieuInterv;
	$mod->MxText("infosPatient",$this->nomu." ".$this->pren.", né(e) le ".$jourNais."/".$moisNais."/".$anneeNais.
		$lieuInterv);*/
	$mod->MxText("infosPatient",$this->nomu." ".ucfirst(strtolower($this->pren)));
		
	$mod->MxText("titreDispo","Consultants disponibles");

	$mod->MxText("titreAffecte","Consultants affectés au patient");
		
	//Gestion du template
	$mod->MxSelect("idListeSelection1","idListeSelection1",$idListeSelection1,
		$tabListeSelection1,'','',"onChange=reload(this.form)");
	
	//Afficher les boutons suivants si droits autorisés
	if (!$session->getDroit("CCAM_CONSULT_".$this->typeListe,"w")){
		$mod->MxBloc("flDroite","delete");
		$mod->MxBloc("flSortir","delete");
	}
	
	
	//Ne jamais afficher les boutons suivants
	
	// Affichage ou non du champs d'informations.
	if ($this->infos) $mod->MxText("informations.infos",$this->infos);
	else $mod->MxBloc("informations","delete");
	
	// Affichage ou non du champs d'erreurs.
	if ($this->erreurs) $mod->MxText("erreurs.errs",$this->erreurs);
	else $mod->MxBloc("erreurs","delete");
		
	if ($sortir){
		$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),$session->getNavi(2)));
	}
	else{
		$mod->MxHidden("hidden","navi=".$session->genNavi($session->getNavi(0),
			$session->getNavi(1),$session->getNavi(2),"DetailConsult"));
	}
	
	return $this->af.=$mod->MxWrite("1"); 
}
}

//Ajout des consultations
function addConsultPatient(){
global $session;
global $options;

$tabSpecialite=$this->tabSpecialite();

if (is_array($_POST['listeGauche'])){
	while (list($key,$val)=each($_POST['listeGauche'])){ 
		if ($val and $val!="aucun#"){
			unset($param);
			$param[codeActe]=$this->getIdConsultSuiv();
			$param[idDomaine]=CCAM_IDDOMAINE;
			$param[idEvent]=$this->idEvent;
			$param[dateEvent]=$this->dateEvent;
			/*if (substr($this->lieuInterv,0,4)=="UHCD"){
        if (substr($this->dateEvent,0,10)==date("Y-m-d")) $param[dateDemande]=$this->dateEvent;
        else $param[dateDemande]=date("Y-m-d")." 08:00:00";
       } 
      else $param[dateDemande]=$this->dateEvent;*/
      $param[dateDemande]=$this->dateEvent;
      $param[dtFinInterv]=$this->dtFinInterv;
			$param[idu]=$this->idu;
			$param[ipp]=$this->ipp;
			$param[nomu]=$this->nomu;
			$param[pren]=$this->pren;
			$param[sexe]=$this->sexe;
			$param[dtnai]=$this->dtnai;
			$param[numSejour]=$this->nsej;
			$param[typeAdm]=$this->typeAdm;
			$param[numUFdem]=$this->numUFdem;
			$param[lieuInterv]=$this->lieuInterv;
			$param[type]="ACTE";
			$param[Urgence]="O";

			unset($paramRq);
			$paramRq[code]=$val;
			$paramRq[idDomaine]=CCAM_IDDOMAINE;
			//$paramRq[cw]="";
			$req=new clResultQuery;
			$res=$req->Execute("Fichier","CCAM_get1Consult",$paramRq,"ResultQuery");

			if ($res[spe][0]=="Psychiatrie") $param[cotationNGAP]="CNPSY 1";
			elseif ($res[spe][0]=="Cardiologie") $param[cotationNGAP]="CSC 1";
			elseif ($res[spe][0]=="CARDIOLOGIE") $param[cotationNGAP]="CSC 1";
			else $param[cotationNGAP]="CS 1";
			$libelleSpe=$res[spe][0];
      
      $param[libelleActe]="Consultation spécialisée (".$libelleSpe.")";
			$param[matriculeIntervenant]=$val;
			$param[nomIntervenant]=$res[nomInterv][0];
			
			global $logs ;
			$logs -> addLog ( "actes", $session->getNaviFull ( ), "Ajout CS '".$param[codeActe]."' : $val" ) ;
			

      if ($libelleSpe){
        if ($tabSpecialite["$libelleSpe"]) $param[numUFexec]=$tabSpecialite["$libelleSpe"];
        else $param[numUFexec]="3301";
      }
      else $param[numUFexec]="3301";
			
			$majrq=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
			$sql=$majrq->addRecord();
			
			//Ecriture dans la BAL
			//if ($this->typeListe=="Sortis") $this->writeBAL($param[codeActe],"creation");
		}
	}
}
}

function tabSpecialite(){
unset($paramRq);
$paramRq[cw]="libelleSpecialite!=''";
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getUFspe",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
unset($tabSpecialite);
for ($i=0;isset($res[libelleSpecialite][$i]);$i++){
  $libelle=$res[libelleSpecialite][$i];
  $tabSpecialite["$libelle"]=$res[numeroUF][$i];
}
return $tabSpecialite;
}

function getIdConsultSuiv(){
//Retourne la valeur suivante du plus grand code commençant CONS...
$req=new clResultQuery;
$typeCode="CONS";
unset($param);
$param[idDomaine]=CCAM_IDDOMAINE;
$param[idEvent]=$this->idEvent;
$param[matricule]=$this->matriculeIntervenant;
$res=$req->Execute("Fichier","CCAM_getMaxCodeCons",$param,"ResultQuery");
$maxCodeRq=$res[cons_max][0];
if ($maxCodeRq!=""){
	$derniers=substr($maxCodeRq,4,3);
	if (substr($derniers,0,2)=="00") $maxCode=substr($derniers,2,1)+1;
	elseif (substr($derniers,0,1)=="0") $maxCode=substr($derniers,1,2)+1;
	else $maxCode=substr($derniers,0,3)+1;
	
	if ($maxCode<10) $maxCode="00".$maxCode;
	elseif ($maxCode<100) $maxCode="0".$maxCode;
	$maxCode=$typeCode.$maxCode;
}
else $maxCode=$typeCode."001";
return $maxCode;
}

//Suppression de la consultation sélectionnée
//dans la liste des consultations affectées au patient en cours
function delConsultPatient($idActe){
global $session;
unset($retourInfos);

//Ecriture de la suppression dans la BAL si le patient est à nouveau rentré dans la liste des présents
/*unset($paramRq);
$paramRq[cw]="codeActe='$idActe' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE;
$req=new clResultQuery;
$res=$req->Execute("Fichier","CCAM_getActesDiagsCotation",$paramRq,"ResultQuery");
//eko($res[INDIC_SVC]);
if ($res[validDefinitive][0]=="O") $this->writeBAL($idActe,"suppression");*/

//Suppression de l'enregistrement dans la table des cotations
$requete=new clRequete(CCAM_BDD,"ccam_cotation_actes",$param);
$sql=$requete->delRecord("codeActe='$idActe' and idEvent=".$this->idEvent." and idDomaine=".CCAM_IDDOMAINE);

	global $logs ;
	$logs -> addLog ( "actes", $session->getNaviFull ( ), "Suppression CS '$idActe'" ) ;
			
$retourInfos="La consultation sélectionnée n'est plus affectée au patient en cours";

return $retourInfos;
}


// Retourne l'affichage de la classe.
function getAffichage(){
return $this->af ;
}

/*
Auteur : Alain Falanga (a.falanga@ch-brignoles.fr)
Date      : 07/12/06
Rev       : 1 - Création
Paramètres: Aucun
Résultats : Nombre de "mouvement" acte externe saisi pour un patient
Description :
Verifyactesession, cette fonction retourne le nombre d'actes
rattachés à un utilisateur (médecin), pour un patient et une session php donnée
L'objectif est de savoir si une saisie a été faites par un outil de codage externe
*/
function verifyactesession ( ) {
  	global $session ;
  	global $options ;
  	// ouverture de la connexion
  	$conn_chb = mysql_connect ( MYSQL_HOST, MYSQL_USER, MYSQL_PASS ) ;
    // Préapartion et execution de la requete
    $result = mysql_select_db ( 'tu' ) ;
    $result = mysql_query ( "SELECT * FROM tu.sessioncora WHERE idmedecin='".$session->getUid()."' AND idsessiontu=".$this->idEvent." AND idphpsession='".session_id()."'" ) ;
    // Obtention du nombre de "mouvement" acte
  	$nrows = mysql_num_rows ( $result ) ;
  	// Fermeture de la connexion
  	mysql_close ( $conn_chb ) ;
  	return $nrows;
}


}
