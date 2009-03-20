<?php

abstract class caToolsBase {
	/*
	 * readTemplate , moteur de templates miniature
	 * @param string nom , nom de la template ( sans le .tpl.php )
	 * @data array tableaux de variables � passer � la template
	 * @return string rendu html 
	 */
	public static function readTemplate($nom,$data = array() )
	{

		//si template sp�cifique pour le site:
		if(file_exists(caTools::getSiteUrlTemplates().$nom.'.tpl.php'))
			$file = caTools::getSiteUrlTemplates().$nom.'.tpl.php';
		else //Templates par defaut
			$file = caTools::getUrlTemplates().$nom.'.tpl.php';
		//instanciation des valeurs � passer � la template	
		if(! is_array($data)) $data = array() ;
		foreach($data as $var => $val) $$var = $val ;
		ob_start(); // start buffer
		include ($file);
		$content = ob_get_contents(); // assign buffer contents to variable
		ob_end_clean(); // end buffer and remove buffer contents
		return $content;
	}	
	/*
	 * loadHelper charge un helper symfony (fonctions de mise en forme html par packages)
	 */
	function loadHelper($helper)
	{
		require_once(caEnvironnement::getBaseUrl()."base/sfHelpers/{$helper}Helper.php");
	}
	
	/*
	 * getUrlTemplates renvoie l'url local o� se trouvent les templates de base
	 * @return string url
	 */
	function getUrlTemplates() 
	{
		return caEnvironnement::getBaseUrl().'/base/templates/';
	}
	/*
	 * getSiteUrlTemplates � h�riter : renvoie l'url local o� se trouvent les templates sp�cialis�es par site
	 * @return string url
	 */
	abstract static public function getSiteUrlTemplates(); //ex :caEnvironnement::getBaseUrl().'/raToulon/templates/';;
	
} 