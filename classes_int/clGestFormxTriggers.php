<?php

class clGestFormxTriggers
{

	//controleur
	function __construct()
	{
		global $options ;
		if( isset($_POST['idTrigger'])  )
		{
			$trigger = new clTuFormxTrigger($_POST['idTrigger']);
			if( isset($_POST['activate'])  )
			{
				$trigger->start();
			}
			else if  ( isset($_POST['desactivate'])  )
			{
				$trigger->close();
			}
		}
		else if ( isset($_POST['export'])  )
		{
			$tabEnquete = clTuFormxTrigger::getTabEnquete($_POST['export']);
			$trigger = new clTuFormxTrigger($tabEnquete['id_trigger']);
			$dateD = new clDate($tabEnquete['date_debut']);
			$dateF = new clDate($tabEnquete['date_fin']);

			$data = array() ;
			$nomFic = '' ;
			clTuFormxTrigger::getDataExport($trigger,$dateD,$dateF,$data,$nomFic);

            $location = clFoRmXtOoLs::exportsGetCsvFromData($data,$nomFic);
            
			header('Location: '.$location);
		}
	}



    static public function genTabinfoIdPassage($idPassage)
    {
        return clPatient::getObjPatientFromIdPassage($idPassage)->getMiniExport() ;
    }
    


//templater
	function getAffichage()
	{

		global $session ;
		$af = '<h2>Gestion des enquetes</h2>';
		$link = 'index.php?navi='.$session->genNavi($session->getNaviFull());
		foreach(clTuFormxTrigger::getTriggers() as $trigger)
		{
			$af .= '<br />'.utf8_decode($trigger->getNomEnquete()).' ';
			if( $trigger->isActive() ) {
				if( $trigger->isClosable() )
					$af .= clTools::genLinkPost($link,"Cloturez l'enquête",array('desactivate'=>'y','idTrigger'=>$trigger->getIdTrigger()),array('alert'=>'Cloturer l\'enquête '.$trigger->getNomEnquete().'?'));
			}
			else if ( $trigger->isActivable() )
				$af .= clTools::genLinkPost($link,"Activez l'enquête",array('activate'=>'y','idTrigger'=>$trigger->getIdTrigger()),array('alert'=>'Commencer l\'enquête '.$trigger->getNomEnquete().'?'));

		}
		$af .= '<h2>Enquetes finies</h2>';
		$af .='L\'export peut parfois se révéler assez long. C\'est un comportement normal.<br />';

		foreach(clTuFormxTrigger::getTabEnquetesFinies() as $tabEnquete)
		{
			$trigger = new  clTuFormxTrigger($tabEnquete['id_trigger']) ;
			$af .= '<br />'.utf8_decode($trigger->getNomEnquete())." du ".clDate::getInstance($tabEnquete['date_debut'])->getSimpleDate();
			$af .=" au ".clDate::getInstance($tabEnquete['date_fin'])->getSimpleDate();
			$af .= ' '.clTools::genLinkPost($link,"Exporter",array('export'=>$tabEnquete['id_enquete']));
		}
		return $af ;
	}
}