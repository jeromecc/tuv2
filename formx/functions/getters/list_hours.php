<?php

//renvoie une liste d'heures pour un select
global $formxSession ;
$formxSession->loadFunc('two_dec');

function list_hours() {
	$res = array();
	for($h=0;$h<24;$h++) {
		for($m=0;$m<60;$m=$m+15) {
			$val = two_dec($h).':'.two_dec($m);
			//$val= $h.':'.$m;
			$res[$val]=$val ;
		}
	}	
	return $res ;
}