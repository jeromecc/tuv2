<?php

function sortie_carpentras(clPatient $patient)
{
	//suppression des instances de formulaires
	$res =clFoRmXtOoLs::ListFromIds($patient->getIDU());

 	if ( is_array ($res)  &&  $res['INDIC_SVC'][2]  > 0 ) {
 		for ($i=0;$i<$res['INDIC_SVC'][2];$i++) {
			//formulaires à effacer
 			if ( in_array( $res['idformx'][$i] , array('CHC_Fiche_Administrative','CHC_Fiche_Examen_Medical','CHC_Fiche_IAO-IDE','CHC_Fiche_Suivi')) ) { //mettre ici les id de formulaires à effacer
 				$idInstance = $res['id_instance'][$i] ;
 				formxTools::simpleRemoveInstance($idInstance);
 			}
			//formulaires à cloturer
			if ( in_array( $res['idformx'][$i] , array('CHC_Synthese')) ) { //mettre ici les id de formulaires à effacer
 				$idInstance = $res['id_instance'][$i] ;
				$formx = new clFoRmX_manip($patient->getIDU(),'NO_POST_THREAT');
				$formx->loadInstance($idInstance);
				$formx->close();
 			}
 		}
 	}

	//suppression des variables globales
	$tabVariables = array( 'Fiche_Administrative_Rep_Legal' , 'ALD' , 'Fiche_Administrative_Pansement' , 'Fiche_Administrative_Non_Venu' ,
'Fiche_Administrative_Orientation' , 'Fiche_Administrative_Etablissement' , 'Fiche_Administrative_Documents' ,
'Fiche_Administrative_AT' , 'Fiche_Administrative_date_AT' , 'Fiche_Administrative_heure_AT' ,
'Fiche_Administrative_minute_AT' , 'IDE_CTE_ARRIVEE' , 'DATE_CTE_ARRIVEE' , 'HEURE_CTE_ARRIVEE' , 'PA_BG_CTE_ARRIVEE' ,
'PA_BD_CTE_ARRIVEE' , 'FC_CTE_ARRIVEE' , 'TEMP_CTE_ARRIVEE' , 'HB_CTE_ARRIVEE' , 'SAO2_CTE_ARRIVEE' , 'DEXTRO_CTE_ARRIVEE' ,
'EVA_CTE_ARRIVEE' , 'FR_CTE_ARRIVEE' , 'ACTES_CTE_ARRIVEE' , 'Fiche_IAO_Nom_IDE' , 'Fiche_IAO_Jour_Consultation' ,
'Fiche_IAO_Heure_Consultation' , 'Fiche_IAO_Accompagnant' , 'Fiche_IAO_qui' , 'Fiche_IAO_lettre_medecin' , 'Fiche_IAO_ordonnance' ,
'Fiche_IAO_Traitement' , 'Fiche_IAO_VAT' , 'Fiche_IAO_Tetanos' , 'Fiche_IAO_Bandelette_Urinaire' , 'IAO_Glucose' , 'IAO_Acetone' , 'IAO_Sang' ,
'IAO_PH' , 'IAO_Proteine' , 'IAO_Nitrite' , 'IAO_Leuco' , 'Fiche_IAO_Actes' , 'DATE_EXAM' , 'HEURE_EXAM' , 'ACTES_CTE_ARRIVEE' , 'ATCD' ,
'ALLERGIES' , 'TRAITEMENTS' , 'OBSERVATIONS_MEDICALES' );

	$obGlobals = formxTools::globalsLoad($patient->getIDU());
	foreach($tabVariables as $variable)
	{
		$obGlobals->del($variable);
	}
	$obGlobals->save();
 	
}

?>
