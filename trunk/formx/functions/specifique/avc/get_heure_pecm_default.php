<?php
global $formxSession ;
$formxSession->loadFunc('two_dec');

function get_heure_pecm_default($formx) {
	global $patient ;
	$dateArrivee = new clDate( $patient->getDateAdmission() );
	$dateArrivee->addHours(1);
	$hours=$dateArrivee->getHours();
	$mins = 15*floor($dateArrivee->getMinutes()/15);
	return two_dec($hours).':'.two_dec($mins);
} 