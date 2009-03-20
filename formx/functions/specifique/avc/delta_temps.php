<?php

function delta_temps($formx) {
	global $patient ;
	
	//si heure PEC inconnue:
	
	//if(! $formx->getValueForm('date_pec_urgences') )
	//	return 99;
	
	
	$obDatePecIoa = new clDate($formx->getFormVar('date_pec_urgences').' '.$formx->getFormVar('heure_pec_urgences'));
	$obDateDebutSympt = new clDate($formx->getFormVar('debut_symptomes_jour').' '.$formx->getFormVar('debut_symptomes_heure'));
	$obDateDebutSymptNeuro = new clDate($formx->getFormVar('debut_symptomes_neuro_jour').' '.$formx->getFormVar('debut_symptomes_neuro_heure'));
	$obDateDebutVueAsympt = new clDate($formx->getFormVar('date_asympt').' '.$formx->getFormVar('heure_asympt'));
	
	if($formx->getFormVar('is_heure_debut_symptoneuro') == 'oui')
		return floor($obDatePecIoa->getDifference($obDateDebutSymptNeuro)/3600);
	if($formx->getFormVar('is_heure_debut_sympto') == 'oui') {
		return floor($obDatePecIoa->getDifference($obDateDebutSympt)/3600);
		
	}
	if($formx->getFormVar('is_heure_debut_sympto') == 'non')
		return floor($obDatePecIoa->getDifference($obDateDebutVueAsympt)/3600);	
	
	
}