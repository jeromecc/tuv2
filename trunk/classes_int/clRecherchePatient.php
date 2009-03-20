<?php

// Titre  : Classe RecherchePatients
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 22 Mai 2007

// Description : 
// Affichage de listes patients.

class clRecherchePatient {

  	// Constructeur.
  	function __construct ( $ajax='' ) {
 		$this->ajax = $ajax ;
 		$this->genListePatientsSortis ( ) ;
  	}

  	// Fabrication de la liste des patients.
  	function genListePatientsSortis ( ) {
  		if ( $_REQUEST['nom'] OR $_REQUEST['prenom'] ) {
  		//$filtre = "WHERE nom LIKE '".$_POST['nom']."%' AND prenom LIKE '".$_POST['prenom']."%' AND sexe='".$_POST['sexe']."'" ;
  		$filtre = "WHERE nom LIKE '".$_POST['nom']."%' AND prenom LIKE '".$_POST['prenom']."%'" ;
  		$req = new clResultQuery ;
  		$param['table'] = "patients_sortis" ;
  		$param['cw'] = $filtre ;
  		$res = $req -> Execute ( "Fichier", "getPatients", $param, "ResultQuery" ) ;
  		
  		$this->genListe ( $res ) ;
  		
		if ( $res['INDIC_SVC'][2] > 1 ) $this->af .= "<br><i>".$res['INDIC_SVC'][2]." résultats en ".sprintf('%.2f',$res['INDIC_SVC']['temps'])." sec</i><br>" ;
  		else $this->af .= "<br><i>".$res['INDIC_SVC'][2]." résultat en ".sprintf('%.2f',$res['INDIC_SVC']['temps'])." sec</i><br>" ;
  		
  		//print affTab ( $res['INDIC_SVC'] ) ;
  		} else {
  			$this->af = "<font color=red>Seulement le nom et le prénom sont utilisés dans la
recherche.</font>
<br/>Vous devez entrer quelques lettres du nom ou du prénom pour voir les résultats." ;
  		}
  	}
  	
  	// Génération de la liste affichée.
  	function genListe ( $res ) {
		// Chargement du template ModeliXe.
    	$mod = new ModeliXe ( "recherchePatients.html" ) ;
    	$mod -> SetModeliXe ( ) ;
  		for ( $i = 0 ; isset ( $res['idpatient'][$i] ) AND $i < 29 ; $i++ ) {
  			$dateN = new clDate ( $res['dt_naissance'][$i] ) ;
  			$mod -> MxText ( "patient.ib", clPatient::genInfoBulle ( $res, $i )." onClick=\"document.forms.creationPatient.nom.value='".$res['nom'][$i]."';" .
  					"document.forms.creationPatient.prenom.value='".$res['prenom'][$i]."';" .
					"document.forms.creationPatient.sexe.value='".$res['sexe'][$i]."';" .
					"document.forms.creationPatient.naissance.value='".$dateN->getDate ( "d/m/Y")."';" .
					"document.forms.creationPatient.adresse.value='".$res['adresse_libre'][$i]."';" .
					"document.forms.creationPatient.cp.value='".$res['adresse_cp'][$i]."';" .
					"document.forms.creationPatient.ville.value='".$res['adresse_ville'][$i]."';" .
					"document.forms.creationPatient.telephone.value='".$res['telephone'][$i]."';" .
					"" .
					"" .
					"\"" ) ;
  			$mod -> MxText ( "patient.nom", $res['nom'][$i] ) ;
  			$mod -> MxText ( "patient.prenom", $res['prenom'][$i] ) ;
  			$mod -> MxText ( "patient.naissance", $dateN->getDate ( 'd/m/Y' ) ) ;
  			if ( strlen ( $res['adresse_libre'][$i] ) > 46 ) $trr = '...' ; else $trr = '' ;
  			$mod -> MxText ( "patient.adresse", substr($res['adresse_libre'][$i],0,46).$trr ) ;
  			$mod -> MxBloc ( "patient", "loop" ) ;
  		}
  		$this->af .= $mod -> MxWrite ( "1" ) ;
  	}

	

  	// Renvoie l'affichage généré par la classe.
  	function getAffichage ( ) {
    	if ( $this->ajax ) { 
    		global $stopAffichage ;
    		$stopAffichage = 1 ; 
    		print $this->af ;
    	} else return $this->af ;
  	}
}

?>