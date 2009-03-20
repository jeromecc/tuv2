<?php

abstract class caToolsBase {
	/*
	 * readTemplate , moteur de templates miniature
	 * @param string nom , nom de la template ( sans le .tpl.php )
	 * @data array tableaux de variables à passer à la template
	 * @return string rendu html 
	 */
	public static function readTemplate($nom,$data = array() )
	{

		//si template spécifique pour le site:
		if(file_exists(caTools::getSiteUrlTemplates().$nom.'.tpl.php'))
			$file = caTools::getSiteUrlTemplates().$nom.'.tpl.php';
		else //Templates par defaut
			$file = caTools::getUrlTemplates().$nom.'.tpl.php';
		//instanciation des valeurs à passer à la template	
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
	 * getUrlTemplates renvoie l'url local où se trouvent les templates de base
	 * @return string url
	 */
	function getUrlTemplates() 
	{
		return caEnvironnement::getBaseUrl().'/base/templates/';
	}
	/*
	 * getSiteUrlTemplates à hériter : renvoie l'url local où se trouvent les templates spécialisées par site
	 * @return string url
	 */
	abstract static public function getSiteUrlTemplates(); //ex :caEnvironnement::getBaseUrl().'/raToulon/templates/';;
	
} 