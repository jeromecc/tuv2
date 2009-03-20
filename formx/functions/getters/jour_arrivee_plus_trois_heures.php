<?php
function jour_arrivee_plus_trois_heures($formx) {
	global $patient ;
	$dateArrivee = new clDate($patient->getDateAdmission());
	$dateArrivee->addHours(3);
	return $dateArrivee->getSimpleDate();
}