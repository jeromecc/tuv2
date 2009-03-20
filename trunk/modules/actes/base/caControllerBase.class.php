<?php

/*
 * CaControllerBase est le controleur: il traite la requete envoy�e par l'utilisateur ou l'automate
 * Puis il appelle les classes template qui fourniront l'affichage
 */

 
 abstract class caControllerBase 
 {
 	
 	/*
 	 * executeRequest
 	 * @param string nom du sous module actes � appeller
 	 * @param string action ou parametre
 	 */
 	public static function executeRequest($module,$action='') {
 	 switch($module) {
 	 default:
 		eval( " return caControllerBase::execute$module(\$action) " );	
 	 }
 	}
 	
 	/*
 	 * sous module helloWord
 	 * test dev
 	 */	
 	 public static function executeHelloWord($action='') 
 	 {
 	 	return "hello word";	
 	 }
 	
 } 