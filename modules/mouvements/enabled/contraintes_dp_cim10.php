<?php

/**
 * contrainte sur les diagnostics interdits
 * @param clPatient $obPatient
 * @return array
 */
function contraintes_dp_cim10(clPatient $obPatient)
{
	
	//le patient est il hospitalisé ou UHCD ?
	if(  $obPatient->isInUHCD() || $obPatient->isHospitalise() )
	{

		$codeDiagPpal = $obPatient->getCodeDiagnostic();
		$libDiagPpal = $obPatient->getLibelleDiagnostic() ;

		//on récupère les diags interdits
		$obRequete = new clRequete(CCAM_BDD, 'codescim10interdits');
		$tabDiagsIntedits = $obRequete->exec_requete("SELECT * FROM codescim10interdits ", 'tab');
		foreach($tabDiagsIntedits as $tabInfoDiag)
		{
			if($tabInfoDiag['code'] == $codeDiagPpal )
			{
				return array( 'isContrainte' => true , 'titreContrainte' => "Diagnostic Interdit" , 'messageContrainte' => "Le diag principal $codeDiagPpal $libDiagPpal est interdit en cas d'hospitalisation ou d'UHCD " ) ;
			}
		}
	}
	
	return array( 'isContrainte' => false ) ;
}
