<?php

//met un z�ro devant les unit�s
function two_dec ($str) {
	if( strlen((string) $str) == 1 )
	return '0'.$str ;
	return $str;
}