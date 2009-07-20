<?php

/**
 * contrainte sur les diagnostics interdits
 * @param clPatient $obPatient
 * @return array
 */
function contraintes_enquetes(clPatient $obPatient)
{
	//on checke si la sortie renvoie des messages bloquants
	return clTuFormxTrigger::getWatcher($obPatient)->checkOnOut() ;

}
