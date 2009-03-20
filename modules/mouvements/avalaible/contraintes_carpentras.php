<?php

function contraintes_carpentras(clPatient $obPatient) 
{

	//on parcourt les formulaires fusions carpentras jusqu'à en trouver un avec le  IDPASS en cours
	foreach( formxTools::peerGetFromIdIdu('CHC_Synthese',$obPatient->getIDU()) as /* @var clFoRmX */ $formx )
	{
		if ( $formx->getFormVar('Val_IDENT_NSEJPatient') == $obPatient->getNSej() )
			return array( 'isContrainte' => false );
	}
	return array( 'isContrainte' => true , 'titreContrainte' => "Formulaire Synthèse" , 'messageContrainte' => "Vous devez créer un formulaire de synthèse." ) ;	
}
