<?php

/*
 * Extension de la classe smarty pour environnement xham
 * 04/06, Emmanuel Cervetti
 */
//$xham->errs->startCatch(); 
//$xham->errs->stopCatch();

if (! class_exists("Smarty")) {
	$conv = array (" var "=>" public ","Smarty_Compiler.class.php" => "Smarty_Compiler.class.php5");
	$s = strtr( file_get_contents(URLLOCAL.'classes_ext/smarty/libs/Smarty.class.php') , $conv);
	file_put_contents(URLLOCAL.'classes_ext/smarty/libs/Smarty.class.php5',$s);
	require(URLLOCAL.'classes_ext/smarty/libs/Smarty.class.php5');
	$conv = array (" var "=>" public ");
	$s = strtr( file_get_contents(URLLOCAL.'classes_ext/smarty/libs/Smarty_Compiler.class.php') , $conv);
	file_put_contents(URLLOCAL.'classes_ext/smarty/libs/Smarty_Compiler.class.php5',$s);
}

class XSmarty extends Smarty {
public $compile_dir;
public $cache_dir;
public $config_dir;

//constructeur
 function XSmarty() {
	$this->compile_dir = URLLOCAL.'classes_ext/smarty/templates_c';
	$this->cache_dir = URLLOCAL.'classes_ext/smarty/cache';
	$this->config_dir = URLLOCAL.'classes_ext/smarty/config_smarty';		
	Smarty::Smarty();	
 }

//modif du rep par defaut de smarty selon templates int ou templates ext
 function smarty4xham_loadTemplate($resource_name) {
	global $errs;
	global $xham;
	if(! is_object($errs))
		$errs = $xham ;
	if(file_exists(URLLOCAL."templates_int/$resource_name")) {
		$this->template_dir = URLLOCAL.'templates_int';
	}elseif(file_exists("templates_gen/$resource_name")) {
		$this->template_dir = URLLOCAL.'templates_gen/';
	} else {
	$errs->addErreur("smarty::Impossible de trouver le fichier $resource_name")	;
	}
 }
	
 function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false) {
	$this->smarty4xham_loadTemplate($resource_name);
	return Smarty::fetch($resource_name,$cache_id,$compile_id,$display);
 }

 function display($resource_name, $cache_id = null, $compile_id = null, $display = false){
	$this->smarty4xham_loadTemplate($resource_name);
	return Smarty::display($resource_name,$cache_id,$compile_id,$display);
 }
 
 
 function listKicker() {
 	
 	
 }
 
 

}

?>
