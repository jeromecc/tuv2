<?php

//met un zéro devant les unités
function two_dec ($str) {
	if( strlen((string) $str) == 1 )
	return '0'.$str ;
	return $str;
}