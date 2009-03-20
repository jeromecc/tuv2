<?php

global $formxSession ;
$formxSession->loadFunc('two_dec');

function heure_now() {
	$date = new clDate('');
	$hours=$date->getHours();
	$mins = 15*floor($date->getMinutes()/15);
	return two_dec($hours).':'.two_dec($mins);
}