<?php

/*
 * CaControllerBase est le controleur: il traite la requete envoyée par l'utilisateur ou l'automate
 * Puis il appelle les classes template qui fourniront l'affichage
 */

 
 abstract class caControllerBase 
 {
 	
 	/*
 	 * executeRequest
 	 * @param string nom du sous module actes à appeller
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