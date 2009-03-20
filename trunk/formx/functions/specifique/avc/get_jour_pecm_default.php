<?php
function get_jour_pecm_default($formx) {
	global $patient ;
	$dateArrivee = new clDate($patient->getDateAdmission());
	$dateArrivee->addHours(1);
	return $dateArrivee->getSimpleDate();
} 