<?php

global $formxSession ;
$formxSession->loadFunc('two_dec');

//transforme toute chaine de date en hh:mm
function heure_simple($str) {
	$date = new clDate($str);
	$hours=$date->getHours();
	$mins = 15*floor($date->getMinutes()/15);
	return two_dec($hours).':'.two_dec($mins);
	
}