<?php
class clExempleExportFormx {

function getAffichage() {
	

	//Recuperation seulement de la donn�e du nom prescipteur dans tous les formulaires radio

	$tab = clFoRmXtOoLs::getinstances('Formulaire_Radio','Val_F_RADIO_Nom_P','');
	$nbResultats = $tab['INDIC_SVC'][2] ;
	$res = array();
	for($i=0;$i< $nbResultats;$i++) {
		$precripteur = $tab['Val_F_RADIO_Nom_P'][$i] ;
		$res[$precripteur]++;
	}
	return affTab($res); 

	
	
	
	//AUTRES EXEMPLES
	
	//Recuperation de toutes les donn�es radio de la date $date1 � la date $date2
	//clFoRmXtOoLs::getinstances('Formulaire_Radio','','',$date1="",$date2="") 
	
	//Recuperation de toutes les donn�es de tous les formulares demandes radio
	//$tabAllDonnesRadio = clFoRmXtOoLs::getinstances('Formulaire_Radio');

	}
	
}

?>