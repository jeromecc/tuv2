<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function libelle_sexe($str)
{
	switch($str)
	{
		case 'M':
			return "Masculin";
		case 'F':
			return "F�minin";
		case 'I':
			return "Ind�termin�";
		default:
			return $str;
	}
}