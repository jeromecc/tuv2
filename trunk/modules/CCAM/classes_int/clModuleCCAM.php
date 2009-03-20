<?php
// Titre  : Classe ModuleCCAM
// Auteur : Christophe Boulay <cboulay@ch-hyeres.fr>
// Date   : 03 Mars 2005
// Description : Classe initiale du module CCAM
//	Affiche le menu CCAM et ses options

class clModuleCCAM{
// Attribut contenant l'affichage.
private $af;

function __construct(){
global $session;
global $options;

include (MODULE_CCAM."ccam_define.php");

switch ($session->getNavi(1)){
	case 'CCAM_CtrlVersionCCAM':
    $ctrlValiditeActes=new clCCAMCtrlActesVersion();
		$this->af.=$ctrlValiditeActes->getAffichage();
  break;
  case 'CCAM_TarifsNGAP':
    $tarifsNGAP=new clCCAMTarifsNGAP();
		$this->af.=$tarifsNGAP->getAffichage();
  break;
  case 'CCAM_Decoupage':
    	$gestionAnatomie=new clCCAMGestionActesDiags();
		$this->af.=$gestionAnatomie->getAffichage();
  break ;
	case 'CCAM_Listes':
    $listeRestreinte=new clCCAMListeRestreinte();
		$this->af.=$listeRestreinte->getAffichage();
	break;
	case 'CCAM_ListesPredefinies':
    	$listesPredefinies=new clCCAMListesPredefinies();
	 	$this->af.=$listesPredefinies->getAffichage();
	break;
  	case 'CCAM_Packs':
    	$gestionPacks=new clCCAMGestionPacks();
		$this->af.=$gestionPacks->getAffichage();
    break;
	case 'CCAM_CopierPacks':
    	$copierPacks=new clCCAMCopierPack();
		$this->af.=$copierPacks->getAffichage();
    break;
   case 'CCAM_Diagnostics':
        $listeDiags=new clCCAMListesComplexes("Diagnostics");
	        $this->af.=$listeDiags->getAffichage();
    break ;
 	case 'CCAM_Medecins':
    	$listeMedecins=new clCCAMListesComplexes("ListeMédecins");
	    $this->af.=$listeMedecins->getAffichage();
   	break ;
  case 'CCAM_GestionAnesthesistes':
    $listeRestreinte=new clCCAMGestionAnesthesiste();
		$this->af.=$listeRestreinte->getAffichage();
	break;
   	case 'ActesRadio':
   		$listeRadio=new clCCAMListeRadio();
		$this->af.=$listeRadio->getAffichage();
   	break;
  	default:
	    $this->af.="Module Administration de la CCAM";
    break;
}
}

function getAffichage(){
return $this->af;
}
}
