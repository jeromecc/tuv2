<?php

/* Classe tellement honteuse que j'ai décidé de ne pas marquer mon nom, na ! */

class clExportFormulaire {

	// Constructeur
	function __construct ( ) {
		$this->genAffichageCS ( ) ;
		
	}
	
	// Affichage des CS avec du joli code HTML mélangé au code PHP, excellent !
	function genAffichageCS ( ) {
		global $session ;
		// Mise en page, arg !
		$af = "<div style=\"padding: 20px;\"><h4>Liste des consultations spécialisées par spécialiste :</h4><br/>" ;
		// Récupération de l'ensemble des formulaires des CS
		$tab = clFoRmXtOoLs::getinstances('formulaire_consultation_specialisee','','','2009-01-01','2009-12-31');
		$nbResultats = $tab['INDIC_SVC'][2] ;
		//eko ( $tab['INDIC_SVC'] ) ;
		//return ;
		$res = array();
		// Récupération de la liste des médecins ayant fait des CS
		for($i=0;$i< $nbResultats;$i++) {
			$med = $tab['Val_F_CS_Spe'][$i] ;
			// Compteur de CS par médecin
			$res[med]++;
		}
		// Tri par ordre décroissant sur le nombre de CS réalisées
		array_multisort ( $res, SORT_DESC ) ;
		// Pour chaque médecin, on affiche un lien pour afficher ses CS
		while ( list ( $key, $val ) = each ( $res ) ) {
			if ( $key and $key != '#' ) {
				$af .= '<a href="?navi='.$session->genNaviFull().'&amp;NOMMED='.$key.'">'.$key.' (<font color="red">'.$val.'</font>)'.'</a><br/>' ;
				// Si le lien a été cliqué, on affiche les CS du médecin.
				if ( $_GET['NOMMED'] == $key ) {
					// Encore de la mise en page bien sale.
					$af .= '<div style="padding-left: 20px;">' ;
					// Affichage des CS du médecin.
					for($i=0;$i< $nbResultats;$i++) {
						if ( $_GET['NOMMED'] == $tab['Val_F_CS_Spe'][$i] ) {
							$af .= ' - '.$tab['Val_F_CS_Date'][$i].' : '.$tab['Val_IDENT_NomPatient'][$i].' '.$tab['Val_IDENT_PrenomPatient'][$i].' ('.$tab['Val_IDENT_IDUPatient'][$i].', Séjour : '.$tab['Val_IDENT_NsejPatient'][$i].') demandée par '.$tab['Val_F_CS_Nom_P'][$i].'<br/>' ;
						}
					}
					$af .= '</div>' ;
				}
			}
		}
		$af .= "</div>" ;
		$this-> af = $af ;
	}

	// Retourne l'affichage généré par la classe.
	function getAffichage ( ) {
		return $this->af ;
	}
	
}

?>
