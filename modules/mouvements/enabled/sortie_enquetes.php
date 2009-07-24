<?php

function sortie_enquetes(clPatient $patient)
{
	global $options ;

	$packageList = new clListesGenerales ;
	$tabMailConflits = $packageList-> getListeItems('Mails Conflit',true) ;
	$params = array( 'mailConflits' => $tabMailConflits );
	clTuFormxTrigger::getWatcher($patient)->launchActionsOnOut($params) ;
}
