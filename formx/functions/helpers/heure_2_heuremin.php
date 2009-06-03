<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function heure_2_heuremin($str)
{
	return floor((float) $str).'h'.floor(60*fmod((float) $str,1)).'min' ;
}

?>
