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
		$delta = $obDatePecIoa->getDifference($obDateDebutSymptNeuro)/3600;
	if($formx->getFormVar('is_heure_debut_sympto') == 'oui') {
		$delta = $obDatePecIoa->getDifference($obDateDebutSympt)/3600;
		
	}
	if($formx->getFormVar('is_heure_debut_sympto') == 'non')
		$delta = $obDatePecIoa->getDifference($obDateDebutVueAsympt)/3600;

	$delta = max(0,$delta);

	$formx->setVar('der_delta_avc', $delta);

	return $delta ;
	
}