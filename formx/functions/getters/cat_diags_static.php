<?php
/* 
 * l'item avec les categories doit s'appeller diag_categorie
 * l'item avec les diags doit s'appeller diag_code
 */

global $formxSession ;
//$formxSession->loadFunc('diags_static');
require("diags_static.php");



/**
 *
 * @param clFoRmX $formx
 */
function cat_diags_static(clFoRmX $formx)
{
	$tabDiags = getTabThesauDiags() ;
	$tabRetour = array() ;
	foreach ( $tabDiags as $tabDiag )
	{
		$cat = $tabDiag['nomliste'];
		if ( ! isset($tabRetour[$cat]) )
		{
			$tabRetour[utf8_encode($cat)] = $cat ;
		}
	}
	return $tabRetour ;
}

?>
