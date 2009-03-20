<?php

global $formxSession ;
$formxSession->loadFunc('two_dec');

function jour_moins_trois_heures() {
	$date = new clDate('');
	$date->addHours(-3);
	return $date->getSimpleDate();
}