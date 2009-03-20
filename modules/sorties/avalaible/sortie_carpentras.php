<?php

function sortie_carpentras(clPatient $patient)
{
	//suppression des instances de formulaires
	$res =clFoRmXtOoLs::ListFromIds($patient->getIDU());

 	if ( is_array ($res)  &&  $res['INDIC_SVC'][2]  > 0 ) {
 		for ($i=0;$i<$res['INDIC_SVC'][2];$i++) {
 			if ( in_array( $res['idformx'][$i] , array('tutorial','bachibouzouk','arrosoir')) ) { //mettre ici les id de formulaires à effacer
 				$idInstance = $res['id_instance'][$i] ;
 				formxTools::simpleRemoveInstance($idInstance);
 			}
 		}
 	}
 	
 	//suppression des variables globales
 	$pGlobals = formxTools::globalsLoad($patient->getIDU());
 	formxTools::globalsDelVar($pGlobals,'pipou');
 	formxTools::globalsDelVar($pGlobals,'pipoux');
 	formxTools::globalsDelVar($pGlobals,'papux');
 	formxTools::globalsDelVar($pGlobals,'gnafouix');
 	formxTools::globalsSave($pGlobals);
 	
}

?>